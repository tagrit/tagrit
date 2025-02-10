(function($) {
  "use strict";
  appValidateForm($('#email-form'),{
    name:'required',
    category:'required',
    subject:'required',
  });

  if($('select[name=email_template]').val() != ''){
    get_exam_template($('select[name=email_template]').val());
  }

  $('select[name=email_template]').on('change', function(){
    var id = $(this).val();
    get_exam_template(id);
  });

})(jQuery);

var page_width = 0;
function init_page_rotation(){
    "use strict";
    $('.preview-form').removeClass('preview-form-overflow');
    var page_list = $('.page');
    var page_obj = page_list.eq(0);
        page_width = page_obj.width();
    var page_content_height = page_obj.find('.page-content').height();
    var page_height = 0;
    if($('#page_rotation').val() == 'portrait'){
        page_height = page_width * 1.41428571429;
        page_obj.width(page_width);
    }
    else{
        let landcape_width = page_width + (page_width / 2);
        page_height = landcape_width * 0.70707070707;  
        page_obj.width(landcape_width);
        $('.preview-form').addClass('overflow-x');
    }
}
function get_exam_template(id){
    "use strict";
    $('#preview_area').html("");
  $.get(admin_url+'ma/get_email_template_preview/'+id, function(reponses){
      $('#preview_area').html(reponses);
      init_page_rotation();
    });
}