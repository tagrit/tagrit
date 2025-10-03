var fnServerParams;
(function($) {
  "use strict";

    fnServerParams = {
      "is_report": '[name="is_report"]',
    };
    $.get(admin_url + 'fleet/get_data_total_cost_trend_chart').done(function(res) {
      res = JSON.parse(res);

      Highcharts.chart('container_chart', {
        chart: {
            type: 'area'
        },
        title: {
            text: 'Total Cost'
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
        series: res.data_total_cost_trend
      });
    });
})(jQuery);
