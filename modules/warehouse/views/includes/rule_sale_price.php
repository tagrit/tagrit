<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="horizontal-scrollable-tabs preview-tabs-top">
  <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
  <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
  <div class="horizontal-tabs">
   <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
    <li role="presentation" class="active">
     <a href="#tab_items" aria-controls="tab_items" role="tab" data-toggle="tab">
       <?php echo _l('items'); ?>
     </a>
   </li>  
   <li role="presentation">
     <a href="#tab_receipt_delivery"  aria-controls="tab_receipt_delivery" role="tab" data-toggle="tab">
       <?php echo _l('wh_receipt_delivery_voucher'); ?>
     </a>
   </li>
   <li role="presentation">
     <a href="#tab_order_return"  aria-controls="tab_order_return" role="tab" data-toggle="tab">
       <?php echo _l('inventory_receipt_inventory_delivery_returns_goods'); ?>
     </a>
   </li>
   <li role="presentation">
    <a href="#tab_packing_lists"  aria-controls="tab_packing_lists" role="tab" data-toggle="tab">
       <?php echo _l('wh_packing_lists'); ?>
     </a>
   </li>

   <li role="presentation">
     <a href="#tab_pdf"  aria-controls="tab_pdf" role="tab" data-toggle="tab">
       <?php echo _l('wh_pdf'); ?>
     </a>
   </li>
   <li role="presentation">
     <a href="#tab_shipment"  aria-controls="tab_shipment" role="tab" data-toggle="tab">
       <?php echo _l('wh_shipments'); ?>
     </a>
   </li>
   <li role="presentation">
     <a href="#tab_seriral_number"  aria-controls="tab_seriral_number" role="tab" data-toggle="tab">
       <?php echo _l('wh_serial_numbers'); ?>
     </a>
   </li>

   </ul>
 </div>
</div>
<div class="tab-content">
  <div role="tabpanel" class="tab-pane ptop10 active" id="tab_items">
    <div class="row">
      <div class="col-md-12">
        <h5 class="no-margin font-bold h5-color"><?php echo _l('_profit_rate_p') ?></h5>
        <hr class="hr-color">
      </div>
    </div>
    <div class="form-group">
      <div onchange="setting_rule_sale_price(this); return false" class="form-group" app-field-wrapper="warehouse_selling_price_rule_profif_ratio">
        <input type="number" min="0" max="100" id="warehouse_selling_price_rule_profif_ratio" name="warehouse_selling_price_rule_profif_ratio" class="form-control" value="<?php echo get_warehouse_option('warehouse_selling_price_rule_profif_ratio'); ?>">
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <h5 class="no-margin font-bold h5-color"><?php echo _l('rate') ?></h5>
        <hr class="hr-color">
      </div>
    </div>

      <div class="form-group">
        <div class="radio radio-primary radio-inline" >
          <input onchange="setting_profit_rate(this); return false" type="radio" id="y_opt_1_" name="profit_rate_by_purchase_price_sale" value="0" <?php if(get_warehouse_option('profit_rate_by_purchase_price_sale') == '0'){ echo "checked" ;}; ?>>
          <label for="y_opt_1_"><?php echo _l('warehouse_profit_rate_sale_price'); ?></label>

          <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('profit_rate_sale_price'); ?>"></i></a>
        </div>
      </div>

      <div class="form-group">
        <div class="radio radio-primary radio-inline" >
          <input onchange="setting_profit_rate(this); return false" type="radio" id="y_opt_2_" name="profit_rate_by_purchase_price_sale" value="1" <?php if(get_warehouse_option('profit_rate_by_purchase_price_sale') == '1'){ echo "checked" ;}; ?>>
          <label for="y_opt_2_"><?php echo _l('warehouse_profit_rate_purchase_price'); ?></label>

          <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('profit_rate_purchase_price'); ?>"></i></a>
        </div>
      </div>

    <div class="row">
        <div class="col-md-5">
          <label for="y_opt_2_"><?php echo _l('the_fractional_part'); ?></label>
        </div>
        <div class="col-md-2">
          <input onchange="setting_rules_for_rounding_prices(this); return false" type="number" min="0" max="100" step="1" id="warehouse_the_fractional_part" name="warehouse_the_fractional_part" class="form-control" value="<?php echo get_warehouse_option('warehouse_the_fractional_part'); ?>">
        </div>
    </div>

    <br/>
    <div class="row">
        <div class="col-md-5">
          <label for="y_opt_2_"><?php echo _l('integer_part'); ?></label>
        </div>
        <div class="col-md-2">
          <input onchange="setting_rules_for_rounding_prices(this); return false" type="number" min="0" max="100" step="1" id="warehouse_integer_part" name="warehouse_integer_part" class="form-control" value="<?php echo get_warehouse_option('warehouse_integer_part'); ?>">
        </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <h5 class="no-margin font-bold h5-color" ><?php echo _l('barcode_setting')?></h5>
        <hr class="hr-color" >
      </div>
    </div>
    <div class="row hide">
      <div class="col-md-12">
        <div class="form-group">
          <div class="checkbox checkbox-primary">
            <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="barcode_with_sku_code" name="purchase_setting[barcode_with_sku_code]" <?php if(get_warehouse_option('barcode_with_sku_code') == 1 ){ echo 'checked';} ?> value="barcode_with_sku_code">
            <label for="barcode_with_sku_code"><?php echo _l('barcode_equal_sku_code'); ?>
            <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('create_barcode_equal_sku_code_tooltip'); ?>"></i></a>
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <div class="checkbox checkbox-primary">
          <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="display_product_name_when_print_barcode" name="purchase_setting[display_product_name_when_print_barcode]" <?php if(get_warehouse_option('display_product_name_when_print_barcode') == 1 ){ echo 'checked';} ?> value="display_product_name_when_print_barcode">
          <label for="display_product_name_when_print_barcode"><?php echo _l('display_product_name_when_print_barcode'); ?>
          <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('display_only_the_first_50'); ?>"></i></a>
        </label>
      </div>
    </div>
  </div>
