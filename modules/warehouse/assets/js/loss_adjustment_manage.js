(function($){
"use strict";
  init_datepicker();
  init_selectpicker();

   var fnServerParams = {  
     "time_filter": "[name='time_filter']",
     "status_filter": "[name='status_filter']",
     "date_create": "[name='date_create']",
     "type_filter": "[name='type_filter']"
   }

     initDataTable('.table-loss_adjustment', admin_url + 'warehouse/loss_adjustment_table', false, false, fnServerParams, [0, 'desc']);
  
  

  $('select[name="type_filter"],select[name="status_filter"]').on('change', function() {
     $('.table-loss_adjustment').DataTable().ajax.reload();
  });
  
})(jQuery);

function filter_date(el){
  "use strict";
  $('.table-loss_adjustment').DataTable().ajax.reload();

}

