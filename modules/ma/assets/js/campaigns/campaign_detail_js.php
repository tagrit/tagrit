<script type="text/javascript">
var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
        "campaign_id": '[name="campaign_id"]',
    };

    $( document ).ready(function() {
    init_lead_table();
    init_client_table();

    $.get(admin_url + 'ma/get_data_campaign_detail_chart/'+$('input[name=campaign_id]').val()).done(function(res) {
        res = JSON.parse(res);
        Highcharts.chart('container_email', {
          chart: {
              type: 'area'
          },
          title: {
              text: '<?php echo _l("email_stats"); ?>'
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
          series: res.data_email
        });

        Highcharts.chart('container_email_total', {
            chart: {
                type: 'column'
            },
            title: {
                text: '<?php echo _l("email_stats_total"); ?>'
            },
            xAxis: {
                categories: [''],
                title: {
                    text: null
                },
            },
            yAxis: {
                min: 0,
                max: 100,
                title: {
                    text: '%'
                }
            },
            tooltip: {
                headerFormat: '<table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    dataLabels: {
                      enabled: true
                    },
                },

            },
            credits: {
              enabled: false
            },
            series: res.data_email_total
        });

        Highcharts.chart('container_text_message', {
          chart: {
              zoomType: 'x'
          },
          title: {
              text: '<?php echo _l("sms_over_time"); ?>'
          },
          subtitle: {
              text: document.ontouchstart === undefined ?
                  'Click and drag in the plot area to zoom in' : 'Pinch the chart to zoom in'
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
                  text: 'SMS'
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
              name: 'SMS',
              data: res.data_text_message
          }]
      });
        
Highcharts.chart('container_point_action', {
        chart: {
            zoomType: 'x'
        },
        title: {
            text: '<?php echo _l("point_action_over_time"); ?>'
        },
        time: {
            timezone: $('input[name=timezone]').val()
        },
        subtitle: {
            text: document.ontouchstart === undefined ?
                'Click and drag in the plot area to zoom in' : 'Pinch the chart to zoom in'
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
    });

    appValidateForm($('#test-campaign-form'), {
        name: 'required',
        email: 'required',
    });
    });
})(jQuery);

function init_lead_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-leads-campaign')) {
    $('.table-leads-campaign').DataTable().destroy();
  }
  initDataTable('.table-leads-campaign', admin_url + 'ma/leads_table', false, false, fnServerParams);
}


function init_client_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-clients-campaign')) {
    $('.table-clients-campaign').DataTable().destroy();
  }
  initDataTable('.table-clients-campaign', admin_url + 'ma/clients_table', false, false, fnServerParams);
}

function test_campaign(){
    $('#test-campaign-modal').modal('show');
}

function delete_test_campaign(id){
    if (confirm_delete()) {
        $.post(admin_url + 'ma/delete_test_campaign/' + id).done(function() {
            window.location.reload();
        });
    }
}

function refresh_test_campaign(id){
    if (confirm_delete()) {
        $.post(admin_url + 'ma/refresh_test_campaign/' + id).done(function() {
            window.location.reload();
        });
    }
}

function run_now_test_campaign(id){
    if (confirm_delete()) {
        $.post(admin_url + 'ma/run_campaign_test/' + id).done(function() {
            window.location.reload();
        });
    }
}
</script>