</div>
<div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <div class="checkbox checkbox-primary">
          <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="wh_show_price_when_print_barcode" name="purchase_setting[wh_show_price_when_print_barcode]" <?php if(get_warehouse_option('wh_show_price_when_print_barcode') == 1 ){ echo 'checked';} ?> value="wh_show_price_when_print_barcode">
          <label for="wh_show_price_when_print_barcode"><?php echo _l('wh_show_price_when_print_barcode'); ?>
          <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('wh_show_price_when_print_barcode_tooltip'); ?>"></i></a>
        </label>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <h5 class="no-margin font-bold h5-color"><?php echo _l('wh_on_total_items') ?></h5>
    <hr class="hr-color">
  </div>
</div>
<div class="form-group">
  <div onchange="setting_wh_on_total_items(this); return false" class="form-group" app-field-wrapper="wh_on_total_items">
    <input type="number" min="0" max="100" id="wh_on_total_items" name="wh_on_total_items" class="form-control" value="<?php echo get_warehouse_option('wh_on_total_items'); ?>">
  </div>
</div>

<?php if (is_admin()) { ?>
  <div class="row">
    <div class="col-md-12">
      <h5 class="no-margin font-bold h5-color" ><?php echo _l('button_update_do_not_update_inventory_numbers')?></h5>
      <hr class="hr-color" >
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <?php echo form_open_multipart(admin_url('warehouse/update_unchecked_inventory_numbers'), array('id'=>'update_unchecked_inventory_numbers')); ?>
      <div class="_buttons">
        <div class="row">
          <div class="col-md-12">
            <button type="button" class="btn btn-info intext-btn" onclick="update_unchecked_inventory_numbers(this); return false;" ><?php echo _l('update'); ?></button>
            <a href="#" class="input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('update_unchecked_inventory_numbers_title'); ?>"></i></a>
          </div>
        </div>

      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
<?php } ?>

    
  </div>
  <div role="tabpanel" class="tab-pane ptop10 " id="tab_receipt_delivery">
    <div class="row">
      <div class="col-md-12">
        <h5 class="no-margin font-bold h5-color" ><?php echo _l('wh_general')?></h5>
        <hr class="hr-color" >
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <div class="checkbox checkbox-primary">
            <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="revert_goods_receipt_goods_delivery" name="purchase_setting[revert_goods_receipt_goods_delivery]" <?php if(get_warehouse_option('revert_goods_receipt_goods_delivery') == 1 ){ echo 'checked';} ?> value="revert_goods_receipt_goods_delivery">
            <label for="revert_goods_receipt_goods_delivery"><?php echo _l('delete_goods_receipt_goods_delivery'); ?>
            <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('delete_goods_receipt_goods_delivery_tooltip'); ?>"></i></a>
          </label>
        </div>
      </div>
    </div>
  </div>

    <div class="row">
      <div class="col-md-12">
        <h5 class="no-margin font-bold h5-color" ><?php echo _l('export_method')?></h5>
        <hr class="hr-color" >
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <div class="form-group">
          <select name="method_fifo" id="method_fifo" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('Alert'); ?>">
            <option value="method_fifo"><?php echo _l('method_fifo') ; ?></option>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <h5 class="no-margin font-bold h5-color" ><?php echo _l('goods_receipt')?></h5>
        <hr class="hr-color" >
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="vendor"></label>
          <div class="checkbox checkbox-primary">
            <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="auto_create_goods_received" name="auto_create_goods_received" <?php if(get_warehouse_option('auto_create_goods_received') == 1 ){ echo 'checked';} ?> value="auto_create_goods_received">
            <label for="auto_create_goods_received"><?php echo _l('create_goods_received_note'); ?>
            <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('create_goods_received_note_tooltip'); ?>"></i></a>
          </label>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class=" form-group">
       <label for="goods_receipt_warehouse"><?php echo _l('goods_receipt_warehouse'); ?></label>
       <select onchange="goods_receipt_warehouse_change(this); return false" name="goods_receipt_warehouse" class="selectpicker" id="goods_receipt_warehouse" data-width="100%" data-none-selected-text="<?php echo _l('warehouse_name'); ?>"> 
        <option value=""></option>
        <?php foreach($warehouses as $wh){ ?>
          <option value="<?php echo new_html_entity_decode($wh['warehouse_id']); ?>" <?php if(get_warehouse_option('goods_receipt_warehouse') == $wh['warehouse_id']){ echo 'selected';} ?> ><?php echo new_html_entity_decode($wh['warehouse_code'].'_'.$wh['warehouse_name']); ?></option>
        <?php } ?>
      </select>
    </div>  
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <div class="checkbox checkbox-primary">
        <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="goods_receipt_required_po" name="purchase_setting[goods_receipt_required_po]" <?php if(get_warehouse_option('goods_receipt_required_po') == 1 ){ echo 'checked';} ?> value="goods_receipt_required_po">
        <label for="goods_receipt_required_po"><?php echo _l('goods_receipt_required_po'); ?></label>
      </div>
    </div>
  </div>
