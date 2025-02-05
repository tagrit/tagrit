var fnServerParams,
 id,
 inventory_asset_account,
 income_account,
 expense_account,
 item_id,
 tax_id,
 category_id,
 payment_account,
 deposit_to,
 expense_payment_account,
 expense_deposit_to,
 payment_mode_id,
 preferred_payment_method,
 credit_note_refund_payment_account,
 credit_note_refund_deposit_to,
 purchase_payment_account,
 purchase_deposit_to;
 
(function($) {
	"use strict";

	appValidateForm($('#item-automatic-form'), {
		'item[]': 'required',
    },item_automatic_form_handler);

  appValidateForm($('#edit-item-automatic-form'), {
		item_id: 'required',
    },item_automatic_form_handler);

  appValidateForm($('#payment-mode-mapping-form'), {
    'payment_mode[]': 'required',
    },payment_mode_mapping_form_handler);

  appValidateForm($('#edit-payment-mode-mapping-form'), {
    'payment_mode_id': 'required',
    },payment_mode_mapping_form_handler);

  appValidateForm($('#tax-mapping-form'), {
    'tax[]': 'required',
    },tax_mapping_form_handler);

  appValidateForm($('#edit-tax-mapping-form'), {
    'tax_id': 'required',
    },tax_mapping_form_handler);

  appValidateForm($('#expense-category-mapping-form'), {
    'category[]': 'required',
    },expense_category_mapping_form_handler);

  appValidateForm($('#edit-expense-category-mapping-form'), {
    'category_id': 'required',
    },expense_category_mapping_form_handler);

		fnServerParams = {

    };

	$('input[name="acc_invoice_automatic_conversion"]').on('change', function() {
	    if($('input[name="acc_invoice_automatic_conversion"]').is(':checked') == true){
	      $('#div_invoice_automatic_conversion').removeClass('hide');
	    }else{
	      $('#div_invoice_automatic_conversion').addClass('hide');
	    }
	});

  $('input[name="acc_payment_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_payment_automatic_conversion"]').is(':checked') == true){
        $('#div_payment_automatic_conversion').removeClass('hide');
      }else{
        $('#div_payment_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_payment_expense_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_payment_expense_automatic_conversion"]').is(':checked') == true){
        $('#div_payment_expense_automatic_conversion').removeClass('hide');
      }else{
        $('#div_payment_expense_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_credit_note_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_credit_note_automatic_conversion"]').is(':checked') == true){
        $('#div_credit_note_automatic_conversion').removeClass('hide');
      }else{
        $('#div_credit_note_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_credit_note_refund_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_credit_note_refund_automatic_conversion"]').is(':checked') == true){
        $('#div_credit_note_refund_automatic_conversion').removeClass('hide');
      }else{
        $('#div_credit_note_refund_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_expense_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_expense_automatic_conversion"]').is(':checked') == true){
        $('#div_expense_automatic_conversion').removeClass('hide');
      }else{
        $('#div_expense_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_tax_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_tax_automatic_conversion"]').is(':checked') == true){
        $('#div_tax_automatic_conversion').removeClass('hide');
      }else{
        $('#div_tax_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_pl_total_insurance_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_pl_total_insurance_automatic_conversion"]').is(':checked') == true){
        $('#div_pl_total_insurance_automatic_conversion').removeClass('hide');
      }else{
        $('#div_pl_total_insurance_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_pl_tax_paye_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_pl_tax_paye_automatic_conversion"]').is(':checked') == true){
        $('#div_pl_tax_paye_automatic_conversion').removeClass('hide');
      }else{
        $('#div_pl_tax_paye_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_pl_net_pay_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_pl_net_pay_automatic_conversion"]').is(':checked') == true){
        $('#div_pl_net_pay_automatic_conversion').removeClass('hide');
      }else{
        $('#div_pl_net_pay_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_wh_stock_import_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_wh_stock_import_automatic_conversion"]').is(':checked') == true){
        $('#div_wh_stock_import_automatic_conversion').removeClass('hide');
      }else{
        $('#div_wh_stock_import_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_wh_stock_import_return_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_wh_stock_import_return_automatic_conversion"]').is(':checked') == true){
        $('#div_wh_stock_import_return_automatic_conversion').removeClass('hide');
      }else{
        $('#div_wh_stock_import_return_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_wh_stock_export_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_wh_stock_export_automatic_conversion"]').is(':checked') == true){
        $('#div_wh_stock_export_automatic_conversion').removeClass('hide');
      }else{
        $('#div_wh_stock_export_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_wh_stock_export_profit_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_wh_stock_export_profit_automatic_conversion"]').is(':checked') == true){
        $('#div_wh_stock_export_profit_automatic_conversion').removeClass('hide');
      }else{
        $('#div_wh_stock_export_profit_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_wh_loss_adjustment_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_wh_loss_adjustment_automatic_conversion"]').is(':checked') == true){
        $('#div_wh_loss_adjustment_automatic_conversion').removeClass('hide');
      }else{
        $('#div_wh_loss_adjustment_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_wh_opening_stock_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_wh_opening_stock_automatic_conversion"]').is(':checked') == true){
        $('#div_wh_opening_stock_automatic_conversion').removeClass('hide');
      }else{
        $('#div_wh_opening_stock_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_pur_order_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_pur_order_automatic_conversion"]').is(':checked') == true){
        $('#div_pur_order_automatic_conversion').removeClass('hide');
      }else{
        $('#div_pur_order_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_pur_invoice_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_pur_invoice_automatic_conversion"]').is(':checked') == true){
        $('#div_pur_invoice_automatic_conversion').removeClass('hide');
      }else{
        $('#div_pur_invoice_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_pur_payment_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_pur_payment_automatic_conversion"]').is(':checked') == true){
        $('#div_pur_payment_automatic_conversion').removeClass('hide');
      }else{
        $('#div_pur_payment_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_mrp_manufacturing_order_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_mrp_manufacturing_order_automatic_conversion"]').is(':checked') == true){
        $('#div_mrp_manufacturing_order_automatic_conversion').removeClass('hide');
      }else{
        $('#div_mrp_manufacturing_order_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_pur_order_return_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_pur_order_return_automatic_conversion"]').is(':checked') == true){
        $('#div_pur_order_return_automatic_conversion').removeClass('hide');
      }else{
        $('#div_pur_order_return_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_pur_refund_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_pur_refund_automatic_conversion"]').is(':checked') == true){
        $('#div_pur_refund_automatic_conversion').removeClass('hide');
      }else{
        $('#div_pur_refund_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_omni_sales_order_return_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_omni_sales_order_return_automatic_conversion"]').is(':checked') == true){
        $('#div_omni_sales_order_return_automatic_conversion').removeClass('hide');
      }else{
        $('#div_omni_sales_order_return_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_omni_sales_refund_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_omni_sales_refund_automatic_conversion"]').is(':checked') == true){
        $('#div_omni_sales_refund_automatic_conversion').removeClass('hide');
      }else{
        $('#div_omni_sales_refund_automatic_conversion').addClass('hide');
      }
  });

  $('input[name="acc_fe_asset_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_fe_asset_automatic_conversion"]').is(':checked') == true){
        $('#div_fe_asset_automatic_conversion').removeClass('hide');
      }else{
        $('#div_fe_asset_automatic_conversion').addClass('hide');
      }
  });

   $('input[name="acc_fe_license_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_fe_license_automatic_conversion"]').is(':checked') == true){
        $('#div_fe_license_automatic_conversion').removeClass('hide');
      }else{
        $('#div_fe_license_automatic_conversion').addClass('hide');
      }
  });

    $('input[name="acc_fe_component_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_fe_component_automatic_conversion"]').is(':checked') == true){
        $('#div_fe_component_automatic_conversion').removeClass('hide');
      }else{
        $('#div_fe_component_automatic_conversion').addClass('hide');
      }
  });

     $('input[name="acc_fe_consumable_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_fe_consumable_automatic_conversion"]').is(':checked') == true){
        $('#div_fe_consumable_automatic_conversion').removeClass('hide');
      }else{
        $('#div_fe_consumable_automatic_conversion').addClass('hide');
      }
  });

      $('input[name="acc_fe_maintenance_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_fe_maintenance_automatic_conversion"]').is(':checked') == true){
        $('#div_fe_maintenance_automatic_conversion').removeClass('hide');
      }else{
        $('#div_fe_maintenance_automatic_conversion').addClass('hide');
      }
  });

       $('input[name="acc_fe_depreciation_automatic_conversion"]').on('change', function() {
      if($('input[name="acc_fe_depreciation_automatic_conversion"]').is(':checked') == true){
        $('#div_fe_depreciation_automatic_conversion').removeClass('hide');
      }else{
        $('#div_fe_depreciation_automatic_conversion').addClass('hide');
      }
  });

  init_item_automatic_table();
  init_expense_category_mapping_table();
  init_payment_mode_mapping_table();
  init_tax_mapping_table();
	init_payment_mode_mapping_table();

  var addMoreLadderInputKey = $('.list-payment-method-mapping #payment_method_mapping').length;
  $("body").on('click', '.new_item_ladder', function() {
    if ($(this).hasClass('disabled')) { return false; }

    addMoreLadderInputKey++;
    var newItem = $('.list-payment-method-mapping').find('#payment_method_mapping').eq(0).clone().appendTo('.list-payment-method-mapping');
    newItem.find('button[role="combobox"]').remove();
    newItem.find('select').selectpicker({
      showSubtext: true,
    });

    newItem.find('label[for="payment_mode[0]"]').attr('for', 'payment_mode[' + addMoreLadderInputKey + ']');
    newItem.find('select[name="payment_mode[0]"]').attr('name', 'payment_mode[' + addMoreLadderInputKey + ']');
    newItem.find('select[id="payment_mode[0]"]').attr('id', 'payment_mode[' + addMoreLadderInputKey + ']').selectpicker('refresh');

    newItem.find('label[for="payment_account_detail[0]"]').attr('for', 'payment_account_detail[' + addMoreLadderInputKey + ']');
    newItem.find('select[name="payment_account_detail[0]"]').attr('name', 'payment_account_detail[' + addMoreLadderInputKey + ']');
    newItem.find('select[id="payment_account_detail[0]"]').attr('id', 'payment_account_detail[' + addMoreLadderInputKey + ']').selectpicker({
      showSubtext: true,
    });

    newItem.find('label[for="deposit_to_detail[0]"]').attr('for', 'deposit_to_detail[' + addMoreLadderInputKey + ']');
    newItem.find('select[name="deposit_to_detail[0]"]').attr('name', 'deposit_to_detail[' + addMoreLadderInputKey + ']');
    newItem.find('select[id="deposit_to_detail[0]"]').attr('id', 'deposit_to_detail[' + addMoreLadderInputKey + ']').selectpicker({
      showSubtext: true,
    });

    newItem.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
    newItem.find('button[name="add"]').removeClass('new_item_ladder').addClass('remove_item_ladder').removeClass('btn-success').addClass('btn-danger');
  });

  $("body").on('click', '.remove_item_ladder', function() {
      $(this).parents('#payment_method_mapping').remove();
  });

  $('#edit-expense-category-mapping-modal input[name="preferred_payment_method"]').on('change', function() {
    if($('#edit-expense-category-mapping-modal input[name="preferred_payment_method"]').is(':checked') == true){
      $('#edit-expense-category-mapping-modal .list-payment-method-mapping').removeClass('hide');
    }else{
      $('#edit-expense-category-mapping-modal .list-payment-method-mapping').addClass('hide');
    }
  });

  $('#expense-category-mapping-modal input[name="preferred_payment_method"]').on('change', function() {
    if($('#expense-category-mapping-modal input[name="preferred_payment_method"]').is(':checked') == true){
      $('#expense-category-mapping-modal .list-payment-method-mapping').removeClass('hide');
    }else{
      $('#expense-category-mapping-modal .list-payment-method-mapping').addClass('hide');
    }
  });
})(jQuery);

