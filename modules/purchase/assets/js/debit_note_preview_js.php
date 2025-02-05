<script>
   init_items_sortable(true);
   init_btn_with_tooltips();
   init_datepicker();
   init_selectpicker();
   init_form_reminder();
   init_tabs_scrollable();

   function refund_debit_note() {
      var debit_note_id = <?php echo $debit_note->id; ?>;
      $('#debit_note').load(admin_url + 'purchase/refund_debit_note/' + debit_note_id);
   }

   function edit_refund(refund_id) {
      var debit_note_id = <?php echo $debit_note->id; ?>;
      $('#debit_note').load(admin_url + 'purchase/refund_debit_note/' + debit_note_id + '/' + refund_id);
   }

   function delete_debit_note_attachment(id) {
    if (confirm_delete()) {
        requestGet('purchase/delete_debit_attachment/' + id).done(function (success) {
            if (success == 1) {
                $("body").find('[data-attachment-id="' + id + '"]').remove();
                init_debit_note($("body").find('input[name="_attachment_sale_id"]').val());
            }
        }).fail(function (error) {
            alert_float('danger', error.responseText);
        });
   }
}


 function send_debit_note(id) {
  "use strict"; 
  $('#additional_dn').html('');
  $('#additional_dn').append(hidden_input('debit_note_id',id));
  $('#send_dn').modal('show');
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
</script>