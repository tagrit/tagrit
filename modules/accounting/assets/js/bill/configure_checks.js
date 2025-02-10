(function($) {
    "use strict";
    $('#acc_current_check_no_icon_a').on('change', function(){
        var val = $(this).val();
        var obj = $('.current_check_no_icon');
        if(val == 'a'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="a"]').removeClass('hide');
        }
        if(val == 'b'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="b"]').removeClass('hide');
        }
        if(val == 'c'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="c"]').removeClass('hide');
        }
        if(val == 'd'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="d"]').removeClass('hide');
        }
        if(val == 'e'){
            obj.find('.exam-icon-a').addClass('hide');
        }
    });
    $('#acc_current_check_no_icon_b').on('change', function(){
        var val = $(this).val();
        var obj = $('.current_check_no_icon');
        if(val == 'a'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="a"]').removeClass('hide');
        }
        if(val == 'b'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="b"]').removeClass('hide');
        }
        if(val == 'c'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="c"]').removeClass('hide');
        }
        if(val == 'd'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="d"]').removeClass('hide');
        }
        if(val == 'e'){
            obj.find('.exam-icon-b').addClass('hide');
        }
    });
    $('#acc_routing_number_icon_a').on('change', function(){
        var val = $(this).val();
        var obj = $('.routing_number_icon');
        if(val == 'a'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="a"]').removeClass('hide');
        }
        if(val == 'b'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="b"]').removeClass('hide');
        }
        if(val == 'c'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="c"]').removeClass('hide');
        }
        if(val == 'd'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="d"]').removeClass('hide');
        }
        if(val == 'e'){
            obj.find('.exam-icon-a').addClass('hide');
        }
    });
    $('#acc_routing_number_icon_b').on('change', function(){
        var val = $(this).val();
        var obj = $('.routing_number_icon');
        if(val == 'a'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="a"]').removeClass('hide');
        }
        if(val == 'b'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="b"]').removeClass('hide');
        }
        if(val == 'c'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="c"]').removeClass('hide');
        }
        if(val == 'd'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="d"]').removeClass('hide');
        }
        if(val == 'e'){
            obj.find('.exam-icon-b').addClass('hide');
        }
    });
    $('#acc_bank_account_icon_a').on('change', function(){
        var val = $(this).val();
        var obj = $('.bank_account_icon');
        if(val == 'a'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="a"]').removeClass('hide');
        }
        if(val == 'b'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="b"]').removeClass('hide');
        }
        if(val == 'c'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="c"]').removeClass('hide');
        }
        if(val == 'd'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="d"]').removeClass('hide');
        }
        if(val == 'e'){
            obj.find('.exam-icon-a').addClass('hide');
        }
    });
    $('#acc_bank_account_icon_b').on('change', function(){
        var val = $(this).val();
        var obj = $('.bank_account_icon');
        if(val == 'a'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="a"]').removeClass('hide');
        }
        if(val == 'b'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="b"]').removeClass('hide');
        }
        if(val == 'c'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="c"]').removeClass('hide');
        }
        if(val == 'd'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="d"]').removeClass('hide');
        }
        if(val == 'e'){
            obj.find('.exam-icon-b').addClass('hide');
        }
    });

    $("input[type='radio'][name='acc_current_check_no_icon_a']").on('click', function(){
        var left_val = $("input[type='radio'][name='acc_current_check_no_icon_a']:checked").val();

        var obj = $('.current_check_no_icon');
        if(left_val == 'a'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="a"]').removeClass('hide');
        }
        if(left_val == 'b'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="b"]').removeClass('hide');
        }
        if(left_val == 'c'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="c"]').removeClass('hide');
        }
        if(left_val == 'd'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="d"]').removeClass('hide');
        }
        if(left_val == 'e'){
            obj.find('.exam-icon-a').addClass('hide');
        }

    });


    $("input[type='radio'][name='acc_current_check_no_icon_b']").on('click', function(){
        var right_val = $("input[type='radio'][name='acc_current_check_no_icon_b']:checked").val();

      
        var obj = $('.current_check_no_icon');
        if(right_val == 'a'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="a"]').removeClass('hide');
        }
        if(right_val == 'b'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="b"]').removeClass('hide');
        }
        if(right_val == 'c'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="c"]').removeClass('hide');
        }
        if(right_val == 'd'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="d"]').removeClass('hide');
        }
        if(right_val == 'e'){
            obj.find('.exam-icon-b').addClass('hide');
        }

    });



     $("input[type='radio'][name='acc_routing_number_icon_a']").on('click', function(){
        var left_val = $("input[type='radio'][name='acc_routing_number_icon_a']:checked").val();

        var obj = $('.routing_number_icon');
        if(left_val == 'a'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="a"]').removeClass('hide');
        }
        if(left_val == 'b'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="b"]').removeClass('hide');
        }
        if(left_val == 'c'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="c"]').removeClass('hide');
        }
        if(left_val == 'd'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="d"]').removeClass('hide');
        }
        if(left_val == 'e'){
            obj.find('.exam-icon-a').addClass('hide');
        }

    });


    $("input[type='radio'][name='acc_routing_number_icon_b']").on('click', function(){
        var right_val = $("input[type='radio'][name='acc_routing_number_icon_b']:checked").val();

 
        var obj = $('.routing_number_icon');
        if(right_val == 'a'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="a"]').removeClass('hide');
        }
        if(right_val == 'b'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="b"]').removeClass('hide');
        }
        if(right_val == 'c'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="c"]').removeClass('hide');
        }
        if(right_val == 'd'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="d"]').removeClass('hide');
        }
        if(right_val == 'e'){
            obj.find('.exam-icon-b').addClass('hide');
        }

    });



    $("input[type='radio'][name='acc_bank_account_icon_a']").on('click', function(){
        var left_val = $("input[type='radio'][name='acc_bank_account_icon_a']:checked").val();

        var obj = $('.bank_account_icon');
        if(left_val == 'a'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="a"]').removeClass('hide');
        }
        if(left_val == 'b'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="b"]').removeClass('hide');
        }
        if(left_val == 'c'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="c"]').removeClass('hide');
        }
        if(left_val == 'd'){
            obj.find('.exam-icon-a').addClass('hide');
            obj.find('.exam-icon-a[data-value="d"]').removeClass('hide');
        }
        if(left_val == 'e'){
            obj.find('.exam-icon-a').addClass('hide');
        }

    });


    $("input[type='radio'][name='acc_bank_account_icon_b']").on('click', function(){
        var right_val = $("input[type='radio'][name='acc_bank_account_icon_b']:checked").val();

 
        var obj = $('.bank_account_icon');
        if(right_val == 'a'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="a"]').removeClass('hide');
        }
        if(right_val == 'b'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="b"]').removeClass('hide');
        }
        if(right_val == 'c'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="c"]').removeClass('hide');
        }
        if(right_val == 'd'){
            obj.find('.exam-icon-b').addClass('hide');
            obj.find('.exam-icon-b[data-value="d"]').removeClass('hide');
        }
        if(right_val == 'e'){
            obj.find('.exam-icon-b').addClass('hide');
        }

    });



    $('input[name="show_bank_address"]').on('click', function(){
        var radios = $('input:radio[name=acc_check_type]');
   


        if($('input[name="show_bank_address"]').is(":checked") && $('input[name="show_2_signatures"]').is(":checked")){
            $('#check_style_3').removeClass('hide');
            $('#check_style_2').addClass('hide');
            $('#check_style_1').addClass('hide');
            $('#check_style_4').addClass('hide');

           
                radios.filter('[value=type_3]').prop('checked', true);
            

        }else if($('input[name="show_bank_address"]').is(":checked") && !$('input[name="show_2_signatures"]').is(":checked")){
            $('#check_style_1').removeClass('hide');
            $('#check_style_2').addClass('hide');
            $('#check_style_3').addClass('hide');
            $('#check_style_4').addClass('hide');

           
                radios.filter('[value=type_1]').prop('checked', true);
            

        }else if(!$('input[name="show_bank_address"]').is(":checked") && $('input[name="show_2_signatures"]').is(":checked")){
            $('#check_style_1').addClass('hide');
            $('#check_style_2').addClass('hide');
            $('#check_style_3').addClass('hide');
            $('#check_style_4').removeClass('hide');

           
                radios.filter('[value=type_4]').prop('checked', true);
            
        }else if(!$('input[name="show_bank_address"]').is(":checked") && !$('input[name="show_2_signatures"]').is(":checked")){
            $('#check_style_1').addClass('hide');
            $('#check_style_2').removeClass('hide');
            $('#check_style_3').addClass('hide');
            $('#check_style_4').addClass('hide');

           
                radios.filter('[value=type_2]').prop('checked', true);
            
        }
    });


    $('input[name="show_2_signatures"]').on('click', function(){
        var radios = $('input:radio[name=acc_check_type]');
        

         if($('input[name="show_bank_address"]').is(":checked") && $('input[name="show_2_signatures"]').is(":checked")){
            $('#check_style_3').removeClass('hide');
            $('#check_style_2').addClass('hide');
            $('#check_style_1').addClass('hide');
            $('#check_style_4').addClass('hide');

           
                radios.filter('[value=type_3]').prop('checked', true);
            

        }else if($('input[name="show_bank_address"]').is(":checked") && !$('input[name="show_2_signatures"]').is(":checked")){
            $('#check_style_1').removeClass('hide');
            $('#check_style_2').addClass('hide');
            $('#check_style_3').addClass('hide');
            $('#check_style_4').addClass('hide');

           
                radios.filter('[value=type_1]').prop('checked', true);
            

        }else if(!$('input[name="show_bank_address"]').is(":checked") && $('input[name="show_2_signatures"]').is(":checked")){
            $('#check_style_1').addClass('hide');
            $('#check_style_2').addClass('hide');
            $('#check_style_3').addClass('hide');
            $('#check_style_4').removeClass('hide');

           
                radios.filter('[value=type_4]').prop('checked', true);
            
        }else if(!$('input[name="show_bank_address"]').is(":checked") && !$('input[name="show_2_signatures"]').is(":checked")){
            $('#check_style_1').addClass('hide');
            $('#check_style_2').removeClass('hide');
            $('#check_style_3').addClass('hide');
            $('#check_style_4').addClass('hide');

           
                radios.filter('[value=type_2]').prop('checked', true);
            
        }
    });

})(jQuery);
