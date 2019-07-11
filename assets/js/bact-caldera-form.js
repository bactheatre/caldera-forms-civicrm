var $ = jQuery;

jQuery( document ).ready(
	function() {

		bactInitializeGlobelCalls.init();
	}
);
var bactInitializeGlobelCalls = function () {
	var flexpassForm = function(){
		jQuery( '#showflexbtn' ).click(
			function(){
				jQuery( "#showflexar" ).show();
				jQuery( ".woocommerce-info1" ).hide();

			}
		);

	}
	var flexpassCheck = function(){

		jQuery( '#FlexSubmit' ).click(
			function(){
				var coupon_code = jQuery( "#coupon_code" ).val();
				var security_wpnonce = jQuery( "#_wpnonce" ).val();
				var data        = {
					'action': 'flexpass_action',
					'coupon_code': coupon_code,
					'security_wpnonce':security_wpnonce };
				$.ajax(
					{
						url: ajaxurl,
						type: 'post',
						data: data,
						beforeSend: function () {
							jQuery( '.loader_img' ).show();
						},
						error: function (error) {
							console.log( error )
						},
						success: function (response) {
							var pageURL          = jQuery( location ).attr( "href" );
							var splitpageURL     = pageURL.split( '#' );
							var pageURL          = splitpageURL[0];
							window.location.href = pageURL;
							jQuery( '.vail_flexpass' ).show();
							jQuery( '#showflexar' ).hide();
							jQuery( '.loader_img' ).hide();

						}
					}
				);
			}
		);
	}


	return {
		init: function () {
			flexpassForm();
			
		}
	};
}();
