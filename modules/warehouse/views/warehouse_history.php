<?php init_head(); ?>

<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12" id="small-table">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-12">
                      <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                      <hr>
                      <br>

                    </div>
                  </div>
                  <div class="row">
                     
                    <div class=" col-md-3">
                        <div class="form-group">
                          <select name="warehouse_filter[]" id="warehouse_filter" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('filters_by_warehouse'); ?>">

                              <?php foreach($warehouse_filter as $warehouse) { ?>
                                <option value="<?php echo new_html_entity_decode($warehouse['warehouse_id']); ?>"><?php echo new_html_entity_decode($warehouse['warehouse_name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class=" col-md-3">
                    <?php $this->load->view('warehouse/item_include/item_select', ['select_name' => 'commodity_filter[]', 'id_name' => 'commodity_filter', 'multiple' => true, 'data_none_selected_text' => 'filters_by_commodity']); ?>
                </div>
                
                <div class=" col-md-2">
                    <div class="form-group">
                      <select name="status[]" id="status" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('filters_by_status'); ?>">

                        <option value="1"><?php echo _l('stock_import'); ?></option>
                        <option value="2"><?php echo _l('stock_export'); ?></option>
                        <option value="3"><?php echo _l('loss_adjustment'); ?></option>
                        <option value="4"><?php echo _l('internal_delivery_note'); ?></option>
                    </select>
                </div>
            </div>
            <div  class="col-md-2 leads-filter-column">
                <div class="form-group" app-field-wrapper="validity_start_date">
                    <div class="input-group date">
                        <input type="text" id="validity_start_date" name="validity_start_date" class="form-control datepicker" value="" autocomplete="off" placeholder="<?php echo _l('from_date') ?>">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar calendar-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div  class="col-md-2 leads-filter-column">
                <div class="form-group" app-field-wrapper="validity_end_date">
                    <div class="input-group date">
                        <input type="text" id="validity_end_date" name="validity_end_date" class="form-control datepicker" value="" autocomplete="off" placeholder="<?php echo _l('to_date') ?>">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar calendar-icon"></i>
                        </div>
                    </div>
                </div>
            </div> 


            
        </div>
                    <br><br>

                      <?php render_datatable(array(
                        _l('id'),
                        _l('form_code'),
                        _l('commodity_code'),
                        _l('warehouse_code'),
                        _l('warehouse_name'),
                        _l('day_vouchers'),
                        _l('opening_stock'),
                        _l('closing_stock'),
                        _l('lot_number').'/'._l('quantity_sold'),
                        _l('expiry_date'),
                        _l('wh_serial_number'),
                        _l('note'),
                        _l('status_label'),
                        ),'table_warehouse_history'); ?>
               </div>
            </div>
         </div>
         
      </div>
   </div>
   
</div>


<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/warehouse_history_js.php';?>
</body>
</html>

