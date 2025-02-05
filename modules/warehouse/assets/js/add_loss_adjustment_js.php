<script>
  var lastAddedItemKey = null;
(function($) {
"use strict";
  // Maybe items ajax search
  init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'warehouse/wh_commodity_code_search');
  wh_calculate_total();

})(jQuery); 


//version2

(function($) {
  "use strict"; 
// Add item to preview from the dropdown for invoices estimates
$("body").on('change', 'select[name="item_select"]', function () {
  if ($('select[name="warehouses"]').val() === '' && $(this).val() != 0) {
    alert('You need to select warehouse');
    $('html,body').animate({
      scrollTop: 0
    }, 'slow');
    $('#wrapper').highlight($('label[for="warehouses"]').text());
    setTimeout(function () {
      $('#wrapper').unhighlight();
    }, 3000);
    return false;
  }
  if ($(this).valid() === true) {
    var itemid = $(this).selectpicker('val');
    if (itemid != '') {
      wh_add_item_to_preview(itemid);
    }
  }

});

// Recaulciate total on these changes
$("body").on('change', 'select.taxes', function () {
  wh_calculate_total();
});

$('.save_detail').on('click', function() {
  submit_form(false);
});

$('input[name="lot_number"]').on('change', function() {
  "use strict"; 

  var commodity_id = $('.main input[name="items"]').val();
  var warehouse_id = $('select[name="warehouses"]').val();
  var lot_number = $('.main input[name="lot_number"]').val();
  var expiry_date = $('.main input[name="expiry_date"]').val();
  
  var available_quantity = loss_adjustment_get_available_quantity(warehouse_id, commodity_id, lot_number, expiry_date);
  $('.main input[name="current_number"]').val(parseFloat(available_quantity));
});

$('input[name="expiry_date"]').on('change', function() {
  "use strict"; 

  var commodity_id = $('.main input[name="items"]').val();
  var warehouse_id = $('select[name="warehouses"]').val();
  var lot_number = $('.main input[name="lot_number"]').val();
  var expiry_date = $('.main input[name="expiry_date"]').val();
  
  var available_quantity = loss_adjustment_get_available_quantity(warehouse_id, commodity_id, lot_number, expiry_date);
  $('.main input[name="current_number"]').val(parseFloat(available_quantity));

});


$('input[name="updates_number"]').on('change', function() {
  "use strict"; 

  var current_number = $('.main input[name="current_number"]').val();
  var updates_number = $('.main input[name="updates_number"]').val();
  var type = $('select[name="type"]').val();

  if(type == 'loss'){
    if(parseFloat(current_number) < parseFloat(updates_number)){
      alert_float('warning', '<?php echo _l('Please_enter_the_actual_quantity_smaller_than_the_quantity_in_stock') ?>', 4000);
    }
  }else if(type == 'adjustment'){
    if(parseFloat(current_number) > parseFloat(updates_number)){
      alert_float('warning', '<?php echo _l('Please_enter_the_actual_quantity_larger_than_the_quantity_in_stock') ?>', 4000);
    }
  }else{
      alert_float('warning', '<?php echo _l('Please_select_lost_adjustment_type') ?>', 4000);
  }
});


})(jQuery);

// Add item to preview
function wh_add_item_to_preview(id) {
  "use strict"; 

  var warehouse_id = $('select[name="warehouses"]').val();
  requestGetJSON('warehouse/get_item_by_id/' + id +'/'+1+'/'+warehouse_id).done(function (response) {
    clear_item_preview_values();

    $('.main input[name="items"]').val(response.itemid);
    $('.main textarea[name="commodity_name"]').val(response.code_description);
    $('.main input[name="unit_name"]').val(response.unit_name);
    $('.main input[name="unit"]').val(response.unit_id);
    $('.main input[name="current_number"]').val(response.available_quantity);
    $('.main input[name="updates_number"]').val('');

    var taxSelectedArray = [];
    if (response.taxname && response.taxrate) {
      taxSelectedArray.push(response.taxname + '|' + response.taxrate);
    }
    if (response.taxname_2 && response.taxrate_2) {
      taxSelectedArray.push(response.taxname_2 + '|' + response.taxrate_2);
    }

    $('.main select.taxes').selectpicker('val', taxSelectedArray);

    var $currency = $("body").find('.accounting-template select[name="currency"]');
    var baseCurency = $currency.attr('data-base');
    var selectedCurrency = $currency.find('option:selected').val();
    var $rateInputPreview = $('.main input[name="rate"]');

    if (baseCurency == selectedCurrency) {
      $rateInputPreview.val(response.rate);
    } else {
      var itemCurrencyRate = response['rate_currency_' + selectedCurrency];
      if (!itemCurrencyRate || parseFloat(itemCurrencyRate) === 0) {
        $rateInputPreview.val(response.rate);
      } else {
        $rateInputPreview.val(itemCurrencyRate);
      }
    }

    $(document).trigger({
      type: "item-added-to-preview",
      item: response,
      item_type: 'item',
    });
  });
}


