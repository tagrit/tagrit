(function($) {
    "use strict";

    $('select[name="settings[acc_integration_sage_accounting_region]"]').on('change', function() {
        var region = $(this).val();
        if(region == 'central_european'){
            $('.south_african_region').addClass('hide');
            $('.central_european_region').removeClass('hide');
        }else{
            $('.central_european_region').addClass('hide');
            $('.south_african_region').removeClass('hide');
        }
    });
})(jQuery);

function test_connect(){
    "use strict";

    var data = {};
    data.api_key = $('input[name="settings[acc_integration_sage_accounting_api_key]"]').val();
    data.username = $('input[name="settings[acc_integration_sage_accounting_username]"]').val();
    data.password = $('input[name="settings[acc_integration_sage_accounting_password]"]').val();

    $.post(admin_url + 'sage_accounting_integration/test_connect', data).done(function(response) {
        response = JSON.parse(response);
        if (response.success === true || response.success == 'true') { 
          alert_float('success', response.message); 
        }else{
          alert_float('danger', response.message); 
        }
    });
}