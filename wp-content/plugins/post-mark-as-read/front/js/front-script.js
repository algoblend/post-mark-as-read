jQuery(document).ready( function($){
    $('#pmarPostID').click(function() {
      var pmarPostID =  $("#pmarPostID").val();
      if (pmarPostID == "" ) {
        alert("Please Reload the page.");
      }else{
        $.ajax({
          url: pmar_ajax_object.pmarAjaxURL,
          type: 'POST',
          data:{
            action: pmar_ajax_object.pmarAjaxAction,
            post_id: pmarPostID,
            nonce: pmar_ajax_object.nonce
          },
          success: function( data ) {
            if (data.success === false) {
              alert(data.data.message || 'An error occurred');
              return;
            }
            if (data.status == 'read') {
              $("#pmarPostID").addClass('pmar_read');
              $("#pmarPostID").empty().html('<i class="fas fa-check"></i> Completed');
            }else if(data.status == 'unread'){
              $("#pmarPostID").removeClass('pmar_read');
              $("#pmarPostID").empty().html('<i class="fas fa-circle"></i> Complete');
            }
          },
          error: function(xhr, status, error) {
            alert('An error occurred: ' + error);
          }
        });
      }
    });
});