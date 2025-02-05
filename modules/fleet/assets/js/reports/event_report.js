var fnServerParams;
(function($) {
  "use strict";

    fnServerParams = {
      "is_report": '[name="is_report"]',
    };
    $.get(admin_url + 'fleet/get_data_event_chart').done(function(res) {
    res = JSON.parse(res);

    Highcharts.chart('container_chart', {
      chart: {
          type: 'area'
      },
      title: {
          text: 'Event Stats'
      },
      time: {
            timezone: $('input[name=timezone]').val()
        },
      xAxis: {
          type: 'datetime',
          labels: {
              format: '{value:%Y-%m-%d}',
              rotation: 45,
              align: 'left'
          }
      },
      yAxis: {
          title: {
              text: ''
          }
      },
      credits: {
          enabled: false
      },
      series: res.data_event
    });

    Highcharts.chart('container_task', {
    chart: {
      type: 'pie',
      options3d: {
        enabled: true,
        alpha: 45
      }
    },
    title: {
      text: 'Statistics by Event Type'
    },
    plotOptions: {
      pie: {
        innerSize: 100,
        depth: 45
      }
    },
    credits: {
        enabled: false
    },
    series: [{
        innerSize: '20%',
        name: 'Total',
        data: res.data_event_stats
      }]
  });
  });

  init_email_log_table();
})(jQuery);

function init_email_log_table() {
"use strict";

 if ($.fn.DataTable.isDataTable('.table-email-logs')) {
   $('.table-email-logs').DataTable().destroy();
 }
 initDataTable('.table-email-logs', admin_url + 'fleet/events_table', false, false, fnServerParams, [3, 'desc']);
}