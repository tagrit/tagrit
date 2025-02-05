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
                      <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                      <hr />
                     </div>
                    </div>
                    <div class="row">    
                          <div class="_buttons col-md-12">
                            <?php if (has_permission('purchase_invoices', '', 'create') || is_admin()) { ?>
                            <a href="<?php echo admin_url('purchase/pur_invoice'); ?>"class="btn btn-info pull-left mright10 display-block">
                                <?php echo _l('new'); ?>
                            </a>
                            <?php } ?>
                            <div class="col-md-2">
                                <?php echo render_date_input('from_date','','',array('placeholder' => _l('from_date') )); ?>
                            </div>
                            <div class="col-md-2">
                                <?php echo render_date_input('to_date','','',array('placeholder' => _l('to_date') )); ?>
                            </div>
                            <div class="col-md-2 form-group">
                             
                              <select name="contract[]" id="contract" class="selectpicker" multiple="true"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('contract'); ?>">
                                 <?php foreach($contracts as $ct){ ?>
                                  <option value="<?php echo pur_html_entity_decode($ct['id']); ?>" ><?php echo pur_html_entity_decode($ct['contract_number']); ?></option>
                                 <?php } ?>
                              </select>


                             </div>
                             <div class="col-md-2 form-group">
                               <select name="pur_orders[]" id="pur_orders" class="selectpicker" multiple="true"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('purchase_order'); ?>">
                                 <?php foreach($pur_orders as $ct){ ?>
                                  <option value="<?php echo pur_html_entity_decode($ct['id']); ?>" <?php if($this->input->get('po') != null && $this->input->get('po') == $ct['id']){ echo 'selected'; } ?> ><?php echo pur_html_entity_decode($ct['pur_order_number']); ?></option>
                                 <?php } ?>
                              </select>
                             </div>
                             <div class="col-md-2 form-group">
                                <?php echo render_select('vendor_ft[]',$vendors,array('userid','company'),'','',array('data-width'=>'100%','data-none-selected-text'=>_l('vendors'),'multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false); ?>
                            </div>
                        </div>
                      </div>
                    
                    <?php 

                    $table_data = array(
                        _l('invoice_code'),
                        _l('invoice_number'),
                        _l('vendor'),
                        _l('contract'),
                        _l('pur_order'),
                        _l('invoice_date'),
                        _l('recurring_from'),
                        _l('invoice_amount'),
                        _l('tax_value'),
                        _l('total_included_tax'),
                        _l('payment_request_status'),
                        _l('payment_status'),
                        _l('transaction_id'),
                        _l('tag'),
                        );

                    $custom_fields = get_custom_fields('pur_invoice',array('show_on_table'=>1));
                    foreach($custom_fields as $field){
                     array_push($table_data,$field['name']);
                    }
                    
                    render_datatable($table_data,'table_pur_invoices'); ?>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
</body>
</html>