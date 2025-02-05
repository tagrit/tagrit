  
  (function($){
    "use strict";

    if($('select[name="criteria_type"]').val() == 'criteria'){
          $('select[name="group_criteria"]').attr('required','');
          $('#select_group_criteria').removeClass('hide');
      }else{
          $('select[name="group_criteria"]').removeAttr('required');
          $('#select_group_criteria').addClass('hide');
      }



  })(jQuery);

    function edit_approval_setting11(invoker,id){
      "use strict";
        appValidateForm($('#approval-setting-form'),{name:'required', related:'required'});

        var name = $(invoker).data('name');
        var related = $(invoker).data('related');
        var setting = $(invoker).data('setting');
        
         $('#approval_id').empty();
        $('#approval_id').append(hidden_input('approval_setting_id',id));

        $('#approval_setting_modal input[name="name"]').val(name);
        $('select[name="related"]').val(related).change();
        
        $.post(admin_url + 'warehouse/get_html_approval_setting/'+ id).done(function(response) {
           response = JSON.parse(response);

            $('.list_approve').html('');
            $('.list_approve').append(response);
        init_selectpicker();

        });
        
        $('#approval_setting_modal').modal('show');
        $('#approval_setting_modal .add-title').addClass('hide');
        $('#approval_setting_modal .edit-title').removeClass('hide');
        
   }