
  <div class="row row-margin-bottom">

    <div class=" col-md-4">
      <?php echo render_input('profit_rate_search','exchange_profit_margin_differences_','',''); ?>
    </div>
   <div class=" col-md-4">
    <?php $this->load->view('warehouse/item_include/item_select', ['select_name' => 'commodity_filter[]', 'id_name' => 'commodity_filter', 'multiple' => true, 'label_name' => 'commodity']); ?>
  </div>
    <!-- update filter by warehouse -->
    <div class=" col-md-4">
      <div class="form-group">
        <label><?php echo _l('warehouse_name') ?></label>
        <select name="warehouse_filter[]" id="warehouse_filter" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="" data-actions-box="true">

            <?php foreach($warehouse_filter as $warehouse) { ?>
              <option value="<?php echo new_html_entity_decode($warehouse['warehouse_id']); ?>"><?php echo new_html_entity_decode($warehouse['warehouse_name']); ?></option>
              <?php } ?>
          </select>
          </div>
    </div>


    
  </div>
  <br/>

  <div class="row">
    <div class="col-md-12">
    <?php 
    $table_data = array(
                        _l('commodity_name'),
                        _l('_profit_rate_p'),
                        _l('purchase_price'),
                        _l('rate'),
                        _l('average_price_of_inventory'),
                        _l('profit_rate_inventory'),
                        _l('exchange_profit_margin_differences'),
                                                 
                      );
    render_datatable($table_data,'table_inventory_inside',
        array('customizable-table')
        ); ?>

    </div>
  </div>


</body>
</html>
