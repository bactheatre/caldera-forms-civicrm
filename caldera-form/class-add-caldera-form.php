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

    	add_action( 'wp_loaded', [ $this, 'bact_scripts_and_styles' ] );
        add_filter( 'caldera_forms_ajax_return', array($this,'filter_caldera_forms_ajax_return'), 10, 2 ); 
        add_filter( 'woocommerce_loop_add_to_cart_link', array($this,'filter_woocommerce_loop_add_to_cart_link'), 10, 2 );
        add_filter( 'wp_ajax_load_caldera_form_by_ajax', array($this,'load_caldera_form_by_ajax_callback'), 10, 2 );
        add_filter( 'wp_ajax_nopriv_load_caldera_form_by_ajax', array($this,'load_caldera_form_by_ajax_callback'), 10, 2 );
        add_action('wp_footer',array($this,'add_model_popup'), 5 );      
	}

	/**
	 * For Replacing Add to cart button in product listing and product detail page .
	 *
	 * @since    1.0.0
     * @param   for producnt quantity int $quantity,  and for product all the data string $product
	 */

	public function filter_woocommerce_loop_add_to_cart_link( $quantity, $product) { 
		wp_enqueue_script( 'cfc-bact-front' );
		//wp_enqueue_style('cfc-bact-bootstrap-css');
        wp_enqueue_script( 'cfc-bact-bootstrap');
        wp_localize_script('cfc-bact-front', 'ajax_custom', array( 'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ) ));
		

	    $post_id= get_the_ID($product);
	    $caldera_form_id=get_post_meta( $post_id, '_caldera_form_id', false );
	    $link = $product->get_permalink();
		    if( empty($caldera_form_id )){

			    echo '<a href="'.site_url().'/shop/?add-to-cart='.$post_id.'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.$post_id.'" data-product_sku="" aria-label="" rel="nofollow">Add to cart</a>';

		    	}else{
		         
		        echo do_shortcode("<button id='butinfo_".$post_id."' class='addtocartbutton checkout-button button alt wc-forward'>Add to cart</button>");

		       echo  '<div style="display:none" id="showform'.$post_id.'">'.do_shortcode('[caldera_form id="'.$caldera_form_id[0].'"]').'</div>';
	        }
	}

	/**
	 * Setting Bootstap css and js
	 *
	 * @since    1.0.0
     * 
     */
	public function bact_scripts_and_styles(){  

	
		//wp_register_style( 'cfc-bact-bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css', [ 'jquery' ], CF_CIVICRM_INTEGRATION_VER );
		wp_register_script( 'cfc-bact-bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js', [ 'jquery' ], CF_CIVICRM_INTEGRATION_VER );
		 // Bact frontend script
		wp_register_script( 'cfc-bact-front', CF_CIVICRM_INTEGRATION_URL . 'assets/js/bact-front.js', [ 'jquery' ], CF_CIVICRM_INTEGRATION_VER );
	}
     

     /**
	 * add_model_popup
	 *
	 * @since    1.0.0
     * 
     */
	public function  add_model_popup(){

	   echo '
          <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	   <div class="modal fade" id="empModal" style="display: none;"  tabindex="-1" role="dialog" 
	                    aria-labelledby="exampleModalLabel" aria-hidden="true"></div>'; 
	}

	/**
	 * Show Pop up when we click on the  Add to cart Button
	 *
	 * @since    1.0.0
	 */
	public function load_caldera_form_by_ajax_callback() { 

	  $product_id=$_POST['product_id'];

	  $_caldera_form_id=get_post_meta( $product_id, '_caldera_form_id', false );

	  echo '<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
					     <h4 class="modal-title" id="exampleModalLabel">'.get_the_title($product_id).'</h4>
					     <h5>Additional information</h5>
					    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					     <span aria-hidden="true">&times;</span>
					     </button>
					 </div>
					<div class="modal-body">'.do_shortcode('[caldera_form id="'.$_caldera_form_id[0].'"]').'
                    </div>
				</div>
		</div>';
       die();
	 } 

	/**
	 * Show Pop up when we click on the  Add to cart Button
	 *
	 * @since    1.0.0
	 */
	 public function filter_caldera_forms_ajax_return($form ,$out) {  

		//print_r($out);

		//die( "working on it ");
	}
}
