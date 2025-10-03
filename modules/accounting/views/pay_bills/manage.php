<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="small-table">
				<div class="panel_s mbot10">
					<div class="panel-body _buttons">
						<div class="row">
							<div class="col-md-11 ">
								<h4 class="no-margin font-bold"><i class="fa fa-inbox" aria-hidden="true"></i> <?php echo _l('acc_paybill_management'); ?></h4>
							</div>
						</div>
					</div>
				</div>

				<div class="panel_s">
					<div class="panel-body">
						<div class="row">    
                           <div class="col-md-6">
                              <?php if(has_permission('accounting_bills','','view')){ ?>
                                 <a href="<?php echo admin_url('accounting/bills'); ?>"class="btn btn-default pull-left mright10 ">
                                    <?php echo _l('acc_back'); ?>
                                 </a>
                              <?php } ?>
                              
                           </div>
                        </div>
                     </br>
                     <div class="row">
                          <div class="col-md-3">
                           <?php echo render_select('vendor_id[]', $list_vendor, array('userid', array('company')), 'acc_vendor', '', ['multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker'], array(), '', '', false); ?>

                          </div>
                          <div class="col-md-3">
                            <?php echo render_date_input('from_date','from_date'); ?>
                          </div>
                          <div class="col-md-3">
                            <?php echo render_date_input('to_date','to_date'); ?>
                       </div>

                        </div>
                        <div class="clearfix"></div>

						<?php render_datatable(array(
							_l('id'),
							_l('pay_bill').' #',
							_l('ref_number'),
							_l('acc_vendor_name'),
							_l('acc_date'),
							_l('acc_amount'),
							_l('options'),
						),'table_paybill',['table_paybill' => 'table_paybill']); ?>
						
					</div>
				</div>
			</div>
			<div class="col-md-7 small-table-right-col">
				<div id="packing_list_sm_view" class="hide">
				</div>
			</div>
		</div>
	</div>
</div>

<?php init_tail(); ?>
<?php require 'modules/accounting/assets/js/pay_bill/manage_js.php';?>
</body>
</html>
