<script>
<?php if(isset($contract)) {?>
  var contract_id = '<?php echo pur_html_entity_decode($contract->id); ?>';
<?php } ?>
(function($) {
  "use strict";
    validate_contract_form();
    function validate_contract_form(selector) {

   var selector = typeof(selector) == 'undefined' ? '#contract-form' : selector;

    appValidateForm($(selector), {
        contract_name: 'required',
        vendor: 'required',
        start_date: 'required',
       
    });

  }

  var _templates = [];
    $.each(contractsTemplates, function (i, template) {
       _templates.push({
          url: admin_url + 'contracts/get_template?name=' + template,
          title: template
       });
    });
var selector = typeof(selector) == 'undefined' ? 'div.editable' : selector;   
    var _editor_selector_check = $(selector);

    if (_editor_selector_check.length === 0) { return; }

    $.each(_editor_selector_check, function() {
        if ($(this).hasClass('tinymce-manual')) {
            $(this).removeClass('tinymce');
        }
    });

    var editor_settings = {
       branding: false,
     selector: selector,
     browser_spellcheck: true,
     height: 400,
     theme: 'modern',
     skin: 'perfex',
     language: app.tinymce_lang,
       relative_urls: false,
       remove_script_host: false,
       inline_styles: true,
       verify_html: false,
       cleanup: false,
       apply_source_formatting: false,
       valid_elements: '+*[*]',
       valid_children: "+body[style], +style[type]",
       file_browser_callback: elFinderBrowser,
       table_default_styles: {
          width: '100%'
       },
       fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt',
       pagebreak_separator: '<p pagebreak="true"></p>',
       plugins: [
          'advlist pagebreak autolink autoresize lists link image charmap hr',
          'searchreplace visualblocks visualchars code',
          'media nonbreaking table contextmenu',
          'paste textcolor colorpicker'
       ],       
        tinycomments_mode: 'embedded',
        tinycomments_author: app.current_user,
       autoresize_bottom_margin: 50,
       toolbar: 'fontselect fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | image link | bullist numlist | restoredraft',
       insert_toolbar: 'image media quicktable | bullist numlist | h2 h3 | hr',
       selection_toolbar: 'save_button bold italic underline superscript | forecolor backcolor link | alignleft aligncenter alignright alignjustify | fontselect fontsizeselect h2 h3',
       contextmenu: "image media inserttable | cell row column deletetable | paste pastetext searchreplace | visualblocks pagebreak charmap | code",
       setup: function (editor) {

          editor.addCommand('mceSave', function () {
             save_contract_content(true);
          });

          editor.addShortcut('Meta+S', '', 'mceSave');

          editor.on('MouseLeave blur', function () {
             if (tinymce.activeEditor.isDirty()) {
                save_contract_content();
             }
          });

          editor.on('MouseDown ContextMenu', function () {
             if (!is_mobile() && !$('.left-column').hasClass('hide')) {
                contract_full_view();
             }
          });

          editor.on('blur', function () {
             $.Shortcuts.start();
          });

          editor.on('focus', function () {
             $.Shortcuts.stop();
          });

       }
    }

    if (_templates.length > 0) {
       editor_settings.templates = _templates;
       editor_settings.plugins[3] = 'template ' + editor_settings.plugins[3];
       editor_settings.contextmenu = editor_settings.contextmenu.replace('inserttable', 'inserttable template');
    }

     if(is_mobile()) {

          editor_settings.theme = 'modern';
          editor_settings.mobile    = {};
          editor_settings.mobile.theme = 'mobile';
          editor_settings.mobile.toolbar = _tinymce_mobile_toolbar();

          editor_settings.inline = false;
          window.addEventListener("beforeunload", function (event) {
            if (tinymce.activeEditor.isDirty()) {
               save_contract_content();
            }
         });
     }

    if (typeof init_tinymce_inline_editor !== "undefined") { 
        init_tinymce_inline_editor({
            saveUsing: save_contract_content,
            onSetup: function(editor) {
                editor.on('MouseDown ContextMenu', function() {
                    if (!is_mobile() && !$('.left-column').hasClass('hide')) {
                        contract_full_view();
                    }
                });
            }
        });

    }else{
        if(tinymce.majorVersion + '.' + tinymce.minorVersion == '6.8.3'){
            tinymce.init({
                selector: selector,
                inline: true,
                browser_spellcheck: true,
                branding: false,
                 plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
                toolbar: 'undo redo | formatselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | table | charmap | fullscreen | help',
                insert_toolbar: 'image media quicktable | bullist numlist | h2 h3 | hr',
                selection_toolbar: 'save_button bold italic underline superscript | forecolor backcolor link | alignleft aligncenter alignright alignjustify | fontselect fontsizeselect h2 h3',
                contextmenu: "image media inserttable | cell row column deletetable | paste pastetext searchreplace | visualblocks pagebreak charmap | code",
                setup: function (editor) {

                  editor.addCommand('mceSave', function () {
                     save_contract_content(true);
                  });

                  editor.addShortcut('Meta+S', '', 'mceSave');

                  editor.on('MouseLeave blur', function () {
                     if (tinymce.activeEditor.isDirty()) {
                        save_contract_content();
                     }
                  });

                  editor.on('MouseDown ContextMenu', function () {
                     if (!is_mobile() && !$('.left-column').hasClass('hide')) {
                        contract_full_view();
                     }
                  });

                  editor.on('blur', function () {
                     $.Shortcuts.start();
                  });

                  editor.on('focus', function () {
                     $.Shortcuts.stop();
                  });

               }
            });
          
            $('.tox-promotion').css('display', 'none');
        

        }else{
          tinymce.init(editor_settings);
        }
    }


    <?php if(isset($contract)){ ?>

   SignaturePad.prototype.toDataURLAndRemoveBlanks = function() {
     var canvas = this._ctx.canvas;
       // First duplicate the canvas to not alter the original
       var croppedCanvas = document.createElement('canvas'),
       croppedCtx = croppedCanvas.getContext('2d');

       croppedCanvas.width = canvas.width;
       croppedCanvas.height = canvas.height;
       croppedCtx.drawImage(canvas, 0, 0);

       // Next do the actual cropping
       var w = croppedCanvas.width,
       h = croppedCanvas.height,
       pix = {
         x: [],
         y: []
       },
       imageData = croppedCtx.getImageData(0, 0, croppedCanvas.width, croppedCanvas.height),
       x, y, index;

       for (y = 0; y < h; y++) {
         for (x = 0; x < w; x++) {
           index = (y * w + x) * 4;
           if (imageData.data[index + 3] > 0) {
             pix.x.push(x);
             pix.y.push(y);

           }
         }
       }
       pix.x.sort(function(a, b) {
         return a - b
       });
       pix.y.sort(function(a, b) {
         return a - b
       });
       var n = pix.x.length - 1;

       w = pix.x[n] - pix.x[0];
       h = pix.y[n] - pix.y[0];
       var cut = croppedCtx.getImageData(pix.x[0], pix.y[0], w, h);

       croppedCanvas.width = w;
       croppedCanvas.height = h;
       croppedCtx.putImageData(cut, 0, 0);

       return croppedCanvas.toDataURL();
     };


     function signaturePadChanged() {

       var input = document.getElementById('signatureInput');
       var $signatureLabel = $('#signatureLabel');
       $signatureLabel.removeClass('text-danger');

       if (signaturePad.isEmpty()) {
         $signatureLabel.addClass('text-danger');
         input.value = '';
         return false;
       }

       $('#signatureInput-error').remove();
       var partBase64 = signaturePad.toDataURLAndRemoveBlanks();
       partBase64 = partBase64.split(',')[1];
       input.value = partBase64;
     }

     var canvas = document.getElementById("signature");
     var signaturePad = new SignaturePad(canvas, {
      maxWidth: 2,
      onEnd:function(){
        signaturePadChanged();
      }
    });

     $('#identityConfirmationForm').submit(function() {
       signaturePadChanged();
     });
get_contract_comments();
<?php } ?>


})(jQuery);