function wh_add_item_to_table(data, itemid) {
  "use strict"; 

  data = typeof (data) == 'undefined' || data == 'undefined' ? wh_get_item_preview_values() : data;

  if (data.available_quantity == "" || data.quantities == "" || data.commodity_code == "" ) {
    return;
  }
  var type = $('select[name="type"]').val();
  if(type == 'loss'){
    if(parseFloat(data.available_quantity) < parseFloat(data.quantities)){
      alert_float('warning', '<?php echo _l('Please_enter_the_actual_quantity_smaller_than_the_quantity_in_stock') ?>', 4000);
      return;
    }
  }else if(type == 'adjustment'){
    if(parseFloat(data.available_quantity) > parseFloat(data.quantities)){
      alert_float('warning', '<?php echo _l('Please_enter_the_actual_quantity_larger_than_the_quantity_in_stock') ?>', 4000);
      return;
    }
  }else{
    if(type == ''){
      alert_float('warning', '<?php echo _l('Please_select_lost_adjustment_type') ?>', 4000);
      return;
    }
  }

  var table_row = '';
  var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.invoice-items-table tbody .item').length + 1;
  lastAddedItemKey = item_key;
  $("body").append('<div class="dt-loader"></div>');
  wh_get_item_row_template('newitems[' + item_key + ']',data.commodity_name, data.lot_number, data.expiry_date, data.available_quantity, data.quantities, data.unit_name, data.commodity_code, data.unit_id, itemid).done(function(output){
    table_row += output;

    $('.invoice-item table.invoice-items-table.items tbody').append(table_row);

    setTimeout(function () {
      wh_calculate_total();
    }, 15);
    init_selectpicker();
    init_datepicker();
    wh_reorder_items('.invoice-item');
    wh_clear_item_preview_values('.invoice-item');
    $('body').find('#items-warning').remove();
    $("body").find('.dt-loader').remove();
    $('#item_select').selectpicker('val', '');

    <?php if(get_option('wh_products_by_serial')){ ?>
    // open serial modal
    if(type == 'loss'){
      var quantity = parseFloat(data.available_quantity) - parseFloat(data.quantities);
      loss_fill_multiple_serial_number_modal(quantity, 'newitems[' + item_key + ']');
    }else if(type == 'adjustment'){
      var quantity =  parseFloat(data.quantities) - parseFloat(data.available_quantity);
      adjustment_fill_multiple_serial_number_modal(quantity, 'newitems[' + item_key + ']');
    }
  <?php }else{ ?>

  <?php } ?>

    return true;
  });
  return false;
}

function wh_get_item_preview_values() {
  "use strict"; 

  var response = {};
  response.commodity_name = $('.invoice-item .main textarea[name="commodity_name"]').val();
  response.lot_number = $('.invoice-item .main input[name="lot_number"]').val();
  response.expiry_date = $('.invoice-item .main input[name="expiry_date"]').val();
  response.available_quantity = $('.invoice-item .main input[name="current_number"]').val();
  response.quantities = $('.invoice-item .main input[name="updates_number"]').val();
  response.unit_name = $('.invoice-item .main input[name="unit_name"]').val();
  response.commodity_code = $('.invoice-item .main input[name="items"]').val();
  response.unit_id = $('.invoice-item .main input[name="unit"]').val();

  return response;
}

function wh_clear_item_preview_values(parent) {
  "use strict"; 

  var previewArea = $(parent + ' .main');
  previewArea.find('input').val('');
  previewArea.find('textarea').val('');
  previewArea.find('select').val('').selectpicker('refresh');
}

