<script>
var fnCheckParams = {
        "bank_account_check": '[name="bank_account_check"]',
        "vendor_ft": '[name="vendor_ft"]',
        "from_date_ft": '[name="from_date_ft"]',
        "to_date_ft": '[name="to_date_ft"]',
        "status" : '[name="status"]'
};
var fnChecksToPrintParams = {
        "bank_account_form_check": '[name="bank_account_form_check"]',
        "first_check_number": '[name="first_check_number"]',
        "print_again": '[name="print_again"]',
};

var hidden_columns = [];

(function($) {
  "use strict";
  $('li.sub-menu-item-accounting_checks').addClass('active');

    $('a').click(function() {
        $(window).unbind('beforeunload');
    });

    init_checks();
    init_bills_table();
    init_checks_table();

    $('select[name="bank_account_check"]').on('change', function(){
        var account = $(this).val();
        if(account != '' && account != null && account != undefined){
          requestGetJSON(admin_url + 'accounting/get_balance_check/'+account).done(function(response) { 
            $('input[name="ending_balance"]').val(format_money(response.balance));
          });
        }else{
            $('input[name="ending_balance"]').val(format_money(0));
        }

        init_bills_table();
        init_checks_table();
    });

     $('select[name="vendor_ft"]').on('change', function(){
        init_checks_table();
     });

     $('select[name="status"]').on('change', function(){
        init_checks_table();
     });


     $('input[name="from_date_ft"]').on('change', function() {
        init_checks_table();
    });
    $('input[name="to_date_ft"]').on('change', function() {
        init_checks_table();
    });

    $('select[name="bank_account_form_check"]').on('change', function(){
        init_checks_to_print_table();
    });

    $("body").on('click', '.table-checks-to-print .checkbox', function() {
        setTimeout(function() {
            calculate_check_to_print_total();
        }, 200);
    });

    appValidateForm($('#reprint_check'), {
        'reprint_check[]':'required',
        'new_check_number':'required',

      },reprint_check_submit);

    $('input[name="is_new_check_number"]').on('change', function() {
        if($('input[name="is_new_check_number"]').is(':checked') == true){
          $('.div_new_check_number').removeClass('hide');
        }else{
          $('.div_new_check_number').addClass('hide');
        }
    });

})(jQuery);

// Init single invoice
function init_checks(id) {
    check_load_small_table_item(id, '#check', 'checkid', 'accounting/get_check_data_ajax', '.table-checks');
    
}

function init_checks_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-checks')) {
    $('.table-checks').DataTable().destroy();
  }
  initDataTable('.table-checks', admin_url + 'accounting/checks_table', [], [], fnCheckParams, [0, 'desc']);
}

function init_bills_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-bills')) {
    $('.table-bills').DataTable().destroy();
  }
  initDataTable('.table-bills', admin_url + 'accounting/bills_in_check_table', [], [], fnCheckParams, []);
}

function check_load_small_table_item(id, selector, input_name, url, table) {
    var _tmpID = $('input[name="' + input_name + '"]').val();
    // Check if id passed from url, hash is prioritized becuase is last
    if (_tmpID !== '' && !window.location.hash) {
        id = _tmpID;
        // Clear the current id value in case user click on the left sidebar credit_note_ids
        $('input[name="' + input_name + '"]').val('');
    } else {
        // check first if hash exists and not id is passed, becuase id is prioritized
        if (window.location.hash && !id) {
            id = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
        }
    }
    if (typeof (id) == 'undefined' || id === '' || !$.isNumeric(id)) {
        return;
    }
    destroy_dynamic_scripts_in_element($(selector))
    if (!$("body").hasClass('small-table')) {
        check_toggle_small_view(table, selector);
    }
    $('input[name="' + input_name + '"]').val(id);
    do_hash_helper(id);
    $(selector).load(admin_url + url + '/' + id);
    if (is_mobile()) {
        $('html, body').animate({
            scrollTop: $(selector).offset().top + 150
        }, 600);
    }
}

// Show/hide full table
function check_toggle_small_view(table, main_data) {

    $("body").toggleClass('small-table');
    var tablewrap = $('#small-table');
    if (tablewrap.length === 0) {
        return;
    }
    var _visible = false;
    if (tablewrap.hasClass('col-md-3')) {
        tablewrap.removeClass('col-md-3').addClass('col-md-12');
        _visible = true;
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-right').addClass('fa fa-angle-double-left');
    } else {
        tablewrap.addClass('col-md-3').removeClass('col-md-12');
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-left').addClass('fa fa-angle-double-right');
    }
    var _table = $(table).DataTable();
    // Show hide hidden columns
    _table.columns(hidden_columns).visible(_visible, false);
    _table.columns.adjust();
    $(main_data).toggleClass('hide');
    $(window).trigger('resize');
}

function print_form(){
    if($('select[name="bank_account_check"]').val() != ''){
        $('select[name="bank_account_form_check"]').val($('select[name="bank_account_check"]').val());
        $('select[name="bank_account_form_check"]').change();
    }

	$('#checks-to-print-modal').modal('show');
    init_checks_to_print_table();
}


function download_checks() {
  "use strict";
  
  $('#download_checks').submit();
}


