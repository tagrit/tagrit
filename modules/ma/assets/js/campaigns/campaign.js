(function($) {
	"use strict";

    $( document ).ready(function() {
      appValidateForm($('.campaign-form'), 
      {
        name: 'required', 
        category: 'required', 
      });
    });
})(jQuery);
