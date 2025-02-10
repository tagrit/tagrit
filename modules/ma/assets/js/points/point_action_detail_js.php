<script type="text/javascript">
var fnServerParams = {};
(function(){
  "use strict";
    fnServerParams = {
        "point_action_id": '[name="point_action_id"]',
    }

    init_leads_table();

    $.get(admin_url + 'ma/get_data_point_action_chart/'+$('input[name=point_action_id]').val()).done(function(res) {
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

    Highcharts.chart('container_campaign_chart', {
        chart: {
            type: 'column'
        },
        title: {
            text: '<?php echo _l("point_action_stats_by_campaign"); ?>'
        },
        xAxis: {
            categories: res.data_point_action_by_campaign.header,
            crosshair: true
        },
        yAxis: {
            title: {
                useHTML: true,
                text: ''
            }
        },
        legend: {
            enabled: false
        },
        credits: {
            enabled: false
        },
        tooltip: {
            headerFormat: '<span class="font-size-10">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};" class="no-padding">{series.name}: </td>' +
                '<td class="no-padding"><b>{point.y}</b></td></tr>',
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
        series: res.data_point_action_by_campaign.data
    });
  });
})(jQuery);


function init_leads_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-leads-point-action')) {
    $('.table-leads-point-action').DataTable().destroy();
  }
  initDataTable('.table-leads-point-action', admin_url + 'ma/leads_table', false, false, fnServerParams);
}
</script>
