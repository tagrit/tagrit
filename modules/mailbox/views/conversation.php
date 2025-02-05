<div role="tabpanel" class="tab-pane" id="conversation">
   <div>
      <div class="activity-feed">
         <table class="table table-mailbox dataTable no-footer">
            <thead>
               <tr>
                  <th>Email's Subject</th>
                  <th>Date</th>
                  <th>Action</th>
               </tr>
            </thead>
            <tbody>
               <?php foreach ($conversation as $key => $value) {
                  if ($value['outbox_id']) {
                   $staff_id  = $value['sender_staff_id'];
                   $date_sent  = $value['date_sent'];
                   $value['mail_id'] = $value['outbox_id'];
                   $id  = $value['outbox_id'];
                  }else{
                    $staff_id  = $value['from_staff_id'];
                    $date_sent  = $value['date_received'];
                    $value['mail_id'] = $value['inbox_id'];
                    $id  = $value['inbox_id'];
                  }
                  $value['profile'] = staff_profile_image($staff_id, ['mr-2 rounded-circle']);
                  $value['module_dir_url'] = module_dir_url(MAILBOX_MODULE);
                  $value['date_sent'] = _dt($date_sent);
                  $value['view_url'] = admin_url().'mailbox/reply/'.$id.'/reply/outbox';
                  $value['get_staff_email_by_id'] = get_staff_email_by_id($staff_id);
                  if ($value['has_attachment'] > 0) {
                      if ($value['outbox_id']) {
                        $attachments = get_mail_attachment($value['outbox_id'],"outbox");
                      }else{
                        $attachments = get_mail_attachment($value['inbox_id'],"inbox");
                      }
                      $value['attachments'] = $attachments;
                      foreach ($value['attachments'] as $key => $values) {
                          // $value['get_mime_class'] = get_mime_class($value['type']);
                          $value['attachments'][$key]['get_mime_class'] = get_mime_class($values['file_type']);
                      }
                  }
                  ?>
               <tr>
                  <td><?php echo $value['subject']; ?></td>
                  <td><?php echo $date_sent; ?></td>
                  <td>
                     <button class="btn btn-primary" type="button" onclick="send_mail_modal(<?php echo htmlspecialchars(json_encode($value), ENT_QUOTES, 'UTF-8') . ', \'' . $module_dir_url . '\''; ?>)">PreView</button>
                     <button onclick="delete_mail_conversation(<?php echo $value['id']; ?>, this)" class="btn btn-danger">Delete</button>
                  </td>
               </tr>
               <?php } ?>
            </tbody>
         </table>
      </div>
      <div class="clearfix"></div>
   </div>
</div>
<div class="modal fade" id="send_mail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="remove_model()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('mail_preview'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="">
                  <div class="email-media">

                    </div>
                    <div class="eamil-body">
                      <p id="message_body">
                        
                      </p>
                      <hr>
                        <div class="email-attch">
                          <p>
                            <?php echo _l('mailbox_file_attachment'); ?>
                          </p>

                        </div>
                    </div>
                </div>
            </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" onclick="remove_model()"><?php echo _l('close'); ?></button>
    </div>
</div>
</div>
</div>
<script type="text/javascript">
function delete_mail_conversation(id,element_s){
    if (id) {
        var token = '<?php echo $_COOKIE['csrf_cookie_name']; ?>';
        $.ajax({
            url : 'https://'+window.location.host+'/admin/mailbox/delete_mail_conversation',
            type : 'POST',
            data : {'id' : id,'csrf_token_name':token},
            dataType:'json',
            success : function(data) {
                if (data.data) {
                     var row = element_s.parentNode.parentNode;
                     row.parentNode.removeChild(row);
                     alert_float('success', data.message);
                }
            },
            error : function(request,error)
            {
              alert_float('error', error.error);
            }
        });

    }
}
function remove_model(){
    $("#send_mail_modal").modal('hide');
}

function send_mail_modal(data,module_dir_url){
    $("#message_body").html(data.body);
    $('#send_mail_modal').modal('show');
    $(".media.mt-0").eq(0).append(data.profile);

            var email_top_section = document.createElement("div");
            email_top_section.classList.add("media");
            email_top_section.classList.add("mt-0");

            var section = data.profile+'<div class="media-body"><div class="float-right d-md-flex fs-15"><small class="mr-2">'+ data.date_sent +'</small><small class="mr-2 cursor"><a href="'+data.view_url+'"><i class="fa fa-reply text-dark" data-toggle="tooltip" title="" data-original-title="mailbox_reply"></i></a></small></div><div class="media-title text-dark font-weight-semiblod">'+data.sender_name+' <span class="text-muted">( '+data.get_staff_email_by_id+' )</span></div><p class="mb-0 font-weight-semiblod">To: '+data.to+'</p><p class="mb-0 font-weight-semiblod">Cc: '+data.cc+'</p></div>';

            email_top_section.innerHTML = section;
            $(".email-media").eq(0).html(email_top_section);
            $(".email-attch").eq(0).html('');
            if (data.has_attachment > 0) {
            p = document.createElement("p");
            p.innerHTML = "File Attachment";
            $(".email-attch").eq(0).append(p);
            data.attachments.forEach(function(datas){
            var path = module_dir_url + '/uploads/' + datas.type + '/' + data.mail_id + '/' + datas.file_name;
           
            var div = document.createElement("div");
            div.classList.add("mbot15");
            div.classList.add("row");

            div.setAttribute("data-attachment-id", datas.mail_id);

            divchild = document.createElement("div");
            divchild.classList.add("col-md-8");

            var inner = '<div class="mbpull-left"><i class="'+datas.get_mime_class+'"></i></div><a href="' + path + '" target="_blank">' + datas.file_name + '</a>';
            divchild.innerHTML = inner;
            div.appendChild(divchild);
            $(".email-attch").eq(0).append(div);
        })
    }
}
setTimeout(function(){
  var send_mail_modal = $('#send_mail_modal');
  $('#send_mail_modal').replaceWith('');
  $("body").append(send_mail_modal.eq(0));
},1000);
</script>