function print_check(issue){
    var html_success = '<iframe id="content_print" class="w100" name="content_print"></iframe>';
    var ids = [];
    var data = {};
    var rows = $('.table-checks-to-print').find('tbody tr');
    var bank_account = $('select[name="bank_account_form_check"]').val();

    $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input');
        if (checkbox.prop('checked') === true) {
            ids.push(checkbox.val());

            if(issue == true){
                $(this).remove();
            }
        }
    });

    if(ids.length > 0){
        data.ids = ids;
        data.issue = issue;
        data.bank_account = bank_account;
        $.post(admin_url + 'accounting/print_check', data).done(function(response){ 
          response = JSON.parse(response); 
            if(navigator.userAgent.indexOf("Firefox") != -1 ){
                var mywindow = window.open('', 'Print checks');
              mywindow.document.write(response.html);

              mywindow.document.close();
              mywindow.focus()
              mywindow.print();
              mywindow.close();
            }else{
                $('.content_cart').html(html_success);
                $("#content_print").contents().find('body').html(response.html);
                $("#content_print").contents().find('body').attr('style','text-align: center');
                $("#content_print").get(0).contentWindow.print();
            }
            
        });
    }else{
        alert_float('warning', 'No checks have been selected yet');
    }
}

function init_checks_to_print_table() {
    "use strict";

    if ($.fn.DataTable.isDataTable('.table-checks-to-print')) {
        $('.table-checks-to-print').DataTable().destroy();
    }
    initDataTable('.table-checks-to-print', admin_url + 'accounting/select_checks_to_print_table', [], [0], fnChecksToPrintParams, []);
    $('input[name="print_again"]').val(0);
}


function calculate_check_to_print_total(){
    "use strict";
    var total_amount = 0;

    var rows = $('.table-checks-to-print').find('tbody tr');
    $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input');
        if (checkbox.is(":checked") == true) {
            total_amount = total_amount + parseFloat(checkbox.data('amount'));
        }
    });
    
    $('#checks-to-print-total-amount').html(format_money(total_amount));
}

function select_all (){
    var rows = $('.table-checks-to-print').find('tbody tr');
    $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input').prop('checked', true);
    });

    $('#mass_select_all').prop('checked', true);

    calculate_check_to_print_total();
}

function select_none (){
    var rows = $('.table-checks-to-print').find('tbody tr');
    $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input').prop('checked', false);
    });

    $('#mass_select_all').prop('checked', false);

    calculate_check_to_print_total();
}

function clear_print_later(){
    var account = $('select[name="bank_account_form_check"]').val();
    requestGetJSON(admin_url + 'accounting/clear_print_later/' + account).done(function (response) {
        if(response.success != false){
            alert_float('success', response.message); 
            init_checks_to_print_table();
            $('#checks-to-print-total-amount').html(format_money(0));
        }
    });

}


function print_again(){
  $('input[name="print_again"]').val(1);

  var conf = confirm('<?php echo _l('re_printing_confirm'); ?>');
  if(conf == true){
    init_checks_to_print_table();
  }
  calculate_check_to_print_total();
}

function reprint_check(){

    requestGetJSON(admin_url + 'accounting/get_next_check_number').done(function (response) {
        $('#reprint-check-modal input[name="new_check_number"]').val(response);
        $('#reprint-check-modal select[name="reprint_check[]"]').val('').change();
        $('#reprint-check-modal').find('button[type="submit"]').prop('disabled', false);
        $('#checks-to-print-modal').modal('hide');
        $('#reprint-check-modal').modal('show');
    });
}


function reprint_check_submit(form){
    var html_success = '<iframe id="content_print" class="w100" name="content_print"></iframe>';
    $('#reprint-check-modal').find('button[type="submit"]').prop('disabled', true);
    
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
        if(navigator.userAgent.indexOf("Firefox") != -1 ){
            var mywindow = window.open('', 'Print checks');
          mywindow.document.write(response.html);

          mywindow.document.close();
          mywindow.focus()
          mywindow.print();
          mywindow.close();
        }else{
            $('.content_cart').html(html_success);
            $("#content_print").contents().find('body').html(response.html);
            $("#content_print").contents().find('body').attr('style','text-align: center');
            $("#content_print").get(0).contentWindow.print();
        }

    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });
}


// Show/hide full table
function check_toggle_small_view(table, main_data) {

    $("body").toggleClass('small-table');
    var tablewrap = $('#small-table');
    if (tablewrap.length === 0) {
        return;
    }
    var _visible = false;
    if (tablewrap.hasClass('col-md-3')) {
        tablewrap.removeClass('col-md-3').addClass('col-md-12');
        _visible = true;
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-right').addClass('fa fa-angle-double-left');
    } else {
        tablewrap.addClass('col-md-3').removeClass('col-md-12');
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-left').addClass('fa fa-angle-double-right');
    }
    var _table = $(table).DataTable();
    // Show hide hidden columns
    _table.columns(hidden_columns).visible(_visible, false);
    _table.columns.adjust();
    $(main_data).toggleClass('hide');
    $(window).trigger('resize');
}

function void_check(checkId){
    $('#void-check-modal textarea[name="reason_for_void"]').val('');
    $('#void-check-modal input[name="void_check"]').val(checkId);
    $('#void-check-modal').find('button[type="submit"]').prop('disabled', false);
    $('#void-check-modal').modal('show');
}

function void_check_save(){
    var data = {};
    data.check_id = $('#void-check-modal input[name="void_check"]').val();
    data.reason_for_void = $('#void-check-modal textarea[name="reason_for_void"]').val();

    $.post(admin_url + 'accounting/void_check', data).done(function(response){
        response = JSON.parse(response);

        if(response.success == true || response.success == 'true'){
            alert_float('success', response.message);
            init_checks($('#void-check-modal textarea[name="void_check"]').val());
        }else{
            alert_float('warning', response.message);
        }
        
        $('#void-check-modal').modal('hide');
    });
}



</script>