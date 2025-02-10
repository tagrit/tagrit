(function($) {
	"use strict";

    appValidateForm($('.campaign-form'), 
    {
      name: 'required', 
      category: 'required', 
    });
})(jQuery);
