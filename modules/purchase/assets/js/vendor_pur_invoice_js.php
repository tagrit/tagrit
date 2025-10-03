<script>
(function($) {
  "use strict";
  $("input[data-type='currency']").on({
    keyup: function() {  
    
      formatCurrency($(this));
    },
    blur: function() { 
      formatCurrency($(this), "blur");
    }
  });


  $('body').on('change', '#unlimited_cycles', function () {
        $(this).parents('.recurring-cycles').find('#cycles').prop('disabled', $(this).prop('checked'));
  });

  // For expenses and recurring tasks
  $("body").on('change', '[name="repeat_every"], [name="recurring"]', function () {
      var val = $(this).val();
      val == 'custom' ? $('.recurring_custom').removeClass('hide') : $('.recurring_custom').addClass('hide');
      if (val !== '' && val != 0) {
          $("body").find('#cycles_wrapper').removeClass('hide');
      } else {
          $("body").find('#cycles_wrapper').addClass('hide');
          $("body").find('#cycles_wrapper #cycles').val(0);
          $('#unlimited_cycles').prop('checked', true).change();
      }
  });

  init_pi_currency();

  <?php if(get_purchase_option('item_by_vendor') != 1){ ?>
    init_ajax_search('items','#item_select.ajax-search',undefined,site_url+'purchase/vendors_portal/pur_commodity_code_search');
  <?php } ?>

  $("body").on('change', 'select[name="item_select"]', function () {
    var itemid = $(this).selectpicker('val');
    if (itemid != '') {
      pur_add_item_to_preview(itemid);
    }
  });

  $("body").on('change', 'select.taxes', function () {
    pur_calculate_total();
  });

  $("body").on('change', 'select[name="currency"]', function () {
    var currency_id = $(this).val();
    if(currency_id != ''){
      $.post(site_url + 'purchase/vendors_portal/get_currency_rate/'+currency_id).done(function(response){
        response = JSON.parse(response);
        if(response.currency_rate != 1){
          $('#currency_rate_div').removeClass('hide');

          $('input[name="currency_rate"]').val(response.currency_rate).change();

          $('#convert_str').html(response.convert_str);
          $('.th_currency').html(response.currency_name);
        }else{
          $('input[name="currency_rate"]').val(response.currency_rate).change();
          $('#currency_rate_div').addClass('hide');
          $('#convert_str').html(response.convert_str);
          $('.th_currency').html(response.currency_name);

        }

      });
    }else{
      alert_float('warning', "<?php echo _l('please_select_currency'); ?>" )
    }

    init_pi_currency();
  });

$("input[name='currency_rate']").on('change', function () { 
    var currency_rate = $(this).val();
    var rows = $('.table.has-calculations tbody tr.item');
    $.each(rows, function () { 
      var old_price = $(this).find('td.rate input[name="og_price"]').val();
      var new_price = currency_rate*old_price;
      $(this).find('td.rate input[type="number"]').val(accounting.toFixed(new_price, 2)).change();

    });
});

$("body").on("change", 'select[name="discount_type"]', function () {
        // if discount_type == ''
        if ($(this).val() === "") {
          $('input[name="order_discount"]').val(0);
        }
        // Recalculate the total
        pur_calculate_total();
      });

$("body").on('click', '.transaction-submit', function () { 
  var that = $(this);
  var form = that.parents('form._transaction_form');

  form.submit();
});

})(jQuery);

var lastAddedItemKey = null;

function formatNumber(n) {
  "use strict"; 
  // format number 1000000 to 1,234,567
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, "<?php echo get_option('thousand_separator'); ?>");
}
function formatCurrency(input, blur) {
  "use strict"; 
  var input_val = input.val();
  if (input_val === "") { return; }
  var original_len = input_val.length;
  var caret_pos = input.prop("selectionStart");
  if (input_val.indexOf("<?php echo get_option('decimal_separator'); ?>") >= 0) {
    var decimal_pos = input_val.indexOf("<?php echo get_option('decimal_separator'); ?>");
    var left_side = input_val.substring(0, decimal_pos);
    var right_side = input_val.substring(decimal_pos);
    left_side = formatNumber(left_side);
    right_side = formatNumber(right_side);
    right_side = right_side.substring(0, 2);
    input_val = left_side + "<?php echo get_option('decimal_separator'); ?>" + right_side;

  } else {
    input_val = formatNumber(input_val);
    input_val = input_val;

  }
  input.val(input_val);
  var updated_len = input_val.length;
  caret_pos = updated_len - original_len + caret_pos;
  input[0].setSelectionRange(caret_pos, caret_pos);
}


