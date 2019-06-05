/**
* Call Back Function after Submitting the Caldera Form .
*
*
*/

// jQuery(document).ready(function($){

//  $('.addtocartbutton').click(function(){

//    var id = this.id;
//    var splitid = id.split('_');
//    var product_id = splitid[1];
// });



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
 return {
        init: function () {
            CalderaFormModel();
            
        }
    };
}();


