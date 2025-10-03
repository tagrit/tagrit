<script type="text/javascript">
var fnServerParams;
(function($) {
  "use strict";

    fnServerParams = {
      "is_report": '[name="is_report"]',
    };
    $.get(admin_url + 'fleet/get_data_income_and_expense_chart').done(function(response) {
    response = JSON.parse(response);

    const chart = Highcharts.chart('profit_and_loss', {
            colors: [ '#119EFA','#84c529','#626f80'],
            chart: {
                inverted: true,
                polar: false
            },
            title: {
                text: '<?php echo _l('profit_and_loss'); ?>'
            },
            
            tooltip: {
                pointFormat: '<span ></span><b>{point.y}</b><br/>',
            },
            yAxis: {
                title: {
                    text: '<?php echo new_html_entity_decode($currency->name); ?>'
                }
            },
            xAxis: {
                categories: ['<?php echo _l('acc_net_income'); ?>', '<?php echo _l('acc_income'); ?>', '<?php echo _l('expenses'); ?>']
            },
            credits: {
              enabled: false
            },
            plotOptions: {
                series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                }
            }
            },
            series: [{
                type: 'column',
                colorByPoint: true,
                data: response.profit_and_loss_chart,
                showInLegend: false
            }]
        });

        Highcharts.chart('sales_chart', {
        colors: [ '#99ff66', '#ef370dc7'],

        title: {
            text: '<?php echo _l("cash_flow"); ?>'
        },

        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },
        credits: {
              enabled: false
            },
        yAxis: {
            title: {
                text: ''
            }
        },
        xAxis: {
            categories: response.sales_chart.categories
        },

        series: response.sales_chart.data,
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
            }
        },
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }

    });
  });

})(jQuery);
</script>
