<script type="text/javascript">
var fnServerParams;
var beginning_balance = <?php echo new_html_entity_decode($reconcile->beginning_balance); ?>;
var ending_balance = <?php echo new_html_entity_decode($reconcile->ending_balance); ?>;
var difference = 0;

(function($) {
    "use strict";
    // Init accountacy currency symbol
    acc_init_currency();

    fnServerParams = {
        "account": '[name="account"]',
        "reconcile": '[name="reconcile"]',
    };

  appValidateForm($('#adjustment-form'), {
      adjustment_date: 'required',
      }, adjustment_form_handler);

  appValidateForm($('#edit-reconcile-form'),{ending_balance:'required', ending_date:'required'});

  init_history_table();

  $("body").on('change', '#mass_select_all_a', function() {
        var to, rows, checked;
        to = $(this).data('to-table');

        rows = $('.table-' + to).find('tbody tr');
        checked = $(this).prop('checked');
        $.each(rows, function() {
            $($(this).find('td').eq(0)).find('input').prop('checked', checked);
        });
    });
})(jQuery);

function init_history_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-reconcile-history')) {
     $('.table-reconcile-history').DataTable().destroy();
  }
  var _table = initDataTable('.table-reconcile-history', admin_url + 'accounting/reconcile_history_table', false, [0], fnServerParams, [1, 'desc']);
}

function calculate(){
    "use strict";
    var history_ids = '';
    var payment = 0;
    var deposit = 0;
    var count_payment = 0;
    var count_deposit = 0;

    var rows = $('.table-reconcile-history').find('tbody tr');
    $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input');
        if (checkbox.prop('checked') == true) {
            if(parseFloat(checkbox.data('payment')) > 0){
              count_payment++;
              payment = payment + parseFloat(checkbox.data('payment'));
            }
            if(parseFloat(checkbox.data('deposit')) > 0){
              deposit = deposit + parseFloat(checkbox.data('deposit'));
              count_deposit++;
            }
            if(history_ids == ''){
               history_ids = checkbox.val();
            }else{
               history_ids += ', ' + checkbox.val();
            }
        }
    });
    
    $('#count_payment').html(count_payment + ' <?php echo _l('payments_uppercase'); ?>');
    $('#count_deposit').html(count_deposit + ' <?php echo _l('deposits_uppercase'); ?>');
    $('#payment_amount').html(format_money(payment));
    $('#deposit_amount').html(format_money(deposit));
    $('#cleared_balance_amount').html(format_money(beginning_balance - payment + deposit));
    difference = ending_balance - (beginning_balance - payment + deposit);
    $('#difference_amount').html(format_money(ending_balance - (beginning_balance - payment + deposit)));
    $('input[name="history_ids"]').val(history_ids);
}
function edit_info(){
  "use strict";
    $('#edit-info-modal').modal('show'); 
}

function save_for_later(){
  "use strict";
    calculate();
    $('input[name="finish"]').val(0);
    $('#reconcile-account-form').submit();
}

function finish_now(){
  "use strict";
    calculate();
    $('input[name="finish"]').val(1);
    
    if(difference == 0){
        $('#reconcile-account-form').submit();
    }else{
        $('#adjustment-modal').modal('show');
    }
}


function adjustment_form_handler(form) {
    "use strict";
    $('#adjustment-modal').find('button[type="submit"]').prop('disabled', true);

    $('input[name="adjustment_amount"]').val(difference);
    $('input[name="finish"]').val(1);

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
        if (response.success === 'close_the_book') {
          alert_float('warning', response.message);
          $('#adjustment-modal').find('button[type="submit"]').prop('disabled', false);
        }else if (response.success === true || response.success == 'true' || $.isNumeric(response.success)) {
          $('#reconcile-account-form').submit();
        }else{
          alert_float('danger', response.message);
        }
        $('#adjustment-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}

// Set the currency for accounting
function acc_init_currency() {
  "use strict";
  var selectedCurrencyId = <?php echo new_html_entity_decode($currency->id); ?>;

  requestGetJSON('misc/get_currency/' + selectedCurrencyId)
      .done(function(currency) {
          // Used for formatting money
          accounting.settings.currency.decimal = currency.decimal_separator;
          accounting.settings.currency.thousand = currency.thousand_separator;
          accounting.settings.currency.symbol = currency.symbol;
          accounting.settings.currency.format = currency.placement == 'after' ? '%v %s' : '%s%v';
          calculate();
      });
}
</script>