</div>

    <div class="row">
      <div class="col-md-12">
        <h5 class="no-margin font-bold h5-color" ><?php echo _l('stock_export')?></h5>
        <hr class="hr-color" >
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <div class="checkbox checkbox-primary">
            <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="auto_create_goods_delivery" name="purchase_setting[auto_create_goods_delivery]" <?php if(get_warehouse_option('auto_create_goods_delivery') == 1 ){ echo 'checked';} ?> value="auto_create_goods_delivery">
            <label for="auto_create_goods_delivery"><?php echo _l('create_goods_delivery_note'); ?>
            <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('create_goods_delivery_note_tooltip'); ?>"></i></a>
          </label>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <div class="checkbox checkbox-primary">
          <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="cancelled_invoice_reverse_inventory_delivery_voucher" name="purchase_setting[cancelled_invoice_reverse_inventory_delivery_voucher]" <?php if(get_warehouse_option('cancelled_invoice_reverse_inventory_delivery_voucher') == 1 ){ echo 'checked';} ?> value="cancelled_invoice_reverse_inventory_delivery_voucher">
          <label for="cancelled_invoice_reverse_inventory_delivery_voucher"><?php echo _l('cancelled_invoice_reverse_inventory_delivery_voucher_note'); ?>
          <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title=""></i></a>
        </label>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <div class="checkbox checkbox-primary">
        <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="uncancelled_invoice_create_inventory_delivery_voucher" name="purchase_setting[uncancelled_invoice_create_inventory_delivery_voucher]" <?php if(get_warehouse_option('uncancelled_invoice_create_inventory_delivery_voucher') == 1 ){ echo 'checked';} ?> value="uncancelled_invoice_create_inventory_delivery_voucher">
        <label for="uncancelled_invoice_create_inventory_delivery_voucher"><?php echo _l('uncancelled_invoice_create_inventory_delivery_voucher_note'); ?>
        <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title=""></i></a>
      </label>
    </div>
  </div>
