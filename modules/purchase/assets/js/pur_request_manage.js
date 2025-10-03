var table_pur_request = $('.table-table_pur_request');

    var Params = {
        "from_date": 'input[name="from_date"]',
        "to_date": 'input[name="to_date"]',
        "department": "[name='department_filter[]']",
    };

(function($) {
	"use strict"; 

	

	initDataTable('.table-table_pur_request', admin_url+'purchase/table_pur_request',[0], [0], Params, [4, 'desc']);

	$.each(Params, function(i, obj) {
        $('select' + obj).on('change', function() {  
            table_pur_request.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
    });

    $('input[name="from_date"]').on('change', function() {
        table_pur_request.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
    });
    $('input[name="to_date"]').on('change', function() {
        table_pur_request.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
    });

	appValidateForm($('#send_rq-form'),{subject:'required',attachment:'required'});
})(jQuery);

 function send_request_quotation(id) {
 	"use strict"; 
 	$('#additional_rqquo').html('');
 	$('#additional_rqquo').append(hidden_input('pur_request_id',id));
 	$('#request_quotation').modal('show');
 }


function share_request(id){
  "use strict"; 
  $('#additional_share').html('');
  $('#additional_share').append(hidden_input('pur_request_id',id));
  $.post(admin_url + 'purchase/get_vendor_shared/'+id).done(function(response){
    response = JSON.parse(response);
    var shared_vendor = response.shared_vendor;
    
     $('select[name="send_to_vendors[]"]').val(shared_vendor.split(',')).change();
   

  });


  $('#share_request').modal('show');
}

function routing_init_editor(selector, settings) {

        "use strict";

      tinymce.remove(selector);

    selector = typeof(selector) == 'undefined' ? '.tinymce' : selector;
    var _editor_selector_check = $(selector);

    if (_editor_selector_check.length === 0) { return; }

    $.each(_editor_selector_check, function() {
      if ($(this).hasClass('tinymce-manual')) {
        $(this).removeClass('tinymce');
      }
    });

    // Original settings
    var _settings = {
      branding: false,
      selector: selector,
      browser_spellcheck: true,
      height: 400,
      theme: 'modern',
      skin: 'perfex',
      language: app.tinymce_lang,
      relative_urls: false,
      inline_styles: true,
      verify_html: false,
      cleanup: false,
      autoresize_bottom_margin: 25,
      valid_elements: '+*[*]',
      valid_children: "+body[style], +style[type]",
      apply_source_formatting: false,
      remove_script_host: false,
      removed_menuitems: 'newdocument restoredraft',
      forced_root_block: false,
      autosave_restore_when_empty: false,
      fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt',
      setup: function(ed) {
            // Default fontsize is 12
            ed.on('init', function() {
              this.getDoc().body.style.fontSize = '12pt';
            });
        },
        table_default_styles: {
            // Default all tables width 100%
            width: '100%',
        },
        plugins: [
        'advlist autoresize autosave lists link image print hr codesample',
        'visualblocks code fullscreen',
        'media save table contextmenu',
        'paste textcolor colorpicker'
        ],
        toolbar1: 'fontselect fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | image link | bullist numlist | restoredraft',
        file_browser_callback: elFinderBrowser,
    };

    // Add the rtl to the settings if is true
    isRTL == 'true' ? _settings.directionality = 'rtl' : '';
    isRTL == 'true' ? _settings.plugins[0] += ' directionality' : '';

    // Possible settings passed to be overwrited or added
    if (typeof(settings) != 'undefined') {
      for (var key in settings) {
        if (key != 'append_plugins') {
          _settings[key] = settings[key];
        } else {
          _settings['plugins'].push(settings[key]);
        }
      }
    }

    // Init the editor
    var editor = tinymce.init(_settings);
    $(document).trigger('app.editor.initialized');

    return editor;
}

function change_pr_approve_status(status, id){
  "use strict";
  if(id > 0){
    $.post(admin_url + 'purchase/change_pr_approve_status/'+status+'/'+id).done(function(response){
      response = JSON.parse(response);
      if(response.success == true){
        if($('#status_span_'+id).hasClass('label-danger')){
          $('#status_span_'+id).removeClass('label-danger');
          $('#status_span_'+id).addClass(response.class);
          $('#status_span_'+id).html(response.status_str+' '+response.html);
        }else if($('#status_span_'+id).hasClass('label-success')){
          $('#status_span_'+id).removeClass('label-success');
          $('#status_span_'+id).addClass(response.class);
          $('#status_span_'+id).html(response.status_str+' '+response.html);
        }else if($('#status_span_'+id).hasClass('label-primary')){
          $('#status_span_'+id).removeClass('label-primary');
          $('#status_span_'+id).addClass(response.class);
          $('#status_span_'+id).html(response.status_str+' '+response.html);
        }else if($('#status_span_'+id).hasClass('label-warning')){
          $('#status_span_'+id).removeClass('label-warning');
          $('#status_span_'+id).addClass(response.class);
          $('#status_span_'+id).html(response.status_str+' '+response.html);
        }
        table_pur_request.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        alert_float('success', response.mess);
      }else{
        alert_float('warning', response.mess);
      }
    });
  }
}