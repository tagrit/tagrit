<script type="text/javascript">
(function($) {
  "use strict";

    $.get(admin_url + 'ma/get_data_point_action_chart').done(function(res) {
    res = JSON.parse(res);
    Highcharts.chart('container_chart', {
        chart: {
            zoomType: 'x'
        },
        title: {
            text: '<?php echo _l("point_action_over_time"); ?>'
        },
        subtitle: {
            text: document.ontouchstart === undefined ?
                '<?php echo _l("click_and_drag_in_the_plot_area_to_zoom_in"); ?>' : '<?php echo _l("pinch_the_chart_to_zoom_in"); ?>'
        },
        xAxis: {
            type: 'datetime',
            labels: {
              format: '{value:%Y-%m-%d}',
              rotation: 45,
              align: 'left'
            }
        },
        time: {
            timezone: $('input[name=timezone]').val()
        },
        yAxis: {
            title: {
                text: '<?php echo _l("point_action"); ?>'
            }
        },
        legend: {
            enabled: false
        },
        credits: {
            enabled: false
        },
        plotOptions: {
            area: {
                fillColor: {
                    linearGradient: {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                marker: {
                    radius: 2
                },
                lineWidth: 1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },

        series: [{
            type: 'area',
            name: '<?php echo _l("point_action"); ?>',
            data: res.data_point_action
        }]
    });
  });

    init_point_action_log_table();
})(jQuery);

function init_point_action_log_table() {
"use strict";

 if ($.fn.DataTable.isDataTable('.table-point-action')) {
   $('.table-point-action').DataTable().destroy();
 }
 initDataTable('.table-point-action', admin_url + 'ma/point_action_log_table', false, false, [], [3, 'desc']);
}
</script>