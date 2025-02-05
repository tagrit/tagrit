<script type="text/javascript">
  var fnServerParams;
  (function($) {
    "use strict";

    fnServerParams = {
      "status": '[name="status"]',
      "type": '[name="type"]',
      "group": '[name="to_date"]',
    };


    $('select[name="status"]').on('change', function() {
      init_part_table();
    });

    $('select[name="type"]').on('change', function() {
      init_part_table();
    });

    $('select[name="group"]').on('change', function() {
      init_part_table();
    });

    init_part_table();

 
})(jQuery);

function init_part_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-parts')) {
    $('.table-parts').DataTable().destroy();
  }
  initDataTable('.table-parts', admin_url + 'fleet/parts_table', [0], [0], fnServerParams, [1, 'desc']);
  $('.dataTables_filter').addClass('hide');
}

</script>

