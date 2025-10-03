<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="clearfix mtop20"></div>
<div class="">
  <div class="email-media">
      <div class="media mt-0">
        <?php echo staff_profile_image($inbox->sender_staff_id, ['mr-2 rounded-circle']); ?>        
        
        <div class="media-body">
          <div class="float-right d-md-flex fs-15">
            <small class="mr-2"><?php echo _dt($inbox->date_sent); ?></small>            
            <small class="mr-2 cursor"><a href="<?php echo admin_url().'mailbox/reply/'.$inbox->id.'/reply/outbox'; ?>"><i class="fa fa-reply text-dark" data-toggle="tooltip" title="" data-original-title="<?php echo _l('mailbox_reply'); ?>"></i></a></small>
          </div>
          <div class="media-title text-dark font-weight-semiblod"><?php echo $inbox->sender_name; ?> <span class="text-muted">( <?php echo get_staff_email_by_id($inbox->sender_staff_id); ?> )</span></div>
          <p class="mb-0 font-weight-semiblod">To: <?php echo $inbox->to; ?></p>
          <p class="mb-0 font-weight-semiblod">Cc: <?php echo $inbox->cc; ?></p>
        </div>
      </div>
    </div>
    <div class="eamil-body">
        <p>
          <?php echo $inbox->body; ?>
        </p>
        <hr>
        <?php if ($inbox->has_attachment > 0) {?>
        <div class="email-attch">          
          <p><?php echo _l('mailbox_file_attachment'); ?></p>
          <div class="emai-img">
            <div class="">
               <?php foreach ($attachments as $attachment) {
                     $attachment_url = module_dir_url(MAILBOX_MODULE).'uploads/'.$type.'/'.$inbox->id.'/'.$attachment['file_name']; 
               ?>
                <div class="mbot15 row" data-attachment-id="<?php echo $attachment['id']; ?>">
                     <div class="col-md-8">
                        <div class="mbpull-left"><i class="<?php echo get_mime_class($attachment['file_type']); ?>"></i></div>
                        <a href="<?php echo $attachment_url; ?>" target="_blank"><?php echo $attachment['file_name']; ?></a>
                        <br />
                        <small class="text-muted"> <?php echo $attachment['file_type']; ?></small>
                     </div>
                   </div>

               <?php
}?>
              
            </div>
          </div>
        </div>
        <?php }?>
      </div>

      <div class="pull-right">
      <button class="btn btn-success" type="button" data-toggle="modal" data-target="#sales_item_modal"><i class="fa fa-bullhorn"></i> <?php echo _l('assign_to_leads'); ?></button>      
	  <button class="btn btn-danger" type="button" data-toggle="modal" data-target="#ticket_item_modal"><i class="fa fa-life-ring"></i> <?php echo _l('assign_to_tickets'); ?></button>	  
      <a href="<?php echo admin_url().'mailbox/reply/'.$inbox->id.'/reply/outbox'; ?>" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-warning">
        <i class="fa fa-reply"></i></i> <?php echo _l('mailbox_reply'); ?></a>
      <a href="<?php echo admin_url().'mailbox/reply/'.$inbox->id.'/forward/outbox'; ?>" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-info">
          <i class="fa fa-share"></i>
          <?php echo _l('mailbox_forward'); ?>          
        </a>
    </div>
</div>
<script>
  var mailid = <?php echo $inbox->id; ?>;
  var mailtype = '<?php echo $type; ?>';
</script>


<div class="modal fade" id="sales_item_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <?php echo form_open_multipart(admin_url().'mailbox/conversationLead', ['id'=>'lead_assign_form']); ?>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('assign_lead'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning affect-warning hide">
                            <?php echo _l('changing_items_affect_warning'); ?>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                             <div class="form-group">
                              <div id="leads">
                      <?php
                    $selected = [];
                       if (is_admin() || get_option('staff_members_create_inline_customer_groups') == '1') {
                           echo render_select_with_input_group('select_lead[]', $leads, ['id', 'name'], 'select_lead', $selected, '<div class="input-group-btn"><a href="#" class="btn btn-default" data-toggle="modal" data-target="#customer_group_modal"><i class="fa fa-plus"></i></a></div>', ['multiple' => true, 'data-actions-box' => true, 'required' => true], [], '', '', false);
                       } else {
                           echo render_select('select_lead[]', $leads, ['id', 'name'], 'select_lead', $selected, ['multiple' => true, 'data-actions-box' => true, 'required' => true], [], '', '', false);
                       }
                      ?>        
                  </div>
                            </div>
                        </div>
                </div>
                <input type="hidden" name="outbox_id" value="<?php echo $outbox_id ?>" >
                <div class="clearfix mbot15"></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
    </div>
</div>
</div>
<?php echo form_close(); ?>
</div>