function wh_get_item_row_template(name, commodity_name, lot_number, expiry_date, available_quantity, quantities, unit_name, commodity_code, unit_id, item_key)  {
  "use strict"; 

  jQuery.ajaxSetup({
    async: false
  });

  var d = $.post(admin_url + 'warehouse/get_loss_adjustment_row_template', {
    name: name,
    commodity_name : commodity_name,
    expiry_date : expiry_date,
    lot_number : lot_number,
    available_quantity : available_quantity,
    quantities : quantities,
    unit_name : unit_name,
    commodity_code : commodity_code,
    unit_id : unit_id,
    item_key : item_key
  });
  jQuery.ajaxSetup({
    async: true
  });
  return d;
}

function wh_delete_item(row, itemid,parent) {
  "use strict"; 

  $(row).parents('tr').addClass('animated fadeOut', function () {
    setTimeout(function () {
      $(row).parents('tr').remove();
      wh_calculate_total();
    }, 50);
  });
  if (itemid && $('input[name="isedit"]').length > 0) {
    $(parent+' #removed-items').append(hidden_input('removed_items[]', itemid));
  }
}

function wh_reorder_items(parent) {
  "use strict"; 

  var rows = $(parent + ' .table.has-calculations tbody tr.item');
  var i = 1;
  $.each(rows, function () {
    $(this).find('input.order').val(i);
    i++;
  });
}

function wh_calculate_total(){
  "use strict";

  if ($('body').hasClass('no-calculate-total')) {
    return false;
  }

  var calculated_tax,
    taxrate,
    item_taxes,
    row,
    _amount,
    _tax_name,
    taxes = {},
    taxes_rows = [],
    subtotal = 0,
    total = 0,
    total_tax_money = 0,
    quantity = 1,
    total_discount_calculated = 0,
    rows = $('.table.has-calculations tbody tr.item'),
    subtotal_area = $('#subtotal'),
    discount_area = $('#discount_area'),
    adjustment = $('input[name="adjustment"]').val(),
    // discount_percent = $('input[name="discount_percent"]').val(),
    discount_percent = 'before_tax',
    discount_fixed = $('input[name="discount_total"]').val(),
    discount_total_type = $('.discount-total-type.selected'),
    discount_type = $('select[name="discount_type"]').val(),
    total_row =  0;

  $('.wh-tax-area').remove();

    $.each(rows, function () {
      total_row++;

    quantity = $(this).find('[data-quantity]').val();
    if (quantity === '') {
      quantity = 1;
      $(this).find('[data-quantity]').val(1);
    }

    _amount = accounting.toFixed($(this).find('td.rate input').val() * quantity, app.options.decimal_places);
    _amount = parseFloat(_amount);

    $(this).find('td.amount').html(format_money(_amount, true));

    subtotal += _amount;
    row = $(this);
    item_taxes = $(this).find('select.taxes').val();
    $(this).find('td.into_money input').val($(this).find('td.rate input').val() * quantity);

    if (item_taxes) {
      $.each(item_taxes, function (i, taxname) {
        taxrate = row.find('select.taxes [value="' + taxname + '"]').data('taxrate');
        calculated_tax = (_amount / 100 * taxrate);
        if (!taxes.hasOwnProperty(taxname)) {
          if (taxrate != 0) {
            _tax_name = taxname.split('|');
            var tax_row = '<tr class="wh-tax-area"><td>' + _tax_name[0] + '(' + taxrate + '%)</td><td id="tax_id_' + slugify(taxname) + '"></td></tr>';
            $(subtotal_area).after(tax_row);
            taxes[taxname] = calculated_tax;
          }
        } else {
                    // Increment total from this tax
                    taxes[taxname] = taxes[taxname] += calculated_tax;
                }
            });
    }
  });

  // Discount by percent
  if ((discount_percent !== '' && discount_percent != 0) && discount_type == 'before_tax' && discount_total_type.hasClass('discount-type-percent')) {
    total_discount_calculated = (subtotal * discount_percent) / 100;
  } else if ((discount_fixed !== '' && discount_fixed != 0) && discount_type == 'before_tax' && discount_total_type.hasClass('discount-type-fixed')) {
    total_discount_calculated = discount_fixed;
  }

  $.each(taxes, function (taxname, total_tax) {
    if ((discount_percent !== '' && discount_percent != 0) && discount_type == 'before_tax' && discount_total_type.hasClass('discount-type-percent')) {
      total_tax_calculated = (total_tax * discount_percent) / 100;
      total_tax = (total_tax - total_tax_calculated);
    } else if ((discount_fixed !== '' && discount_fixed != 0) && discount_type == 'before_tax' && discount_total_type.hasClass('discount-type-fixed')) {
      var t = (discount_fixed / subtotal) * 100;
      total_tax = (total_tax - (total_tax * t) / 100);
    }

    total += total_tax;
    total_tax_money += total_tax;
    total_tax = format_money(total_tax);
    $('#tax_id_' + slugify(taxname)).html(total_tax);
  });

  total = (total + subtotal);

  // Discount by percent
  if ((discount_percent !== '' && discount_percent != 0) && discount_type == 'after_tax' && discount_total_type.hasClass('discount-type-percent')) {
    total_discount_calculated = (total * discount_percent) / 100;
  } else if ((discount_fixed !== '' && discount_fixed != 0) && discount_type == 'after_tax' && discount_total_type.hasClass('discount-type-fixed')) {
    total_discount_calculated = discount_fixed;
  }

  total = total - total_discount_calculated;
  adjustment = parseFloat(adjustment);

  // Check if adjustment not empty
  if (!isNaN(adjustment)) {
    total = total + adjustment;
  }

  var discount_html = '-' + format_money(total_discount_calculated);
    $('input[name="discount_total"]').val(accounting.toFixed(total_discount_calculated, app.options.decimal_places));

  // Append, format to html and display
  $('.discount-total').html(discount_html);
  $('.adjustment').html(format_money(adjustment));

  $('.wh-total').html(format_money(total) + hidden_input('total_amount', accounting.toFixed(total, app.options.decimal_places)));
  if(total_row == 0){
    $('#warehouses').attr("disabled", false); 
  }else{
    $('#warehouses').attr("disabled", true); 
  }
  $('#warehouses').selectpicker('refresh')

  $(document).trigger('wh-loss-adjustment-total-calculated');

}



