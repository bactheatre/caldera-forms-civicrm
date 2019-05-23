<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/
 * @since      1.0.0
 *
 * @package    Bact_woocommerce_civicrm
 * @subpackage Bact_woocommerce_civicrm/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bact_woocommerce_civicrm
 * @subpackage Bact_woocommerce_civicrm/admin
 * @author     Sainath Batte <sainath.batte@clariontechnologies.co.in>
 */
class Add_CV_Custom_Field_Group {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		add_action( 'save_post', array( $this, 'add_custom_field_set' ), 13, 2 );
	}
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $post  this parameter is for getting the event data.
	 */
	public function add_custom_field_set( $post) {

		/**
		* This function adding Custom Field Group  in Civicrm for participant record.
		*/
		global $post;

		 $ot_event_id=$_POST['post_ID'];

	
			// Setting  and Verifying Value for Campaign in civicrm .
			if ( isset( $_POST['post_type'] ) && 'qsot-event' === $_POST['post_type'] && isset( $_POST['_wpnonce'] ) ) {
				if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) ) ) {

					$caldera_form_detail= get_post_meta( $ot_event_id, '_caldera_form_id', false );

					
					$caldera_form_title  = explode( '+', $caldera_form_detail[0] );

						try{	

							$custom_group = civicrm_api3('CustomGroup', 'create', [
									  'title' => $caldera_form_title[1],
									  'extends' => "Participant",
									  'return'           => array( 'name' ),
									]);

                           $custom_group_id     = $custom_group['id'];
                           $custom_group_values = $custom_group['values'][$custom_group_id];
                           $custom_group_name   = $custom_group_values['name'];
                          
                            if( get_post_meta( $ot_event_id, '_custom_field_group_name', false )){
                               
                                  $temp_custom_field_name = get_post_meta( $ot_event_id, '_custom_field_group_name', false );
                            	  $custom_group_name = $temp_custom_field_name[0];
	                           
                            }else{

                            	 $custom_group_name   = $custom_group_values['name'];
                            	 
                            }
                        
                      
                             $CustomField = civicrm_api3('CustomField', 'create', [
										 'custom_group_id' => $custom_group_name,
										  'name' => "ABC1",
										  'label' => "Test1",
										  'data_type' => "String",
										  'option_group_id' => "custom_data_type",
										  'html_type' => "Text",
							]);

							
						} catch ( CiviCRM_API3_Exception $e ) {
											// Handle error here.
										$error_message = $e->getMessage();
										$error_code    = $e->getErrorCode();
										$error_data    = $e->getExtraParams();
										//Error Log file 
										$log  = "Date : ".' - '.date("F j, Y, g:i a").PHP_EOL.
								        "Error Message: ".$error_message.PHP_EOL.
								        "Error Code: ".$error_code.PHP_EOL.
								        "Class: Add_CV_Custom_Field_Group".PHP_EOL."------------------------------------------".PHP_EOL;
										//Save string to log, use FILE_APPEND to append.
										$pluginlog = plugin_dir_path(__FILE__).'debug.log';
										error_log($log, 3, $pluginlog);
								
						}

					
						/**
						*Setting meta field for the Reference  Custom Field Group .
						*/
						if ( get_post_meta( $ot_event_id, '_custom_field_group_name', false ) ) {
							// If the custom field already has a value, update it.
							update_post_meta($ot_event_id, '_custom_field_group_name', $custom_group_name );
							} else {
							// If the custom field doesn't have a value, add it.
							add_post_meta($ot_event_id, '_custom_field_group_name', $custom_group_name );
						}

						 
				}
			}
		}
	}