function init_item_automatic_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-item-automatic')) {
     $('.table-item-automatic').DataTable().destroy();
  }
  initDataTable('.table-item-automatic', admin_url + 'accounting/item_automatic_table', false, false, fnServerParams, [0, 'desc']);
}

function init_expense_category_mapping_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-item-automatic')) {
     $('.table-item-automatic').DataTable().destroy();
  }
  initDataTable('.table-item-automatic', admin_url + 'accounting/item_automatic_table', false, false, fnServerParams, [0, 'desc']);
}

function init_expense_category_mapping_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-expense-category-mapping')) {
     $('.table-expense-category-mapping').DataTable().destroy();
  }
  initDataTable('.table-expense-category-mapping', admin_url + 'accounting/expense_category_mapping_table', false, false, fnServerParams, [0, 'desc']);
}

function init_tax_mapping_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-tax-mapping')) {
     $('.table-tax-mapping').DataTable().destroy();
  }
  initDataTable('.table-tax-mapping', admin_url + 'accounting/tax_mapping_table', false, false, fnServerParams, [0, 'desc']);
}

function init_payment_mode_mapping_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-payment-mode-mapping')) {
     $('.table-payment-mode-mapping').DataTable().destroy();
  }
  initDataTable('.table-payment-mode-mapping', admin_url + 'accounting/payment_mode_mapping_table', false, false, fnServerParams, [0, 'desc']);
}