function numberWithCommas(x) {
  "use strict";
    x = x.toString().replace('.', "<?php echo get_option('decimal_separator'); ?>");

    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "<?php echo get_option('thousand_separator'); ?>");
}

function removeCommas(str) {
  "use strict";
  var decimal_separator = '<?php echo get_option('decimal_separator'); ?>';

  if(decimal_separator == ','){
    str = str.split('.').join('');
    return(str.replace(',','.'));
  }else{
    return(str.replace(/,/g,''));
  }
}
 
function contract_change(el){
  "use strict";
  if(el.value != ''){
    $.post(site_url + 'purchase/vendors_portal/contract_change/'+el.value).done(function(response) {
      response = JSON.parse(response);
      $('select[name="pur_order"]').val(response.purchase_order).change();
    });
  }
}


function pur_order_change(el){
  "use strict";
  if(el.value != ''){
    $.post(site_url + 'purchase/vendors_portal/pur_order_change/'+el.value).done(function(response) {
      response = JSON.parse(response);
        if(response){ 
          $('select[name="currency"]').val(response.currency).change();
          $('input[name="currency_rate"]').val(response.currency_rate).change();
          $('input[name="shipping_fee"]').val(response.shipping_fee);
          $('input[name="order_discount"]').val(response.order_discount);
          $('select[name="add_discount_type"]').val('amount');

          $('select[name="discount_type"]').val(response.discount_type).change();

          $('.invoice-item table.invoice-items-table.items tbody').html('');
          $('.invoice-item table.invoice-items-table.items tbody').append(response.list_item);

          $('input[name="discount_percent"]').val(response.discount_percent).change();

          setTimeout(function () {
            pur_calculate_total();
          }, 15);

          init_selectpicker();
          pur_reorder_items('.invoice-item');
          pur_clear_item_preview_values('.invoice-item');
          $('body').find('#items-warning').remove();
          $("body").find('.dt-loader').remove();
          $('#item_select').selectpicker('val', '');
        }
    });

    $('#recurring_div').addClass('hide');
    $('select[name="recurring"]').val(0).change();
  }else{
    $('#recurring_div').removeClass('hide');
    $('select[name="recurring"]').val(0).change();
  }
}

// Set the currency for accounting
function init_pi_currency(id, callback) {
    var $accountingTemplate = $("body").find('.accounting-template');

    if ($accountingTemplate.length || id) {
        var selectedCurrencyId = !id ? $accountingTemplate.find('select[name="currency"]').val() : id;

        requestGetJSON('purchase/vendors_portal/get_currency/' + selectedCurrencyId)
            .done(function (currency) {
                // Used for formatting money
                accounting.settings.currency.decimal = currency.decimal_separator;
                accounting.settings.currency.thousand = currency.thousand_separator;
                accounting.settings.currency.symbol = currency.symbol;
                accounting.settings.currency.format = currency.placement == 'after' ? '%v %s' : '%s%v';

                pur_calculate_total();

                if(callback) {
                    callback();
                }
            });
    }
}

