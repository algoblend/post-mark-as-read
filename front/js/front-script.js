jQuery(document).ready( function($){
  /* Some event will trigger the ajax call, you can push whatever data to the server, */
  /* simply passing it to the "data" object in ajax call */

    $('#pmarPostID').click(function() {
      var pmarPostID =  $("#pmarPostID").val();
      if (pmarPostID == "" ) {
        alert("Please Reload the page.");
      }else{
        $.ajax({
          url: pmar_ajax_object.pmarAjaxURL, /* this is the object instantiated in wp_localize_script function */
          type: 'POST',
          data:{
            action: pmar_ajax_object.pmarAjaxAction, /* this is the function in your functions.php that will be triggered */
            post_id: pmarPostID
          },
          success: function( data ) {
            //  Do something with the result from server
            if (data.status == "error") {

            }
            if (data.status == 'read') {
              $("#pmarPostID").addClass('pmar_read');
              $("#pmarPostID").empty().html('<i class="fas fa-check"></i> Completed');
            }else if(data.status == 'unread'){
              $("#pmarPostID").removeClass('pmar_read');
              $("#pmarPostID").empty().html('<i class="fas fa-circle"></i> Complete');
            }
          }
        });
      }
    });
});