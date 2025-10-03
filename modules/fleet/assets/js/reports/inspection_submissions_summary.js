(function($) {
  "use strict";

$.get(admin_url + 'fleet/get_data_inspection_submissions_summary_chart').done(function(res) {
  res = JSON.parse(res);

  Highcharts.chart('container_chart', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Submissions by User'
    },
    time: {
        timezone: $('input[name=timezone]').val()
    },
    xAxis: {
        categories: res.data_inspection_submissions_summary.categories,
        crosshair: true
    },
    yAxis: {
        title: {
            text: ''
        }
    },
    credits: {
        enabled: false
    },
    series: [{
  name: 'Total',
  data: res.data_inspection_submissions_summary.data
  }]
  });
});
})(jQuery);