function pur_calculate_total(from_discount_money){
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
    total_money = 0,
    total_tax_money = 0,
    quantity = 1,
    total_discount_calculated = 0,
    item_total_payment,
    rows = $('.table.has-calculations tbody tr.item'),
    subtotal_area = $('#subtotal'),
    discount_area = $('#discount_area'),
    adjustment = $('input[name="adjustment"]').val(),
    // discount_percent = $('input[name="discount_percent"]').val(),
    discount_percent = 'before_tax',
    discount_fixed = $('input[name="discount_total"]').val(),
    discount_total_type = $('.discount-total-type.selected'),
    discount_type = $('select[name="discount_type"]').val(),
    additional_discount = $('input[name="additional_discount"]').val(),
    add_discount_type = $('select[name="add_discount_type"]').val();

    var shipping_fee = $('input[name="shipping_fee"]').val();
    if(shipping_fee == ''){
      shipping_fee = 0;
      $('input[name="shipping_fee"]').val(0);
    }

  $('.wh-tax-area').remove();

    $.each(rows, function () {
    var item_discount = 0;
    var item_discount_money = 0;
    var item_discount_from_percent = 0;
    var item_discount_percent = 0;
    var item_tax = 0,
        item_amount  = 0;

    quantity = $(this).find('[data-quantity]').val();
    if (quantity === '') {
      quantity = 1;
      $(this).find('[data-quantity]').val(1);
    }
    item_discount_percent = $(this).find('td.discount input').val();
    item_discount_money = $(this).find('td.discount_money input').val();

    if (isNaN(item_discount_percent) || item_discount_percent == '') {
      item_discount_percent = 0;
    }

    if (isNaN(item_discount_money) || item_discount_money == '') {
      item_discount_money = 0;
    }

    if(from_discount_money == 1 && item_discount_money > 0){
      $(this).find('td.discount input').val('');
    }

    _amount = accounting.toFixed($(this).find('td.rate input').val() * quantity, app.options.decimal_places);
    item_amount = _amount;
    _amount = parseFloat(_amount);

    $(this).find('td.into_money').html(format_money(_amount));
    $(this).find('td._into_money input').val(_amount);

    subtotal += _amount;
    row = $(this);
    item_taxes = $(this).find('select.taxes').val();

    if(discount_type == 'after_tax'){
      if (item_taxes) {
        $.each(item_taxes, function (i, taxname) {
          taxrate = row.find('select.taxes [value="' + taxname + '"]').data('taxrate');
          calculated_tax = (_amount / 100 * taxrate);
          item_tax += calculated_tax;
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
    }
    
      //Discount of item
      if( item_discount_percent > 0 && from_discount_money != 1){

        
        if(discount_type == 'after_tax'){
          item_discount_from_percent = (parseFloat(item_amount) + parseFloat(item_tax) ) * parseFloat(item_discount_percent) / 100;
        }else if(discount_type == 'before_tax'){
          item_discount_from_percent = parseFloat(item_amount) * parseFloat(item_discount_percent) / 100;
        }
        
        if(item_discount_from_percent != item_discount_money){
          item_discount_money = item_discount_from_percent;
        }
      }

      if( item_discount_money > 0){
        item_discount = parseFloat(item_discount_money);
      }

      item_total_payment = parseFloat(item_amount) + parseFloat(item_tax) - parseFloat(item_discount);
      // Append value to item
      total_discount_calculated += parseFloat(item_discount);
      $(this).find('td.discount_money input').val(item_discount);


      if(discount_type == 'before_tax'){ 
        if (item_taxes) {
          var after_dc_amount = _amount - parseFloat(item_discount);
          $.each(item_taxes, function (i, taxname) {
              taxrate = row.find('select.taxes [value="' + taxname + '"]').data('taxrate');
              calculated_tax = (after_dc_amount / 100 * taxrate);
              item_tax += calculated_tax;
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
      }

      var after_tax = _amount + item_tax;
      var before_tax = _amount;

      item_total_payment = parseFloat(item_amount) + parseFloat(item_tax) - parseFloat(item_discount);

      $(this).find('td.total_after_discount input').val(item_total_payment);

    $(this).find('td.label_total_after_discount').html(format_money(item_total_payment));

    $(this).find('td._total').html(format_money(after_tax));
    $(this).find('td._total_after_tax input').val(after_tax);

    $(this).find('td.tax_value input').val(item_tax);

  });

  var order_discount_percent = $('input[name="order_discount"]').val();  
  var order_discount_percent_val = 0;

  // Discount by percent
  if ((order_discount_percent !== '' && order_discount_percent != 0) && discount_type == 'before_tax' && add_discount_type == 'percent') {
    total_discount_calculated += parseFloat((subtotal * order_discount_percent) / 100);
    order_discount_percent_val = (subtotal * order_discount_percent) / 100;
  } else if ((order_discount_percent !== '' && order_discount_percent != 0) && discount_type == 'before_tax' && add_discount_type == 'amount') {
    total_discount_calculated += parseFloat(order_discount_percent);
    order_discount_percent_val = order_discount_percent;
  }

  $.each(taxes, function (taxname, total_tax) {
    if ((order_discount_percent !== '' && order_discount_percent != 0) && discount_type == 'before_tax' && add_discount_type == 'percent') {
      var total_tax_calculated = (total_tax * order_discount_percent) / 100;
      total_tax = (total_tax - total_tax_calculated);
    } else if ((order_discount_percent !== '' && order_discount_percent != 0) && discount_type == 'before_tax' && add_discount_type == 'amount') {
      var t = (order_discount_percent / subtotal) * 100;
      total_tax = (total_tax - (total_tax * t) / 100);
    }

    total += total_tax;
    total_tax_money += total_tax;
    total_tax = format_money(total_tax);
    $('#tax_id_' + slugify(taxname)).html(total_tax);
  });


  total = (total + subtotal);
  total_money = total;
  // Discount by percent
  if ((order_discount_percent !== '' && order_discount_percent != 0) && discount_type == 'after_tax' && add_discount_type == 'percent') {
    total_discount_calculated += parseFloat((total * order_discount_percent) / 100);
    order_discount_percent_val = (total * order_discount_percent) / 100;
  } else if ((order_discount_percent !== '' && order_discount_percent != 0) && discount_type == 'after_tax' && add_discount_type == 'amount') {
    total_discount_calculated += parseFloat(order_discount_percent);
    order_discount_percent_val = order_discount_percent;
  }





  total = parseFloat(total) - parseFloat(total_discount_calculated) - parseFloat(additional_discount);
  adjustment = parseFloat(adjustment);

  // Check if adjustment not empty
  if (!isNaN(adjustment)) {
    total = total + adjustment;
  }

  total+= parseFloat(shipping_fee);

  var discount_html = '-' + format_money(parseFloat(total_discount_calculated)+ parseFloat(additional_discount));
    $('input[name="discount_total"]').val(accounting.toFixed(total_discount_calculated, app.options.decimal_places));
    
  // Append, format to html and display
  $('.shiping_fee').html(format_money(shipping_fee));
  $('.order_discount_value').html(format_money(order_discount_percent_val));
  $('.wh-total_discount').html(discount_html + hidden_input('dc_total', accounting.toFixed(order_discount_percent_val, app.options.decimal_places))  );
  $('.adjustment').html(format_money(adjustment));
  $('.wh-subtotal').html(format_money(subtotal) + hidden_input('total_mn', accounting.toFixed(subtotal, app.options.decimal_places)));
  $('.wh-total').html(format_money(total) + hidden_input('grand_total', accounting.toFixed(total, app.options.decimal_places)));

  $(document).trigger('purchase-quotation-total-calculated');

}

function init_selectpicker() {
    appSelectPicker();
}

function init_datepicker(element_date, element_time) {
    appDatepicker({
        element_date: element_date,
        element_time: element_time,
    });
}

function requestGetJSON(uri, params) {
    params = typeof (params) == 'undefined' ? {} : params;
    params.dataType = 'json';
    return requestGet(uri, params);
}

function requestGet(uri, params) {
    params = typeof (params) == 'undefined' ? {} : params;
    var options = {
        type: 'GET',
        url: uri.indexOf(site_url) > -1 ? uri : site_url + uri
    };
    return $.ajax($.extend({}, options, params));
}

function pur_add_item_to_preview(id) {
  "use strict";
  var currency_rate = $('input[name="currency_rate"]').val();
  requestGetJSON('purchase/vendors_portal/get_item_by_id/' + id +'/'+ currency_rate).done(function (response) {
    pur_clear_item_preview_values();

    $('.main input[name="item_code"]').val(response.itemid);
    $('.main textarea[name="item_name"]').val(response.code_description);
    $('.main textarea[name="description"]').val(response.long_description);
    $('.main input[name="unit_price"]').val(response.purchase_price);
    $('.main input[name="unit_name"]').val(response.unit_name);
    $('.main input[name="unit_id"]').val(response.unit_id);
    $('.main input[name="quantity"]').val(1);

    $('.selectpicker').selectpicker('refresh');


    var taxSelectedArray = [];
    if (response.taxname && response.taxrate) {
      taxSelectedArray.push(response.taxname + '|' + response.taxrate);
    }
    if (response.taxname_2 && response.taxrate_2) {
      taxSelectedArray.push(response.taxname_2 + '|' + response.taxrate_2);
    }

    $('.main select.taxes').selectpicker('val', taxSelectedArray);
    $('.main input[name="unit"]').val(response.unit_name);

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

function pur_add_item_to_table(data, itemid) {
  "use strict";

  data = typeof (data) == 'undefined' || data == 'undefined' ? pur_get_item_preview_values() : data;

  if (data.quantity == ""  ) {
    
    return;
  }
  var currency_rate = $('input[name="currency_rate"]').val();
  var to_currency = $('select[name="currency"]').val();
  var table_row = '';
  var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.invoice-items-table tbody .item').length + 1;
  lastAddedItemKey = item_key;
  $("body").append('<div class="dt-loader"></div>');
  pur_get_item_row_template('newitems[' + item_key + ']',data.item_name, data.description, data.quantity, data.unit_name, data.unit_price, data.taxname, data.item_code, data.unit_id, data.tax_rate, data.discount, itemid, currency_rate, to_currency).done(function(output){
    table_row += output;

    $('.invoice-item table.invoice-items-table.items tbody').append(table_row);

    setTimeout(function () {
      pur_calculate_total();
    }, 15);
    init_selectpicker();
    pur_reorder_items('.invoice-item');
    pur_clear_item_preview_values('.invoice-item');
    $('body').find('#items-warning').remove();
    $("body").find('.dt-loader').remove();
        $('#item_select').selectpicker('val', '');

    return true;
  });
  return false;
}

function pur_get_item_preview_values() {
  "use strict";

  var response = {};
  response.item_name = $('.invoice-item .main textarea[name="item_name"]').val();
  response.description = $('.invoice-item .main textarea[name="description"]').val();
  response.quantity = $('.invoice-item .main input[name="quantity"]').val();
  response.unit_name = $('.invoice-item .main input[name="unit_name"]').val();
  response.unit_price = $('.invoice-item .main input[name="unit_price"]').val();
  response.taxname = $('.main select.taxes').selectpicker('val');
  response.item_code = $('.invoice-item .main input[name="item_code"]').val();
  response.unit_id = $('.invoice-item .main input[name="unit_id"]').val();
  response.tax_rate = $('.invoice-item .main input[name="tax_rate"]').val();
  response.discount = $('.invoice-item .main input[name="discount"]').val();


  return response;
}


function pur_clear_item_preview_values(parent) {
  "use strict";

  var previewArea = $(parent + ' .main');
  previewArea.find('input').val('');
  previewArea.find('textarea').val('');
  previewArea.find('select').val('').selectpicker('refresh');
}

function pur_reorder_items(parent) {
  "use strict";

  var rows = $(parent + ' .table.has-calculations tbody tr.item');
  var i = 1;
  $.each(rows, function () {
    $(this).find('input.order').val(i);
    i++;
  });
}

function pur_delete_item(row, itemid,parent) {
  "use strict";

  $(row).parents('tr').addClass('animated fadeOut', function () {
    setTimeout(function () {
      
    }, 50);


  });
  $(row).parents('tr').remove();
      pur_calculate_total();
  if (itemid && $('input[name="isedit"]').length > 0) {
    $(parent+' #removed-items').append(hidden_input('removed_items[]', itemid));
  }
}

function pur_get_item_row_template(name, item_name, description, quantity, unit_name, unit_price, taxname,  item_code, unit_id, tax_rate, discount, item_key, currency_rate, to_currency)  {
  "use strict";

  jQuery.ajaxSetup({
    async: false
  });

  var d = $.post(site_url + 'purchase/vendors_portal/get_purchase_invoice_row_template', {
    name: name,
    item_name : item_name,
    item_description : description,
    quantity : quantity,
    unit_name : unit_name,
    unit_price : unit_price,
    taxname : taxname,
    item_code : item_code,
    unit_id : unit_id,
    tax_rate : tax_rate,
    discount : discount,
    item_key : item_key,
    currency_rate: currency_rate,
    to_currency: to_currency
  });
  jQuery.ajaxSetup({
    async: true
  });
  return d;
}

function format_money(total, excludeSymbol) {

    if (typeof (excludeSymbol) != 'undefined' && excludeSymbol) {
        return accounting.formatMoney(total, {
            symbol: ''
        });
    }

    return accounting.formatMoney(total);
}

function init_ajax_search(type, selector, server_data, url) {
    var ajaxSelector = $('body').find(selector);

    if (ajaxSelector.length) {
        var options = {
            ajax: {
                url: (typeof (url) == 'undefined' ? site_url + 'purchase/vendors_portal/get_relation_data' : url),
                data: function () {
                    var data = {};
                    data.type = type;
                    data.rel_id = '';
                    data.q = '{{{q}}}';
                    if (typeof (server_data) != 'undefined') {
                        jQuery.extend(data, server_data);
                    }
                    return data;
                }
            },
            locale: {
                emptyTitle: app.lang.search_ajax_empty,
                statusInitialized: app.lang.search_ajax_initialized,
                statusSearching: app.lang.search_ajax_searching,
                statusNoResults: app.lang.not_results_found,
                searchPlaceholder: app.lang.search_ajax_placeholder,
                currentlySelected: app.lang.currently_selected
            },
            requestDelay: 500,
            cache: false,
            preprocessData: function (processData) {
                var bs_data = [];
                var len = processData.length;
                for (var i = 0; i < len; i++) {
                    var tmp_data = {
                        'value': processData[i].id,
                        'text': processData[i].name,
                    };
                    if (processData[i].subtext) {
                        tmp_data.data = {
                            subtext: processData[i].subtext
                        };
                    }
                    bs_data.push(tmp_data);
                }
                return bs_data;
            },
            preserveSelectedPosition: 'after',
            preserveSelected: true
        };
        if (ajaxSelector.data('empty-title')) {
            options.locale.emptyTitle = ajaxSelector.data('empty-title');
        }
        ajaxSelector.selectpicker().ajaxSelectPicker(options);
    }
}
</script>