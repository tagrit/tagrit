<script type="text/javascript">
(function($) {
  "use strict";

  $.get(admin_url + 'ma/get_data_asset_chart/'+$('input[name=asset_id]').val()).done(function(res) {
    res = JSON.parse(res);
    Highcharts.chart('container_download_chart', {
        chart: {
            zoomType: 'x'
        },
        title: {
            text: '<?php echo _l("number_of_downloads_over_time"); ?>'
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
        yAxis: {
            title: {
                text: '<?php echo _l("number_of_downloads"); ?>'
            }
        },
        time: {
            timezone: $('input[name=timezone]').val()
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
            name: '<?php echo _l("number_of_downloads"); ?>',
            data: res.data_asset_download
        }]
    });
  });

})(jQuery);


function copy_public_link(){
  "use strict";
  	var link = $('#link_register').val();
    var copyText = document.getElementById("link_register");
    copyText.select();
    copyText.setSelectionRange(0, 99999)
    document.execCommand("copy");
    alert_float('success','Copied!');
}
</script>