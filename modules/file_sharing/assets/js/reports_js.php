<script>
var data = {};
var report_from_choose,
fnServerParams,
sharing_chart,
download_chart;

(function($) {
  "use strict";
  sharing_chart = $('#sharing-chart');
  download_chart = $('#download-chart');
  report_from_choose = $('#report-time');
  $('select[name="year_requisition"]').on('change', function() {
    gen_reports();
  });
  $('select[name="type"]').on('change', function() {
    gen_reports();
  });
  $('select[name="staff_filter"]').on('change', function() {
    gen_reports();
  });
  $('select[name="hash_share"]').on('change', function() {
    gen_reports();
  });

})(jQuery);

function init_report(e, type) {
  "use strict";

   var report_wrapper = $('#report');

   if (report_wrapper.hasClass('hide')) {
        report_wrapper.removeClass('hide');
   }

   $('head title').html($(e).text());


   report_from_choose.addClass('hide');

   $('#year_requisition').addClass('hide');
   $('#div_staff_filter').addClass('hide');
   $('#div_type_filter').addClass('hide');
   $('#div_hash_filter').addClass('hide');

    download_chart.addClass('hide');
    sharing_chart.addClass('hide');

  $('select[name="months-report"]').selectpicker('val', 'this_month');
    // Clear custom date picker
      $('#currency').removeClass('hide');

      if (type != 'download_chart' && type != 'sharing_chart') {
        report_from_choose.removeClass('hide');
      }
      if(type == 'sharing_chart'){
        sharing_chart.removeClass('hide');
        $('#year_requisition').removeClass('hide');
        $('#div_staff_filter').removeClass('hide');
        $('#div_type_filter').removeClass('hide');
        $('#div_hash_filter').removeClass('hide');
      }else if(type == 'download_chart'){

        download_chart.removeClass('hide');
        $('#year_requisition').removeClass('hide');
        $('#div_staff_filter').removeClass('hide');
        $('#div_type_filter').addClass('hide');
        $('#div_hash_filter').removeClass('hide');
      }

      gen_reports();
}

// Main generate report function
function gen_reports() {
  "use strict";

 if (!download_chart.hasClass('hide')) {
    init_download_chart();
 }else if (!sharing_chart.hasClass('hide')) {
    init_sharing_chart();
 }
}
function init_download_chart() {
  "use strict";
var canvas = document.getElementById("download_chart");
  var data = {};

  data.year = $('select[name="year_requisition"]').val();
  data.type = $('select[name="type"]').val();
  data.staff_filter = $('select[name="staff_filter"]').val();
  data.hash_share = $('select[name="hash_share"]').val();

  $.post(admin_url + 'file_sharing/download_chart/', data).done(function(response) {
     response = JSON.parse(response);
    Highcharts.setOptions({
      chart: {
          style: {
              fontFamily: 'inherit !important',
              fill: 'black'
          }
      },
      colors: [ '#119EFA','#ef370dc7','#15f34f','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B']
     });
        Highcharts.chart('download_chart', {
         chart: {
             type: 'column'
         },
         title: {
             text: '<?php echo _l("downloads_chart"); ?>'
         },
         subtitle: {
             text: ''
         },
         credits: {
            enabled: false
          },
         xAxis: {
             categories: response.month,
             crosshair: true,
         },
         yAxis: {
             min: 0,
             title: {
              text: ''
             }
         },
         tooltip: {
             headerFormat: '<span>{point.key}</span><table>',
             pointFormat: '<tr>' +
                 '<td><b>{point.y:.0f} {series.name}</b></td></tr>',
             footerFormat: '</table>',
             shared: true,
             useHTML: true
         },
         plotOptions: {
             column: {
                 pointPadding: 0.2,
                 borderWidth: 0
             }
         },

         series: [{
            type: 'column',
            colorByPoint: true,
            name: '',
            data: response.data,
            showInLegend: false
         }]
     });
        
  })
}
function init_sharing_chart() {
  "use strict";
var canvas = document.getElementById("sharing_chart");
  var data = {};
  data.year = $('select[name="year_requisition"]').val();
  data.type = $('select[name="type"]').val();
  data.staff_filter = $('select[name="staff_filter"]').val();
  data.hash_share = $('select[name="hash_share"]').val();

  $.post(admin_url + 'file_sharing/sharing_chart/', data).done(function(response) {
     response = JSON.parse(response);
    Highcharts.setOptions({
      chart: {
          style: {
              fontFamily: 'inherit !important',
              fill: 'black'
          }
      },
      colors: [ '#119EFA','#ef370dc7','#15f34f','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B']
     });
        Highcharts.chart('sharing_chart', {
         chart: {
             type: 'column'
         },
         title: {
             text: '<?php echo _l("sharing_chart"); ?>'
         },
         subtitle: {
             text: ''
         },
         credits: {
            enabled: false
          },
         xAxis: {
             categories: response.month,
             crosshair: true,
         },
         yAxis: {
             min: 0,
             title: {
              text: ''
             }
         },
         tooltip: {
             headerFormat: '<span>{point.key}</span><table>',
             pointFormat: '<tr>' +
                 '<td><b>{point.y:.0f} {series.name}</b></td></tr>',
             footerFormat: '</table>',
             shared: true,
             useHTML: true
         },
         plotOptions: {
             column: {
                 pointPadding: 0.2,
                 borderWidth: 0
             }
         },

         series: [{
            type: 'column',
            colorByPoint: true,
            name: '',
            data: response.data,
            showInLegend: false
         }]
     });
        
  })
}
</script>


