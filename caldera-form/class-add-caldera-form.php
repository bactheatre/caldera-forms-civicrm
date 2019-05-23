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
        add_filter( 'caldera_forms_ajax_return', array($this,'filter_caldera_forms_ajax_return'), 10, 2 ); 
        add_filter( 'woocommerce_loop_add_to_cart_link', array($this,'filter_woocommerce_loop_add_to_cart_link'), 10, 2 );
        add_filter( 'wp_head', array($this,'bact_js_variables'), 10, 2 );
        add_filter( 'wp_ajax_load_caldera_form_by_ajax', array($this,'load_caldera_form_by_ajax_callback'), 10, 2 );
        add_filter( 'wp_ajax_nopriv_load_caldera_form_by_ajax', array($this,'load_caldera_form_by_ajax_callback'), 10, 2 );
	}

	/**
	 * For Replacing Add to cart button in product listing and product detail page .
	 *
	 * @since    1.0.0
     * @param   for producnt quantity int $quantity,  and for product all the data string $product
	 */

	public function filter_woocommerce_loop_add_to_cart_link( $quantity, $product) { 

	    $post_id= get_the_ID($product);

	    $caldera_form_id=get_post_meta( $post_id, '_caldera_form_id', false );

	    $link = $product->get_permalink();
	   
	    echo do_shortcode("<button id='butinfo_".$post_id."' class='addtocartbutton checkout-button button alt wc-forward'>Learn more</button>");
	 	
	}


	/**
	 * Setting Ajax Url For the Ajax Request call 
	 *
	 * @since    1.0.0
     * 
     */

	public function bact_js_variables(){  ?>
		  <script type="text/javascript">
			var ajaxurl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
		  </script>
		 <?php
	}

	/**
	 * Show Pop up when we click on the  Add to cart Button
	 *
	 * @since    1.0.0
	 */
	public function load_caldera_form_by_ajax_callback() { 

	  $product_id=$_POST['product_id'];

	  $_caldera_form_id=get_post_meta( $product_id, '_caldera_form_id', false );

	  echo do_shortcode('[caldera_form id="'.$_caldera_form_id[0].'"]');

	 // echo '<a href="/shop/?add-to-cart='.$product_id.'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.$product_id.'" data-product_sku="" aria-label="" rel="nofollow">Add to cart</a>';


	 } 

	/**
	 * Show Pop up when we click on the  Add to cart Button
	 *
	 * @since    1.0.0
	 */
	public function filter_caldera_forms_ajax_return($form ,$out) {  


      echo "still working on it ";

	}
}
