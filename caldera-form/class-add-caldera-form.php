<?php 


/**
 * Add Caldera Form
 *
 * @since 0.4.2
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
     * @var object $plugin The plugin instance.
	 */
    public function __construct( $plugin ) {

    	// Wp Loaded Hook is for adding the Java Script and css files on wp_load hook
    	add_action( 'wp_loaded', [ $this, 'bact_scripts_and_styles' ] );

    	// To Modify the Add to cart button for adding Toggle (pop up calder form )

        add_filter( 'woocommerce_loop_add_to_cart_link', array($this,'filter_woocommerce_loop_add_to_cart_link'), 10, 2 );

        add_action( 'caldera_forms_submit_complete', array($this,'caldera_forms_additional_data'), 10, 4);


        add_action( 'caldera_forms_entry_saved', array($this,'slug_process_form', 10, 3 ) );

        // this ajax call is on hold for now when we add the pop up for caldera form then it it will work 

        // add_filter( 'wp_ajax_load_caldera_form_by_ajax', array($this,'load_caldera_form_by_ajax_callback'), 10, 2 );


        // add_filter( 'wp_ajax_nopriv_load_caldera_form_by_ajax', array($this,'load_caldera_form_by_ajax_callback'), 10, 2 );


       // add_action( 'wp_footer',array($this,'add_model_popup'), 5 );  

        add_action( 'woocommerce_thankyou', array( $this, 'civicrm_sync_additional_data' ), 10, 1 );    
	}

	/**
	 * For Replacing Add to cart button in product listing and product detail page .
	 *
	 * @since    1.0.0
     * @param   for producnt quantity int $quantity,  and for product all the data string $product
	 */
	public function filter_woocommerce_loop_add_to_cart_link( $quantity, $product) { 

		wp_enqueue_script( 'cfc-bact-front' );
        wp_localize_script('cfc-bact-front', 'ajax_custom', array( 'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ) ));
	    $product_id= get_the_ID($product);
	    $caldera_form_id=get_post_meta( $product_id, '_caldera_form_id', false );
	    $link = $product->get_permalink();
		if( empty($caldera_form_id )){

			echo '<a href="'.site_url().'/shop/?add-to-cart='.$product_id.'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.$product_id.'" data-product_sku="" aria-label="" rel="nofollow">Add to cart</a>';
        }else{

		    echo do_shortcode('[caldera_form_modal id="'.$caldera_form_id[0].'" type="button"]Add to cart [/caldera_form_modal]');
		  
	    }
	}

	/**
	 * Setting Bootstap css and js
	 *
	 * @since    1.0.0
     * 
     */
	public function bact_scripts_and_styles(){  
		 // Bact frontend script
		wp_register_script( 'cfc-bact-front', CF_CIVICRM_INTEGRATION_URL . 'assets/js/bact-front.js', [ 'jquery' ], CF_CIVICRM_INTEGRATION_VER );
	}

	/**
	 * Addtional Information For Participant 
	 *
	 * @since    1.0.0
     * @param   for producnt quantity int $quantity,  and for product all the data string $product
	 */


	public function civicrm_sync_additional_data( $order_id ) {

		$order = wc_get_order( $order_id );
		// Iterating through each order item.
		$count=0; 
		foreach ( $order->get_items() as $item_id => $item ) {
			$event_date_id = wc_get_order_item_meta( $item_id, '_event_id', true ); // event date ID.
			$event_area_id   = get_post_meta( $event_date_id, '_event_area_id', false );
			$event_area_type = get_post_meta( $event_area_id, '_qsot-event-area-type', false );
			$ot_event_id     = wp_get_post_parent_id( $event_date_id );
			$cv_event_id     = get_post_meta( $event_date_id, '_civi_event_id', false );
			$campaign_id     = get_post_meta( $ot_event_id, 'campaign_id', false );
			
			// sessions Varaible value Updated
			$_SESSION['addtional_data']['event_id'][$count]=$cv_event_id;
			$_SESSION['addtional_data']['campaign_id'][$count]=$cv_event_id;
		}

		$email           = $order->get_billing_email();
		
		if ( 'general-admission' === $event_area_type[0] ) {

			$role_id = 'Non Attendee';
		} else {
			$role_id = 'Attendee';
		}
		// Checking Duplicate Contact In CIVICRM DB using Dedupe Rule .
		try {  
				$contact_parent = civicrm_api3(
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

				$contact_parent_id = $contact ['id'];

				$create_child_contact = civicrm_api3(
					'Contact', 'create', [
					'contact_type' => 'Individual',
					'first_name'   => 'Children Name',
		            'return'       => array( 'id' ),]);

		        $contact_child_id = $create_child_contact['id'];

                $_SESSION['addtional_data']['contact_id'][$count]=$contact_child_id;

		     	$relationship_contact = civicrm_api3('Relationship', 'create', [
								  'contact_id_a' => $contact_id,
								  'contact_id_b' => $contact_child_id,
								  'relationship_type_id' => 1, ]);
		        $addtional_data =$_SESSION['addtional_data'];
				foreach ($addtional_data as $values ) {
					// Creating child  Participant Record In Purchase Event In CIVICRM Participant .
					$participant_result  = civicrm_api3( 'Participant', 'create', $values );
				 }

		} catch ( CiviCRM_API3_Exception $e ) {
				// Handle error here.
			$error_message = $e->getMessage();
			$error_code    = $e->getErrorCode();
			$error_data    = $e->getExtraParams();
			//Error Log file 
			$log  = "Date : ".' - '.date("F j, Y, g:i a").PHP_EOL.
									        "Error Message: ".$error_message.PHP_EOL.
									        "Error Code: ".$error_code.PHP_EOL.
									        "------------------------------------------".PHP_EOL;
			//Save string to log, use FILE_APPEND to append.
			$pluginlog = plugin_dir_path(__FILE__).'debug.log';
			error_log($log, 3, $pluginlog);
	   }
	}


}