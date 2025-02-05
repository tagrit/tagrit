<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
        	<?php if((debits_can_be_applied_to_invoice($pur_invoice->payment_status) && $debits_available > 0)) { ?>
			<div class="alert alert-warning mbot5">
			   <?php echo app_format_money($debits_available, $vendor_currency->name).' '. _l('x_debits_available'); ?>
			   <br />
			   <a href="#" data-toggle="modal" data-target="#apply_debits"><?php echo _l('apply_debits'); ?></a>
			</div>
			<?php } ?>

          <div class="panel-body">
          	<div class="row">
             <div class="col-md-12">
              <h4 class="no-margin font-bold"><?php echo _l($title); ?> </h4>
 				<br>
             </div>
            </div>
            	<?php 
            		$base_currency = get_base_currency_pur();
            		if($pur_invoice->currency != 0){
            			$base_currency = pur_get_currency_by_id($pur_invoice->currency);
            		}
            	 ?>
            	<?php echo form_hidden('invoice_id', $pur_invoice->id) ?>
	            <div class="horizontal-scrollable-tabs preview-tabs-top">
	            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
	            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
	            <div class="horizontal-tabs">
	               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
	                  <li role="presentation" class="active">
	                     <a href="#tab_pur_invoice" aria-controls="tab_pur_invoice" role="tab" data-toggle="tab">
	                     <?php echo _l('pur_invoice'); ?>
	                     </a>
	                  </li>
	                  <li role="presentation">
	                     <a href="#payment_record" aria-controls="payment_record" role="tab" data-toggle="tab">
	                     <?php echo _l('payment_record'); ?>
	                     </a>
	                  </li>
	                  <?php if(count($applied_debits) > 0) { ?>
	                  <li role="presentation">
	                     <a href="#invoice_applied_debits" aria-controls="invoice_applied_debits" role="tab" data-toggle="tab">
	                     <?php echo _l('applied_debits'); ?> <span class="badge"><?php echo count($applied_debits); ?></span>
	                     </a>
	                  </li>
	                  <?php } ?>   
	                  <li role="presentation">
	                     <a href="#tab_reminders" onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?php echo pur_html_entity_decode($pur_invoice->id) ;?> + '/' + 'pur_invoice', undefined, undefined, undefined,[1,'asc']); return false;" aria-controls="tab_reminders" role="tab" data-toggle="tab">
	                     <?php echo _l('pur_invoice_reminders'); ?>
	                     <?php
	                        $total_reminders = total_rows(db_prefix().'reminders',
	                          array(
	                           'isnotified'=>0,
	                           'staff'=>get_staff_user_id(),
	                           'rel_type'=>'pur_invoice',
	                           'rel_id'=>$pur_invoice->id
	                           )
	                          );
	                        if($total_reminders > 0){
	                          echo '<span class="badge">'.$total_reminders.'</span>';
	                        }
	                        ?>
	                     </a>
	                  </li>
	                     <?php
	                     $customer_custom_fields = false;
	                     if(total_rows(db_prefix().'customfields',array('fieldto'=>'pur_invoice','active'=>1)) > 0 ){
	                          $customer_custom_fields = true;
	                      ?>
	                  <li role="presentation" >
	                     <a href="#custom_fields" aria-controls="custom_fields" role="tab" data-toggle="tab">
	                     <?php echo _l( 'custom_fields'); ?>
	                     </a>
	                  </li>
	                  <?php } ?>
	                  <li role="presentation">
	                     <a href="#tab_tasks" onclick="init_rel_tasks_table(<?php echo pur_html_entity_decode($pur_invoice->id); ?>,'pur_invoice'); return false;" aria-controls="tab_tasks" role="tab" data-toggle="tab">
	                     <?php echo _l('tasks'); ?>
	                     </a>
	                  </li>
	                  <li role="presentation">
	                     <a href="#tab_notes" onclick="get_sales_notes_inv(<?php echo pur_html_entity_decode($pur_invoice->id); ?>,'purchase'); return false" aria-controls="tab_notes" role="tab" data-toggle="tab">
	                     <?php echo _l('pur_invoice_notes'); ?>
	                     <span class="notes-total">
	                        <?php $totalNotes       = total_rows(db_prefix().'notes', ['rel_id' => $pur_invoice->id, 'rel_type' => 'pur_invoice']);
	                        if($totalNotes > 0){ ?>
	                           <span class="badge"><?php echo ($totalNotes); ?></span>
	                        <?php } ?>
	                     </span>
	                     </a>
	                  </li>
	                  <li role="presentation" class="tab-separator">
	                    <?php
	                              $totalComments = total_rows(db_prefix().'pur_comments',['rel_id' => $pur_invoice->id, 'rel_type' => 'pur_invoice']);
	                              ?>
	                     <a href="#discuss" aria-controls="discuss" role="tab" data-toggle="tab">
	                     <?php echo _l('pur_discuss'); ?>
	                      <span class="badge comments-indicator<?php echo $totalComments == 0 ? ' hide' : ''; ?>"><?php echo $totalComments; ?></span>
	                     </a>
	                  </li>
	                  <li role="presentation">
	                     <a href="#attachment" aria-controls="attachment" role="tab" data-toggle="tab">
	                     <?php echo _l('attachment'); ?>
	                     </a>
	                  </li>  
	                  
	                  
	               </ul>
	            </div>
	         	</div>

	         	<div class="row">
	         		<div class="col-md-3">
	         			<?php $class = '';
	         			if($pur_invoice->payment_status == 'unpaid'){
	         				$class = 'danger';
	         			}elseif($pur_invoice->payment_status == 'paid'){
	         				$class = 'success';
	         			}elseif($pur_invoice->payment_status == 'partially_paid'){
	         				$class = 'warning';
	         			} ?>
	         			<span class="label label-<?php echo pur_html_entity_decode($class); ?> mtop5 s-status invoice-status-3"><?php echo _l($pur_invoice->payment_status); ?></span>
	         		</div>
	         		<div class="col-md-9 _buttons">
	         			<div class="visible-xs">
		                  <div class="mtop10"></div>
		               	</div>
		               	<div class="pull-right">

		               		<div class="btn-group">
                                <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" ><i class="fa-regular fa-file-pdf"></i><?php if (is_mobile()) {
                                echo ' PDF';
                            } ?> <span class="caret"></span></a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li class="hidden-xs"><a
                                            href="<?php echo admin_url('purchase/purchase_invoice_pdf/' . $pur_invoice->id . '?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a>
                                    </li>
                                    <li class="hidden-xs"><a
                                            href="<?php echo admin_url('purchase/purchase_invoice_pdf/' . $pur_invoice->id . '?output_type=I'); ?>"
                                            target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                                    <li><a
                                            href="<?php echo admin_url('purchase/purchase_invoice_pdf/' . $pur_invoice->id); ?>"><?php echo _l('download'); ?></a>
                                    </li>
                                    <li>
                                        <a href="<?php echo admin_url('purchase/purchase_invoice_pdf/' . $pur_invoice->id . '?print=true'); ?>"
                                            target="_blank">
                                            <?php echo _l('print'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>

		               		<a href="<?php echo admin_url('purchase/pur_invoice/'.$pur_invoice->id); ?>" data-toggle="tooltip" title="<?php echo _l('edit_invoice'); ?>" class="btn btn-default btn-with-tooltip mright5" data-placement="bottom"><i class="fa fa-pencil-square"></i></a>
		               		
		               	   <?php if(purinvoice_left_to_pay($pur_invoice->id) > 0){ ?>
			               	<a href="#" onclick="add_payment(<?php echo pur_html_entity_decode($pur_invoice->id); ?>); return false;" class="btn btn-success pull-right"><i class="fa fa-plus-square"></i>&nbsp;<?php echo ' '._l('payment'); ?></a>
			               <?php } ?>
		               	</div>

	         		</div>
	         	</div>

	         	<div class="clearfix"></div>
         		<hr class="hr-panel-heading" />

	         	<div class="tab-content">
	         		<div role="tabpanel" class="tab-pane ptop10 active" id="tab_pur_invoice">
	         			<div class="col-md-6 pad_left_0">
	         				<div class="col-md-6 pad_left_0 border-right">
	         					<p><?php echo _l('invoice_code').':'; ?><span class="pull-right bold"><?php echo pur_html_entity_decode($pur_invoice->invoice_number); ?></span></p>
	         				</div>
	         				<div class="col-md-6 pad_right_0">
	         					<p><?php echo _l('invoice_number').':'; ?><span class="pull-right bold">
	         						<?php $vendor_invoice_number = $pur_invoice->vendor_invoice_number != '' ?  $pur_invoice->vendor_invoice_number : $pur_invoice->invoice_number;
	         							echo pur_html_entity_decode($vendor_invoice_number); 
	         					?></span></p>
	         				</div>

	         				<div class="col-md-12 pad_left_0 pad_right_0">
	         					<hr class="mtop5 mbot5">
	         				</div>

	         				<div class="col-md-6 pad_left_0 border-right">
	         					<p><?php echo _l('invoice_date').':'; ?><span class="pull-right bold"><?php echo _d($pur_invoice->invoice_date); ?></span></p>
	         				</div>

	         				<div class="col-md-6 pad_right_0">
	         					<p><?php echo _l('contract').':'; ?><span class="pull-right bold"><a href="<?php echo admin_url('purchase/contract/'.$pur_invoice->contract); ?>" ><?php echo get_pur_contract_number($pur_invoice->contract); ?></a></span></p>
	         				</div>

	         				
	         				<div class="col-md-12 pad_left_0 pad_right_0">
	         					<hr class="mtop5 mbot5">
	         				</div>

	         				<div class="col-md-6  pad_left_0  border-right  ">
	         					<p><?php echo _l('pur_due_date').':'; ?><span class="pull-right bold"><?php echo _d($pur_invoice->duedate); ?></span></p>
	         				</div>
	         				<div class="col-md-6 pad_right_0">
	         					<p><?php echo _l('purchase_order').':'; ?><span class="pull-right bold"><a href="<?php echo admin_url('purchase/purchase_order/'.$pur_invoice->pur_order); ?>" ><?php echo get_pur_order_subject($pur_invoice->pur_order); ?></a></span></p>
	         					
	         				</div>
	         				<div class="col-md-12 pad_left_0 pad_right_0">
	         					<hr class="mtop5 mbot5">
	         				</div>
	         				<div class="col-md-6 pad_left_0">
	         					<p><?php echo _l('invoice_amount').':'; ?><span class="pull-right bold"><?php echo app_format_money($pur_invoice->total,$base_currency->symbol); ?></span></p>
	         				</div>

	         			</div>

	         			<div class="col-md-6">

	         				<div class="col-md-12 pad_left_0 pad_right_0 ">
	         					<div id="inputTagsWrapper">
			                       <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
			                       <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($pur_invoice) ? prep_tags_input(get_tags_in($pur_invoice->id,'pur_invoice')) : ''); ?>" data-role="tagsinput">
			                    </div>
	         				</div>
	         				<div class="col-md-12 pad_left_0 pad_right_0">
	         					<hr class="mtop5 mbot5">
	         				</div>

	         				<div class="col-md-6 pad_left_0 border-right">
	         					<p><?php echo _l('transaction_id').':'; ?><span class="pull-right bold"><?php echo pur_html_entity_decode($pur_invoice->transactionid); ?></span></p>
	         				</div>
	         				<div class="col-md-6 pad_right_0">
	         					<p><?php echo _l('transaction_date').':'; ?><span class="pull-right bold"><?php echo _d($pur_invoice->transaction_date); ?></span></p>
	         				</div>
	         				<div class="col-md-12 pad_left_0 pad_right_0">
	         					<hr class="mtop5 mbot5">
	         				</div>
	         				<div class="col-md-6 pad_left_0 border-right">
	         					<p><?php echo _l('add_from').':'; ?><span class="pull-right bold"><a href="<?php echo admin_url('staff/profile/'.$pur_invoice->add_from); ?>" ><?php echo get_staff_full_name($pur_invoice->add_from); ?></a></span></p>
	         				</div>
	         				<div class="col-md-6 pad_right_0">
	         					<p><?php echo _l('date_add').':'; ?><span class="pull-right bold"><?php echo _d($pur_invoice->date_add); ?></span></p>
	         				</div>
	         				<div class="col-md-12 pad_left_0 pad_right_0">
	         					<hr class="mtop5 mbot5">
	         				</div>

	         			</div>

	         			<div class="col-md-12 pad_left_0 pad_right_0">
		         			<div class="table-responsive">
	                           <table class="table items items-preview estimate-items-preview" data-type="estimate">
	                              <thead>
	                                 <tr>
	          
	                                    <th class="description" width="50%" align="left"><?php echo _l('items'); ?></th>
	                                    <th align="right"><?php echo _l('purchase_quantity'); ?></th>
	                                    <th align="right"><?php echo _l('purchase_unit_price'); ?></th>
	                                    <th align="right"><?php echo _l('into_money'); ?></th>
	                                    <?php if(get_option('show_purchase_tax_column') == 1){ ?>
	                                    <th align="right"><?php echo _l('tax'); ?></th>
	                                    <?php } ?>
	                                    <th align="right"><?php echo _l('sub_total'); ?></th>
	                                    <th align="right"><?php echo _l('discount(%)'); ?></th>
	                                    <th align="right"><?php echo _l('discount(money)'); ?></th>
	                                    <th align="right"><?php echo _l('total'); ?></th>
	                                 </tr>
	                              </thead>
	                              <tbody class="ui-sortable">

	                                 <?php $item_discount = 0;
	                                 if(count($invoice_detail) > 0){
	                                    $count = 1;
	                                    $t_mn = 0;
	                                    
	                                 foreach($invoice_detail as $es) { ?>
	                                 <tr nobr="true" class="sortable">

	                                    <td class="description" align="left;"><span><strong><?php 
	                                    $item = get_item_hp($es['item_code']); 
	                                    if(isset($item) && isset($item->commodity_code) && isset($item->description)){
	                                       echo pur_html_entity_decode($item->commodity_code.' - '.$item->description);
	                                    }else{
	                                       echo pur_html_entity_decode($es['item_name']);
	                                    }
	                                    ?></strong><?php if($es['description'] != ''){ ?><br><span><?php echo pur_html_entity_decode($es['description']); ?></span><?php } ?></td>
	                                    <td align="right"  width="12%"><?php echo pur_html_entity_decode($es['quantity']); ?></td>
	                                    <td align="right"><?php echo app_format_money($es['unit_price'],$base_currency->symbol); ?></td>
	                                    <td align="right"><?php echo app_format_money($es['into_money'],$base_currency->symbol); ?></td>
	                                    <?php if(get_option('show_purchase_tax_column') == 1){ ?>
	                                    <td align="right"><?php echo app_format_money(($es['total'] - $es['into_money']),$base_currency->symbol); ?></td>
	                                    <?php } ?>
	                                    <td class="amount" align="right"><?php echo app_format_money($es['total'],$base_currency->symbol); ?></td>
	                                    <td class="amount" width="12%" align="right"><?php echo ($es['discount_percent'].'%'); ?></td>
	                                    <td class="amount" align="right"><?php echo app_format_money($es['discount_money'],$base_currency->symbol); ?></td>
	                                    <td class="amount" align="right"><?php echo app_format_money($es['total_money'],$base_currency->symbol); ?></td>
	                                 </tr>
	                              <?php 
	                              $t_mn += $es['total_money'];
	                              $item_discount += $es['discount_money'];
	                              $count++; } } ?>
	                              </tbody>
	                           </table>
	                        </div>
	                    </div>

                        <div class="col-md-5 col-md-offset-7 pad_left_0 pad_right_0">
	                        <table class="table text-right">
	                           <tbody>
	                              <tr id="inv_subtotal">
	                                 <td><span class="bold"><?php echo _l('subtotal'); ?></span>
	                                 </td>
	                                 <td class="inv_subtotal">
	                                    <?php echo app_format_money($pur_invoice->subtotal,$base_currency->symbol); ?>
	                                 </td>
	                              </tr>

	                              <?php if($tax_data['preview_html'] != ''){
	                                echo pur_html_entity_decode($tax_data['preview_html']);
	                              } ?>


	                              <?php if(($pur_invoice->discount_total + $item_discount) > 0){ ?>
	                              
	                              <tr id="inv_discount_total">
	                                 <td><span class="bold"><?php echo _l('discount_total(money)'); ?></span>
	                                 </td>
	                                 <td class="inv_discount_total">
	                                    <?php echo '-'.app_format_money(($pur_invoice->discount_total + $item_discount), $base_currency->symbol); ?>
	                                 </td>
	                              </tr>
	                              <?php } ?>

	                              <?php if($pur_invoice->shipping_fee  > 0){ ?>
	                              
	                              <tr id="inv_discount_total">
	                                 <td><span class="bold"><?php echo _l('pur_shipping_fee'); ?></span>
	                                 </td>
	                                 <td class="inv_discount_total">
	                                    <?php echo app_format_money($pur_invoice->shipping_fee, $base_currency->symbol); ?>
	                                 </td>
	                              </tr>
	                              <?php } ?>


	                              <tr id="inv_total">
	                                 <td><span class="bold"><?php echo _l('total'); ?></span>
	                                 </td>
	                                 <td class="inv_total bold">
	                                    <?php echo app_format_money($pur_invoice->total, $base_currency->symbol); ?>
	                                 </td>
	                              </tr>
	                           </tbody>
	                        </table>
	                     </div>


	         			<div class="col-md-12 pad_left_0 pad_right_0 ">
         					<p><span class="bold"><?php echo _l('adminnote').': '; ?></span><span><?php echo pur_html_entity_decode($pur_invoice->adminnote); ?></span></p>
         				</div>
         				<div class="col-md-12 pad_left_0 pad_right_0">
         					<hr class="mtop5 mbot5">
         				</div>
	         			<div class="col-md-12 pad_left_0 pad_right_0 ">
         					<p><span class="bold"><?php echo _l('vendor_note').': '; ?></span><span><?php echo pur_html_entity_decode($pur_invoice->vendor_note); ?></span></p>
         				</div>
         				<div class="col-md-12 pad_left_0 pad_right_0">
         					<hr class="mtop5 mbot5">
         				</div>
         				<div class="col-md-12 pad_left_0 pad_right_0 ">
         					<p><span class="bold"><?php echo _l('terms').': '; ?></span><span><?php echo pur_html_entity_decode($pur_invoice->terms); ?></span></p>
         				</div>
	         		</div>
	         		<div role="tabpanel" class="tab-pane" id="tab_reminders">
		               <a href="#" data-toggle="modal" class="btn btn-info" data-target=".reminder-modal-pur_invoice-<?php echo pur_html_entity_decode($pur_invoice->id); ?>"><i class="fa fa-bell-o"></i> <?php echo _l('estimate_set_reminder_title'); ?></a>
		               <hr />
		               <?php render_datatable(array( _l( 'reminder_description'), _l( 'reminder_date'), _l( 'reminder_staff'), _l( 'reminder_is_notified')), 'reminders'); ?>
		               <?php $this->load->view('admin/includes/modals/reminder',array('id'=>$pur_invoice->id,'name'=>'pur_invoice','members'=>$members,'reminder_title'=>_l('estimate_set_reminder_title'))); ?>
		            </div>

		            <?php if($customer_custom_fields) { ?>
		              <div role="tabpanel" class="tab-pane" id="custom_fields">
		                <?php echo form_open(admin_url('purchase/update_customfield_invoice/'.$pur_invoice->id)); ?>
		                 <?php $rel_id=( isset($pur_invoice) ? $pur_invoice->id : false); ?>
		                 <?php echo render_custom_fields( 'pur_invoice',$rel_id); ?>

		                <div class="bor_top_0" >
		                   <button id="obgy_btn2" type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
		               </div>
		                <?php echo form_close(); ?>
		              </div>
		             <?php } ?>

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

		            <div role="tabpanel" class="tab-pane" id="tab_notes">
	                    <br>
	                     <?php echo form_open(admin_url('purchase/add_pur_invoice_note/'.$pur_invoice->id),array('id'=>'sales-notes','class'=>'pur_invoice-notes-form')); ?>
	                     <?php echo render_textarea('description'); ?>
	                     <div class="text-right">
	                        <button type="submit" class="btn btn-info mtop15 mbot15"><?php echo _l('estimate_add_note'); ?></button>
	                     </div>
	                     <?php echo form_close(); ?>
	                     <hr />
	                     <div class="panel_s mtop20 no-shadow" id="sales_notes_area">
	                     </div>
                  	</div>

                  	<div role="tabpanel" class="tab-pane" id="payment_record">
		               <div class="col-md-6 pad_left_0" >
		               <h4 class="font-medium mbot15 bold text-success"><?php echo _l('payment_for_pur_invoice').' '.$pur_invoice->invoice_number; ?></h4>
		               </div>
		               
		               <div class="clearfix"></div>
		               <table class="table dt-table">
		                   <thead>
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
		                           <td><?php echo app_format_money($pay['amount'],$base_currency->symbol); ?></td>
		                           <td><?php echo get_payment_mode_by_id($pay['paymentmode']); ?></td>
		                           <td><?php echo pur_html_entity_decode($pay['transactionid']); ?></td>
		                           <td><?php echo _d($pay['date']); ?></td>
		                           <td><?php echo get_status_approve($pay['approval_status']); ?></td>
		                           <td>
		                           	<?php if(has_permission('purchase_invoices','','edit') || is_admin()){ ?>
		                           		<a href="<?php echo admin_url('purchase/payment_invoice/'.$pay['id']); ?>" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="top" title="<?php echo _l('view'); ?>" ><i class="fa fa-eye "></i></a>
		                           	<?php } ?>

		                           	<?php if(has_permission('purchase_invoices','','delete') || is_admin()){ ?>
		                            <a href="<?php echo admin_url('purchase/delete_payment_pur_invoice/'.$pay['id'].'/'.$pur_invoice->id); ?>" class="btn btn-danger btn-icon _delete" data-toggle="tooltip" data-placement="top" title="<?php echo _l('delete'); ?>" ><i class="fa fa-remove"></i></a>
		                        	<?php } ?>
		                           </td>
		                        </tr>
		                     <?php } ?>
		                  </tbody>
		               </table>
		            </div>
		            <?php if(count($applied_debits) > 0){ ?>
		            <div class="tab-pane" role="tabpanel" id="invoice_applied_debits">
		               <div class="table-responsive">
		                  <table class="table table-bordered table-hover no-mtop">
		                     <thead>
		                        <th><span class="bold"><?php echo _l('debit_note'); ?> #</span></th>
		                        <th><span class="bold"><?php echo _l('debit_date'); ?></span></th>
		                        <th><span class="bold"><?php echo _l('debit_amount'); ?></span></th>
		                     </thead>
		                     <tbody>
		                        <?php foreach($applied_debits as $debit) { ?>
		                        <tr>
		                           <td>
		                              <a href="<?php echo admin_url('purchase/debit_notes/'.$debit['debit_id']); ?>"><?php echo format_debit_note_number($debit['debit_id']); ?></a>
		                           </td>
		                           <td><?php echo _d($debit['date']); ?></td>
		                           <td><?php echo app_format_money($debit['amount'], $base_currency->symbol) ?>
		                              <?php if(has_permission('purchase_debit_notes','','delete')){ ?>
		                              <a href="<?php echo admin_url('purchase/delete_invoice_applied_debit/'.$debit['id'].'/'.$debit['debit_id'].'/'.$pur_invoice->id); ?>" class="pull-right text-danger _delete"><i class="fa fa-trash"></i></a>
		                              <?php } ?>
		                           </td>
		                        </tr>
		                        <?php } ?>
		                     </tbody>
		                  </table>
		               </div>
		            </div>
		            <?php } ?>
                  	<div role="tabpanel" class="tab-pane" id="attachment">
                  		<?php echo form_open_multipart(admin_url('purchase/purchase_invoice_attachment/'.$pur_invoice->id),array('id'=>'partograph-attachments-upload')); ?>
		            
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

		             
		                   <button id="obgy_btn2" type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
		          
		                <?php echo form_close(); ?>
		               
		               <div class="col-md-12" id="purinv_pv_file">
		                                    <?php
		                                        $file_html = '';
		                                        if(count($pur_invoice_attachments) > 0){
		                                            $file_html .= '<hr />
		                                                    <p class="bold text-muted">'._l('customer_attachments').'</p>';
		                                            foreach ($pur_invoice_attachments as $f) {
		                                                $href_url = site_url(PURCHASE_PATH.'pur_invoice/'.$f['rel_id'].'/'.$f['file_name']).'" download';
		                                                                if(!empty($f['external'])){
		                                                                  $href_url = $f['external_link'];
		                                                                }
		                                               $file_html .= '<div class="mbot15 row inline-block full-width" data-attachment-id="'. $f['id'].'">
		                                              <div class="col-md-8">
		                                                 <a name="preview-purinv-btn" onclick="preview_purinv_btn(this); return false;" rel_id = "'. $f['rel_id']. '" id = "'.$f['id'].'" href="Javascript:void(0);" class="mbot10 mright5 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="'. _l('preview_file').'"><i class="fa fa-eye"></i></a>
		                                                 <div class="pull-left"><i class="'. get_mime_class($f['filetype']).'"></i></div>
		                                                 <a href=" '. $href_url.'" target="_blank" download>'.$f['file_name'].'</a>
		                                                 <br />
		                                                 <small class="text-muted">'.$f['filetype'].'</small>
		                                              </div>
		                                              <div class="col-md-4 text-right">';
		                                                if($f['staffid'] == get_staff_user_id() || is_admin()){
		                                                $file_html .= '<a href="#" class="text-danger" onclick="delete_purinv_attachment('. $f['id'].'); return false;"><i class="fa fa-times"></i></a>';
		                                                } 
		                                               $file_html .= '</div></div>';
		                                            }
		                                            $file_html .= '<hr />';
		                                            echo pur_html_entity_decode($file_html);
		                                        }
		                                     ?>
		                                  </div>

		               <div id="purinv_file_data"></div>
                  	</div>

                  	<div role="tabpanel" class="tab-pane" id="tab_tasks">
                     <?php init_relation_tasks_table(array('data-new-rel-id'=>$pur_invoice->id,'data-new-rel-type'=>'pur_invoice')); ?>
                  </div>


	         	</div>

          </div>
        </div>
    </div>
  </div>
</div>

<div class="modal fade" id="payment_record_pur" tabindex="-1" role="dialog">
    <div class="modal-dialog dialog_30" >
        <?php echo form_open(admin_url('purchase/add_invoice_payment/'.$pur_invoice->id),array('id'=>'purinvoice-add_payment-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_payment'); ?></span>
                    <span class="add-title"><?php echo _l('new_payment'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                     <div id="additional"></div>
                     <?php echo render_input('amount','amount',purinvoice_left_to_pay($pur_invoice->id),'number',array('max' => purinvoice_left_to_pay($pur_invoice->id))); ?>
                        <?php echo render_date_input('date','payment_edit_date'); ?>
                        <?php echo render_select('paymentmode',$payment_modes,array('id','name'),'payment_mode'); ?>
                        
                        <?php echo render_input('transactionid','payment_transaction_id'); ?>
                        <?php echo render_textarea('note','note','',array('rows'=>7)); ?>

                    </div>
                </div>
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

 
<?php init_tail(); ?>
<?php $this->load->view('debit_notes/apply_invoice_debits'); ?>   
</body>
</html>