</div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <div class="checkbox checkbox-primary">
        <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="goods_delivery_required_po" name="purchase_setting[goods_delivery_required_po]" <?php if(get_warehouse_option('goods_delivery_required_po') == 1 ){ echo 'checked';} ?> value="goods_delivery_required_po">
        <label for="goods_delivery_required_po"><?php echo _l('goods_delivery_required_po'); ?></label>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <div class="checkbox checkbox-primary">
        <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="notify_customer_when_change_delivery_status" name="purchase_setting[notify_customer_when_change_delivery_status]" <?php if(get_warehouse_option('notify_customer_when_change_delivery_status') == 1 ){ echo 'checked';} ?> value="notify_customer_when_change_delivery_status">
        <label for="notify_customer_when_change_delivery_status"><?php echo _l('notify_customer_when_change_delivery_status'); ?></label>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <div class="checkbox checkbox-primary">
        <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="wh_hide_shipping_fee" name="purchase_setting[wh_hide_shipping_fee]" <?php if(get_warehouse_option('wh_hide_shipping_fee') == 1 ){ echo 'checked';} ?> value="wh_hide_shipping_fee">
        <label for="wh_hide_shipping_fee"><?php echo _l('wh_hide_shipping_fee'); ?></label>
      </div>
    </div>
  </div>
