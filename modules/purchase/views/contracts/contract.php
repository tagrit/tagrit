<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">

			<?php if(isset($contract)){
                    echo form_hidden('contractid',$contract->id);
                  }
			echo form_open_multipart($this->uri->uri_string(),array('id'=>'contract-form','class'=>'_transaction_form'));
			
			?>
			<div class="col-md-5 left-column">
        <div class="panel_s accounting-template estimate">
        <div class="panel-body">
          
          <div class="row">
             <div class="col-md-12">
                <p class="bold p_style" ><?php echo pur_html_entity_decode($title); ?></p>
                <hr class="hr_style" />
                <div class="row">

                <div class="col-md-6">
                  <?php $contract_description = (isset($contract) ? $contract->contract_name : '');
                  echo render_input('contract_name','contract_description',$contract_description); ?>
                </div>

                <div class="col-md-6">
                  <?php $contract_name = (isset($contract) ? $contract->contract_name : '');
                  echo render_input('service_category','service_category',$contract_name); ?>
                </div>

                <div class="col-md-6 form-group">
                  <label for="vendor"><?php echo _l('vendor'); ?></label>
                  <select name="vendor" id="vendor" class="selectpicker" onchange="vendor_change(this); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                    <option value=""></option>
                    <?php foreach($vendors as $or){ ?>
                      <option value="<?php echo pur_html_entity_decode($or['userid']); ?>" <?php if(isset($contract) && $contract->vendor == $or['userid']){ echo 'selected'; }else{ if(isset($ven) && $ven == $or['userid']){ echo 'selected'; } } ?>><?php echo pur_html_entity_decode($or['company']); ?></option>
                    <?php } ?>
                  </select>
                </div>

                <div class="col-md-6 form-group">
                  <label for="pur_order"><?php echo _l('pur_order'); ?></label>
                  <select name="pur_order" id="pur_order" class="selectpicker" onchange="view_pur_order(this); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                    <option value=""></option>
                    <?php foreach($pur_orders as $or){ ?>
                      <option value="<?php echo pur_html_entity_decode($or['id']); ?>" <?php if(isset($contract) && $contract->pur_order == $or['id']){ echo 'selected'; } ?>><?php echo pur_html_entity_decode($or['pur_order_number']).' - '.pur_html_entity_decode($or['pur_order_name']); ?></option>
                    <?php } ?>
                  </select>
                </div>
                <?php 
                  $project_id = '';
                  if($this->input->get('project')){
                    $project_id = $this->input->get('project');
                  }
                 ?>

                <div class="col-md-6 form-group">
                  <label for="project"><?php echo _l('project'); ?></label>
                  <select name="project" id="project" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                    <option value=""></option>
                    <?php foreach($projects as $pj){ ?>
                      <option value="<?php echo pur_html_entity_decode($pj['id']); ?>" <?php if(isset($contract) && $contract->project == $pj['id']){ echo 'selected'; }else if(!isset($contract) && $pj['id'] == $project_id){ echo 'selected';} ?>><?php echo pur_html_entity_decode($pj['name']); ?></option>
                    <?php } ?>
                  </select>
                </div>
  
                <div class="col-md-6 form-group">
                  <label for="department"><?php echo _l('department'); ?></label>
                  <select name="department" readonly="true" id="department" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                    <option value=""></option>
                    <?php foreach($departments as $dpm){ ?>
                      <option value="<?php echo pur_html_entity_decode($dpm['departmentid']); ?>" <?php if(isset($contract) && $contract->department == $dpm['departmentid']){ echo 'selected'; } ?>><?php echo pur_html_entity_decode($dpm['name']); ?></option>
                    <?php } ?>
                  </select>
                </div>
                
                <div class="col-md-6">
                  <?php $start_date = (isset($contract) ? _d($contract->start_date) : _d(date('Y-m-d')));
                  echo render_date_input('start_date','start_date',$start_date); ?>
                </div>

                <div class="col-md-6">
                  <?php $end_date = (isset($contract) ? _d($contract->end_date) : '');
                   echo render_date_input('end_date','end_date',$end_date); ?>
                </div>

                <div class="col-md-6">
                   <?php $payment_terms = (isset($contract) ? $contract->payment_terms : '');
                    echo render_input('payment_terms','payment_terms',$payment_terms); ?> 
                </div>

                <div class="col-md-6">
                    <?php $payment_amount = (isset($contract) ? app_format_money($contract->payment_amount,'') : '');
                    echo render_input('payment_amount','payment_amount',$payment_amount,'text',array('data-type' => 'currency')); ?> 
                </div>

                <div class="col-md-6 form-group">
                  <label for="payment_cycle"><?php echo _l('payment_cycle'); ?></label>
                  <select name="payment_cycle" readonly="true" id="payment_cycle" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                    <option value=""></option>
                    <option value="one_time" <?php if(isset($contract) && $contract->payment_cycle == 'one_time'){ echo 'selected'; }elseif(!isset($contract)){ echo 'selected'; } ?>><?php echo _l('one_time'); ?></option>
                    <option value="monthly"  <?php if(isset($contract) && $contract->payment_cycle == 'monthly'){ echo 'selected'; } ?>><?php echo _l('monthly'); ?></option>
                    <option value="quarterly"  <?php if(isset($contract) && $contract->payment_cycle == 'quarterly'){ echo 'selected'; } ?>><?php echo _l('quarterly'); ?></option>
                    <option value="every_6_months"  <?php if(isset($contract) && $contract->payment_cycle == 'every_6_months'){ echo 'selected'; } ?>><?php echo _l('every_6_months'); ?></option>
                  </select>
                </div>
                <div class="col-md-6">
                  <?php $contract_value = (isset($contract) ? app_format_money($contract->contract_value,'') : '');
                  echo render_input('contract_value','contract_value',$contract_value,'text',array('data-type' => 'currency')); ?>
        
                </div>
                

                <div class="col-md-6">
                  <label for="signed_status"><?php echo _l('signed_status'); ?></label>
                  <select name="signed_status" readonly="true" id="signed_status" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                    <option value=""></option>
                    <option value="not_signed" <?php if(isset($contract) && $contract->signed_status == 'not_signed'){ echo 'selected'; }elseif(!isset($contract)){ echo 'selected'; } ?>><?php echo _l('not_signed'); ?></option>
                    <option value="signed"  <?php if(isset($contract) && $contract->signed_status == 'signed'){ echo 'selected'; } ?>><?php echo _l('signed'); ?></option>
                  </select>
                </div>
                <div class="col-md-6">
                  <?php $signed_date = (isset($contract) ? _d($contract->signed_date) : '');
                   echo render_date_input('signed_date','signed_date',$signed_date); ?>
        
                </div>

                <div class="col-md-12">
                  <div class="attachments">
                    <div class="attachment">
                      <div class="mbot15">
                        <div class="form-group">
                          <label for="attachment" class="control-label"><?php echo _l('ticket_add_attachments'); ?></label>
                          <div class="input-group">
                            <input type="file" extension="<?php echo str_replace('.','',get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="attachments[0]" accept="<?php echo get_ticket_form_accepted_mimes(); ?>">
                            <span class="input-group-btn">
                              <button class="btn btn-success add_more_attachments p8-half" data-max="5" type="button"><i class="fa fa-plus"></i></button>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

             </div>
            </div>
          </div>

        </div>

        <div class="row">
          <div class="col-md-12 mtop15">
             <div class="panel-body bottom-transaction">
                <?php $value = (isset($contract) ? $contract->note : ''); ?>
                <?php echo render_textarea('note','decription',$value,array('rows'=>8),array(),'mtop15'); ?>
               
                <div class="btn-bottom-toolbar text-right">
                  
                  <button type="button" class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit">
                  <?php echo _l('submit'); ?>
                  </button>
                </div>
             </div>
               <div class="btn-bottom-pusher"></div>
          </div>
        </div>
        </div>

			</div>
			<?php echo form_close(); ?>
			<?php if(isset($contract)) { ?>
        <div class="col-md-7 right-column">
          <div class="panel_s">
              <div class="panel-body">

                <div class="col-md-12 mtop15">
                <div class="horizontal-scrollable-tabs preview-tabs-top">
                     <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                     <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                     <div class="horizontal-tabs">
                        <ul class="nav nav-tabs tabs-in-body-no-margin contract-tab nav-tabs-horizontal mbot15" role="tablist">
                           <li role="presentation" class="<?php if(!$this->input->get('tab') || $this->input->get('tab') == 'tab_content'){echo 'active';} ?>">
                              <a href="#tab_content" aria-controls="tab_content" role="tab" data-toggle="tab">
                              <?php echo _l('contract_content'); ?>
                              </a>
                           </li>
                           <li role="presentation">
                               <a href="#payment" aria-controls="payment" role="tab" data-toggle="tab">
                               <?php echo _l('payment'); ?>
                               </a>
                           </li>   
                           <li role="presentation">
                             <a href="#tab_reminders" onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?php echo pur_html_entity_decode($contract->id) ;?> + '/' + 'pur_contract', undefined, undefined, undefined,[1,'asc']); return false;" aria-controls="tab_reminders" role="tab" data-toggle="tab">
                             <?php echo _l('estimate_reminders'); ?>
                             <?php
                                $total_reminders = total_rows(db_prefix().'reminders',
                                  array(
                                   'isnotified'=>0,
                                   'staff'=>get_staff_user_id(),
                                   'rel_type'=>'pur_contract',
                                   'rel_id'=>$contract->id
                                   )
                                  );
                                if($total_reminders > 0){
                                  echo '<span class="badge">'.$total_reminders.'</span>';
                                }
                                ?>
                             </a>
                          </li>
                          <li role="presentation">
                             <a href="#tab_notes" onclick="get_sales_notes_contract(<?php echo pur_html_entity_decode($contract->id); ?>,'purchase'); return false" aria-controls="tab_notes" role="tab" data-toggle="tab">
                             <?php echo _l('estimate_notes'); ?>
                             <span class="notes-total">
                                <?php $totalNotes       = total_rows(db_prefix().'notes', ['rel_id' => $contract->id, 'rel_type' => 'pur_contract']);
                                if($totalNotes > 0){ ?>
                                   <span class="badge"><?php echo ($totalNotes); ?></span>
                                <?php } ?>
                             </span>
                             </a>
                          </li>

                          <li role="presentation" class="">
                            <?php
                                      $totalComments = total_rows(db_prefix().'pur_comments',['rel_id' => $contract->id, 'rel_type' => 'pur_contract']);
                                      ?>
                             <a href="#discuss" aria-controls="discuss" role="tab" data-toggle="tab">
                             <?php echo _l('pur_discuss'); ?>
                              <span class="badge comments-indicator<?php echo $totalComments == 0 ? ' hide' : ''; ?>"><?php echo $totalComments; ?></span>
                             </a>
                          </li> 

                          <li role="presentation">
                             <a href="#tab_tasks" onclick="init_rel_tasks_table(<?php echo pur_html_entity_decode($contract->id); ?>,'pur_contract'); return false;" aria-controls="tab_tasks" role="tab" data-toggle="tab">
                             <?php echo _l('tasks'); ?>
                             </a>
                          </li>
                           <li role="presentation" >
                              <a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab">
                              <?php echo _l('attachments'); ?>
                              </a>
                           </li>

                           <li role="presentation" class="tab-separator">
                            <a href="#tab_templates" onclick="get_templates('pur_contracts', <?php echo $contract->id ?>); return false" aria-controls="tab_templates" role="tab" data-toggle="tab">
                                  <?php echo _l('templates'); ?>
                               </a>
                            </li>
                           <li role="presentation" class="tab-separator toggle_view">
                              <a href="#" onclick="contract_full_view(); return false;" data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>">
                              <i class="fa fa-expand"></i></a>
                           </li>
                        </ul>
                     </div>
                  </div>
                  </div>
                <div class="tab-content">
                  <div role="tabpanel" class="tab-pane<?php if(!$this->input->get('tab') || $this->input->get('tab') == 'tab_content'){echo ' active';} ?>" id="tab_content">
                    <div class="col-md-12 text-right _buttons mtop10 mbot10">
                      <div class="btn-group">
                         <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>
                         <ul class="dropdown-menu dropdown-menu-right">
                            <li class="hidden-xs"><a href="<?php echo admin_url('purchase/pdf_contract/'.$contract->id.'?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                            <li class="hidden-xs"><a href="<?php echo admin_url('purchase/pdf_contract/'.$contract->id.'?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                            <li><a href="<?php echo admin_url('purchase/pdf_contract/'.$contract->id); ?>"><?php echo _l('download'); ?></a></li>
                            <li>
                               <a href="<?php echo admin_url('purchase/pdf_contract/'.$contract->id.'?print=true'); ?>" target="_blank">
                               <?php echo _l('print'); ?>
                               </a>
                            </li>
                         </ul>

                         <a href="javascript:void(0)" onclick="send_contract('<?php echo pur_html_entity_decode($contract->id); ?>'); return false;" class="btn btn-success mleft10" ><i class="fa fa-envelope" data-toggle="tooltip" title="<?php echo _l('send_to_vendor') ?>"></i></a>

                      </div>
                      <?php if($contract->signed_status == 'not_signed') { ?>
                      <button onclick="accept_action();" class="btn btn-success pull-right action-button mleft5"><?php echo _l('e_signature_sign'); ?></button> 
                      <?php }elseif($contract->signed_status == 'signed'){ ?>
                        <span class="btn success-bg content-view-status contract-html-is-signed" ><?php echo _l('signed'); ?></span>
                      <?php } ?>
                    </div>
                    <hr class="hr-panel-heading" />
                    <div class="editable tc-content div_content">
                       <?php
                          if(empty($contract->content)){
                           echo hooks()->apply_filters('new_contract_default_content', '<span class="text-danger text-uppercase mtop15 editor-add-content-notice"> ' . _l('click_to_add_content') . '</span>');
                          } else {
                           echo pur_html_entity_decode($contract->content);
                          }
                          ?>
                    </div>
                    <?php if($contract->signed_status == 'signed' || $contract->signed == 1) { ?>
                        <div class="row mtop25">
                          <?php if($contract->signed == 1){ ?>
                            <div class="col-md-6  text-left">

                                <p class="bold"><?php echo _l('document_signature_text_vendor'); ?>
                                    
                                  </p>
                                  <div class="pull-right">
                                     <img src="<?php echo site_url(PURCHASE_PATH.'vendor_sign/'.$contract->id.'/signature.png'); ?>" class="img-responsive" alt="">
                                  </div>

                            </div>
                          <?php } ?>
                          <?php if($contract->signed_status == 'signed'){ ?>
                             <div class="col-md-6 <?php if($contract->signed == 0){ echo 'col-md-offset-6';} ?> text-right">
                                <p class="bold"><?php echo _l('document_signature_text'); ?>
                                  
                                </p>
                                <div class="pull-right">
                                   <img src="<?php echo site_url(PURCHASE_PATH.'contract_sign/'.$contract->id.'/signature.png'); ?>" class="img-responsive" alt="">
                                </div>
                             </div>
                           <?php } ?>
                        </div>
                        <?php } ?>
                  </div>

                  <div role="tabpanel" class="tab-pane" id="discuss">
                    <div class="row contract-comments mtop15">
                       <div class="col-md-12">
                          <div id="contract-comments"></div>
                          <div class="clearfix"></div>
                          <textarea name="content" id="comment" rows="4" class="form-control mtop15 contract-comment"></textarea>
                          <button type="button" class="btn btn-info mtop10 pull-right" onclick="add_contract_comment();"><?php echo _l('proposal_add_comment'); ?></button>
                       </div>
                    </div>
                  </div>

                  <div role="tabpanel" class="tab-pane" id="tab_reminders">
                     <a href="#" data-toggle="modal" class="btn btn-info" data-target=".reminder-modal-pur_contract-<?php echo pur_html_entity_decode($contract->id); ?>"><i class="fa fa-bell-o"></i> <?php echo _l('estimate_set_reminder_title'); ?></a>
                     <hr />
                     <?php render_datatable(array( _l( 'reminder_description'), _l( 'reminder_date'), _l( 'reminder_staff'), _l( 'reminder_is_notified')), 'reminders'); ?>
                     <?php $this->load->view('admin/includes/modals/reminder',array('id'=>$contract->id,'name'=>'pur_contract','members'=>$members,'reminder_title'=>_l('estimate_set_reminder_title'))); ?>
                  </div>

                  <div role="tabpanel" class="tab-pane" id="payment">
                      <table class="table dt-table">
                       <thead>
                          <th><?php echo _l('invoice_no'); ?></th>
                         <th><?php echo _l('payments_table_amount_heading'); ?></th>
                          <th><?php echo _l('payments_table_mode_heading'); ?></th>
                          <th><?php echo _l('payment_transaction_id'); ?></th>
                          <th><?php echo _l('payments_table_date_heading'); ?></th>
                          <th><?php echo _l('approval_status'); ?></th>
                          <th><?php echo _l('options'); ?></th>
                       </thead>
                      <tbody>
                         <?php foreach($payment as $pay) { ?>
                            <tr>
                              <td><a href="<?php echo admin_url('purchase/purchase_invoice/'.$pay['pur_invoice']); ?>" ><?php echo get_pur_invoice_number($pay['pur_invoice']); ?></a></td>
                               <td><?php echo app_format_money($pay['amount'],''); ?></td>
                               <td><?php echo get_payment_mode_by_id($pay['paymentmode']); ?></td>
                               <td><?php echo pur_html_entity_decode($pay['transactionid']); ?></td>
                               <td><?php echo _d($pay['date']); ?></td>
                               <td><?php echo get_status_approve($pay['approval_status']); ?></td>
                               <td>
                                <?php if(has_permission('purchase_invoices','','edit') || is_admin()){ ?>
                                  <a href="<?php echo admin_url('purchase/payment_invoice/'.$pay['id']); ?>" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="top" title="<?php echo _l('view'); ?>" ><i class="fa fa-eye "></i></a>
                                <?php } ?>
                                
                               </td>
                            </tr>
                         <?php } ?>
                      </tbody>
                   </table>
                  </div>

                  <div role="tabpanel" class="tab-pane" id="tab_notes">
                    <br>
                     <?php echo form_open(admin_url('purchase/add_pur_contract_note/'.$contract->id),array('id'=>'sales-notes','class'=>'contract-notes-form')); ?>
                     <?php echo render_textarea('description'); ?>
                     <div class="text-right">
                        <button type="submit" class="btn btn-info mtop15 mbot15"><?php echo _l('estimate_add_note'); ?></button>
                     </div>
                     <?php echo form_close(); ?>
                     <hr />
                     <div class="panel_s mtop20 no-shadow" id="sales_notes_area">
                     </div>
                  </div>

                  <div role="tabpanel" class="tab-pane" id="tab_tasks">
                     <?php init_relation_tasks_table(array('data-new-rel-id'=>$contract->id,'data-new-rel-type'=>'pur_contract')); ?>
                  </div>

                  <div role="tabpanel" class="tab-pane"  id="attachments">
                    <div class="col-md-12" id="ic_pv_file">
                      <?php
                         
                          $file_html = '';
                         
                          if(count($attachments) > 0){
                              $file_html .= '<hr />
                                      <p class="bold text-muted">'._l('attachments').'</p>';
                              foreach ($attachments as $f) {
                                  $href_url = site_url(PURCHASE_PATH.'pur_contract/'.$f['rel_id'].'/'.$f['file_name']).'" download';
                                                  if(!empty($f['external'])){
                                                    $href_url = $f['external_link'];
                                                  }
                                 $file_html .= '<div class="mbot15 row inline-block full-width" data-attachment-id="'. $f['id'].'">
                                <div class="col-md-8">
                                   <a name="preview-ic-btn" onclick="preview_ic_btn(this); return false;" rel_id = "'. $f['rel_id']. '" id = "'.$f['id'].'" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left mright5" data-toggle="tooltip" title data-original-title="'. _l('preview_file').'"><i class="fa fa-eye"></i></a>
                                   <div class="pull-left"><i class="'. get_mime_class($f['filetype']).'"></i></div>
                                   <a href=" '. $href_url.'" target="_blank" download>'.$f['file_name'].'</a>
                                   <br />
                                   <small class="text-muted">'.$f['filetype'].'</small>
                                </div>
                                <div class="col-md-4 text-right">';
                                  if($f['staffid'] == get_staff_user_id() || is_admin()){
                                  $file_html .= '<a href="#" class="text-danger" onclick="delete_ic_attachment('. $f['id'].'); return false;"><i class="fa fa-times"></i></a>';
                                  } 
                                 $file_html .= '</div></div>';
                              }
                              $file_html .= '<hr />';
                              echo pur_html_entity_decode($file_html);
                          }
                       ?>
                    </div>
                  
                    <div id="ic_file_data"></div>
                  </div>

                  <div role="tabpanel" class="tab-pane" id="tab_templates">
                     <div class="row contract-templates">
                        <div class="col-md-12">
                           <button type="button" class="btn btn-info mtop10" onclick="add_pur_template('pur_contracts', <?php echo $contract->id ?>);"><?php echo _l('add_template'); ?></button>
                           <hr>
                        </div>
                        <div class="col-md-12">
                           <div id="contract-templates" class="contract-templates-wrapper"></div>
                        </div>
                     </div>
                  </div>

              </div>
          </div>
        </div>
        <div class="modal fade" id="add_action" tabindex="-1" role="dialog">
           <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-body">
                   <?php
                            $selected = '';
                            foreach($staff as $member){
                             if(isset($contract)){
                               if(get_staff_user_id() == $member['staffid']) {
                                 $selected = $member['staffid'];
                               }
                             }
                            }
                            echo render_select('signer',$staff,array('staffid',array('firstname','lastname')),'signer',$selected,array('disabled'=> true));
                            ?>
                   <?php echo render_input('email','email',get_staff(get_staff_user_id())->email,'text',array('disabled'=> true)); ?>
                 <p class="bold" id="signatureLabel"><?php echo _l('signature'); ?></p>
                    <div class="signature-pad--body">
                      <canvas id="signature" height="160" width="550"></canvas>
                    </div>
                    <input type="text" class="ip_style" tabindex="-1" name="signature" id="signatureInput">
                    <div class="dispay-block">
                      <button type="button" class="btn btn-default btn-xs clear" tabindex="-1" onclick="signature_clear();"><?php echo _l('clear'); ?></button>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
                   <button onclick="sign_request(<?php echo pur_html_entity_decode($contract->id); ?>);" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" class="btn btn-success"><?php echo _l('e_signature_sign'); ?></button>
                  </div>
              </div><!-- /.modal-content -->
           </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
      <?php } ?>
		</div>
	</div>
</div>
</div>
<div id="modal-wrapper"></div>
<div class="modal fade" id="send_contract" tabindex="-1" role="dialog">
  <div class="modal-dialog">
      <?php echo form_open_multipart(admin_url('purchase/send_contract'),array('id'=>'send_contract-form')); ?>
      <div class="modal-content modal_withd">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">
                  <span><?php echo _l('send_a_contract'); ?></span>
              </h4>
          </div>
          <div class="modal-body">
              <div id="additional_contract"></div>
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
                      <label for="attach_pdf"><?php echo _l('attach_contract_pdf'); ?></label>
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

<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/purchase/assets/js/contract_js.php';?>
