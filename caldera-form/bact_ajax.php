<?php if ( __FILE__ == $_SERVER['SCRIPT_FILENAME'] ) die( header( 'Location: /') );

// load the ajax handler class. performs most authentication and request validation for registered ajax requests
class BACT_AJAX {
	// container for the singleton instance

   public $plugin;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var  object $plugin The plugin instance.
	 * @param  object $plugin  instance.
	 */
	public function __construct( $plugin ) {

	
	    add_action( 'wp_ajax_qsot-frontend-ajax', array( &$this, 'handle_request' ) );
		add_action( 'wp_ajax_nopriv_qsot-frontend-ajax', array( &$this, 'handle_request' ) );
       
	}

	public function handle_request(){

		die("test");
	}

}