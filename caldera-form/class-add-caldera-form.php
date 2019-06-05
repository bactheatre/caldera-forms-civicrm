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

        add_action( 'woocommerce_checkout_order_processed', array( $this, 'civicrm_sync_additional_data' ), 10, 1 );    

        add_action( 'woocommerce_thankyou', array( $this, 'sessions_data_unset' ), 10, 1 );   

        
        
        add_action( 'woocommerce_after_cart_item_name', array( $this, 'caldera_form_edit_additional_data' ), 10, 2 );   

        add_action( 'caldera_forms_entry_saved', array( $this,'slug_cf_store_entry_id'), 10, 3 ) ;   
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
        $email           = $order->get_billing_email();

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

		$contact_parent_id = $contact_parent ['id'];

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
				$_SESSION['addtional_data'][$count]['event_id']    = $cv_event_id[0];
				$_SESSION['addtional_data'][$count]['campaign_id'] = $campaign_id[0];
			   
			
			// Checking Duplicate Contact In CIVICRM DB using Dedupe Rule .
			try {       
				       $create_child_contact = civicrm_api3(
						'Contact', 'create', [
						'contact_type' => 'Individual',
						'first_name'   => $_SESSION['addtional_data'][$count]['custom_2'],
			            'return'       => array( 'id' ),]);

				/*if($contact_parent_id) {
	             $relationship_contact = civicrm_api3('Relationship', 'create', [
									  'contact_id_a' => $contact_parent_id,
									  'contact_id_b' => $contact_child_id,
									  'relationship_type_id' => 1, ]);
	            }*/

	            $_SESSION['addtional_data'][$count]['contact_id']= $create_child_contact['id'];
			    $addtional_data=$_SESSION['addtional_data'][$count];

			    // Creating child  Participant Record In Purchase Event In CIVICRM Participant .
				$participant_result  = civicrm_api3( 'Participant', 'create', $addtional_data );
			

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
		 $count++; 
	    }

	}
     
    /**
	 * Addtional Information For Participant 
	 *
	 * @since    1.0.0
     * @param   for producnt quantity int $quantity,  and for product all the data string $product
	 */

    public function caldera_form_edit_additional_data(  $cart_item, $cart_item_key ) {

   
    	$entry_id=$_SESSION['addtional_data_edit']['entry_id'];
        $ot_event_id=$cart_item['event_id'];
        $caldera_form_id=get_post_meta( $ot_event_id, '_caldera_form_id', false );

        if( !empty($caldera_form_id ) && $_SESSION['addtional_data_edit']['caldera_form_id']== $caldera_form_id[0] ){

	    echo do_shortcode('[caldera_form_modal id="'.$caldera_form_id[0].'"  entry="'.$entry_id.'"  type="button"] Edit Participant [/caldera_form_modal]');
       	}
    }


     /**
	 * Addtional Information For Participant 
	 *
	 * @since    1.0.0
     * @param   for producnt quantity int $quantity,  and for product all the data string $product
	 */

    public function slug_cf_store_entry_id( $entry_id, $new_entry, $form) {

    	$_SESSION['addtional_data_edit']['entry_id'][]       = $entry_id;

    	$_SESSION['addtional_data_edit']['caldera_form_id'][] = $form['ID'];
    
    }

    /**
	 * Addtional Information For Participant 
	 *
	 * @since    1.0.0
     * @param   for producnt quantity int $quantity,  and for product all the data string $product
	 */

    public function sessions_data_unset( $order_id  ){

     unset ($_SESSION["addtional_data"]);

    
		                
    
    }



    

}