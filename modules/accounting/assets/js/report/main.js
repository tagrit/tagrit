(function($) {
	"use strict";
	$('.tree').treegrid();

	appValidateForm($('#filter-form'), {
			from_date: 'required',
			to_date: 'required',
    	}, filter_form_handler);

	$('#filter-form').submit();

  $('select[name="reconcile_account"]').on('change',function(){
    $('input[name="hidden_reconcile_account"]').val($(this).val());
    $.post(admin_url + 'accounting/reconcile_account_change/'+$(this).val()).done(function(response) {
      response = JSON.parse(response);
      $('select[name="reconcile"]').html(response);
      $('select[name="reconcile"]').selectpicker('refresh');
      $('input[name="hidden_reconcile"]').val($('select[name="reconcile"]').val());
    });
  });

  $('select[name="date_filter_type"]').on('change',function(){
    if($(this).val() == 'to_date'){
      $('input[name="from_date"]').parents('.col-md-3').addClass('hide');
    }else{
      $('input[name="from_date"]').parents('.col-md-3').removeClass('hide');
    }
  });
})(jQuery);


function printDiv() 
{
	"use strict";
    var element = document.getElementById('accordion');
    var opt = {
      margin:       0.5,
      filename:     $('input[name="type"]').val()+'.pdf',
      image:        { type: 'jpeg', quality: 1 },
      html2canvas:  { scale: 2 },
      jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    // Old monolithic-style usage:
    html2pdf(element, opt);
}

function printDiv2() 
{
  "use strict";
    var element = document.getElementById('accordion');
    var opt = {
      margin:       0.5,
      filename:     $('input[name="type"]').val()+'.pdf',
      image:        { type: 'jpeg', quality: 1 },
      html2canvas:  { scale: 2 },
      jsPDF:        { unit: 'in', format: 'letter', orientation: 'landscape' }
    };

    // Old monolithic-style usage:
    html2pdf(element, opt);
}

function printExcel(){
	"use strict";
   $(".tree").tableHTMLExport({
      type:'csv',
      filename:$('input[name="type"]').val()+'.csv',
    });
}

function filter_form_handler(form) {
	"use strict";
    if($('select[name="display_rows_by"]').val() != undefined){
      if($('select[name="display_rows_by"]').val() == $('select[name="display_columns_by"]').val()){
        alert('Warning: Row and column headings must be different.');
        return false;
      }
    }

    if($('input[name="type"]').val() == 'custom_summary_report'){
      if($('select[name="page_type"]').val() == 'vertical'){
        $('#DivIdToPrint').addClass('page');
        $('#DivIdToPrint').removeClass('page-size2');

        $('#export_to_pdf_btn').attr('onclick', 'printDiv(); return false;');
      }

      if($('select[name="page_type"]').val() == 'horizontal'){
        $('#DivIdToPrint').removeClass('page');
        $('#DivIdToPrint').addClass('page-size2');
        $('#export_to_pdf_btn').attr('onclick', 'printDiv2(); return false;');
      }
    }

    var formURL = form.action;
    var formData = new FormData($(form)[0]);
    //show box loading
    var html = '';
      html += '<div class="Box">';
      html += '<span>';
      html += '<span></span>';
      html += '</span>';
      html += '</div>';
      $('#box-loading').html(html);

    $.ajax({
        type: $(form).attr('method'),
        data: formData,
        mimeType: $(form).attr('enctype'),
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response) {
    	$('#DivIdToPrint').html(response);
		$('.tree').treegrid();

		//hide boxloading
	    $('#box-loading').html('');
	    $('button[id="uploadfile"]').removeAttr('disabled');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}