(function($) {
  "use strict";
  $("input[data-type='currency']").on({
    keyup: function() {  
    
      formatCurrency($(this));
    },
    blur: function() { 
      formatCurrency($(this), "blur");
    }
  });
})(jQuery);
function formatNumber(n) {
  "use strict"; 
  // format number 1000000 to 1,234,567
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, "<?php echo get_option('thousand_separator'); ?>");
}
function formatCurrency(input, blur) {
  "use strict"; 
  var input_val = input.val();
  if (input_val === "") { return; }
  var original_len = input_val.length;
  var caret_pos = input.prop("selectionStart");
  if (input_val.indexOf("<?php echo get_option('decimal_separator'); ?>") >= 0) {
    var decimal_pos = input_val.indexOf("<?php echo get_option('decimal_separator'); ?>");
    var left_side = input_val.substring(0, decimal_pos);
    var right_side = input_val.substring(decimal_pos);
    left_side = formatNumber(left_side);
    right_side = formatNumber(right_side);
    right_side = right_side.substring(0, 2);
    input_val = left_side + "<?php echo get_option('decimal_separator'); ?>" + right_side;

  } else {
    input_val = formatNumber(input_val);
    input_val = input_val;

  }
  input.val(input_val);
  var updated_len = input_val.length;
  caret_pos = updated_len - original_len + caret_pos;
  input[0].setSelectionRange(caret_pos, caret_pos);
}

