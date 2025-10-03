<script>

	var lastAddedItemKey = null;
	(function($) {
		"use strict";  

		appValidateForm($('#add_edit_order_return'), {
			email: 'required',
			phonenumber: 'required',
			order_return_name: 'required',
			rel_id: 'required',
			company_id: 'required'
		});

	 // Maybe items ajax search
	 init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'purchase/pur_commodity_code_search/rate');
	 pur_calculate_total();
	  

	})(jQuery);


	(function($) {
		"use strict";

init_or_currency();

$("body").on('change', 'select[name="currency"]', function () {
  init_or_currency();
});

// Add item to preview from the dropdown for invoices estimates
$("body").on('change', 'select[name="item_select"]', function () {
	var itemid = $(this).selectpicker('val');
	if (itemid != '') {
		pur_add_item_to_preview(itemid);
	}
});

// Recaulciate total on these changes
$("body").on('change', 'select.taxes', function () {
	pur_calculate_total();
});

$("body").on('change', 'input[name="additional_discount"]', function () {

	// var additional_discount = $('input[name="additional_discount"]').val();
	// var main_additional_discount = $('input[name="main_additional_discount"]').val();
	// if(parseFloat(additional_discount) <= parseFloat(main_additional_discount)){
		pur_calculate_total();
	// }
});

$("body").on('click', '.add_order_return', function () {
	submit_form(false);
});

$('.add_order_return_send').on('click', function() {
	submit_form(true);
});

$("body").on('change', 'select[name="return_type"]', function () {
	var return_type = $(this).val();
	
		var rel_id = $('select[name="rel_id"]').val();
		var rel_type = $('input[name="rel_type"]').val();
		$.post(admin_url + 'purchase/order_return_get_item_data/'+rel_id+'/'+rel_type+'/'+return_type).done(function(response){
			response = JSON.parse(response);
			$('select[name="company_id"]').val(response.company_id).change();
			$('input[name="email"]').val(response.email);
			$('input[name="phonenumber"]').val(response.phonenumber);
			$('input[name="order_number"]').val(response.order_number);
			$('input[name="order_date"]').val(response.order_date);
			$('input[name="number_of_item"]').val(response.number_of_item);
			$('input[name="order_total"]').val(response.order_total);

			$('input[name="main_additional_discount"]').val(response.additional_discount);
			$('input[name="additional_discount"]').val(response.additional_discount);
			$('.invoice-item table.invoice-items-table.items tbody').html('');
			$('.invoice-item table.invoice-items-table.items tbody').append(response.result);
			$('select[name="currency"]').val(response.currency).change();
			//$('select[name="return_type"]').val('fully').change();

			setTimeout(function () {
				if(rel_type == 'sales_return_order'){
					wh_sale_order_calculate_total();
				}
				else{
					pur_calculate_total();				
				}
			}, 15);

			init_selectpicker();
			init_datepicker();
			pur_reorder_items('.invoice-item');
			pur_clear_item_preview_values('.invoice-item');
			$('body').find('#items-warning').remove();
			$("body").find('.dt-loader').remove();
			$('#item_select').selectpicker('val', '');
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

})(jQuery);


// Add item to preview
function pur_add_item_to_preview(id) {
	"use strict";

	requestGetJSON('purchase/get_item_by_id/' + id +'/'+true).done(function (response) {
		clear_item_preview_values();

		$('.main input[name="commodity_code"]').val(response.itemid);
		$('.main textarea[name="commodity_name"]').val(response.code_description);
		$('.main input[name="unit_price"]').val(response.rate);
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

	if ( data.quantity == "" || data.commodity_code == "" ) {
		
		if(parseFloat(data.quantity) < 0){
			//check_available_quantity
			alert_float('warning', '<?php echo _l('please_choose_quantity_more_than_0') ?>');
		}

		return;
	}
	var table_row = '';
	var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.invoice-items-table tbody .item').length + 1;
	lastAddedItemKey = item_key;
	$("body").append('<div class="dt-loader"></div>');
	pur_get_item_row_template('newitems[' + item_key + ']',data.commodity_name, data.quantity, data.unit_name,data.unit_price, data.taxname, data.commodity_code, data.unit_id, data.tax_rate, data.discount, itemid).done(function(output){
		table_row += output;

		$('.invoice-item table.invoice-items-table.items tbody').append(table_row);

		setTimeout(function () {
			pur_calculate_total();
		}, 15);
		init_selectpicker();
		init_datepicker();
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
	response.commodity_name = $('.invoice-item .main textarea[name="commodity_name"]').val();
	response.quantity = $('.invoice-item .main input[name="quantity"]').val();
	response.unit_name = $('.invoice-item .main input[name="unit_name"]').val();
	response.unit_price = $('.invoice-item .main input[name="unit_price"]').val();
	response.taxname = $('.main select.taxes').selectpicker('val');
	response.commodity_code = $('.invoice-item .main input[name="commodity_code"]').val();
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

function pur_get_item_row_template(name, commodity_name, quantity, unit_name, unit_price, taxname, commodity_code, unit_id, tax_rate, discount, item_key)  {
	"use strict";

	jQuery.ajaxSetup({
		async: false
	});

	var d = $.post(admin_url + 'purchase/get_order_return_row_template', {
		name: name,
		commodity_name : commodity_name,
		quantity : quantity,
		unit_name : unit_name,
		unit_price : unit_price,
		taxname : taxname,
		commodity_code : commodity_code,
		unit_id : unit_id,
		tax_rate : tax_rate,
		discount : discount,
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
			pur_calculate_total();
		}, 50);
	});
	if (itemid && $('input[name="isedit"]').length > 0) {
		$(parent+' #removed-items').append(hidden_input('removed_items[]', itemid));
	}
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

function pur_calculate_total(){
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
	item_discount_percent = 0,
	item_discount = 0,
	item_total_payment,
	rows = $('.table.has-calculations tbody tr.item'),
	subtotal_area = $('#subtotal'),
	discount_area = $('#discount_area'),
	adjustment = $('input[name="adjustment"]').val(),
	fee_return_order = $('input[name="fee_return_order"]').val(),
		// discount_percent = $('input[name="discount_percent"]').val(),
		discount_percent = 'before_tax',
		discount_fixed = $('input[name="discount_total"]').val(),
		discount_total_type = $('.discount-total-type.selected'),
		discount_type = $('select[name="discount_type"]').val(),
		additional_discount = $('input[name="additional_discount"]').val(),
		main_additional_discount = $('input[name="main_additional_discount"]').val();

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
      if( item_discount_percent > 0 ){

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
  if ((order_discount_percent !== '' && order_discount_percent != 0) && discount_type == 'before_tax') {
    total_discount_calculated += parseFloat(order_discount_percent);
    order_discount_percent_val = order_discount_percent;
  }

  $.each(taxes, function (taxname, total_tax) {
     if ((order_discount_percent !== '' && order_discount_percent != 0) && discount_type == 'before_tax') {
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

if ((order_discount_percent !== '' && order_discount_percent != 0) && discount_type == 'after_tax') {
    total_discount_calculated += parseFloat(order_discount_percent);
    order_discount_percent_val = order_discount_percent;
  }


  
  //total_discount_calculated = total_discount_calculated;

  total = parseFloat(total) - parseFloat(total_discount_calculated) - parseFloat(additional_discount);
	
	adjustment = parseFloat(adjustment);

	// Check if adjustment not empty
	if (!isNaN(adjustment)) {
		total = total + adjustment;
	}

	if (!isNaN(parseFloat(total_discount_calculated)) && !isNaN(parseFloat(additional_discount))) {
		var discount_html = '-' + format_money(parseFloat(total_discount_calculated)+ parseFloat(additional_discount));
	}else if(!isNaN(parseFloat(total_discount_calculated))){
		var discount_html = '-' + format_money(parseFloat(total_discount_calculated));
	}else if(!isNaN(parseFloat(additional_discount))){
		var discount_html = '-' + format_money(parseFloat(additional_discount));
	}
	total = total - fee_return_order;

	$('input[name="discount_total"]').val(accounting.toFixed(total_discount_calculated, app.options.decimal_places));


	// Append, format to html and display
	$('.wh-total_discount').html(discount_html + hidden_input('discount_total', accounting.toFixed(total_discount_calculated, app.options.decimal_places))  );
	$('.adjustment').html(format_money(adjustment));

	$('.wh-additional_discount').html('<input class="form-control" type="number" name="additional_discount" value="' + additional_discount + '">');
	$('.wh-fee_for_return_order').html('-'+format_money(fee_return_order));
	$('.wh-subtotal').html(format_money(subtotal) + hidden_input('subtotal', accounting.toFixed(subtotal, app.options.decimal_places)) + hidden_input('total_amount', accounting.toFixed(total_money, app.options.decimal_places)));
	$('.wh-total').html(format_money(total) + hidden_input('total_after_discount', accounting.toFixed(total, app.options.decimal_places)));

	$(document).trigger('wh-packing-list-total-calculated');

}

function get_available_quantity(commodity_code_name, from_stock_name, available_quantity_name){
	"use strict"; 

	var data = {};
	data.commodity_id = $('input[name="'+commodity_code_name+'"]').val();
	data.warehouse_id = $('select[name="'+from_stock_name+'"]').val();
	if(data.commodity_id != '' && data.warehouse_id != ''){
		$.post(admin_url + 'purchase/get_quantity_inventory', data).done(function(response){
			response = JSON.parse(response);
			$('input[name="'+available_quantity_name+'"]').val(response.value);
		});
	}else{
		$('input[name="'+available_quantity_name+'"]').val(0);
	}

	setTimeout(function () {
		pur_calculate_total();
	}, 15);

}

function submit_form(save_and_send_request) {
	"use strict";

	pur_calculate_total();

	var $itemsTable = $('.invoice-items-table');
	var $previewItem = $itemsTable.find('.main');
	var check_warehouse_status = true,
	check_quantity_status = true;

	if ( $itemsTable.length && $itemsTable.find('.item').length === 0) {
		alert_float('warning', '<?php echo _l('wh_enter_at_least_one_product'); ?>', 3000);
		return false;
	}

	$('input[name="save_and_send_request"]').val(save_and_send_request);

	var rows = $('.table.has-calculations tbody tr.item');
	$.each(rows, function () {
		var quantity_value = $(this).find('td.quantities input').val();
		if(parseFloat(quantity_value) == 0){
			check_quantity_status = false;
		}
		
	})

	if( check_quantity_status == true ){
		// Add disabled to submit buttons
		$(this).find('.add_goods_receipt_send').prop('disabled', true);
		$(this).find('.add_goods_receipt').prop('disabled', true);
		$('#add_edit_order_return').submit();
	}else{
		alert_float('warning', '<?php echo _l('please_choose_quantity_export') ?>');

	}

	return true;
}

function invoice_change(){
	"use strict";

	var invoice_id = $('select[name="invoice_id"]').val();

	$.post(admin_url + 'purchase/copy_invoices/'+invoice_id).done(function(response){
		response = JSON.parse(response);

		$('input[name="additional_discount"]').val((response.goods_delivery.additional_discount));
		$('.invoice-item table.invoice-items-table.items tbody').html('');
		$('.invoice-item table.invoice-items-table.items tbody').append(response.result);

		setTimeout(function () {
			pur_calculate_total();
		}, 15);

		init_selectpicker();
		init_datepicker();
		pur_reorder_items('.invoice-item');
		pur_clear_item_preview_values('.invoice-item');
		$('body').find('#items-warning').remove();
		$("body").find('.dt-loader').remove();
		$('#item_select').selectpicker('val', '');

		$('select[name="staff_id"]').val((response.goods_delivery.addedfrom)).change();
		$('textarea[name="description"]').val((response.goods_delivery.description)).change();
		$('input[name="address"]').val((response.goods_delivery.address));
		$('select[name="customer_code"]').val((response.goods_delivery.customer_code)).change();
		$('input[name="invoice_no"]').val(response.invoice_no);
	});

}


$('select[name="delivery_note_id"]').on('change', function() {
	"use strict";

	var delivery_note_id = $('select[name="delivery_note_id"]').val();
	$.post(admin_url + 'purchase/packing_list_copy_delivery_note/'+delivery_note_id).done(function(response){
		response = JSON.parse(response);

		$('input[name="additional_discount"]').val((response.additional_discount));
		$('input[name="main_additional_discount"]').val((response.additional_discount));
		$('.invoice-item table.invoice-items-table.items tbody').html('');
		$('.invoice-item table.invoice-items-table.items tbody').append(response.result);
		$('select[name="clientid"]').val(response.customer_id).change();

		setTimeout(function () {
			pur_calculate_total();
		}, 15);

		init_selectpicker();
		init_datepicker();
		pur_reorder_items('.invoice-item');
		pur_clear_item_preview_values('.invoice-item');
		$('body').find('#items-warning').remove();
		$("body").find('.dt-loader').remove();
		$('#item_select').selectpicker('val', '');

	});
});


$("body").on('change', '#company_id', function () {
	var rel_type = $('input[name="rel_type"]').val();
	var company_id = $('select[name="company_id"]').val();
	
	if(company_id.length > 0){
		requestGetJSON('purchase/pur_client_data/' + company_id +'/'+ rel_type).done(function (response) {
			$('input[name="email"]').val(response.email);
			$('input[name="phonenumber"]').val(response.phonenumber);
			
			$('input[name="fee_return_order"]').val(response.fee_return_order);
			$('#return_policies_information').html(response.return_policies_information)
			setTimeout(function () {
				pur_calculate_total();
			}, 15);
		});
	}else{
		$('input[name="email"]').val('');
		$('input[name="phonenumber"]').val('');
		$('select[name="currency"]').val('').change();
	}
});



$('select[name="rel_id"]').on('change', function() {
	"use strict";
	var rel_id = $('select[name="rel_id"]').val();
	var rel_type = $('input[name="rel_type"]').val();
	$.post(admin_url + 'purchase/order_return_get_item_data/'+rel_id+'/'+rel_type).done(function(response){
		response = JSON.parse(response);
		$('select[name="company_id"]').val(response.company_id).change();
		$('input[name="email"]').val(response.email);
		$('input[name="phonenumber"]').val(response.phonenumber);
		$('input[name="order_number"]').val(response.order_number);
		$('input[name="order_date"]').val(response.order_date);
		$('input[name="number_of_item"]').val(response.number_of_item);
		$('input[name="order_total"]').val(response.order_total);

		$('input[name="main_additional_discount"]').val(response.additional_discount);
		$('input[name="additional_discount"]').val(response.additional_discount);
		$('.invoice-item table.invoice-items-table.items tbody').html('');
		$('.invoice-item table.invoice-items-table.items tbody').append(response.result);
		$('select[name="currency"]').val(response.currency).change();
		$('select[name="return_type"]').val('fully').change();
		$('input[name="order_discount"]').val(response.discount_total);
		$('select[name="discount_type"]').val(response.discount_type).change();


		setTimeout(function () {
			if(rel_type == 'sales_return_order'){
				wh_sale_order_calculate_total();
			}
			else{
				pur_calculate_total();				
			}
		}, 15);

		init_selectpicker();
		init_datepicker();
		pur_reorder_items('.invoice-item');
		pur_clear_item_preview_values('.invoice-item');
		$('body').find('#items-warning').remove();
		$("body").find('.dt-loader').remove();
		$('#item_select').selectpicker('val', '');
	});
});

function wh_sale_order_calculate_total(){
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
	item_discount_percent = 0,
	item_discount = 0,
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
		main_additional_discount = $('input[name="main_additional_discount"]').val();

		$('.wh-tax-area').remove();

		$.each(rows, function () {

			var item_tax = 0,
			item_amount  = 0;

			quantity = $(this).find('[data-quantity]').val();
			if (quantity === '') {
				quantity = 1;
				$(this).find('[data-quantity]').val(1);
			}
			item_discount_percent = $(this).find('td.discount input').val();

			if (isNaN(item_discount_percent) || item_discount_percent == '') {
				item_discount_percent = 0;
			}

			_amount = accounting.toFixed($(this).find('td.rate input').val() * quantity, app.options.decimal_places);
			item_amount = _amount;
			_amount = parseFloat(_amount);

			$(this).find('td.amount').html(format_money(_amount));

			subtotal += _amount;
			row = $(this);
			item_taxes = $(this).find('select.taxes').val();

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
			//Discount of item
			item_discount = (parseFloat(item_amount) + parseFloat(item_tax) ) * parseFloat(item_discount_percent) / 100;
			item_total_payment = parseFloat(item_amount) + parseFloat(item_tax) - parseFloat(item_discount);

			// Append value to item
			total_discount_calculated += item_discount;
			$(this).find('td.discount_money input').val(item_discount);
			$(this).find('td.total_after_discount input').val(item_total_payment);

			$(this).find('td.label_discount_money').html(format_money(item_discount));
			$(this).find('td.label_total_after_discount').html(format_money(item_total_payment));

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
	total_money = total;
	// Discount by percent
	if ((discount_percent !== '' && discount_percent != 0) && discount_type == 'after_tax' && discount_total_type.hasClass('discount-type-percent')) {
		total_discount_calculated = (total * discount_percent) / 100;
	} else if ((discount_fixed !== '' && discount_fixed != 0) && discount_type == 'after_tax' && discount_total_type.hasClass('discount-type-fixed')) {
		total_discount_calculated = discount_fixed;
	}

	if (!isNaN(parseFloat(additional_discount))) {
		total = total - total_discount_calculated - parseFloat(additional_discount);
	} else {
		total = total - total_discount_calculated;
	}

	adjustment = parseFloat(adjustment);

	// Check if adjustment not empty
	if (!isNaN(adjustment)) {
		total = total + adjustment;
	}

	if (!isNaN(parseFloat(total_discount_calculated)) && !isNaN(parseFloat(additional_discount))) {
		var discount_html = '-' + format_money(parseFloat(total_discount_calculated)+ parseFloat(additional_discount));
	}else if(!isNaN(parseFloat(total_discount_calculated))){
		var discount_html = '-' + format_money(parseFloat(total_discount_calculated));
	}else if(!isNaN(parseFloat(additional_discount))){
		var discount_html = '-' + format_money(parseFloat(additional_discount));
	}

	$('input[name="discount_total"]').val(accounting.toFixed(total_discount_calculated, app.options.decimal_places));


	// Append, format to html and display
	$('.wh-total_discount').html(discount_html + hidden_input('discount_total', accounting.toFixed(total_discount_calculated, app.options.decimal_places))  );
	$('.adjustment').html(format_money(adjustment));

	$('.wh-additional_discount').html('<input type="number" name="additional_discount" min="0.0" step="any" max="'+main_additional_discount+'" value="' + additional_discount + '" readonly="true">');

	$('.wh-subtotal').html(format_money(subtotal) + hidden_input('subtotal', accounting.toFixed(subtotal, app.options.decimal_places)) + hidden_input('total_amount', accounting.toFixed(total_money, app.options.decimal_places)));
	$('.wh-total').html(format_money(total) + hidden_input('total_after_discount', accounting.toFixed(total, app.options.decimal_places)));

	$(document).trigger('wh-packing-list-total-calculated');

}

function wh_sales_order_delete_item(row, itemid,parent) {
	"use strict";
	$(row).parents('tr').addClass('animated fadeOut', function () {
		setTimeout(function () {
			$(row).parents('tr').remove();
			wh_sale_order_calculate_total();
		}, 50);
	});
	if (itemid && $('input[name="isedit"]').length > 0) {
		$(parent+' #removed-items').append(hidden_input('removed_items[]', itemid));
	}
	if($('.sortable.item').length == 0){
		$('select[name="company_id"]').val('').change();
		$('input[name="email"]').val('');
		$('input[name="phonenumber"]').val('');
		$('input[name="order_number"]').val('');
		$('input[name="number_of_item"]').val('');
		$('input[name="order_total"]').val('');
		$('input[name="main_additional_discount"]').val(0);
		$('body').find('#items-warning').remove();
		$("body").find('.dt-loader').remove();
		$('#item_select').selectpicker('val', '');
	}
}

// Set the currency for accounting
function init_or_currency(id, callback) {
    var $accountingTemplate = $("body").find('.accounting-template');

    if ($accountingTemplate.length || id) {
        var selectedCurrencyId = !id ? $accountingTemplate.find('select[name="currency"]').val() : id;

        requestGetJSON('misc/get_currency/' + selectedCurrencyId)
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


</script>