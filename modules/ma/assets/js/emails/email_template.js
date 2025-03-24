(function($) {
  "use strict";

    $( document ).ready(function() {
    appValidateForm($('#email-template-form'), 
    {
      name: 'required', 
      category: 'required',
    });
    });
})(jQuery);