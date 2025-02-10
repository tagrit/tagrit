<script type="text/javascript">
var fnServerParams = {};
(function(){
  "use strict";
    fnServerParams = {
        "stage_id": '[name="stage_id"]',
    }

    init_leads_table();
    $.get(admin_url + 'ma/get_data_stage_detail_chart/'+$('input[name=stage_id]').val()).done(function(res) {
        res = JSON.parse(res);

        Highcharts.chart('container_stage', {
          chart: {
              type: 'area'
          },
          title: {
              text: '<?php echo _l("leads_in_time"); ?>'
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
          series: res.data_stage_detail
        });

        Highcharts.chart('container_stage_campaign', {
            chart: {
                type: 'column'
            },
            title: {
                text: '<?php echo _l("stage_stats_by_campaign"); ?>'
            },
            xAxis: {
                categories: res.data_stage_campaign_detail.header,
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
            series: res.data_stage_campaign_detail.data
        });
    });
  })(jQuery);

    function init_leads_table() {
      "use strict";

      if ($.fn.DataTable.isDataTable('.table-leads-stage')) {
        $('.table-leads-stage').DataTable().destroy();
      }
      initDataTable('.table-leads-stage', admin_url + 'ma/leads_table', false, false, fnServerParams);
    }
  </script>