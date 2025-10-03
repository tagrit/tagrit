var fnServerParams;
(function($) {
    "use strict";

  fnServerParams = {
      "status": '[name="status"]',
      "software": '[name="software"]',
      "transaction_type": '[name="transaction_type"]',
      "type": '[name="type"]',
      "from_date": '[name="from_date"]',
      "to_date": '[name="to_date"]',
      "organization": '[name="organization"]',
    };

  init_sync_logs_table();

  $('select[name="organization"]').on('change', function() {
    init_sync_logs_table();
  });
  
  $('select[name="status"]').on('change', function() {
    init_sync_logs_table();
  });

  $('select[name="transaction_type"]').on('change', function() {
    init_sync_logs_table();
  });

  $('select[name="type"]').on('change', function() {
    init_sync_logs_table();
  });

  $('input[name="from_date"]').on('change', function() {
    init_sync_logs_table();
  });

  $('input[name="to_date"]').on('change', function() {
    init_sync_logs_table();
  });
})(jQuery);

function init_sync_logs_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-sync-logs')) {
     $('.table-sync-logs').DataTable().destroy();
  }
  initDataTable('.table-sync-logs', admin_url + 'sage_accounting_integration/sync_logs_table', [0], [0], fnServerParams, [4, 'desc']);
}
