<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
			echo form_open($this->uri->uri_string(),array('id'=>'pur_order-form','class'=>'_transaction_form'));
			
			?>
      <?php 
        $id = '';
        if(isset($loss_adjustment)){
          $id = $loss_adjustment->id;
          echo form_hidden('isedit');
        }
       ?>
      <input type="hidden" name="id" value="<?php echo new_html_entity_decode($id); ?>">
			<div class="col-md-12">
        <div class="panel_s accounting-template estimate">
         <div class="panel-body">
          <div class="row">
           <div class="col-md-12">
            <h4 class="no-margin font-bold"><i class="fa fa-adjust menu-icon" aria-hidden="true"></i><?php echo _l('loss_adjustment'); ?></h4>
             <hr>
           </div>
         </div>
         <div class="row">
               <div class="col-md-4">
                 <?php
                  $time = (isset($loss_adjustment) ? _dt($loss_adjustment->time) : _dt(date('Y-m-d H:i:s')));
                  echo render_datetime_input('time','_time',$time);
                    $type = '';
                    $reason = '';
                    $prescription_id = '';
                    if(isset($loss_adjustment)){
                      $type = $loss_adjustment->type;
                      $reason = $loss_adjustment->reason;
                    }
                   ?>
               </div>
               <div class="col-md-4 form-group">
                 <label for="vendor"><span class="text-danger">* </span><?php echo _l('type_label'); ?></label>
                  <select name="type" class="selectpicker" id="type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required="true"> 
                        <option value=""></option>
                        <option value="loss" <?php if($type == 'loss'){ echo 'selected'; } ?>><?php echo _l('loss'); ?></option>
                        <option value="adjustment" <?php if($type == 'adjustment'){ echo 'selected'; } ?>><?php echo _l('adjustment'); ?></option>
                  </select>
                 <br><br>
               </div>

               <div class="col-md-4 form-group">
                 <label for="vendor"><span class="text-danger">* </span><?php echo _l('_warehouse'); ?></label>
                  <select name="warehouses" class="selectpicker" id="warehouses" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required="true"> 
                        <option value=""></option>
                        <?php foreach($warehouses as $wh){ ?>
                          <option value="<?php echo new_html_entity_decode($wh['id']); ?>" <?php if(isset($loss_adjustment) && $loss_adjustment->warehouses == $wh['id']){ echo 'selected';} ?> ><?php echo new_html_entity_decode($wh['label']); ?></option>
                        <?php } ?>
                  </select>
                 <br><br>
               </div>                  
                            
         </div>
       </div>

       <div class="panel-body mtop10 invoice-item">
            <div class="row">
              <div class="col-md-4">
                <?php $this->load->view('warehouse/item_include/main_item_select'); ?>
              </div>
              <div class="col-md-8 text-right">
                <label class="bold mtop10 text-right" data-toggle="tooltip" title="" data-original-title="<?php echo _l('support_barcode_scanner_tooltip'); ?>"><?php echo _l('support_barcode_scanner'); ?>
                <i class="fa fa-question-circle i_tooltip"></i></label>
              </div>
            </div>

            <div class="table-responsive s_table ">
              <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                <thead>
                  <tr>
                    <th></th>
                    <th width="40%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
                    <th width="17%" align="right"><?php echo _l('lot_number'); ?></th>
                    <th width="17%" align="right"><?php echo _l('expiry_date'); ?></th>
                    <th width="13%" align="right" class="qty"><?php echo _l('available_quantity'); ?></th>
                    <th width="13%" align="right" class="qty"><?php echo _l('stock_quantity'); ?></th>

                    <th align="center"><i class="fa fa-cog"></i></th>
                    <th align="center"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php echo new_html_entity_decode($loss_adjustment_row_template); ?>
                </tbody>
              </table>
            </div>
            <div id="removed-items"></div>
          </div>

        <div class="row">
          <div class="col-md-12 mtop15">
             <div class="panel-body bottom-transaction">

                <?php echo render_textarea('reason','reason',$reason,array(),array(),'mtop15'); ?>

                <div class="btn-bottom-toolbar text-right">
                  <a href="<?php echo admin_url('warehouse/loss_adjustment'); ?>"class="btn btn-default text-right mright5"><?php echo _l('close'); ?></a>
                  <?php if(isset($loss_adjustment) && $loss_adjustment->status == 0){ ?>

                    <?php if (has_permission('wh_loss_adjustment', '', 'create') || is_admin() || has_permission('wh_loss_adjustment', '', 'edit')) { ?>
                    <button type="button" class="btn-tr save_detail btn btn-info ">
                    <?php echo _l('submit'); ?>
                    </button>
                     <?php } ?>

                  <?php }  ?>
                  <?php 
                    if(!isset($loss_adjustment)){ ?>
                      <?php if (has_permission('wh_loss_adjustment', '', 'create') || is_admin() || has_permission('wh_loss_adjustment', '', 'edit')) { ?>
                       <button type="button" class="btn-tr save_detail btn btn-info ">
                                <?php echo _l('submit'); ?>
                       </button>
                     <?php } ?>
                  <?php } ?>
                </div>
             </div>
               <div class="btn-bottom-pusher"></div>
          </div>
        </div>


        </div>

			</div>
			<?php echo form_close(); ?>
			
		</div>
	</div>
</div>
</div>
<div id="add_modal_wrapper"></div>
<div id="delete_modal_wrapper"></div>


<?php init_tail(); ?>

<?php require 'modules/warehouse/assets/js/add_loss_adjustment_js.php';?>
</body>
</html>