function submit_form(save_and_send_request) {
  "use strict"; 

  wh_calculate_total();

  var $itemsTable = $('.invoice-items-table');
  var $previewItem = $itemsTable.find('.main');

  if ( $itemsTable.length && $itemsTable.find('.item').length === 0) {
    alert_float('warning', '<?php echo _l('wh_enter_at_least_one_product'); ?>', 3000);
    return false;
  }

  var rows = $('.table.has-calculations tbody tr.item');
  var check_quantity = true,
      check_available_quantity = true,
      check_the_same_available_quantity = true;

  $.each(rows, function () {
    var available_quantity_value = $(this).find('td.available_quantity input').val();
    var quantity_value = $(this).find('td.quantities input').val();

    
    if(parseFloat(available_quantity_value) == 0){
      check_available_quantity = false;
    }
    if(parseFloat(available_quantity_value) == parseFloat(quantity_value) ){
      check_the_same_available_quantity = false;
    }

  })

  if(check_available_quantity == true && check_the_same_available_quantity == true){
    // Remove the disabled attribute from the disabled fields becuase if they are disabled won't be sent with the request.
    $('select[name="warehouses"]').prop('disabled', false);
    // Add disabled to submit buttons
    $(this).find('.save_detail').prop('disabled', true);
    $('#pur_order-form').submit();
  }else{
    if(check_available_quantity == false){
      alert_float('warning', '<?php echo _l('No_adjustment_is_allowed_when_the_product_has_an_Available_quantity_of_0') ?>');
    }else if(check_the_same_available_quantity == false){
      alert_float('warning', '<?php echo _l('Please_choose_Stock_quantity_different_from_Available_quantity') ?>');
    }
  }

  return true;
}

function la_get_available_quantity(commodity_code_name, lot_number_name, expiry_date_name, name_available_quantity){
  "use strict"; 

  var warehouse_id = $('select[name="warehouses"]').val();
  var commodity_id = $('input[name="'+commodity_code_name+'"]').val();
  var lot_number = $('input[name="'+lot_number_name+'"]').val();
  var expiry_date = $('input[name="'+expiry_date_name+'"]').val();

  var available_quantity = loss_adjustment_get_available_quantity(warehouse_id, commodity_id, lot_number, expiry_date);
   $('input[name="'+name_available_quantity+'"]').val(parseFloat(available_quantity));

}

function loss_adjustment_get_available_quantity(warehouse_id, commodity_id, lot_number, expiry_date) {
  var data ={};
  data.warehouse_id = warehouse_id;
  data.commodity_id = commodity_id;
  data.lot_number = lot_number;
  data.expiry_date = expiry_date;
  var available_quantity = 0;
    jQuery.ajaxSetup({
    async: false
  });
  
  $.post(admin_url + 'warehouse/quantity_inventory',data).done(function(response){
    response = JSON.parse(response);
    available_quantity = parseFloat(response.value);
    
  });
  jQuery.ajaxSetup({
    async: true
  });
  return available_quantity;
}