function add_item_automatic(invoker) {
  "use strict";

  $('#item-automatic-modal').find('button[type="submit"]').prop('disabled', false);
  $('#item-automatic-modal').modal('show');
  $('#item-automatic-modal input[name="id"]').val('');
  $('#item-automatic-modal select[name="transfer_funds_from"]').val('').change();
  $('#item-automatic-modal select[name="transfer_funds_to"]').val('').change();
  $('#item-automatic-modal input[name="date"]').val('');
  $('#item-automatic-modal input[name="transfer_amount"]').val('');
}

function edit_item_automatic(invoker) {
  "use strict";

  	id = $(invoker).data('id');
	item_id = $(invoker).data('item-id');
	inventory_asset_account = $(invoker).data('inventory-asset-account');
	income_account = $(invoker).data('income-account');
	expense_account = $(invoker).data('expense-account');

    $('#edit-item-automatic-modal').find('button[type="submit"]').prop('disabled', false);
    $('#edit-item-automatic-modal input[name="id"]').val(id);
    $('#edit-item-automatic-modal select[name="item_id"]').val(item_id).change();
    $('#edit-item-automatic-modal select[name="inventory_asset_account"]').val(inventory_asset_account).change();
    $('#edit-item-automatic-modal select[name="income_account"]').val(income_account).change();
    $('#edit-item-automatic-modal select[name="expense_account"]').val(expense_account).change();

    $('#edit-item-automatic-modal').modal('show');
}


