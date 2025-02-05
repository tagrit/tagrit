(function($) {
  "use strict";

    appValidateForm($('#filter-form'), {
      from_date: 'required',
      to_date: 'required',
      }, filter_form_handler);
    
    $('#filter-form').submit();
})(jQuery);


function filter_form_handler(form) {
  "use strict";

    var formURL = form.action;
    var formData = new FormData($(form)[0]);
    //show box loading
    var html = '';
      html += '<div class="Box">';
      html += '<span>';
      html += '<span></span>';
      html += '</span>';
      html += '</div>';
      $('#box-loading').html(html);

    $.ajax({
        type: $(form).attr('method'),
        data: formData,
        mimeType: $(form).attr('enctype'),
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response) {
      $('#DivIdToPrint').html(response);

    //hide boxloading
      $('#box-loading').html('');
      $('button[id="uploadfile"]').removeAttr('disabled');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    var from_date = $('input[name="from_date"]').val();
    var to_date = $('input[name="to_date"]').val();

    $.get(admin_url + 'fleet/get_data_cost_meter_trend_chart?from_date='+ from_date+'&to_date='+to_date).done(function(res) {
        res = JSON.parse(res);

        Highcharts.chart('container_chart', {
      chart: {
          type: 'area'
      },
      title: {
          text: 'Event Stats'
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
      series: res.data_cost_meter_trend
    });
      });

    return false;
}