<div class="modal fade" id="customer_group_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('customer_group_edit_heading'); ?></span>
                    <span class="add-title"><?php echo _l('add_new', _l('lead_lowercase')); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/leads/lead', ['id' => 'customer-group-modal']); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
					    <?php echo render_input('name', 'lead_group_name'); ?>
						<?php echo render_input('email', 'lead_group_email'); ?>
                        <?php echo form_hidden('id'); ?>
                    </div>
                </div>
            </div>
            <input type="hidden" name="description" value="" >
            <input type="hidden" name="address" value="" >
            <input type="hidden" name="assigned" value="" >
            <input type="hidden" name="status" value="1">
            <input type="hidden" name="source" value="1">
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener('load',function(){
       appValidateForm($('#customer-group-modal'), {
        name: 'required',
        email: {
            required: true,
            email: true
        }
    }, manage_customer_groups);
       $('#customer_group_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var group_id = $(invoker).data('id');
        $('#customer_group_modal .add-title').removeClass('hide');
        $('#customer_group_modal .edit-title').addClass('hide');
        $('#customer_group_modal input[name="id"]').val('');
        $('#customer_group_modal input[name="name"]').val('');
        // is from the edit button
        if (typeof(group_id) !== 'undefined') {
            $('#customer_group_modal input[name="id"]').val(group_id);
            $('#customer_group_modal .add-title').addClass('hide');
            $('#customer_group_modal .edit-title').removeClass('hide');
            $('#customer_group_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
        }
    });
   });
    function manage_customer_groups(form) {
        var data = $(form).serialize();
        var url = form.action;
    var formData = new URLSearchParams(data);
        var nameValue = formData.get('name');
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                if($.fn.DataTable.isDataTable('.table-customer-groups')){
                    $('.table-customer-groups').DataTable().ajax.reload();
                }
                if($('body').hasClass('dynamic-create-groups') && typeof(response.id) != 'undefined') {
          console.log(data);
                    var groups = $('select[name="select_lead[]"]');
                    groups.prepend('<option value="'+response.id+'">'+nameValue+'</option>');
                    groups.selectpicker('refresh');
                }
                alert_float('success', response.message);
            }
            $('#customer_group_modal').modal('hide');
        });
        return false;
    }
</script>



<div class="modal fade" id="ticket_item_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <?php echo form_open_multipart(admin_url().'mailbox/conversationTicket', ['id'=>'ticket_assign_form']); ?>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('assign_ticket'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning affect-warning hide">
                            <?php echo _l('changing_items_affect_warning'); ?>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                             <div class="form-group">
                              <div id="tickets">
							<select id="clientid" name="select_customer[]" multiple="false" data-live-search="true" data-width="100%" class="ajax-search<?php if(isset($invoice) && empty($invoice->clientid)){echo ' customer-removed';} ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

							<?php $selected = (isset($invoice) ? $invoice->clientid : '');

							if($selected == ''){
								$selected = (isset($customer_id) ? $customer_id: '');
							}

							if($selected != ''){
								$rel_data = get_relation_data('customer',$selected);
								$rel_val = get_relation_values($rel_data,'customer');
								echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
							}?>
							
							</select>        
                  </div>
                            </div>
                        </div>
                </div>
                <input type="hidden" name="outbox_id" value="<?php echo $outbox_id ?>" >
                <div class="clearfix mbot15"></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
    </div>
</div>
</div>
<?php echo form_close(); ?>
</div>

<div class="modal fade" id="customer_group_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('customer_group_edit_heading'); ?></span>
                    <span class="add-title"><?php echo _l('add_new', _l('ticket_lowercase')); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/tickets/ticket', ['id' => 'customer-group-modal']); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('name', 'ticket_group_name'); ?>
            <?php echo render_input('email', 'ticket_group_email'); ?>
                        <?php echo form_hidden('id'); ?>
                    </div>
                </div>
            </div>
            <input type="hidden" name="description" value="" >
            <input type="hidden" name="address" value="" >
            <input type="hidden" name="assigned" value="" >
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener('load',function(){
       appValidateForm($('#customer-group-modal'), {
        name: 'required',
    email: 'required'
    }, manage_customer_groups);
       $('#customer_group_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var group_id = $(invoker).data('id');
        $('#customer_group_modal .add-title').removeClass('hide');
        $('#customer_group_modal .edit-title').addClass('hide');
        $('#customer_group_modal input[name="id"]').val('');
        $('#customer_group_modal input[name="name"]').val('');
        // is from the edit button
        if (typeof(group_id) !== 'undefined') {
            $('#customer_group_modal input[name="id"]').val(group_id);
            $('#customer_group_modal .add-title').addClass('hide');
            $('#customer_group_modal .edit-title').removeClass('hide');
            $('#customer_group_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
        }
    });
   });
    function manage_customer_groups(form) {
        var data = $(form).serialize();
        var url = form.action;
    var formData = new URLSearchParams(data);
        var nameValue = formData.get('name');
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                if($.fn.DataTable.isDataTable('.table-customer-groups')){
                    $('.table-customer-groups').DataTable().ajax.reload();
                }
                if($('body').hasClass('dynamic-create-groups') && typeof(response.id) != 'undefined') {
          console.log(data);
                    var groups = $('select[name="select_ticket[]"]');
                    groups.prepend('<option value="'+response.id+'">'+nameValue+'</option>');
                    groups.selectpicker('refresh');
                }
                alert_float('success', response.message);
            }
            $('#customer_group_modal').modal('hide');
        });
        return false;
    }
</script>