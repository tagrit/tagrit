<script>
var commission_table,
report_from_choose,
fnServerParams,
commission_chart,
statistics_cost_of_purchase_orders;
(function($) {
  "use strict";

  commission_table = $('#commission_table');
  commission_chart = $('#commission-chart');
  report_from_choose = $('#report-time');
  fnServerParams = {
    "products_services": '[name="products_services"]',
    "staff_filter": '[name="staff_filter"]',
    "client_filter": '[name="client_filter"]',
    "products_services_chart": '[name="products_services_chart"]',
    "staff_filter_chart": '[name="staff_filter_chart"]',
    "client_filter_chart": '[name="client_filter_chart"]',
    "report_months": '[name="months-report"]',
    "year_requisition": "[name='year_requisition']",
    "is_client": "[name='is_client']",
  }

     gen_reports();
})(jQuery);


// Main generate report function
function gen_reports() {
  "use strict";
 if (!commission_table.hasClass('hide')) {
 }
}

</script>


