<script>
    var hidden_columns = [5];
    Dropzone.autoDiscover = false;
    var Expenses_ServerParams = {
        "type": '[name="type"]',
        "from_date": '[name="from_date"]',
        "to_date": '[name="to_date"]',
        "type": "select[name='type']",
        "vendor_id": "select[name='vendor_id[]']",

    };

(function($) {
  "use strict";

    $('li.menu-item-accounting_expenses').addClass('active');
    $('li.sub-menu-item-accounting_bills').addClass('active');

    appValidateForm($('#void-bill-form'), {
      },void_bill_form_handler);

     init_bill_table();
     init_bills();

    $('input[name="from_date"]').on('change', function() {
      init_bill_table();
    });

    $('input[name="to_date"]').on('change', function() {
      init_bill_table();
    });

    $(' select[name="type"], select[name="vendor_id[]"]').on('change', function() {
        init_bill_table();
    });


})(jQuery);


function init_bill_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-bills')) {
    $('.table-bills').DataTable().destroy();
  }

  if($('input[name="type"]').val() == 'approved'){
    $('.btn_pay_bills').remove();
    initDataTable('.table-bills', admin_url+'accounting/bills_table', [], [0], Expenses_ServerParams, [3, 'desc']).columns([1,5,7]).visible(false, false).columns.adjust();
    $('.dataTables_length').parents('.row').eq(0).after('<div class="row"><div class="col-md-12"><a href="#" onclick="pay_bills(); return false;" class="btn btn-default btn_pay_bills" ><?php echo _l('acc_pay_bills'); ?></a> <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('acc_pay_bills_to_vendor'); ?>"></i></div></div>');
  }else if($('input[name="type"]').val() == 'paid'){
    initDataTable('.table-bills', admin_url+'accounting/bills_table', [], [], Expenses_ServerParams, [3, 'desc']).columns([0,1]).visible(false, false).columns.adjust();

  }else{
    initDataTable('.table-bills', admin_url+'accounting/bills_table', [], [0], Expenses_ServerParams, [3, 'desc']).columns([1, 5, 7]).visible(false, false).columns.adjust();
    $('.dataTables_length').parents('.row').eq(0).after('<div class="row"><div class="col-md-6"><a href="#" onclick="bulk_approve(); return false;" class="btn btn-default btn_pay_bills mright5" ><?php echo _l('bulk_approve'); ?></a><a href="#" onclick="pay_bills(); return false;" class="btn btn-default btn_pay_bills" ><?php echo _l('acc_pay_bills'); ?></a> <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('acc_pay_bills_to_vendor'); ?>"></i></div></div>');
  }
}

function init_bills(id) {
    "use strict";

    bill_load_small_table_item(id, '#bill_div', 'billid', 'accounting/get_bill_data_ajax', '.table-bills');
}

function bill_load_small_table_item(id, selector, input_name, url, table) {
    "use strict";
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
    if (typeof (id) == 'undefined' || id === '') {
        return;
    }
    destroy_dynamic_scripts_in_element($(selector))
    if (!$("body").hasClass('small-table')) {
        bill_toggle_small_view(table, selector);
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
function bill_toggle_small_view(table, main_data) {
    "use strict";

    $("body").toggleClass('small-table');
    var tablewrap = $('#small-table');
    if (tablewrap.length === 0) {
        return;
    }
    var _visible = false;
    if (tablewrap.hasClass('col-md-5')) {
        tablewrap.removeClass('col-md-5').addClass('col-md-12');
        _visible = true;
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-right').addClass('fa fa-angle-double-left');
    } else {
        tablewrap.addClass('col-md-5').removeClass('col-md-12');
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-left').addClass('fa fa-angle-double-right');
    }
    var _table = $(table).DataTable();
    // Show hide hidden columns
    _table.columns(hidden_columns).visible(_visible, false);
    _table.columns.adjust();
    $(main_data).toggleClass('hide');
    $(window).trigger('resize');
}

function approve_payable(billid) {
  "use strict";

  requestGetJSON(admin_url + 'accounting/bill_appove_payable/' + billid).done(function (response) {
        if(response.success != false){
            init_bill_table();
            alert_float('success', response.message); 
        }else{
            alert_float('warning', response.message); 
        }
    });
}

function void_bill(billid) {
  "use strict";
    $('#void-bill-modal').find('button[type="submit"]').prop('disabled', false);

    $('#void-bill-modal input[name="id"]').val(billid);
    $('#void-bill-modal textarea[name="reason_for_void"]').val('');

    $('#void-bill-modal').modal('show');

    // requestGetJSON(admin_url + 'accounting/void_bill/' + billid).done(function (response) {
    //     if(response.success != false){
    //         init_bill_table();
    //         alert_float('success', response.message); 
    //     }
    // });
}

function delete_bill(billid) {
  "use strict";
   
    requestGetJSON(admin_url + 'accounting/delete_bill_ajax/' + billid).done(function (response) {
        if(response.success != false){
            init_bill_table();
            alert_float('success', response.message); 
        }else{
            alert_float('warning', response.message); 
        }
    });
}

function pay_bills(){
    "use strict";
    var rows = $('.table-bills').find('tbody tr');
    var ids = [];
    var vendor_ids = '';
    var check_vendor = 0;
    $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input');
        if (checkbox.prop('checked') === true) {
            if(vendor_ids == ''){
                vendor_ids = checkbox.data('vendor');
            }else{
                if(vendor_ids != checkbox.data('vendor')){
                    alert_float('warning', '<?php echo _l('acc_select_bill_of_vendor'); ?>'); 
                    check_vendor = 1;
                }
            }
            ids.push(checkbox.val());


        }
    });

    if(ids.length == 0){
        alert_float('warning', '<?php echo _l('acc_select_bill'); ?>'); 
    }else{
        if(check_vendor == 1){
            alert_float('warning', '<?php echo _l('acc_select_bill_of_vendor'); ?>');
        }else{
            $('input[name="bill_ids"]').val(ids.toString());
            $('#pay_bill-form').submit();  
        }
    }

}


function bulk_approve(){
    "use strict";
    var rows = $('.table-bills').find('tbody tr');
    var ids = [];
    $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input');
        if (checkbox.prop('checked') === true) {
            ids.push(checkbox.val());
        }
    });

    if(ids.length == 0){
        alert_float('warning', '<?php echo _l('acc_select_bill'); ?>'); 
    }else{
        var data = {};
        data.is_bulk_action = 1;
        data.bill_ids = ids;

        $.post(admin_url + 'accounting/bill_appove_payable', data).done(function (response) {
            response = JSON.parse(response);

            if(response.success != false){
                init_bill_table();
                alert_float('success', response.message); 
            }else{
                alert_float('warning', response.message); 
            }
        });
    }
}

function void_bill_form_handler(form) {
    "use strict";
    $('#void-bill-modal').find('button[type="submit"]').prop('disabled', true);
    
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
        if (response.success === true || response.success == 'true' || $.isNumeric(response.success)) {
            alert_float('success', response.message);

          init_bill_table();
        }else{
          alert_float('warning', response.message);
        }
        $('#void-bill-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}

</script>