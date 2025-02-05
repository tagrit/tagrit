<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="mtop5 mbot25"><?php echo _l('vendor_statement'); ?></h4>
<hr />
<div class="row">
 <div class="col-md-4">
   <?php $this->load->view('vendors/groups/_statement_period_select', ['onChange'=>'render_vendor_statement()']); ?>
</div>
<div class="col-md-8 col-xs-12">
   <div class="text-right _buttons pull-right">

      <a href="#" id="statement_print" target="_blank" class="btn btn-default btn-with-tooltip mright5" data-toggle="tooltip" title="<?php echo _l('print'); ?>" data-placement="bottom">
          <i class="fa fa-print"></i>
      </a>

      <a href="" id="statement_pdf"  class="btn btn-default btn-with-tooltip mright5" data-toggle="tooltip" title="<?php echo _l('view_pdf'); ?>" data-placement="bottom">
          <i class="fa fa-file-pdf"></i>
      </a>

      <a href="#" class="btn-with-tooltip btn btn-default" data-toggle="modal" data-target="#send_statement"><span data-toggle="tooltip" data-title="<?php echo _l('send_to_email'); ?>" data-placement="bottom"><i class="fa fa-envelope"></i></span></a>
</div>
</div>
<div class="clearfix"></div>
<div class="col-md-12">
    <h4><?php echo _l('vendor_statement_for').' '.get_vendor_company_name($client->userid); ?></h4>
</div>
<div class="clearfix"></div>

<div class="col-md-12 mtop15">
    <div class="panel_s">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                   <address class="text-right">
                       <?php echo format_organization_info(); ?>
                   </address>
               </div>
               <div class="col-md-12">
                   <hr />
               </div>
               <div class="col-md-7">
                   <address>
                    <p><?php echo _l('statement_bill_to'); ?>:</p>
                    <?php echo format_vendor_info($client, 'statement', 'billing'); ?>
                 </address>
             </div>
             <div id="statement-html"></div>
         </div>
     </div>
 </div>
</div>
</div>

<div class="modal fade" id="send_statement" tabindex="-1" role="dialog">
  <div class="modal-dialog">
      <?php echo form_open_multipart('',array('id'=>'send_statement_form')); ?>
      <div class="modal-content modal_withd">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">
                  <span><?php echo _l('send_a_statement'); ?></span>
              </h4>
          </div>
          <div class="modal-body">
              <div id="additional_po"></div>
              <div class="row">
                <div class="col-md-12 form-group">
                  <label for="send_to"><span class="text-danger">* </span><?php echo _l('send_to'); ?></label>
                    <select name="send_to[]" id="send_to" class="selectpicker" required multiple="true"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                        <?php foreach($vendor_contacts as $s) { ?>
                        <option value="<?php echo pur_html_entity_decode($s['email']); ?>" data-subtext="<?php echo pur_html_entity_decode($s['firstname'].' '.$s['lastname']); ?>" selected><?php echo pur_html_entity_decode($s['email']); ?></option>
                          <?php } ?>
                    </select>
                    <br>
                </div>     
                <div class="col-md-12">
                  <div class="checkbox checkbox-primary">
                      <input type="checkbox" name="attach_pdf" id="attach_pdf" checked>
                      <label for="attach_pdf"><?php echo _l('attach_purchase_statement_pdf'); ?></label>
                  </div>
                </div>

                <div class="col-md-12">
                 <?php echo render_textarea('content','additional_content','',array('rows'=>6,'data-task-ae-editor'=>true, !is_mobile() ? 'onclick' : 'onfocus'=>(!isset($routing) || isset($routing) && $routing->description == '' ? 'routing_init_editor(\'.tinymce-task\', {height:200, auto_focus: true});' : '')),array(),'no-mbot','tinymce-task'); ?> 
                </div>     
                <div id="type_care">
                  
                </div>        
              </div>
          </div>
          <div class="modal-footer">
              <button type=""class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
              <button id="sm_btn" type="submit" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-info"><?php echo _l('pur_send'); ?></button>
          </div>
      </div><!-- /.modal-content -->
          <?php echo form_close(); ?>
      </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
   
</script>

