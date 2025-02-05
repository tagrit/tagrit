<script>


//preview purchase order attachment
function preview_proposal_btn(invoker){
  "use strict"; 
    var id = $(invoker).attr('id');
    var rel_id = $(invoker).attr('rel_id');
    view_proposal_file(id, rel_id);
}

function view_proposal_file(id, rel_id) {
  "use strict"; 
      $('#proposal_file_data').empty();
      $("#proposal_file_data").load(admin_url + 'warehouse/file_proposal/' + id + '/' + rel_id, function(response, status, xhr) {
          if (status == "error") {
              alert_float('danger', xhr.statusText);
          }
      });
}

function close_modal_preview(){
  "use strict"; 
 $('._project_file').modal('hide');
}

function delete_wh_proposal_attachment(id) {
  "use strict"; 
    if (confirm_delete()) {
        requestGet('warehouse/delete_proposal_attachment/' + id).done(function(success) {
            if (success == 1) {
                $("#proposal_pv_file").find('[data-attachment-id="' + id + '"]').remove();
            }
        }).fail(function(error) {
            alert_float('danger', error.responseText);
        });
    }
  }



function proposal_convert_processing(argument, proposal_id) {
  "use strict";
  var data={};
      data.proposal_id = proposal_id;
      $.post(admin_url + 'warehouse/proposal_convert_processing', data).done(function(response){
          response = JSON.parse(response); 
          if(response.status == true || response.status == "true"){
            alert_float('success', response.message);
          }else{

            alert_float('warning', response.message);
          }
          location.reload();

      });

}

function convert_lead_to_customer(){
  "use strict";
  $('#convert_lead_to_client_modal').modal('show');
}
  


</script>