<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div class="row">
                     <div class="col-md-12">
                      <h4 class="no-margin font-bold"><i class="fa fa-shopping-basket" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                      <hr />
                     </div>
                    </div>
                    <div class="row">    
                        <div class="_buttons col-md-2">
                            <?php if (has_permission('purchase_request', '', 'create') || is_admin()) { ?>
                            <a href="<?php echo admin_url('purchase/pur_request'); ?>"class="btn btn-info pull-left mright10 display-block">
                                <?php echo _l('new_pur_request'); ?>
                            </a>
                            <?php } ?>
                        </div>
                        <div class="col-md-4">
                          <select name="department_filter[]" id="department_filter" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('department'); ?>">
                            <?php foreach($departments as $s) { ?>
                              <option value="<?php echo pur_html_entity_decode($s['departmentid']); ?>" <?php if(isset($pur_request) && $s['departmentid'] == $pur_request->department){ echo 'selected'; } ?>><?php echo pur_html_entity_decode($s['name']); ?></option>
                              <?php } ?>
                          </select>
                        </div>
                        <div class="col-md-3">
                            <?php echo render_date_input('from_date','','',array('placeholder' => _l('from_date') )); ?>
                        </div>
                        <div class="col-md-3">
                            <?php echo render_date_input('to_date','','',array('placeholder' => _l('to_date') )); ?>
                        </div>
                    </div>
                    <?php render_datatable(array(
                        _l('pur_rq_code'),
                        _l('pur_rq_name'),
                        _l('requester'),
                        _l('department'),
                        _l('request_date'),
                        _l('status'),
                        _l('po_no'),
                        _l('options'),
                        ),'table_pur_request'); ?>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="request_quotation" tabindex="-1" role="dialog">
  <div class="modal-dialog">
      <?php echo form_open_multipart(admin_url('purchase/send_request_quotation'),array('id'=>'send_rq-form')); ?>
      <div class="modal-content modal_withd">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">
                  <span><?php echo _l('send_a_pr'); ?></span>
              </h4>
          </div>
          <div class="modal-body">
              <div id="additional_rqquo"></div>
              <div class="row">
                <div class="col-md-12 form-group">
                  <label for="send_to"><span class="text-danger">* </span><?php echo _l('send_to'); ?></label>
                    <select name="send_to[]" id="send_to" class="selectpicker" required multiple="true"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                        <?php foreach($vendor_contacts as $s) { ?>
                        <option value="<?php echo pur_html_entity_decode($s['email']); ?>" data-subtext="<?php echo pur_html_entity_decode($s['firstname'].' '.$s['lastname'].' - '. get_vendor_company_name($s['userid'])); ?>" selected><?php echo pur_html_entity_decode($s['email']); ?></option>
                          <?php } ?>
                    </select>
                    <br>
                </div>     
                <div class="col-md-12">
                  <div class="checkbox checkbox-primary">
                      <input type="checkbox" name="attach_pdf" id="attach_pdf" checked>
                      <label for="attach_pdf"><?php echo _l('attach_purchase_request_pdf'); ?></label>
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

<div class="modal fade" id="share_request" tabindex="-1" role="dialog">
  <div class="modal-dialog">
      <?php echo form_open_multipart(admin_url('purchase/share_request'),array('id'=>'share_request-form')); ?>
      <div class="modal-content modal_withd">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">
                  <span><?php echo _l('share_request'); ?></span>
              </h4>
          </div>
          <div class="modal-body">
              <div id="additional_share"></div>
              <div class="row">

                <div class="col-md-12 form-group">
                      <label for="send_to_vendors"><?php echo _l('pur_send_to_vendors'); ?></label>
                      <select name="send_to_vendors[]" id="send_to_vendors" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >

                          <?php foreach($vendors as $s) { ?>
                          <option value="<?php echo pur_html_entity_decode($s['userid']); ?>" <?php if(isset($pur_request) && in_array($s['userid'], $vendors_arr)){ echo 'selected';  } ?> ><?php echo pur_html_entity_decode($s['company']); ?></option>
                            <?php } ?>

                      </select>  
                    </div>    
      

     
              </div>
          </div>
          <div class="modal-footer">
              <button type=""class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
              <button id="sm_btn" type="submit" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-info"><?php echo _l('pur_share'); ?></button>
          </div>
      </div><!-- /.modal-content -->
          <?php echo form_close(); ?>
      </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
</body>
</html>