function item_automatic_form_handler(form) {
    "use strict";
    $('#item-automatic-modal').find('button[type="submit"]').prop('disabled', true);

    var formURL = form.action;
    var formData = new FormData($(form)[0]);

    $.ajax({
        type: $(form).attr('method'),
        data: formData,
        mimeType: $(form).attr('enctype'),
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response) {
        response = JSON.parse(response);
        if (response.success == 'close_the_book' || $.isNumeric(response.success)) {
          alert_float('warning', response.message);
        }else if (response.success === true || response.success == 'true' || $.isNumeric(response.success)) {
          alert_float('success', response.message);
          init_item_automatic_table();
        }else {
          alert_float('danger', response.message);
        }
        $('#item-automatic-modal').modal('hide');
        $('#edit-item-automatic-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}

function add_tax_mapping(invoker) {
  "use strict";

  $('#tax-mapping-modal').find('button[type="submit"]').prop('disabled', false);
  $('#tax-mapping-modal').modal('show');
  $('#tax-mapping-modal input[name="id"]').val('');
  $('#tax-mapping-modal select[name="tax[]"]').val('').change();
  $('#tax-mapping-modal select[name="payment_account"]').val($('#acc_tax_payment_account').val()).change();
  $('#tax-mapping-modal select[name="deposit_to"]').val($('#acc_tax_deposit_to').val()).change();
  $('#tax-mapping-modal select[name="expense_payment_account"]').val($('#acc_expense_tax_payment_account').val()).change();
  $('#tax-mapping-modal select[name="expense_deposit_to"]').val($('#acc_expense_tax_deposit_to').val()).change();
}

function edit_tax_mapping(invoker) {
  "use strict";

    id = $(invoker).data('id');
    tax_id = $(invoker).data('tax-id');
    payment_account = $(invoker).data('payment-account');
    deposit_to = $(invoker).data('deposit-to');
    expense_payment_account = $(invoker).data('expense-payment-account');
    expense_deposit_to = $(invoker).data('expense-deposit-to');
    purchase_payment_account = $(invoker).data('purchase-payment-account');
    purchase_deposit_to = $(invoker).data('purchase-deposit-to');

    $('#edit-tax-mapping-modal').find('button[type="submit"]').prop('disabled', false);
    $('#edit-tax-mapping-modal input[name="id"]').val(id);
    $('#edit-tax-mapping-modal select[name="tax_id"]').val(tax_id).change();
    $('#edit-tax-mapping-modal select[name="payment_account"]').val(payment_account).change();
    $('#edit-tax-mapping-modal select[name="deposit_to"]').val(deposit_to).change();
    $('#edit-tax-mapping-modal select[name="expense_payment_account"]').val(expense_payment_account).change();
    $('#edit-tax-mapping-modal select[name="expense_deposit_to"]').val(expense_deposit_to).change();
    $('#edit-tax-mapping-modal select[name="purchase_payment_account"]').val(purchase_payment_account).change();
    $('#edit-tax-mapping-modal select[name="purchase_deposit_to"]').val(purchase_deposit_to).change();

    $('#edit-tax-mapping-modal').modal('show');
}

function tax_mapping_form_handler(form) {
    "use strict";
    $('#tax-mapping-modal').find('button[type="submit"]').prop('disabled', true);
    $('#edit-tax-mapping-modal').find('button[type="submit"]').prop('disabled', true);

    var formURL = form.action;
    var formData = new FormData($(form)[0]);

    $.ajax({
        type: $(form).attr('method'),
        data: formData,
        mimeType: $(form).attr('enctype'),
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response) {
        response = JSON.parse(response);
        if (response.success == 'close_the_book' || $.isNumeric(response.success)) {
          alert_float('warning', response.message);
        }else if (response.success === true || response.success == 'true' || $.isNumeric(response.success)) {
          alert_float('success', response.message);
          init_tax_mapping_table();
        }else {
          alert_float('danger', response.message);
        }
        $('#tax-mapping-modal').modal('hide');
        $('#edit-tax-mapping-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}


function add_expense_category_mapping(invoker) {
  "use strict";

  $('#expense-category-mapping-modal').find('button[type="submit"]').prop('disabled', false);
  $('#expense-category-mapping-modal input[name="id"]').val('');
  $('#expense-category-mapping-modal select[name="category[]"]').val('').change();
  $('#expense-category-mapping-modal select[name="payment_account"]').val($('#acc_expense_payment_account').val()).change();
  $('#expense-category-mapping-modal select[name="deposit_to"]').val($('#acc_expense_deposit_to').val()).change();
  $('#expense-category-mapping-modal #preferred_payment_method').attr('checked', false).change();

  requestGetJSON(admin_url + 'accounting/get_list_payment_method_mapping').done(function(response) {
    $('#expense-category-mapping-modal .list-payment-method-mapping').html(response.html);
    init_selectpicker();
    $('#expense-category-mapping-modal').modal('show');
  });

}

function edit_expense_category_mapping(invoker) {
  "use strict";

    id = $(invoker).data('id');
    category_id = $(invoker).data('category-id');
    payment_account = $(invoker).data('payment-account');
    deposit_to = $(invoker).data('deposit-to');
    preferred_payment_method = $(invoker).data('preferred-payment-method') == 1 ? true : false;

    $('#edit-expense-category-mapping-modal').find('button[type="submit"]').prop('disabled', false);
    $('#edit-expense-category-mapping-modal input[name="id"]').val(id);
    $('#edit-expense-category-mapping-modal select[name="category_id"]').val(category_id).change();
    $('#edit-expense-category-mapping-modal select[name="payment_account"]').val(payment_account).change();
    $('#edit-expense-category-mapping-modal select[name="deposit_to"]').val(deposit_to).change();

    $('#edit-expense-category-mapping-modal #edit_preferred_payment_method').attr('checked', preferred_payment_method).change();

    requestGetJSON(admin_url + 'accounting/get_list_payment_method_mapping/'+category_id).done(function(response) {
      $('#edit-expense-category-mapping-modal .list-payment-method-mapping').html(response.html);
      init_selectpicker();
      $('#edit-expense-category-mapping-modal').modal('show');
    });

}

function expense_category_mapping_form_handler(form) {
    "use strict";
    $('#expense-category-mapping-modal').find('button[type="submit"]').prop('disabled', true);
    $('#edit-expense-category-mapping-modal').find('button[type="submit"]').prop('disabled', true);

    var formURL = form.action;
    var formData = new FormData($(form)[0]);

    $.ajax({
        type: $(form).attr('method'),
        data: formData,
        mimeType: $(form).attr('enctype'),
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response) {
        response = JSON.parse(response);
        if (response.success == 'close_the_book' || $.isNumeric(response.success)) {
          alert_float('warning', response.message);
        }else if (response.success === true || response.success == 'true' || $.isNumeric(response.success)) {
          alert_float('success', response.message);
          init_expense_category_mapping_table();
        }else {
          alert_float('danger', response.message);
        }
        $('#expense-category-mapping-modal').modal('hide');
        $('#edit-expense-category-mapping-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}

function add_payment_mode_mapping(invoker) {
  "use strict";

  $('#payment-mode-mapping-modal').find('button[type="submit"]').prop('disabled', false);
  $('#payment-mode-mapping-modal').modal('show');
  $('#payment-mode-mapping-modal input[name="id"]').val('');
  $('#payment-mode-mapping-modal select[name="payment_mode[]"]').val('').change();
  $('#payment-mode-mapping-modal select[name="payment_account"]').val($('#acc_payment_payment_account').val()).change();
  $('#payment-mode-mapping-modal select[name="deposit_to"]').val($('#acc_payment_deposit_to').val()).change();
  $('#payment-mode-mapping-modal select[name="expense_payment_account"]').val($('#acc_expense_payment_account').val()).change();
  $('#payment-mode-mapping-modal select[name="expense_deposit_to"]').val($('#acc_expense_deposit_to').val()).change();
  $('#payment-mode-mapping-modal select[name="credit_note_refund_payment_account"]').val($('#acc_credit_note_refund_payment_account').val()).change();
  $('#payment-mode-mapping-modal select[name="credit_note_refund_deposit_to"]').val($('#acc_credit_note_refund_deposit_to').val()).change();
}

function edit_payment_mode_mapping(invoker) {
  "use strict";

    id = $(invoker).data('id');
    payment_mode_id = $(invoker).data('payment-mode-id');
    payment_account = $(invoker).data('payment-account');
    deposit_to = $(invoker).data('deposit-to');
    expense_payment_account = $(invoker).data('expense-payment-account');
    expense_deposit_to = $(invoker).data('expense-deposit-to');
    credit_note_refund_payment_account = $(invoker).data('credit-note-refund-payment-account');
    credit_note_refund_deposit_to = $(invoker).data('credit-note-refund-deposit-to');

    $('#edit-payment-mode-mapping-modal').find('button[type="submit"]').prop('disabled', false);
    $('#edit-payment-mode-mapping-modal input[name="id"]').val(id);
    $('#edit-payment-mode-mapping-modal select[name="payment_mode_id"]').val(payment_mode_id).change();
    $('#edit-payment-mode-mapping-modal select[name="payment_account"]').val(payment_account).change();
    $('#edit-payment-mode-mapping-modal select[name="deposit_to"]').val(deposit_to).change();
    $('#edit-payment-mode-mapping-modal select[name="expense_payment_account"]').val(expense_payment_account).change();
    $('#edit-payment-mode-mapping-modal select[name="expense_deposit_to"]').val(expense_deposit_to).change();
    $('#edit-payment-mode-mapping-modal select[name="credit_note_refund_payment_account"]').val(credit_note_refund_payment_account).change();
    $('#edit-payment-mode-mapping-modal select[name="credit_note_refund_deposit_to"]').val(credit_note_refund_deposit_to).change();

    $('#edit-payment-mode-mapping-modal').modal('show');
}

function payment_mode_mapping_form_handler(form) {
    "use strict";
    $('#payment-mode-mapping-modal').find('button[type="submit"]').prop('disabled', true);
    $('#edit-payment-mode-mapping-modal').find('button[type="submit"]').prop('disabled', true);

    var formURL = form.action;
    var formData = new FormData($(form)[0]);

    $.ajax({
        type: $(form).attr('method'),
        data: formData,
        mimeType: $(form).attr('enctype'),
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response) {
        response = JSON.parse(response);
        if (response.success == 'close_the_book' || $.isNumeric(response.success)) {
          alert_float('warning', response.message);
        }else if (response.success === true || response.success == 'true' || $.isNumeric(response.success)) {
          alert_float('success', response.message);
          init_payment_mode_mapping_table();
        }else {
          alert_float('danger', response.message);
        }
        $('#payment-mode-mapping-modal').modal('hide');
        $('#edit-payment-mode-mapping-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}