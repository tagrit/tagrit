<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">

    <?php echo form_open_multipart(admin_url('purchase/pur_invoice_form'),array('id'=>'pur_invoice-form','class'=>'_pur_invoice_form _transaction_form')); ?>
    	<?php
    		if(isset($pur_order)){
		        echo form_hidden('isedit');
		    }
    	 ?>
      <div class="col-md-12">
        <div class="panel_s accounting-template estimate">
          <div class="panel-body">

          	<div class="row">
             <div class="col-md-12">
              <h4 class="no-margin font-bold"><i class="fa <?php if(isset($pur_invoice)){ echo 'fa-pencil-square';}else{ echo 'fa-plus';} ?>" aria-hidden="true"></i> <?php echo _l($title); ?> <?php if(isset($pur_invoice)){ echo ' '.pur_html_entity_decode($pur_invoice->invoice_number); } ?></h4>
              <hr />
             </div>
            </div>

            <div class="row">
            	<?php $additional_discount = 0; ?>
                  <input type="hidden" name="additional_discount" value="<?php echo pur_html_entity_decode($additional_discount); ?>">
            	<div class="col-md-6">
            		<?php echo form_hidden('id', (isset($pur_invoice) ? $pur_invoice->id : '') ); ?>
	            	<div class="col-md-6 pad_left_0">
	            		<label for="invoice_number"><span class="text-danger">* </span><?php echo _l('invoice_code'); ?></label>
		            	<?php
	                    $prefix = get_purchase_option('pur_inv_prefix');
	                    $next_number = get_purchase_option('next_inv_number');
	                    $number = (isset($pur_invoice) ? $pur_invoice->number : $next_number);
	                    echo form_hidden('number',$number); ?> 
	                           
	                    <?php $invoice_number = ( isset($pur_invoice) ? $pur_invoice->invoice_number : $prefix.str_pad($next_number,5,'0',STR_PAD_LEFT));
	                    echo render_input('invoice_number','',$invoice_number ,'text',array('readonly' => '', 'required' => 'true')); ?>
	                </div>

	                <div class="col-md-6 pad_right_0">
	                	<?php $vendor_invoice_number = ( (isset($pur_invoice) && $pur_invoice->vendor_invoice_number != '') ? $pur_invoice->vendor_invoice_number : $invoice_number);
	                    echo render_input('vendor_invoice_number','invoice_number',$vendor_invoice_number ,'text',array()); ?>
	                </div>

	                <div class="col-md-6 pad_left_0 form-group">
	                	<label for="vendor"><span class="text-danger">* </span><?php echo _l('pur_vendor'); ?></label>
	                    <select name="vendor" id="vendor" class="selectpicker" onchange="pur_vendor_change(this); return false;" required="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
	                        <option value=""></option>
	                        <?php foreach($vendors as $ven){ ?>
	                        	<option value="<?php echo pur_html_entity_decode($ven['userid']); ?>" <?php if(isset($pur_invoice) && $pur_invoice->vendor == $ven['userid']){ echo 'selected'; } ?>><?php echo pur_html_entity_decode($ven['vendor_code'].' '.$ven['company']); ?></option>
	                        <?php } ?>
	                    </select>
	                </div>

	                <div class="col-md-6 form-group pad_right_0">
	                	<label for="contract"><?php echo _l('contract'); ?></label>
	                    <select name="contract" id="contract" class="selectpicker" onchange="contract_change(this); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
	                        <option value=""></option>
	                        <?php foreach($contracts as $ct){ ?>
	                        	<option value="<?php echo pur_html_entity_decode($ct['id']); ?>" <?php if(isset($pur_invoice) && $pur_invoice->contract == $ct['id']){ echo 'selected'; } ?>><?php echo pur_html_entity_decode($ct['contract_number']); ?></option>
	                        <?php } ?>
	                    </select>
	                </div>
	              
	                <div class="col-md-6 form-group pad_left_0">
	                	<label for="pur_order"><?php echo _l('pur_order'); ?></label>
	                    <select name="pur_order" id="pur_order" class="selectpicker" onchange="pur_order_change(this); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
	                        <option value=""></option>
	                        <?php foreach($pur_orders as $ct){ ?>
	                        	<option value="<?php echo pur_html_entity_decode($ct['id']); ?>" <?php if(isset($pur_invoice) && $pur_invoice->pur_order == $ct['id']){ echo 'selected'; } ?>><?php echo pur_html_entity_decode($ct['pur_order_number']); ?></option>
	                        <?php } ?>
	                    </select>
	                </div>

	                <div class="col-md-6 pad_right_0">
	                	<label for="invoice_date"><span class="text-danger">* </span><?php echo _l('invoice_date'); ?></label>
	                	<?php $invoice_date = ( isset($pur_invoice) ? _d($pur_invoice->invoice_date) : _d(date('Y-m-d')) );
	                	 echo render_date_input('invoice_date','',$invoice_date,array( 'required' => 'true')); ?>
	                </div>

	                <div class="col-md-6 pad_left_0">
	                	<label for="invoice_date"><?php echo _l('pur_due_date'); ?></label>
	                	<?php $duedate = ( isset($pur_invoice) ? _d($pur_invoice->duedate) : _d(date('Y-m-d')) );
	                	 echo render_date_input('duedate','',$duedate); ?>
	                </div>

	                <div id="recurring_div" class="<?php if(isset($pur_invoice) && $pur_invoice->pur_order != null){ echo 'hide';} ?>">

	                <div class="form-group col-md-12 pad_left_0 pad_right_0">
	                	<label for="recurring"><?php echo _l('invoice_add_edit_recurring'); ?></label>
	                    <select name="recurring" id="recurring" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
	                        <?php for($i = 0; $i <=12; $i++){ ?>
	                        	<?php
                              $selected = '';
                              if(isset($pur_invoice)){
                                
	                             if($pur_invoice->recurring == $i){
	                               $selected = 'selected';
	                             }
                              }
                              if($i == 0){
                               $reccuring_string =  _l('invoice_add_edit_recurring_no');
                              } else if($i == 1){
                               $reccuring_string = _l('invoice_add_edit_recurring_month',$i);
                              } else {
                               $reccuring_string = _l('invoice_add_edit_recurring_months',$i);
                              }
                              ?>

	                        	<option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $reccuring_string; ?></option>
	                        <?php } ?>
	                    </select>
	                </div>

	                <div id="cycles_wrapper" class="<?php if(!isset($pur_invoice) || (isset($pur_invoice) && $pur_invoice->recurring == 0)){echo ' hide';}?>">
	                     <div class="col-md-12 pad_left_0 pad_right_0">
	                        <?php $value = (isset($pur_invoice) ? $pur_invoice->cycles : 0); ?>
	                        <div class="form-group recurring-cycles">
	                          <label for="cycles"><?php echo _l('recurring_total_cycles'); ?>
	                            <?php if(isset($pur_invoice) && $pur_invoice->total_cycles > 0){
	                              echo '<small>' . _l('cycles_passed', $pur_invoice->total_cycles) . '</small>';
	                            }
	                            ?>
	                          </label>
	                          <div class="input-group">
	                            <input type="number" class="form-control"<?php if($value == 0){echo ' disabled'; } ?> name="cycles" id="cycles" value="<?php echo $value; ?>" <?php if(isset($pur_invoice) && $pur_invoice->total_cycles > 0){echo 'min="'.($pur_invoice->total_cycles).'"';} ?>>
	                            <div class="input-group-addon">
	                              <div class="checkbox">
	                                <input type="checkbox"<?php if($value == 0){echo ' checked';} ?> id="unlimited_cycles">
	                                <label for="unlimited_cycles"><?php echo _l('cycles_infinity'); ?></label>
	                              </div>
	                            </div>
	                          </div>
	                        </div>
	                     </div>
	                </div>

	             	</div>

	                
	            </div>

	            <div class="col-md-6">
	            	<div class="col-md-6 pad_left_0">
                     <?php
                        $currency_attr = array('data-show-subtext'=>true, 'required' => true);

                        $selected = '';
                        foreach($currencies as $currency){
                          if(isset($pur_invoice) && $pur_invoice->currency != 0){
                            if($currency['id'] == $pur_invoice->currency){
                              $selected = $currency['id'];
                            }
                          }else{
                           if($currency['isdefault'] == 1){
                             $selected = $currency['id'];
                           }
                          }
                        }
       
                        ?>
                        <label for="currency"><span class="text-danger">* </span><?php echo _l('invoice_add_edit_currency') ?></label>
                     <?php echo render_select('currency', $currencies, array('id','name','symbol'), '', $selected, $currency_attr); ?>
                  	</div>
	                <div class="col-md-6 form-group">
	                    <div id="inputTagsWrapper">
	                       <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
	                       <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($pur_invoice) ? prep_tags_input(get_tags_in($pur_invoice->id,'pur_invoice')) : ''); ?>" data-role="tagsinput">
	                    </div>
	                </div>
	                <div class="col-md-6 pad_left_0">
	                	<?php $transactionid = ( isset($pur_invoice) ? $pur_invoice->transactionid : '');
	                	echo render_input('transactionid','transaction_id',$transactionid); ?>
	                </div>
	                <div class="col-md-6 pad_right_0">
	                	<?php $transaction_date = ( isset($pur_invoice) ? $pur_invoice->transaction_date : '');
	                	echo render_date_input('transaction_date','transaction_date',$transaction_date); ?>
	                </div>


                    <div class="col-md-12  pad_left_0 pad_right_0">
                       <div class="form-group select-placeholder">
                          <label for="discount_type"
                              class="control-label"><?php echo _l('discount_type'); ?></label>
                          <select name="discount_type" class="selectpicker" data-width="100%"
                              data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                             
                              <option value="before_tax" <?php
                        if (isset($pur_invoice)) {
                            if ($pur_invoice->discount_type == 'before_tax') {
                                echo 'selected';
                            }
                        } ?>><?php echo _l('discount_type_before_tax'); ?></option>
                              <option value="after_tax" <?php if (isset($pur_invoice)) {
                            if ($pur_invoice->discount_type == 'after_tax' || $pur_invoice->discount_type == null) {
                                echo 'selected';
                            }
                        }else {
                          echo 'selected';
                        } ?>><?php echo _l('discount_type_after_tax'); ?></option>
                          </select>
                      </div>
                    </div>
                
	                
	                <div class="col-md-12 pad_left_0 pad_right_0">
				        <div class="attachments">
				          <div class="attachment">
				            <div class="mbot15">
				              <div class="form-group">
				                <label for="attachment" class="control-label"><?php echo _l('ticket_add_attachments'); ?></label>
				                <div class="input-group">
				                  <input type="file" extension="<?php echo str_replace('.','',get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="attachments[0]" accept="<?php echo get_ticket_form_accepted_mimes(); ?>">
				                  <span class="input-group-btn">
				                    <button class="btn btn-success add_more_attachments p8-half" data-max="10" type="button"><i class="fa fa-plus"></i></button>
				                  </span>
				                </div>
				              </div>
				            </div>
				          </div>
				        </div>
				    </div>
	            </div>

            </div>

			<?php $rel_id=( isset($pur_invoice) ? $pur_invoice->id : false); ?>
            <?php echo render_custom_fields( 'pur_invoice',$rel_id); ?>
        	
          </div>



          <div class="panel-body mtop10 invoice-item">

		        <div class="row">
		          <div class="col-md-4">
		            <?php $this->load->view('purchase/item_include/main_item_select'); ?>
		          </div>

				          <?php
				        $base_currency = get_base_currency();

		                $po_currency = $base_currency;
		                if(isset($pur_invoice) && $pur_invoice->currency != 0){
		                  $po_currency = pur_get_currency_by_id($pur_invoice->currency);
		                } 

		                $from_currency = (isset($pur_invoice) && $pur_invoice->from_currency != null) ? $pur_invoice->from_currency : $base_currency->id;
		                echo form_hidden('from_currency', $from_currency);

		              ?>
		          <div class="col-md-8 <?php if($po_currency->id == $base_currency->id){ echo 'hide'; } ?>" id="currency_rate_div">
		            <div class="col-md-10 text-right">
		              
		              <p class="mtop10"><?php echo _l('currency_rate'); ?><span id="convert_str"><?php echo ' ('.$base_currency->name.' => '.$po_currency->name.'): ';  ?></span></p>
		            </div>
		            <div class="col-md-2 pull-right">
		              <?php $currency_rate = 1;
		                if(isset($pur_invoice) && $pur_invoice->currency != 0){
		                  $currency_rate = pur_get_currency_rate($base_currency->name, $po_currency->name);
		                }
		              echo render_input('currency_rate', '', $currency_rate, 'number', [], [], '', 'text-right'); 
		              ?>
		            </div>
		          </div>
		        </div> 
		        <div class="row">
		          <div class="col-md-12">
		            <div class="table-responsive s_table ">
		              <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
		                <thead>
		                  <tr>
		                    <th></th>
		                    <th width="12%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
		                    <th width="15%" align="left"><?php echo _l('item_description'); ?></th>
		                    <th width="10%" align="right"><?php echo _l('unit_price'); ?><span class="th_currency"><?php echo '('.$po_currency->name.')'; ?></span></th>
		                    <th width="10%" align="right" class="qty"><?php echo _l('quantity'); ?></th>
		                    <th width="12%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
		                    <th width="10%" align="right"><?php echo _l('tax_value'); ?><span class="th_currency"><?php echo '('.$po_currency->name.')'; ?></span></th>
		                    <th width="10%" align="right"><?php echo _l('pur_subtotal_after_tax'); ?><span class="th_currency"><?php echo '('.$po_currency->name.')'; ?></span></th>
		                    <th width="7%" align="right"><?php echo _l('discount').'(%)'; ?></th>
		                    <th width="10%" align="right"><?php echo _l('discount(money)'); ?><span class="th_currency"><?php echo '('.$po_currency->name.')'; ?></span></th>
		                    <th width="10%" align="right"><?php echo _l('total'); ?><span class="th_currency"><?php echo '('.$po_currency->name.')'; ?></span></th>
		                    <th align="center"><i class="fa fa-cog"></i></th>
		                  </tr>
		                </thead>
		                <tbody>
		                  <?php echo $pur_invoice_row_template; ?>
		                </tbody>
		              </table>
		            </div>
		          </div>
		         <div class="col-md-8 col-md-offset-4">
		          <table class="table text-right">
		            <tbody>
		              <tr id="subtotal">
		                <td><span class="bold"><?php echo _l('subtotal'); ?> :</span>
		                  <?php echo form_hidden('total_mn', ''); ?>
		                </td>
		                <td class="wh-subtotal">
		                </td>
		              </tr>
		              
		              <tr id="order_discount_percent">
		                <td>
		                  <div class="row">
		                    <div class="col-md-7">
		                      <span class="bold"><?php echo _l('pur_discount'); ?> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo _l('discount_percent_note'); ?>" ></i></span>
		                    </div>
		                    <div class="col-md-3">
		                      <?php $discount_total = isset($pur_invoice) ? $pur_invoice->discount_total : '';
		                      echo render_input('order_discount', '', $discount_total, 'number', ['onchange' => 'pur_calculate_total()', 'onblur' => 'pur_calculate_total()']); ?>
		                    </div>
		                     <div class="col-md-2">
		                        <select name="add_discount_type" id="add_discount_type" class="selectpicker" onchange="pur_calculate_total(); return false;" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
		                            <option value="percent">%</option>
		                            <option value="amount" selected><?php echo _l('amount'); ?></option>
		                        </select>
		                     </div>
		                  </div>
		                </td>
		                <td class="order_discount_value">

		                </td>
		              </tr>

		              <tr id="total_discount">
		                <td><span class="bold"><?php echo _l('total_discount'); ?> :</span>
		                  <?php echo form_hidden('dc_total', ''); ?>
		                </td>
		                <td class="wh-total_discount">
		                </td>
		              </tr>

		              <tr>
		                <td>
		                 <div class="row">
		                  <div class="col-md-9">
		                   <span class="bold"><?php echo _l('pur_shipping_fee'); ?></span>
		                 </div>
		                 <div class="col-md-3">
		                  <input type="number" onchange="pur_calculate_total()" data-toggle="tooltip" value="<?php if(isset($pur_invoice)){ echo $pur_invoice->shipping_fee; }else{ echo '0';} ?>" class="form-control pull-left text-right" name="shipping_fee">
		                </div>
		              </div>
		              </td>
		              <td class="shiping_fee">
		              </td>
		              </tr>
		              
		              <tr id="totalmoney">
		                <td><span class="bold"><?php echo _l('grand_total'); ?> :</span>
		                  <?php echo form_hidden('grand_total', ''); ?>
		                </td>
		                <td class="wh-total">
		                </td>
		              </tr>
		            </tbody>
		          </table>
		        </div>
		        <div id="removed-items"></div> 
		        </div>
		        </div>

		        <div class="row">
		          <div class="col-md-12 mtop15">
		             <div class="panel-body bottom-transaction">
		             	<div class="col-md-12 pad_left_0 pad_right_0">
	                	<?php $adminnote = ( isset($pur_invoice) ? $pur_invoice->adminnote : '');
		                	echo render_textarea('adminnote','adminnote',$adminnote, array('rows' => 7)) ?>
		                </div>

		                <div class="col-md-12 pad_left_0 pad_right_0">
		                	<?php $vendor_note = ( isset($pur_invoice) ? $pur_invoice->vendor_note : '');
		                	echo render_textarea('vendor_note','vendor_note',$vendor_note, array('rows' => 7)) ?>
		                </div>
		                <div class="col-md-12 pad_left_0 pad_right_0">
		                	<?php $terms = ( isset($pur_invoice) ? $pur_invoice->terms : '');
		                	echo render_textarea('terms','terms',$terms, array('rows' => 7)) ?>
		                </div>

		                <div class="btn-bottom-toolbar text-right">
                  
		                  <button type="button" class="btn-tr save_detail btn btn-info mleft10 transaction-submit">
		                  <?php echo _l('submit'); ?>
		                  </button>
		                </div>

		             </div>
		               <div class="btn-bottom-pusher"></div>
		          </div>
		        </div>
      	</div>

      	<?php echo form_close(); ?>
  	</div>
  </div>
</div>

<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/purchase/assets/js/pur_invoice_js.php';?>