</div>

  </div>
  

  <div role="tabpanel" class="tab-pane ptop10 " id="tab_order_return">
   <div class="row hide">
    <div class="col-md-12">
      <h5 class="no-margin font-bold h5-color"><?php echo _l('return_request_must_be_placed_within_X_days_after_the_delivery_date') ?></h5>
      <hr class="hr-color">
    </div>
  </div>
  <div class="form-group hide">
    <div onchange="setting_rule_sale_price(this); return false" class="form-group" app-field-wrapper="wh_return_request_within_x_day">
      <input type="number" min="0" max="100" id="wh_return_request_within_x_day" name="wh_return_request_within_x_day" class="form-control" value="<?php echo get_warehouse_option('wh_return_request_within_x_day'); ?>">
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <h5 class="no-margin font-bold h5-color" ><?php echo _l('warehouse_receive_return_order') ?></h5>
      <hr class="hr-color">
    </div>
  </div>
  <div class=" form-group">
    <select onchange="goods_receipt_warehouse_change(this); return false" name="warehouse_receive_return_order" class="selectpicker" id="warehouse_receive_return_order" data-width="100%" data-none-selected-text="<?php echo _l('warehouse_name'); ?>"> 
      <option value=""></option>
      <?php foreach($warehouses as $wh){ ?>
        <option value="<?php echo new_html_entity_decode($wh['warehouse_id']); ?>" <?php if(get_warehouse_option('warehouse_receive_return_order') == $wh['warehouse_id']){ echo 'selected';} ?> ><?php echo new_html_entity_decode($wh['warehouse_code'].'_'.$wh['warehouse_name']); ?></option>
      <?php } ?>
    </select>
  </div>  

  <div class="row hide">
    <div class="col-md-12">
      <h5 class="no-margin font-bold h5-color" data-toggle="tooltip" title="" data-original-title="<?php echo _l('fee_for_return_order_tooltip'); ?>"><?php echo _l('fee_for_return_order') ?></h5>
      <hr class="hr-color">
    </div>
  </div>
  <div class="form-group hide">
    <div onchange="setting_fee_for_return_order(this); return false" class="form-group" app-field-wrapper="wh_fee_for_return_order">
      <input type="number" min="0" max="100" id="wh_fee_for_return_order" name="wh_fee_for_return_order" class="form-control" value="<?php echo get_warehouse_option('wh_fee_for_return_order'); ?>" data-toggle="tooltip" title="" data-original-title="<?php echo _l('fee_for_return_order_tooltip'); ?>">
    </div>
  </div>

  <div class="row hide">
    <div class="col-md-12">
      <div class="form-group">
        <div class="checkbox checkbox-primary">
          <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="wh_refund_loyaty_point" name="purchase_setting[wh_refund_loyaty_point]" <?php if(get_warehouse_option('wh_refund_loyaty_point') == 1 ){ echo 'checked';} ?> value="wh_refund_loyaty_point">
          <label for="wh_refund_loyaty_point" data-toggle="tooltip" title="" data-original-title="<?php echo _l('refund_loyalty_point_tooltip'); ?>"><?php echo _l('refund_loyalty_point'); ?>
          </label>
        </div>
      </div>
    </div>
  </div>


  <div class="row">
    <div class="col-md-12">
      <h5 class="no-margin font-bold h5-color"><?php echo _l('return_policies_information') ?></h5>
      <hr class="hr-color">
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <?php echo render_textarea('wh_return_policies_information', '', get_warehouse_option('wh_return_policies_information'), array(), array(), '', 'tinymce'); ?>
    </div>
  </div>

  <?php if(has_permission('wh_setting', '', 'edit') || has_permission('wh_setting', '', 'create') ){ ?>
    <button type="button" class="btn btn-info pull-right submit_policies_information" onclick ="submit_policies_information(this); return false"><?php echo _l('submit'); ?></button>
  <?php } ?>

  </div>

  <div role="tabpanel" class="tab-pane ptop10 " id="tab_packing_lists">
    <div class="row">
      <div class="col-md-12">
        <h5 class="no-margin font-bold h5-color" ><?php echo _l('wh_custom_measurement_name')?></h5>
        <hr class="hr-color" >
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <div onchange="setting_custom_measurements_name(this); return false" class="form-group" >
            <label><small class="req text-danger">* </small><?php echo _l('wh_custom_name_for_meter'); ?></label>
            <input id="custom_name_for_meter" name="custom_name_for_meter" data-name="custom_name_for_meter" class="form-control" value="<?php echo get_option('custom_name_for_meter'); ?>">
          </div>
        </div>

      </div>
      <div class="col-md-6">
        <div class="form-group">
          <div onchange="setting_custom_measurements_name(this); return false" class="form-group" >
            <label><small class="req text-danger">* </small><?php echo _l('wh_custom_name_for_kg'); ?></label>
            <input id="custom_name_for_kg" name="custom_name_for_kg" data-name="custom_name_for_kg" class="form-control" value="<?php echo get_option('custom_name_for_kg'); ?>">
          </div>
        </div>
      </div>
      <div class="col-md-6">
         <div class="form-group">
          <div onchange="setting_custom_measurements_name(this); return false" class="form-group" >
            <label><small class="req text-danger">* </small><?php echo _l('wh_custom_name_for_m3'); ?></label>
            <input id="custom_name_for_m3" name="custom_name_for_m3" data-name="custom_name_for_m3" class="form-control" value="<?php echo get_option('custom_name_for_m3'); ?>">
          </div>
        </div>

      </div>
      
    </div>
  </div>

  <div role="tabpanel" class="tab-pane ptop10 " id="tab_pdf">
    <div class="row">
      <div class="col-md-12">
        <h5 class="no-margin font-bold h5-color" ><?php echo _l('wh_general')?></h5>
        <hr class="hr-color" >
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <div class="checkbox checkbox-primary">
            <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="goods_delivery_pdf_display_warehouse_lotnumber_bottom_infor" name="purchase_setting[goods_delivery_pdf_display_warehouse_lotnumber_bottom_infor]" <?php if(get_warehouse_option('goods_delivery_pdf_display_warehouse_lotnumber_bottom_infor') == 1 ){ echo 'checked';} ?> value="goods_delivery_pdf_display_warehouse_lotnumber_bottom_infor">
            <label for="goods_delivery_pdf_display_warehouse_lotnumber_bottom_infor"><?php echo _l('goods_delivery_pdf_display_warehouse_lotnumber_bottom_infor'); ?>
          </label>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <div class="checkbox checkbox-primary">
          <input onchange="show_item_cf_on_pdf(this); return false" type="checkbox" id="show_item_cf_on_pdf" name="purchase_setting[show_item_cf_on_pdf]" <?php if(get_option('show_item_cf_on_pdf') == 1 ){ echo 'checked';} ?> value="show_item_cf_on_pdf">
          <label for="show_item_cf_on_pdf"><?php echo _l('show_item_cf_on_pdf'); ?>
        </label>
      </div>
    </div>
  </div>
