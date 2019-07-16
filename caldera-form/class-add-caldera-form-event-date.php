<?php  if ( __FILE__ == $_SERVER['SCRIPT_FILENAME'] ) die( header( 'Location: /') );
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bact_woocommerce_civicrm
 * @subpackage caldera-form-civicrm/caldera-form
 * @author     Sainath Batte <sainath.batte@clariontechnologies.co.in>
 */
class Add_Caldera_Form_Event_Date extends QSOT_Templates  {
	/**
	 * Plugin reference.
	 *
	 * @since 0.4.4
	 * @access public
	 * @var object $plugin The plugin instance
	 */
	public $plugin;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var  object $plugin The plugin instance.
	 * @param  object $plugin  instance.
	 */
	public function __construct( $plugin ) {

		// Wp Loaded Hook is for adding the Java Script and css files on wp_load hook.
		add_action( 'wp_loaded', [ $this, 'bact_scripts_and_styles' ] );
        add_filter( 'qsot-event-frontend-settings', array( $this, 'overtake_some_woocommerce_core_templates'),10,2);
        add_filter('qsot-locate-template', array(__CLASS__, 'locate_template'), 10, 4);
        add_action( 'woocommerce_after_cart_item_quantity_update', array( &$this, 'update_reservations_on_cart_update' ), 10, 3 );
        add_action('wp_head', array( $this, 'hook_javascript') );

	}

	/**
	 * Setting Javascript function for add to cart.
	 *
	 * @since    1.0.0
	 */
	public function bact_scripts_and_styles() {
		// Bact frontend script.
		wp_register_script( 'bact_front_event_date', CF_CIVICRM_INTEGRATION_URL . 'assets/js/bact_front_event_date.js', [ 'jquery' ], CF_CIVICRM_INTEGRATION_VER );
		wp_enqueue_script( 'bact_front_event_date' );
	}

	/**
	 * Addtional Information For Participant.
	 *
	 * @since    1.0.0
	 * @param   form submited entry id               $entry_id .
	 * @param   new entry id when new entry is added $new_entry .
	 * @param  form structure with all field        $form .
	 */

    public	function overtake_some_woocommerce_core_templates( $data ,$event){

      $data['templates']= $this->get_templates($event);

   

      return $data;
    }
	
  // get the frontend template to use in the event selection ui.
	public function get_templates( $event ) {
		// make sure we have an event area
		$event->event_area = isset( $event->event_area ) && is_object( $event->event_area ) ? $event->event_area : apply_filters( 'qsot-event-area-for-event', false, $GLOBALS['post'] );

		// if there is no event area, then bail
		if ( ! isset( $event->event_area ) || ! is_object( $event->event_area ) )
			return apply_filters( 'qsot-gaea-event-frontend-templates', apply_filters( 'qsot-event-frontend-templates', array(), $event ) );

		// get a list of all the templates we need
		$needed_templates = apply_filters( 'qsot-gaea-frontend-templates', array( 'ticket-selection', 'owns', 'msgs', 'msg', 'error', 'ticket-type' ), $event, $this );

		// aggregate the data needed for the templates
		$args = array(
			'limit' => apply_filters( 'qsot-event-ticket-purchase-limit', 0, $event->ID ),
			'max' => 1000000,
			'cart_url' => '#',
		);

		$cart = WC()->cart;
		// if there is a cart, then try to update the cart url
		if ( is_object( $cart ) )
			$args['cart_url'] = $cart->get_cart_url();

		// figure out the true max, based on available info
		;

		// allow modification of the args
		$args = apply_filters( 'qsot-gaea-frontend-templates-data', $args, $event, $this );

		$templates = array();
		// load each template in the list
		 foreach ( $needed_templates as $template )
	     $templates[ $template ] = QSOT_Templates::maybe_include_template( 'event-area/general-admission/' . $template . '.php', $args );
		 return apply_filters( 'qsot-gaea-event-frontend-templates', $templates, $event );
	}

