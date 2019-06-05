<?php
/**
 * Add Meta Box for Caldera form.
 *
 * @since 0.4.2
 */

class Caldera_Form_Meta_Box_Admin {

/**
	 * Plugin reference.
	 *
	 * @since 0.4.4
	 * @access public
	 * @var object $plugin The plugin instance
	 */
	public $plugin;

	/**
	 * Initialises this object.
	 *
	 * @since 0.4.4
	 */

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin) {

	
		add_action( 'add_meta_boxes', array( $this, 'calder_form_meta_box' ) );
		add_action( 'save_post', array( $this, 'calder_form_save' ) );
	}

	/**
	 * Adds Event Type meta box container.
	 *
	 * @param  string $post_type post type of the opene ticket event.
	 */
	public function calder_form_meta_box( $post_type ) {
			$post_types = array( 'qsot-event' );
		// limit meta box to certain post types.
		if ( in_array( $post_type, $post_types, true ) ) {
			add_meta_box(
				'_caldera_form_id',
				'Additional Information Form',
				array( $this, 'get_caldera_form' ),
				$post_type,
				'side',
				'default'
			);
		}
	}
	/**
	 * Fetching Event Type form CiviCrm.
	 *
	 *  @param  string $post event post data.
	 */
	public function get_caldera_form( $post ) {
		/**
		 * This function Fetching the data of event type in CiviCRM and  showing on the meta box in event custom post type
		*/
		global $post;
		// Nonce field to validate form request came from current site.
		wp_nonce_field( basename( __FILE__ ), 'calder_form_nonce' );
		$calder_form_id   = get_post_meta( $post->ID, '_caldera_form_id', true );
		?>
			<select name="_caldera_form_id">
				<option value="0">Select Form</option>
				<?php $forms = Caldera_Forms_Forms::get_forms( true );
				foreach ( $forms as $calder_form ) {
					?>
				<option value="<?php echo esc_html( $calder_form['ID']); ?>" 
										  <?php
											if ( $calder_form_id === $calder_form['ID']  ) {

												echo esc_html( __( 'selected', 'Bact_woocommerce_civicrm' ) ); }
											?>
				><?php echo esc_html( $calder_form['name'] ); ?></option>
				<?php } ?>
			</select>
			<?php
		
	}
	/**
	 * Save  Event Type form CiviCrm to Event In Open Ticket .
	 *
	 * @param   string $post_id event post id.
	 */
	public function calder_form_save( $post_id ) {

		if ( isset( $_POST['_wpnonce'] ) && isset( $_POST['post_type'] ) && 'qsot-event' === $_POST['post_type'] ) {

			// Setting  and Verifying Value.
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $otevent_type_data['_wpnonce'] ) ) ) ) {
				$ot_event_form_data = $_POST;

				/*
				* We need to verify this came from the our screen and with.
				* proper authorization,
				* because save can be triggered at other times.
				*/
				// Check if our nonce is set.
				// Return if the user doesn't have edit permissions.
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return $post_id;
				}
				// Verify this came from the our screen and with proper authorization,
				// because save_post can be triggered at other times.
				if ( ! isset( $ot_event_form_data['_caldera_form_id'] ) || ! wp_verify_nonce( $ot_event_form_data['calder_form_nonce'], basename( __FILE__ ) ) ) {
					return $post_id;
				}
				// Now that we're authenticated, time to save the data.
				// This sanitizes the data from the field and saves it into an array $events_meta.
				$events_meta['_caldera_form_id'] = sanitize_text_field( $ot_event_form_data['_caldera_form_id'] );
				// Cycle through the $events_meta array.
				// Note, in this example we just have one item, but this is helpful if you have multiple.
				foreach ( $events_meta as $key => $value ) :
					// Don't store custom data twice.
					if ( 'revision' === $post->post_type ) {
						return;
					}
					if ( get_post_meta( $post_id, $key, false ) ) {
						// If the custom field already has a value, update it.
						update_post_meta( $post_id, $key, $value );
					} else {
						// If the custom field doesn't have a value, add it.
						add_post_meta( $post_id, $key, $value );
					}
					if ( ! $value ) {
						// Delete the meta key if there's no value.
						delete_post_meta( $post_id, $key );
					}
				endforeach;
			}
		}
	}
}