function view_pur_order(invoker){
  "use strict";
  var pur_order = invoker.value;
  if(pur_order != ''){
    $.post(admin_url + 'purchase/view_pur_order/'+pur_order).done(function(response){
        response = JSON.parse(response);

        $('input[name="contract_value"]').val(response.total);
        $('select[name="buyer"]').val(response.buyer).change();
        $('select[name="project"]').val(response.project).change();
        $('select[name="department"]').val(response.department).change();
    });
  }else{
    alert_float('warning', '<?php echo _l('please_chose_pur_order'); ?>');
  }
}

function vendor_change(el){
  "use strict"; 
  var vendor = $(el).val();
  if(vendor != ''){
      $.post(admin_url + 'purchase/vendor_contract_change/'+vendor).done(function(response){
        response = JSON.parse(response);
        $('select[name="pur_order"]').html(response.html);
        $('select[name="pur_order"]').selectpicker('refresh');
      });
  }
}


   function save_contract_content(manual) {
    "use strict";
    var editor = tinyMCE.activeEditor;
    var data = {};
    data.contract_id = contract_id;
    data.content = editor.getContent();
    $.post(admin_url + 'purchase/save_contract_data', data).done(function (response) {
       response = JSON.parse(response);
       if (typeof (manual) != 'undefined') {
          // Show some message to the user if saved via CTRL + S
          alert_float('success', response.message);
       }
       // Invokes to set dirty to false
       editor.save();
    }).fail(function (error) {
       var response = JSON.parse(error.responseText);
       alert_float('danger', response.message);
    });
   }

   function contract_full_view() {
    "use strict";
    $('.left-column').toggleClass('hide');
    $('.right-column').toggleClass('col-md-7');
    $('.right-column').toggleClass('col-md-12');
    $(window).trigger('resize');
   }
  function accept_action() {
    "use strict";
      $('#add_action').modal('show');
  }

  function signature_clear(){
    "use strict";
    var canvas = document.getElementById("signature");
    var signaturePad = new SignaturePad(canvas, {
      maxWidth: 2,
      onEnd:function(){
        //signaturePadChanged();
      }
    });
    signaturePad.clear();
    //signaturePadChanged();
  }

  function sign_request(id){
    "use strict";
    change_signed_status(id,'signed');
  }

  function change_signed_status(request_id, status){
    "use strict";
      var data = {};
      data.status = status;
      data.signature = $('input[name="signature"]').val();
      
      $.post(admin_url + 'purchase/sign_contract/' + request_id, data).done(function(response){
          response = JSON.parse(response); 
          if (response.success === true || response.success == 'true') {
              alert_float('success', response.message);
              window.location.reload();
          }
      });
  }

function preview_ic_btn(invoker){
    "use strict";
    var id = $(invoker).attr('id');
    var rel_id = $(invoker).attr('rel_id');
    view_ic_file(id, rel_id);
}

function view_ic_file(id, rel_id) {
    "use strict";
      $('#ic_file_data').empty();
      $("#ic_file_data").load(admin_url + 'purchase/file_pur_contract/' + id + '/' + rel_id, function(response, status, xhr) {
          if (status == "error") {
              alert_float('danger', xhr.statusText);
          }
      });
}

function close_modal_preview(){
    "use strict";
 $('._project_file').modal('hide');
}

function delete_ic_attachment(id) {
    "use strict";
    if (confirm_delete()) {
        requestGet('purchase/delete_pur_contract_attachment/' + id).done(function(success) {
            if (success == 1) {
                $("#ic_pv_file").find('[data-attachment-id="' + id + '"]').remove();
            }
        }).fail(function(error) {
            alert_float('danger', error.responseText);
        });
    }
  }


