jQuery(document).ready(function($){

 $('.addtocartbutton').click(function(){

   var id = this.id;
   var splitid = id.split('_');
   var product_id = splitid[1];

  $('#showform'+product_id).toggle(500);
/*
     var data = {
      'action': 'load_caldera_form_by_ajax',
      'product_id': product_id, };

   // AJAX request
   $.ajax({
    url: ajax_custom.ajaxurl,
    type: 'post',
    data: data,

     beforeSend: function() {
         $('#empModal').empty();
      },
    success: function(response){ 

      // Add response in Modal body
      $('#empModal').append(response);

      // Display Modal
      $('#empModal').modal('show'); 
    }
  });*/
 });
});


