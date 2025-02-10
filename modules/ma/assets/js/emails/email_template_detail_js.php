<script type="text/javascript">
var fnServerParams = {};
(function(){
  "use strict";
    fnServerParams = {
        "email_template_id": '[name="email_template_id"]',
    }

    init_leads_table();
   $.get(admin_url + 'ma/get_data_email_template_chart/'+$('input[name=email_template_id]').val()).done(function(res) {
    res = JSON.parse(res);

    Highcharts.chart('container', {
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
      series: res.data_email_template
    });

    Highcharts.chart('container_campaign', {
    chart: {
        type: 'column'
    },
    title: {
        text: '<?php echo _l("email_stats_by_campaign"); ?>'
    },
    xAxis: {
        categories: res.data_email_template_by_campaign.header,
        crosshair: true
    },
    yAxis: {
        title: {
            useHTML: true,
            text: ''
        }
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
    series: res.data_email_template_by_campaign.data
});
  });

   

    $('.add_language').on('click', function(){
      $('#language-modal').modal('show');
    });

    $('.clone_language').on('click', function(){
      $('#clone-design-modal').modal('show');
    });

    appValidateForm($('#language-form'), 
    {
      language: 'required',
    });

    appValidateForm($('#clone-design-form'), 
    {
      from_language: 'required',
      to_language: 'required',
    });

  })(jQuery);

function init_leads_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-leads-email-template')) {
    $('.table-leads-email-template').DataTable().destroy();
  }
  initDataTable('.table-leads-email-template', admin_url + 'ma/leads_table', false, false, fnServerParams);
}
</script>