	// locate a given template. first check the theme for it, then our plugin dirs for fallbacks
	public static function locate_template( $current='', $files=array(), $load=false, $require_once=false ) {
		// normalize the list of potential files
		$files = ! empty( $files ) ? (array)$files : $files;

		// if we have a list of files
		if ( is_array( $files ) && count( $files ) ) {
			// first search the theme
			$templ = locate_template( $files, $load, $require_once );

			// if there was not a matching file in the theme, then search our backup dirs
			if ( empty( $templ ) ) {
				// aggregate a list of backup dirs to search
				$dirs = apply_filters( 'qsot-template-dirs', array( self::$o->core_dir . 'templates/' ) );
				$qsot_path = '';

				// add the legacy directory within the theme that holds the legacy OTCE templates
				array_unshift( $dirs, get_stylesheet_directory() . '/' . $qsot_path, get_template_directory() . '/' . $qsot_path );

				// for each file in the list, try to find it in each backup dir
				foreach ( $files as $file ) {
					// normalize the filename, and skip any empty ones
					$file = trim( $file );
					if ( '' === $file )
						continue;

					// check each backup dir for this file
					foreach ( $dirs as $dir ) {
						$dir = trailingslashit( $dir );
						// if the file exists, then use that one, and bail the remainder of the search
						if ( file_exists( $dir . $file ) && is_readable( $dir . $file ) ) {

							if('event-area/general-admission/owns.php'== $file){
								$templ=plugin_dir_path(__FILE__).'templates/event-area/general-admission/custom-owns.php';
							}else{
							$templ = $dir . $file;
						     }
							break 2;
						}
					}
				}
			}
			// if there is a template found, and we are being asked to include it, the include it, by either 'require' or 'include' depending on the passed params
			if ( ! empty( $templ ) && $load ) {
				if ( $require_once )
					require_once $templ;
				else
					include $templ;
			}

			// if we found a template, make sure to update the return value with the full path to the file
			if ( ! empty( $templ ) )
				$current = $templ;
		}

		return $current;
	}
	

	// when updating the quantity of tickets in the cart page, we need to perform the same update on our reservations, if allowed
	public function update_reservations_on_cart_update( $cart_item_key, $quantity, $old_quantity ) {

		// if this is not an update cart scenario, then bail now
		if ( ! isset( $_POST['update_cart'] ) )
			return;

		// fetch the zoner
		$zoner = $this->get_zoner();
		$stati = $zoner->get_stati();

		// get the cart item
		$items = WC()->cart->get_cart();
		$item = isset( $items[ $cart_item_key ] ) ? $items[ $cart_item_key ] : false;
		if ( empty( $item ) || ! isset( $item['event_id'] ) )
			return;

		// load the event and check that it is for this type of event area before doing anything else
		$area_type = apply_filters( 'qsot-event-area-type-for-event', false, $item['event_id'] );
		if ( ! is_object( $area_type ) || is_wp_error( $area_type ) || $area_type->get_slug() !== $this->get_slug() )
			return;

		// remove recursive filter
		remove_action( 'woocommerce_after_cart_item_quantity_update', array( &$this, 'update_reservations_on_cart_update' ), 10 );

		// update the reservations
		$result = $zoner->reserve( false, array(
			'event_id' => $item['event_id'],
			'ticket_type_id' => $item['product_id'],
			'quantity' => $quantity,
		) );

		// if the update failed, then revert the quantity
		if ( ! is_wp_error( $result ) && is_scalar( $result ) && $result > 0 ) {
			// if the final quantity does not equal the requested quantity, then pop a message indicating that the reason is because there are not enough tickets
			if ( $result != $quantity )
				wc_add_notice( sprintf( __( 'There were not %d tickets available. We reserved %d for you instead, which is all that is available.', 'opentickets-community-edition' ), $quantity, $result ), 'error' );

			WC()->cart->set_quantity( $cart_item_key, $result, true );
		} else if ( ! $result || is_wp_error( $result ) ) {
			// reset the quantity and pop an error as to why
			WC()->cart->set_quantity( $cart_item_key, $old_quantity, true );
			if ( is_wp_error( $result ) )
				wc_add_notice( implode( '', $result->get_error_messages() ), 'error' );
			else
				wc_add_notice( __( 'Could not update the quantity of that item.', 'opentickets-community-edition' ), 'error' );
		}

		// readd this filter for later checks
		
	}

	/**
	 * Addtional Jquery files and script modal .
	 *
	 * @since    1.0.0
	 * @param   form submited entry id               $entry_id .
	 * @param   new entry id when new entry is added $new_entry .
	 * @param  form structure with all field        $form .
	 */


     public function hook_javascript() { 
      ?>
     
      <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
	  <script>
	  $( function() {
	    $( "#tabs" ).tabs();
	  } );
        </script>
    <?php
}

}
