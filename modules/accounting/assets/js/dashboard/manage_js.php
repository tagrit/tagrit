<script type="text/javascript">
var date_filter;
(function($) {
	"use strict";
    Highcharts.setOptions({
        lang: {
            thousandsSep: ','
        }
    });
    dashboard_custom_view('last_30_days',"<?php echo _l('last_30_days'); ?>",'last_30_days');
})(jQuery);

// Sets table filters dropdown to active
function dashboard_do_filter_active(value, parent_selector) {
    "use strict";
    if (value !== '' && typeof(value) != 'undefined') {

        $('[data-cview="all"]').parents('li').removeClass('active');
        var selector = $('[data-cview="' + value + '"]');
        if (typeof(parent_selector) != 'undefined') {
            selector = $(parent_selector + ' [data-cview="' + value + '"]');
        }
        var parent = selector.parents('li');
        if (parent.hasClass('filter-group')) {
            var group = parent.data('filter-group');
            $('[data-filter-group="' + group + '"]').not(parent).removeClass('active');
            $.each($('[data-filter-group="' + group + '"]').not(parent), function() {
                $('input[name="' + $(this).find('a').attr('data-cview') + '"]').val('');
            });
        }
        if (!parent.not('.dropdown-submenu').hasClass('active')) {
            parent.addClass('active');

        }
        return value;
    } else {
        $('._filters input').val('');
        $('._filter_data li.active').removeClass('active');
        $('[data-cview="all"]').parents('li').addClass('active');
        return "";
    }
}

// Datatables custom view will fill input with the value
function dashboard_custom_view(value, $lang, custom_input_name, clear_other_filters) {
    "use strict";

    $('.tab_currency_default').addClass('active');
    $('.tab_non_currency_default').removeClass('active');

    date_filter = value;

    $('#btn_filter').html('<i class="fa fa-filter" aria-hidden="true"></i> '+$lang);

    //show box loading
    var html = '';
      html += '<div class="Box">';
      html += '<span>';
      html += '<span></span>';
      html += '</span>';
      html += '</div>';
      $('#box-loading').html(html);

    var name = typeof(custom_input_name) == 'undefined' ? 'custom_view' : custom_input_name;
    if (typeof(clear_other_filters) != 'undefined') {
        var filters = $('._filter_data li.active').not('.clear-all-prevent');
        filters.removeClass('active');
        $.each(filters, function() {
            var input_name = $(this).find('a').attr('data-cview');
            $('._filters input[name="' + input_name + '"]').val('');
        });
    }
    var _cinput = dashboard_do_filter_active(name);
   
    requestGet('accounting/get_data_dashboard?date_filter=' + value).done(function(response) {
        response = JSON.parse(response);

        $('#bank_accounts').html(response.bank_accounts);
        $('#convert_status').html(response.convert_status);

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

    Highcharts.chart('expenses_chart', {
        
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: '<?php echo _l("expense_account_overview"); ?>'
        },
        credits: {
          enabled: false
        },
        tooltip: {
            pointFormat: '<b>{point.amount} <?php echo new_html_entity_decode($currency->name); ?></b>'
        },
        
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        console.log(this);
                        return '<b>'+this.key+'</b>: '+Highcharts.numberFormat(this.point.amount,2)+ ' <?php echo new_html_entity_decode($currency->name); ?>';
                    }
                },

            }
        },
        series: [{
            colorByPoint: true,
            data: response.expenses_chart,
        }]
    });

    Highcharts.chart('sales_chart', {
        colors: [ '#99ff66','#84c529','#ffcc99','#ef370dc7'],

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

    Highcharts.chart('income_chart', {
        colors: [ '#626f80','#ef370dc7','#84c529','#119EFA'],
        chart: {
            type: 'column'
        },
        title: {
            text: '<?php echo _l("acc_income"); ?>'
        },
        credits: {
              enabled: false
            },
        yAxis: {
            min: 0,
            title: {
                text: ''
            }
        },
        xAxis: {
            categories: ['']
        },
        tooltip: {
            pointFormat: '<span >{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
            shared: true
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true
                }
            },
        },
        series: response.income_chart
    });

    //hide boxloading
    $('#box-loading').html('');
    });
}

function change_currency_convert_status(currency){
    "use strict";
    //show box loading
    var html = '';
      html += '<div class="Box">';
      html += '<span>';
      html += '<span></span>';
      html += '</span>';
      html += '</div>';
      $('#box-loading').html(html);

    requestGet('accounting/get_data_convert_status_dashboard?date_filter=' + date_filter+'&currency=' + currency).done(function(response) {
        response = JSON.parse(response);

        $('#convert_status').html(response.convert_status);
        //hide boxloading
        $('#box-loading').html('');
    });
}

function change_currency_income_chart(currency){
    "use strict";
    //show box loading
    var html = '';
      html += '<div class="Box">';
      html += '<span>';
      html += '<span></span>';
      html += '</span>';
      html += '</div>';
      $('#box-loading').html(html);
    requestGet('accounting/get_data_income_chart?date_filter=' + date_filter+'&currency=' + currency).done(function(response) {
        response = JSON.parse(response);

        Highcharts.chart('income_chart', {
            colors: [ '#626f80','#ef370dc7','#84c529','#119EFA'],
            chart: {
                type: 'column'
            },
            title: {
                text: '<?php echo _l("acc_income"); ?>'
            },
            credits: {
                  enabled: false
                },
            yAxis: {
                min: 0,
                title: {
                    text: ''
                }
            },
            xAxis: {
                categories: ['']
            },
            tooltip: {
                pointFormat: '<span >{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
                shared: true
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: true
                    }
                },
            },
            series: response.income_chart
        });

        //hide boxloading
        $('#box-loading').html('');
    });
}

function change_currency_sales_chart(currency){
    "use strict";
    //show box loading
    var html = '';
      html += '<div class="Box">';
      html += '<span>';
      html += '<span></span>';
      html += '</span>';
      html += '</div>';
      $('#box-loading').html(html);
    requestGet('accounting/get_data_sales_chart?date_filter=' + date_filter+'&currency=' + currency).done(function(response) {
        response = JSON.parse(response);

        Highcharts.chart('sales_chart', {
            colors: [ '#99ff66','#84c529','#ffcc99','#ef370dc7'],

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

        //hide boxloading
        $('#box-loading').html('');
    });
}


</script>