</div>

    <div class="row">
      <div class="col-md-12">
        <h5 class="no-margin font-bold h5-color" ><?php echo _l('stock_export')?></h5>
        <hr class="hr-color" >
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <div class="checkbox checkbox-primary">
            <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="goods_delivery_pdf_display" name="purchase_setting[goods_delivery_pdf_display]" <?php if(get_warehouse_option('goods_delivery_pdf_display') == 1 ){ echo 'checked';} ?> value="goods_delivery_pdf_display">
            <label for="goods_delivery_pdf_display"><?php echo _l('goods_delivery_pdf_display'); ?>
          </label>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <div class="checkbox checkbox-primary">
          <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="goods_delivery_pdf_display_outstanding" name="purchase_setting[goods_delivery_pdf_display_outstanding]" <?php if(get_warehouse_option('goods_delivery_pdf_display_outstanding') == 1 ){ echo 'checked';} ?> value="goods_delivery_pdf_display_outstanding">
          <label for="goods_delivery_pdf_display_outstanding"><?php echo _l('goods_delivery_pdf_display_outstanding'); ?>
        </label>
      </div>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <div class="checkbox checkbox-primary">
          <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="wh_shortened_form_pdf" name="purchase_setting[wh_shortened_form_pdf]" <?php if(get_warehouse_option('wh_shortened_form_pdf') == 1 ){ echo 'checked';} ?> value="wh_shortened_form_pdf">
          <label for="wh_shortened_form_pdf"><?php echo _l('wh_shortened_form_pdf'); ?>
        </label>
      </div>
    </div>
  </div>