function get_sales_notes_contract(id, controller) {
  "use strict";
    requestGet(controller + '/get_notes_pur_contract/' + id).done(function(response) {
        $('#sales_notes_area').html(response);
        var totalNotesNow = $('#sales-notes-wrapper').attr('data-total');
        if (totalNotesNow > 0) {
            $('.notes-total').html('<span class="badge">' + totalNotesNow + '</span>').removeClass('hide');
        }
    });
}

<?php if(isset($contract)) { ?>

function add_contract_comment() {
  "use strict";
    var comment = $('#comment').val();
    if (comment == '') {
       return;
    }
    var data = {};
    data.content = comment;
    data.rel_id = contract_id;
    data.rel_type = 'pur_contract';
    $('body').append('<div class="dt-loader"></div>');
    $.post(admin_url + 'purchase/add_comment', data).done(function (response) {
       response = JSON.parse(response);
       $('body').find('.dt-loader').remove();
       if (response.success == true) {
          $('#comment').val('');
          get_contract_comments();
       }
    });
   }

 function get_contract_comments() {
  "use strict";
  if (typeof (contract_id) == 'undefined') {
     return;
  }
  requestGet('purchase/get_comments/' + contract_id+'/pur_contract').done(function (response) {
     $('#contract-comments').html(response);
     var totalComments = $('[data-commentid]').length;
     var commentsIndicator = $('.comments-indicator');
     if(totalComments == 0) {
          commentsIndicator.addClass('hide');
     } else {
       commentsIndicator.removeClass('hide');
       commentsIndicator.text(totalComments);
     }
  });
 }

 function remove_contract_comment(commentid) {
  "use strict";
  if (confirm_delete()) {
     requestGetJSON('purchase/remove_comment/' + commentid).done(function (response) {
        if (response.success == true) {

          var totalComments = $('[data-commentid]').length;

           $('[data-commentid="' + commentid + '"]').remove();

           var commentsIndicator = $('.comments-indicator');
           if(totalComments-1 == 0) {
             commentsIndicator.addClass('hide');
          } else {
             commentsIndicator.removeClass('hide');
             commentsIndicator.text(totalComments-1);
          }
        }
     });
  }
 }

 function edit_contract_comment(id) {
  "use strict";
  var content = $('body').find('[data-contract-comment-edit-textarea="' + id + '"] textarea').val();
  if (content != '') {
     $.post(admin_url + 'purchase/edit_comment/' + id, {
        content: content
     }).done(function (response) {
        response = JSON.parse(response);
        if (response.success == true) {
           alert_float('success', response.message);
           $('body').find('[data-contract-comment="' + id + '"]').html(nl2br(content));
        }
     });
     toggle_contract_comment_edit(id);
  }
 }

 function toggle_contract_comment_edit(id) {
  "use strict";
     $('body').find('[data-contract-comment="' + id + '"]').toggleClass('hide');
     $('body').find('[data-contract-comment-edit-textarea="' + id + '"]').toggleClass('hide');
 }


 function send_contract(id) {
  "use strict"; 
  $('#additional_contract').html('');
  $('#additional_contract').append(hidden_input('contract_id',id));
  $('#send_contract').modal('show');
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

function add_pur_template(rel_type, rel_id) {
    $('#modal-wrapper').load(admin_url + 'purchase/modal_template', {
        slug: 'new',
        rel_type: rel_type,
        rel_id: rel_id,
    }, function () {
        if ($('#TemplateModal').is(':hidden')) {
            $('#TemplateModal').modal({
                backdrop: 'static',
                show: true
            });
        }
        appValidateForm($('#template-form'), {
            name: 'required'
        });
        tinymce.remove('#content');
        init_editor('#content');
    });
}

function get_templates(rel_type, rel_id) {
    if (rel_type === 'pur_contracts') {
        $('#contract-templates').load(admin_url + 'templates', {
            rel_type: rel_type,
            rel_id: rel_id
        });
    }
}

function insert_template(wrapper, rel_type, id) {
    requestGetJSON(admin_url + 'templates/index/' + id).done(function (response) {
        var data = response.data;
        tinymce.activeEditor.execCommand('mceInsertContent', false, data.content);
        if (rel_type == 'pur_contracts') {
            $('a[aria-controls="tab_content"]').click()
        }
        tinymce.activeEditor.focus();
    });
}

<?php } ?>
</script>