(function($) {
  "use strict";

    appValidateForm($('#email-template-form'), 
    {
      name: 'required', 
      category: 'required',
    });
})(jQuery);