<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="commission_table" class="hide">
   <div class="row">
        <!--  <div class="col-md-3" id="div_staff_filter">
         <?php echo render_select('staff_filter', $members, array('id', 'firstname', 'lastname'), 'staff', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
         </div>
         <div class="col-md-3">
         <?php echo render_select('products_services', $products, array('id', 'label'), 'products_services', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
         </div> -->
         <!-- <div class="col-md-3">
         <?php
         $statuss = [['id' => '3', 'label' => _l('invoice_status_unpaid')],
         ['id' => '1', 'label' => _l('waiting')],
         ['id' => '2', 'label' => _l('invoice_status_paid')]];
          echo render_select('status', $statuss, array('id', 'label'), 'invoice_dt_table_heading_status', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
         </div> -->
   <div class="clearfix"></div>
</div>
<table class="table table-commission scroll-responsive">
   <thead>
      <tr>
         <th><?php echo _l('hash_share'); ?></th>
         <th><?php echo _l('name'); ?></th>
         <th><?php echo _l('view'); ?></th>
         <th><?php echo _l('upload_and_override'); ?></th>
         <th><?php echo _l('delete'); ?></th>
         <th><?php echo _l('project_setting_upload_files'); ?></th>
         <th><?php echo _l('download'); ?></th>
         <th><?php echo _l('fs_share_expiration_date'); ?></th>
         <th><?php echo _l('download_limits'); ?></th>
         <th><?php echo _l('downloads'); ?></th>
         <th><?php echo _l('sharer'); ?></th>
         <th><?php echo _l('type'); ?></th>
         <th><?php echo _l('proposal_date_created'); ?></th>
         <th><?php echo _l('last_updated'); ?></th>
         <th><?php echo _l('status'); ?></th>
         <th><?php echo _l('options'); ?></th>
      </tr>
   </thead>
   <tbody></tbody>
   <tfoot>
      <tr>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
      </tr>
   </tfoot>
</table>
</div>

<div class="modal fade" id="share-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title add-title"><?php echo _l('fs_share')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('file_sharing/edit_sharing'),array('id'=>'fs-share-form'));?>
         <?php echo form_hidden('id'); ?>
         
         <div class="modal-body">
            <div class="row mtop15 mbot15 fs-gr-radio">
               <div class="col-md-12">
                  <label class="radio-inline">
                     <input type="radio" name="type" value="fs_staff" id="fs_staff">
                     <span class="inline-mbot"><?php echo _l('fs_staff'); ?></span>
                  </label>
                  <label class="radio-inline">
                     <input type="radio" name="type" value="fs_client" id="fs_client">
                     <span class="inline-mbot"><?php echo _l('fs_client'); ?></span>
                  </label>
                  <label class="radio-inline">
                     <input type="radio" name="type" value="fs_public" id="fs_public">
                     <span class="inline-mbot"><?php echo _l('fs_public'); ?></span>
                  </label>
               </div>
            </div>
            <div id="div_staff">
               <?php 
               $selected_role = array();
               echo render_select('role[]',$roles,array('roleid',array('name')),'role',$selected_role,array('multiple'=>true, 'data-actions-box' => true),array(),'','',false); ?>
               <?php 
               $selected = array();
               echo render_select('staff[]',$staffs,array('staffid',array('firstname','lastname')),'staff',$selected,array('multiple'=>true, 'data-actions-box' => true),array(),'','',false); ?>
            </div>
            <div id="div_client" class="hide">
               <?php 
               $selected_role = array();
               echo render_select('customer_group[]',$client_groups,array('id','name'),'customer_group',$selected_role,array('multiple'=>true, 'data-actions-box' => true),array(),'','',false); ?>
               <?php 
               $selected = array();
               echo render_select('customer[]',$clients,array('userid','company'),'customer',$selected,array('multiple'=>true, 'data-actions-box' => true),array(),'','',false); ?>
            </div>
            <div id="div_public" class="hide">
                    <div class="row">
                  <div class="col-md-12">
                        <label for="public_link" class="control-label"><?php echo _l('fs_link'); ?></label>
                        <div class="input-group">
                           <input type="text" class="form-control" id="public_link" name="public_link" autocomplete="off" value="<?php echo site_url('file_sharing/d3ghf6'); ?>" readonly>
                           <span class="input-group-addon">
                              <a href="javascript:void(0);" onclick="copy_public_link(); return false;"><i class="fa fa-clone"></i></a>
                           </span>
                           <span class="input-group-addon">
                              <a href="javascript:void(0);" onclick="send_mail(); return false;"><i class="fa fa-envelope"></i></a>
                           </span>
                        </div>
                     </div>
               </div>
               
            </div>
            <div class="form-group">
               <div class="checkbox checkbox-primary">
                  <input type="checkbox" name="expiration_date_apply" id="expiration_date_apply" value="1">
                  <label for="expiration_date_apply"><?php echo _l('expiration_date_apply'); ?></label>
               </div>
            </div>
            <div class="mleft15 hide" id="div_expiration_date">
               <?php echo render_date_input('expiration_date', 'fs_share_expiration_date') ?>
               <div class="form-group">
                  <div class="checkbox checkbox-primary">
                     <input type="checkbox" name="expiration_date_delete" id="expiration_date_delete" value="1">
                     <label for="expiration_date_delete"><?php echo _l('delete_file_when_finished'); ?></label>
                  </div>
               </div>
            </div>
              <div class="form-group">
               <div class="checkbox checkbox-primary">
                  <input type="checkbox" name="download_limits_apply" id="download_limits_apply" value="1">
                  <label for="download_limits_apply"><?php echo _l('download_limits_apply'); ?></label>
               </div>
            </div>
            <div class="mleft15 hide" id="div_download_limit">
               <?php echo render_input('download_limits', 'download_limits', '', 'number', array('min' => 1)) ?>
               <div class="form-group">
                  <div class="checkbox checkbox-primary">
                     <input type="checkbox" name="download_limits_delete" id="download_limits_delete" value="1">
                     <label for="download_limits_delete"><?php echo _l('delete_file_when_finished'); ?></label>
                  </div>
               </div>
            </div>
            <div class="row hide" id="div_public_permisstion">
               <div class="col-md-6">
                  <h5 class="title mbot5"><?php echo _l('fs_permisstion') ?></h5>
                   <div class="row">
                       <div class="col-md-6 mtop10 border-right">
                           <span><?php echo _l('download'); ?></span>
                       </div>
                       <div class="col-md-6 mtop10">
                           <div class="onoffswitch">
                               <input type="checkbox" id="public_is_download" data-perm-id="5" class="onoffswitch-checkbox"  value="1" name="public_is_download" checked>
                               <label class="onoffswitch-label" for="public_is_download"></label>
                           </div>
                       </div>
                   </div>
               </div>
            </div>
            <div class="row hide" id="div_permisstion">
               <div class="col-md-6">
                  <h5 class="title mbot5"><?php echo _l('fs_permisstion') ?></h5>
                   <div class="row">
                       <div class="col-md-6 mtop10 border-right">
                           <span><?php echo _l('view'); ?></span>
                       </div>
                       <div class="col-md-6 mtop10">
                           <div class="onoffswitch">
                               <input type="checkbox" id="is_read" data-perm-id="5" class="onoffswitch-checkbox" value="1" name="is_read" checked>
                               <label class="onoffswitch-label" for="is_read"></label>
                           </div>
                       </div>
                       <div class="col-md-6 mtop10 border-right">
                           <span><?php echo _l('upload_and_override'); ?></span>
                       </div>
                       <div class="col-md-6 mtop10">
                           <div class="onoffswitch">
                               <input type="checkbox" id="is_write" data-perm-id="6" class="onoffswitch-checkbox"  value="1" name="is_write" checked>
                               <label class="onoffswitch-label" for="is_write"></label>
                           </div>
                       </div>
                       <div class="col-md-6 mtop10 border-right">
                           <span><?php echo _l('delete'); ?></span>
                       </div>
                       <div class="col-md-6 mtop10">
                           <div class="onoffswitch">
                               <input type="checkbox" id="is_delete" data-perm-id="5" class="onoffswitch-checkbox"  value="1" name="is_delete" checked>
                               <label class="onoffswitch-label" for="is_delete"></label>
                           </div>
                       </div>
                       <div class="col-md-6 mtop10 border-right">
                           <span><?php echo _l('project_setting_upload_files'); ?></span>
                       </div>
                       <div class="col-md-6 mtop10">
                           <div class="onoffswitch">
                               <input type="checkbox" id="is_upload" data-perm-id="6" class="onoffswitch-checkbox"  value="1" name="is_upload" checked>
                               <label class="onoffswitch-label" for="is_upload"></label>
                           </div>
                       </div>
                       <div class="col-md-6 mtop10 border-right">
                           <span><?php echo _l('download'); ?></span>
                       </div>
                       <div class="col-md-6 mtop10">
                           <div class="onoffswitch">
                               <input type="checkbox" id="is_download" data-perm-id="5" class="onoffswitch-checkbox"  value="1" name="is_download" checked>
                               <label class="onoffswitch-label" for="is_download"></label>
                           </div>
                       </div>
                   </div>
               </div>
            </div>
            <hr>
            <div class="rows" id="div_password">
               <label for="password" class="control-label"><?php echo _l('staff_add_edit_password'); ?></label>
               <div class="input-group">
                  <input type="password" class="form-control password" name="password" autocomplete="off">
                  <span class="input-group-addon">
                  <a href="#password" class="show_password" onclick="showPassword('password'); return false;"><i class="fa fa-eye"></i></a>
                  </span>
                  
               </div>
            </div>
            <div class="col-md-12"></div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>