/*scanner barcode*/
$(document).ready(function() {
  var pressed = false;
  var chars = [];
  $(window).keypress(function(e) {
    if (e.key == '%') {
      pressed = true;
    }
    chars.push(String.fromCharCode(e.which));
    if (pressed == false) {
      setTimeout(function() {
        if (chars.length >= 8) {
          var barcode = chars.join('');
          requestGetJSON('warehouse/wh_get_item_by_barcode/' + barcode).done(function (response) {
            if(response.status == true || response.status == 'true'){
              wh_add_item_to_preview(response.id);
              alert_float('success', response.message);
            }else{
              alert_float('warning', '<?php echo _l('no_matching_products_found') ?>');
            }
          });

        }
        chars = [];
        pressed = false;
      }, 200);
    }
    pressed = true;
  });
});

function loss_fill_multiple_serial_number_modal(quantity, prefix_name) {
  "use strict";

  if( quantity > 0){
    $("#delete_modal_wrapper").load("<?php echo admin_url('warehouse/warehouse/loss_fill_multiple_serial_number_modal'); ?>", {
      slug: 'add',
      quantity:quantity,
      prefix_name:prefix_name,
    }, function() {
      $("body").find('#deleteSerialNumberModal').modal({ show: true, backdrop: 'static' });
    });
  }else{
    alert_float('warning', "<?php echo _l('please_choose_quantity_more_than_0') ?>");
  }

  init_selectpicker();
  $(".selectpicker").selectpicker('refresh');
}

function loss_wh_view_serial_number(name_available_quantity, name_quantities, serial_input, prefix_name){
  "use strict";

  var serial_input_value = $('input[name="'+serial_input+'"]').val();
  if(serial_input_value == ''){
    var quantity = $('input[name="'+name_quantities+'"]').val();
    var available_quantity = $('input[name="'+name_available_quantity+'"]').val();
    var _quantity = parseFloat(available_quantity) - parseFloat(quantity);

    loss_fill_multiple_serial_number_modal(parseInt(_quantity), prefix_name);
  }else{

    $("#delete_modal_wrapper").load("<?php echo admin_url('warehouse/warehouse/loss_fill_multiple_serial_number_modal'); ?>", {
      slug: 'edit',
      serial_input_value:serial_input_value,
      prefix_name:prefix_name,

    }, function() {
      $("body").find('#deleteSerialNumberModal').modal({ show: true, backdrop: 'static' });
    });
  }

}

function adjustment_fill_multiple_serial_number_modal(quantity, prefix_name) {
  "use strict";

  if( quantity > 0){
    $("#add_modal_wrapper").load("<?php echo admin_url('warehouse/warehouse/adjustment_fill_multiple_serial_number_modal'); ?>", {
      slug: 'add',
      quantity:quantity,
      prefix_name:prefix_name,
    }, function() {
      $("body").find('#addSerialNumberModal').modal({ show: true, backdrop: 'static' });
    });
  }else{
    alert_float('warning', "<?php echo _l('please_choose_quantity_more_than_0') ?>");
  }

  init_selectpicker();
  $(".selectpicker").selectpicker('refresh');
}

function adjustment_wh_view_serial_number(name_available_quantity, name_quantities, serial_input, prefix_name){
  "use strict";

  var serial_input_value = $('input[name="'+serial_input+'"]').val();
  if(serial_input_value == ''){
    var quantity = $('input[name="'+name_quantities+'"]').val();
    var available_quantity = $('input[name="'+name_available_quantity+'"]').val();
    var _quantity = parseFloat(quantity) - parseFloat(available_quantity);
    
    adjustment_fill_multiple_serial_number_modal(parseInt(_quantity), prefix_name);
  }else{

    $("#add_modal_wrapper").load("<?php echo admin_url('warehouse/warehouse/adjustment_fill_multiple_serial_number_modal'); ?>", {
      slug: 'edit',
      serial_input_value:serial_input_value,
      prefix_name:prefix_name,

    }, function() {
      $("body").find('#addSerialNumberModal').modal({ show: true, backdrop: 'static' });
    });
  }

}

</script>