(function($) {
	"use strict";
	if($('input[name="acc_close_the_books"]').is(':checked') == true){
		if($('select[name="acc_allow_changes_after_viewing"]').val() == 'allow_changes_after_viewing_a_warning'){
	      	appValidateForm($('#general-settings-form'), {
		        acc_closing_date: 'required',
		    });
	    }else{
	    	appValidateForm($('#general-settings-form'), {
		        acc_closing_date: 'required',		        
		    });
	    }
	}

	$('input[name="acc_close_the_books"]').on('change', function() {
	    if($('input[name="acc_close_the_books"]').is(':checked') == true){
	      	appValidateForm($('#general-settings-form'), {
		        acc_closing_date: 'required',		        
		    });
	      	$('#div_close_the_books').removeClass('hide');
	    }else{
	    	appValidateForm($('#general-settings-form'), {
		    });
	      	$('#div_close_the_books').addClass('hide');
	    }
	});

	$('input[name="acc_enable_account_numbers"]').on('change', function() {
	    if($('input[name="acc_enable_account_numbers"]').is(':checked') == true){
	      $('#div_enable_account_numbers').removeClass('hide');
	    }else{
	      $('#div_enable_account_numbers').addClass('hide');
	    }
	});

	$('select[name="acc_allow_changes_after_viewing"]').on('change', function() {
	    if($('select[name="acc_allow_changes_after_viewing"]').val() == 'allow_changes_after_viewing_a_warning'){
	      	appValidateForm($('#general-settings-form'), {
		        acc_closing_date: 'required',
		    });
	      	$('#div_close_book_password').addClass('hide');
	    }else{
	    	appValidateForm($('#general-settings-form'), {
		        acc_closing_date: 'required',		        
		    });
	      $('#div_close_book_password').removeClass('hide');
	    }
	});

	$('select[name="acc_accounting_method"]').on('change', function() {
	    if($('select[name="acc_accounting_method"]').val() == 'cash'){
	    	$('.detail_type_note_1').html($('#detail_type_note_cash_1').html());
		    $('.detail_type_note_2').html($('#detail_type_note_cash_2').html());
		    $('.detail_type_note_3').html($('#detail_type_note_cash_3').html());
	    }else{
	      	$('.detail_type_note_1').html($('#detail_type_note_accrual_1').html());
	      	$('.detail_type_note_2').html($('#detail_type_note_accrual_2').html());
	      	$('.detail_type_note_3').html($('#detail_type_note_accrual_3').html());
	    }
	});

	if($('select[name="acc_accounting_method"]').val() == 'cash'){
      $('.detail_type_note_1').html($('#detail_type_note_cash_1').html());
      $('.detail_type_note_2').html($('#detail_type_note_cash_2').html());
      $('.detail_type_note_3').html($('#detail_type_note_cash_3').html());
    }else{
      $('.detail_type_note_1').html($('#detail_type_note_accrual_1').html());
      $('.detail_type_note_2').html($('#detail_type_note_accrual_2').html());
      $('.detail_type_note_3').html($('#detail_type_note_accrual_3').html());
    }
	
})(jQuery);