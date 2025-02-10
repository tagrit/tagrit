(function($) {
  "use strict";


  appValidateForm($('#text-messages-form'),{
    name:'required',
    category:'required',
  });

  $('.textarea-merge-field').on('click', function(e) {
    e.preventDefault();
    var textArea = $('textarea[name="' + $(this).data('to') + '"]');
    textArea.val(textArea.val() + $(this).text());
  });
})(jQuery);