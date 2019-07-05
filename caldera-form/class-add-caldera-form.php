<?php
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
class Add_Caldera_Form {
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

		// To Modify the Add to cart button for adding Toggle (pop up calder form ).
		add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'filter_woocommerce_loop_add_to_cart_link' ), 10, 2 );

		add_action( 'woocommerce_checkout_order_processed', array( $this, 'civicrm_sync_additional_data' ), 10, 1 );

		add_action( 'woocommerce_thankyou', array( $this, 'sessions_data_unset' ), 10, 1 );

		add_action( 'woocommerce_after_cart_item_name', array( $this, 'caldera_form_edit_additional_data' ), 10, 3 );

		add_action( 'caldera_forms_entry_saved', array( $this, 'slug_cf_store_entry_id' ), 10, 3 );

		add_filter( 'wp_ajax_load_caldera_delete_entry', array( $this, 'load_caldera_delete_entry' ), 1000  );

		add_filter( 'wp_ajax_nopriv_load_caldera_delete_entry', array( $this, 'load_caldera_delete_entry' ), 1000 );

		add_filter( 'woocommerce_return_to_shop_redirect', array( $this,'wc_empty_cart_redirect_url') );

		/*Updating Session Variable**/

		if(isset($_GET['delete-child'])) {
			$entry_id = $_GET['delete-child'];
			Caldera_Forms_Entry_Bulk::delete_entries( array( $entry_id ) );
			$count = 0; foreach ( $_SESSION['additional_data'] as $key ) {
			$session_entry_id = $_SESSION['additional_data']['entry_id'][ $count ];
				if ( $entry_id == $session_entry_id ) {
					unset( $_SESSION['additional_data']['entry_id'][ $count ] );
					unset( $_SESSION['additional_data']['caldera_form_id'][ $count ] );
					unset( $_SESSION['additional_data']['event_id'][ $count ] );
				}
			   $count++;
			}
	    }
	   /*Updating Session Variable**/
	}


	/**
	 * For Replacing Add to cart button in product listing and product detail page .
	 *
	 * @since    1.0.0
	 * @param   quantity of Product $quantity.
	 * @param   Product data        $product .
	 */
	public function filter_woocommerce_loop_add_to_cart_link( $quantity, $product ) {

		wp_enqueue_script( 'cfc-bact-front' );

		wp_localize_script( 'cfc-bact-front', 'ajax_custom', array( 'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ) ) );

		$product_id = get_the_ID( $product );

		$caldera_form_id = get_post_meta( $product_id, '_caldera_form_id', false );

		$link = $product->get_permalink();

		// checking Caldera form is assigned or not.
		if ( empty( $caldera_form_id ) ) {

			echo '<a href="' . site_url() . '/shop/?add-to-cart=' . $product_id . '" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . $product_id . '" data-product_sku="" aria-label="" rel="nofollow">Add to cart</a>';
		} else {

			echo do_shortcode( '[caldera_form_modal id="' . $caldera_form_id[0] . '" type="button"]Add to cart [/caldera_form_modal]' );
		}
	}

	/**
	 * Setting Javascript function for add to cart.
	 *
	 * @since    1.0.0
	 */
	public function bact_scripts_and_styles() {
		// Bact frontend script.
		wp_register_script( 'cfc-bact-front', CF_CIVICRM_INTEGRATION_URL . 'assets/js/bact-front.js', [ 'jquery' ], CF_CIVICRM_INTEGRATION_VER );
	}
	/**
	 * Addtional data For Event Participant.
	 *
	 * @since    1.0.0
	 * @param   $order_id
	 */
	public function civicrm_sync_additional_data( $order_id ) {
		$order = wc_get_order( $order_id );
		$email = $order->get_billing_email();

		$contact_parent    = civicrm_api3(
			'Contact', 'duplicatecheck', array(
				'return'           => array( 'id' ),
				'dedupe_rule_id'   => 4,   // This Field is Required to check the duplicate email.
				'check_permission' => false,
				'match'            => array(
					'contact_type' => 'Individual',
					'email'        => $email,
				),
			)
		);
		$contact_parent_id = $contact_parent ['id'];
		// Iterating through each order item.
		foreach ( $order->get_items() as $item_id => $item ) {
			$event_date_id   = wc_get_order_item_meta( $item_id, '_event_id', true ); // event date ID.
			$event_area_id   = get_post_meta( $event_date_id, '_event_area_id', false );
			$event_area_type = get_post_meta( $event_area_id, '_qsot-event-area-type', false );
			$ot_event_id     = wp_get_post_parent_id( $event_date_id );
			$cv_event_id     = get_post_meta( $event_date_id, '_civi_event_id', false );
			$campaign_id     = get_post_meta( $ot_event_id, 'campaign_id', false );
			$count           = 0;

			foreach ( $_SESSION['additional_data'] as $key ) {

				$entry_id = $_SESSION['additional_data']['entry_id'][ $count ];

				$form_id = $_SESSION['additional_data']['caldera_form_id'][ $count ];

				$form = Caldera_Forms_Forms::get_form( $form_id );

				foreach ( $form['processors'] as $key ) {
					$additional_data = $key['config'];
				}
				// Get form entry.
				$entry        = new Caldera_Forms_Entry( $form, $entry_id );
				$custom_field = $entry->get_fields();
				foreach ( $custom_field as $key ) {
					$caldera_form_field_ids[ $key->field_id ] = $key->value;
				}

				// Mapping Dynamic Front Mapping custom field and caldera form values.
				foreach ( $caldera_form_field_ids as $key => $value ) {
					$data_key = array_search( $key, $additional_data );
					if ( $data_key !== false ) {
						$additional_data[ $data_key ] = $value;
					}
				}
				// Checking Duplicate Contact In CIVICRM DB using Dedupe Rule .
				try {
					$create_child_contact = civicrm_api3(
						'Contact', 'create', [
							'contact_type' => 'Individual',
							'first_name'   => $additional_data['source'],
							'return'       => array( 'id' ),
						]
					);

					// Adding Relationship between child and parent.
					if ( $contact_parent_id ) {
						$relationship_contact = civicrm_api3(
							'Relationship', 'create', [
								'contact_id_a'         => $contact_parent_id,
								'contact_id_b'         => $create_child_contact['id'],
								'relationship_type_id' => 1,
							]
						);
					}

					// Updating Event Id and Contact ID here .
					$additional_data ['contact_id'] = $create_child_contact['id'];
					$additional_data['event_id']    = $cv_event_id[0];
					$additional_data['campaign_id'] = $campaign_id[0];

					unset( $additional_data['source'] );

					$participant_result = civicrm_api3( 'Participant', 'create', $additional_data );

					$_SESSION['addtional_count'] = $additional_data;
					$count++;

				} catch ( CiviCRM_API3_Exception $e ) {
					// Handle error here.
					$error_message = $e->getMessage();
					$error_code    = $e->getErrorCode();
					$error_data    = $e->getExtraParams();
					// Error Log file.
					$log = 'Date : ' . ' - ' . date( 'F j, Y, g:i a' ) . PHP_EOL .
												'Error Message: ' . $error_message . PHP_EOL .
												'Error Code: ' . $error_code . PHP_EOL .
												'------------------------------------------' . PHP_EOL;
					// Save string to log, use FILE_APPEND to append.
					$pluginlog = plugin_dir_path( __FILE__ ) . 'debug.log';
					error_log( $log, 3, $pluginlog );
				}
			}
		}
	}
	/**
	 * Adding Edit Caldera form for Editing  data for additional participant.
	 *
	 * @since  1.0.0
	 *
	 * @param  $cart_item
	 * @param  $cart_item_key
	 */
	public function caldera_form_edit_additional_data( $cart_item, $cart_item_key  ) {
		global $woocommerce;
			wp_enqueue_script( 'cfc-bact-front' );
			wp_localize_script( 'cfc-bact-front', 'ajax_custom', array( 'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ) ) );
		$ot_event_id     = $cart_item['event_id'];
		$caldera_form_id = get_post_meta( $ot_event_id, '_caldera_form_id', false );
		$count           = 0;
		$count_qty       = 0;
		

	if(isset($_SESSION['additional_data'])){
		echo '<table>';

		foreach ( $_SESSION['additional_data'] as $key ) {

			$entry_id            = $_SESSION['additional_data']['entry_id'][ $count ];
			$form_id             = $_SESSION['additional_data']['caldera_form_id'][ $count ];
			$session_ot_event_id = $_SESSION['additional_data']['event_id'][ $count ];

				if ( $form_id == $caldera_form_id[0] && $session_ot_event_id == $ot_event_id ) {
					echo '<tr><td>'.do_shortcode( '[caldera_form_modal id="' . $caldera_form_id[0] . '"  entry="' . $entry_id . '"  type="button"] Edit Participant [/caldera_form_modal]' ).'</td> <td>';

					echo '<a href="'.site_url().'/cart/?delete-child='.$entry_id.'" class="delete_entry" id="entry_' . $entry_id . '"> Delete </a>';
					$count_qty++;
					$_SESSION['product_quantity']=$count_qty;

				    add_filter( 'woocommerce_quantity_input_args', 
					function ( $args, $product ) {
					$args['input_value'] 	= $_SESSION['product_quantity'];
					$args['max_value'] 	    = 80; 
					$args['min_value'] 	    = 1;   	
					$args['step'] 		    = 1; 
	                return $args;	},10, 2 ); 
				}

				$count++;
				echo '</td></tr>';
		}
		echo '</table>';
	}
	}
	
	/**
	 * Addtional Information For Participant.
	 *
	 * @since    1.0.0
	 * @param   form submited entry id               $entry_id .
	 * @param   new entry id when new entry is added $new_entry .
	 * @param  form structure with all field        $form .
	 */
	public function slug_cf_store_entry_id( $entry_id, $new_entry, $form ) {

		if ( isset( $_POST['_cf_cr_pst'] ) ) {
			$event_id = sanitize_text_field( wp_unslash( $_POST['_cf_cr_pst'] ) );
		}
		$_SESSION['additional_data']['entry_id'][]        = $entry_id;
		$_SESSION['additional_data']['caldera_form_id'][] = $form['ID'];
		$_SESSION['additional_data']['event_id'][]        = $event_id;
	}
	/**
	 * Unset the Session Variable Additional Variable.
	 *
	 * @since    1.0.0
	 */
	public function sessions_data_unset() {

		unset( $_SESSION['additional_data'] );
	}
	/**
	 *  Delete one Caldera Forms entry
	 *
	 * @since    1.0.0
	 */
	public function load_caldera_delete_entry() {

		$entry_id = $_POST['entry_id'];
		Caldera_Forms_Entry_Bulk::delete_entries( array( $entry_id ) );
		$count = 0; foreach ( $_SESSION['additional_data'] as $key ) {
		$session_entry_id = $_SESSION['additional_data']['entry_id'][ $count ];
			if ( $entry_id == $session_entry_id ) {
				unset( $_SESSION['additional_data']['entry_id'][ $count ] );
				unset( $_SESSION['additional_data']['caldera_form_id'][ $count ] );
				unset( $_SESSION['additional_data']['event_id'][ $count ] );
			}
		   $count++;
		}
		die();
	}

    /**
	 * Unset the Session on emty cart
	 *
	 * @since    1.0.0
	 */
	 public function wc_empty_cart_redirect_url(   ) {

	 	unset( $_SESSION['additional_data']);

	 	return site_url().'/shop';
	}
}