</div>

    <div class="row">
      <div class="col-md-12">
        <h5 class="no-margin font-bold h5-color" ><?php echo _l('wh_packing_lists')?></h5>
        <hr class="hr-color" >
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <div class="checkbox checkbox-primary">
            <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="packing_list_pdf_display_rate" name="packing_list_pdf_display_rate" <?php if(get_warehouse_option('packing_list_pdf_display_rate') == 1 ){ echo 'checked';} ?> value="packing_list_pdf_display_rate">
            <label for="packing_list_pdf_display_rate"><?php echo _l('packing_list_pdf_display_rate'); ?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <div class="checkbox checkbox-primary">
            <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="packing_list_pdf_display_tax" name="packing_list_pdf_display_tax" <?php if(get_warehouse_option('packing_list_pdf_display_tax') == 1 ){ echo 'checked';} ?> value="packing_list_pdf_display_tax">
            <label for="packing_list_pdf_display_tax"><?php echo _l('packing_list_pdf_display_tax'); ?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <div class="checkbox checkbox-primary">
            <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="packing_list_pdf_display_subtotal" name="packing_list_pdf_display_subtotal" <?php if(get_warehouse_option('packing_list_pdf_display_subtotal') == 1 ){ echo 'checked';} ?> value="packing_list_pdf_display_subtotal">
            <label for="packing_list_pdf_display_subtotal"><?php echo _l('packing_list_pdf_display_subtotal'); ?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <div class="checkbox checkbox-primary">
            <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="packing_list_pdf_display_discount_percent" name="packing_list_pdf_display_discount_percent" <?php if(get_warehouse_option('packing_list_pdf_display_discount_percent') == 1 ){ echo 'checked';} ?> value="packing_list_pdf_display_discount_percent">
            <label for="packing_list_pdf_display_discount_percent"><?php echo _l('packing_list_pdf_display_discount_percent'); ?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <div class="checkbox checkbox-primary">
            <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="packing_list_pdf_display_discount_amount" name="packing_list_pdf_display_discount_amount" <?php if(get_warehouse_option('packing_list_pdf_display_discount_amount') == 1 ){ echo 'checked';} ?> value="packing_list_pdf_display_discount_amount">
            <label for="packing_list_pdf_display_discount_amount"><?php echo _l('packing_list_pdf_display_discount_amount'); ?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <div class="checkbox checkbox-primary">
            <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="packing_list_pdf_display_totalpayment" name="packing_list_pdf_display_totalpayment" <?php if(get_warehouse_option('packing_list_pdf_display_totalpayment') == 1 ){ echo 'checked';} ?> value="packing_list_pdf_display_totalpayment">
            <label for="packing_list_pdf_display_totalpayment"><?php echo _l('packing_list_pdf_display_totalpayment'); ?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <div class="checkbox checkbox-primary">
            <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="packing_list_pdf_display_summary" name="packing_list_pdf_display_summary" <?php if(get_warehouse_option('packing_list_pdf_display_summary') == 1 ){ echo 'checked';} ?> value="packing_list_pdf_display_summary">
            <label for="packing_list_pdf_display_summary"><?php echo _l('packing_list_pdf_display_summary'); ?>
          </label>
        </div>
      </div>
    </div>
  </div>
  



  </div>

  <div role="tabpanel" class="tab-pane ptop10 " id="tab_shipment">
    <div class="row">
      <div class="col-md-12">
        <h5 class="no-margin font-bold h5-color" ><?php echo _l('wh_general')?></h5>
        <hr class="hr-color" >
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <div class="checkbox checkbox-primary">
            <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="wh_display_shipment_on_client_portal" name="purchase_setting[wh_display_shipment_on_client_portal]" <?php if(get_warehouse_option('wh_display_shipment_on_client_portal') == 1 ){ echo 'checked';} ?> value="wh_display_shipment_on_client_portal">
            <label for="wh_display_shipment_on_client_portal"><?php echo _l('wh_display_shipment_on_client_portal'); ?>
          </label>
        </div>
      </div>
    </div>
  </div>
</div>

<div role="tabpanel" class="tab-pane ptop10 " id="tab_seriral_number">
  <div class="row">
    <div class="col-md-12">
      <h5 class="no-margin font-bold h5-color" ><?php echo _l('wh_general')?></h5>
      <hr class="hr-color" >
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <div class="checkbox checkbox-primary">
          <input onchange="auto_create_change_setting(this); return false" type="checkbox" id="wh_products_by_serial" name="purchase_setting[wh_products_by_serial]" <?php if(get_warehouse_option('wh_products_by_serial') == 1 ){ echo 'checked';} ?> value="wh_products_by_serial">
          <label for="wh_products_by_serial"><?php echo _l('wh_products_by_serial'); ?>
        </label>
      </div>
    </div>
  </div>
</div>
</div>

</div>


<div class="clearfix"></div>

<?php require 'modules/warehouse/assets/js/rule_sale_price_js.php';?>
</body>
</html>


