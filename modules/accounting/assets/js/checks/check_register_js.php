<script>
var fnCheckParams = {
        "bank_account_check": '[name="bank_account_check"]',
        "status": '[name="status"]',
        "from_date": '[name="from_date"]',
        "to_date": '[name="to_date"]',
};

var hidden_columns = [];

(function($) {
  "use strict";
    $('li.sub-menu-item-accounting_checks').addClass('active');

    $('a').click(function() {
        $(window).unbind('beforeunload');
    });

    init_check_register_table();

    $('select[name="bank_account_check"]').on('change', function(){
        init_check_register_table();
    });

    $('select[name="status"]').on('change', function(){
        init_check_register_table();
    });

    $('input[name="from_date"]').on('change', function(){
        init_check_register_table();
    });

    $('input[name="to_date"]').on('change', function(){
        init_check_register_table();
    });

})(jQuery);

function init_check_register_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-checks')) {
    $('.table-checks').DataTable().destroy();
  }
  initDataTable('.table-checks', admin_url + 'accounting/check_register_table', [], [], fnCheckParams, []);
}


</script>