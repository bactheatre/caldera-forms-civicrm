/**
* Call Back Function after Submitting the Caldera Form .
*
*
*/

var $ = jQuery;
jQuery(document).ready(function() {
    initializeGlobelCalls.init();
});

var initializeGlobelCalls = function () {

    var CalderaFormModel = function(){
       jQuery("form").each(function(){
          jQuery(this).find("input[type=submit]").on("click", function(){
            var product_id = jQuery(this).parents('form').find("input[name=_cf_cr_pst]").val();
            console.log(product_id);
            var pageURL = jQuery(location).attr("href");
            var splitpageURL = pageURL.split('#');
            var pageURL = splitpageURL[0];
            window.location.href =pageURL+"?add-to-cart="+product_id;
           })
       });
     }

 var qty_get = function(){
    $( document ).on( 'change', 'input.qty', function() {

        var item_hash = $( this ).attr( 'name' ).replace(/cart\[([\w]+)\]\[qty\]/g, "$1");
        var item_quantity = $( this ).val();
        var currentVal = parseFloat(item_quantity);

        function qty_cart() {

            $.ajax({
                type: 'POST',
                url: 'http://localhost/ADC/bact/wp-admin/admin-ajax.php'
                data: {
                    action: 'qty_cart',
                    hash: item_hash,
                    quantity: currentVal
                },
                success: function(data) {
               
                }
            });  

        }

        qty_cart();

    });

}













 return {
        init: function () {
            CalderaFormModel();
            qty_get();
            
        }
    };
}();

