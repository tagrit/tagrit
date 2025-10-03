(function($) {
    "use strict";

    $( document ).ready(function() {
  $(document).on("change", "input[type=radio][name*=ma_smtp_type]", function() { 
      if(this.value == 'other_smtp'){
          $('.div_other_smtp').removeClass('hide');
          $('.div_test_email').removeClass('hide');
      }else{
          $('.div_other_smtp').addClass('hide');
          $('.div_test_email').addClass('hide');
      }

  });

  $(document).on("change", "input[type=radio][name*=ma_unsubscribe]", function() { 
      if(this.value == '1'){
          $('.div_unsubscribe').removeClass('hide');
      }else{
          $('.div_unsubscribe').addClass('hide');
      }
  });

  $(document).on("change", "input[type=radio][name*=ma_email_sending_limit]", function() { 
      if(this.value == '1'){
          $('.div_email_sending_limit').removeClass('hide');
      }else{
          $('.div_email_sending_limit').addClass('hide');
      }
  });

  

  $('.add_smtp_config').on('click', function(){
      $('#smtp-config-modal').modal('show');
    });

  
  appValidateForm($('#smtp-config-form'), 
    {
      name: 'required',
    });
  });
})(jQuery);

function ma_test_email(id) {
  var email = $('input[name="test_email_'+id+'"]').val();
  if (email != '') {
    $(this).attr('disabled', true);
     $.post(admin_url + 'ma/sent_smtp_test_email/'+id, {
      test_email: email
    }).done(function(data) {
       window.location.reload();
    });
  }
}
