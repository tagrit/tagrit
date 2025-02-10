(function($) {
  "use strict";


  appValidateForm($('#marketing-message-form'),{
    name:'required',
    category:'required',
    type:'required',
  });

  $('select[name="type"]').on('change', function(){
    if($(this).val() == 'email'){
      $('#div_web_notification').addClass('hide');
      $('#div_email').removeClass('hide');
    }else if($(this).val() == 'web_notification'){
      $('#div_web_notification').removeClass('hide');
      $('#div_email').addClass('hide');
    }else{
      $('#div_web_notification').addClass('hide');
      $('#div_email').addClass('hide');
    }
  });
})(jQuery);