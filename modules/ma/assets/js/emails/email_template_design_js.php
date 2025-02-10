<script type="text/javascript">
loadEditor = () => {
    const options = {};
    this.editor = unlayer.createEditor({
      ...options,
      id: 'EmailEditor',
      displayMode: 'email',
    });

    if($("input[name=data_design]").val() != ''){
      this.editor.loadDesign(JSON.parse(JSON.parse($("input[name=data_design]").val())));
    }

  registerCallback = (type, callback) => {
    this.editor.registerCallback(type, callback);
  };

  addEventListener = (type, callback) => {
    this.editor.addEventListener(type, callback);
  };

  loadDesign = (design) => {
    this.editor.loadDesign(design);
  };

  saveDesign = (callback) => {
    this.editor.saveDesign((design) => {
      $("input[name=data_design]").val(JSON.stringify(design, false));
    });
  };

  exportHtml = (callback) => {
    this.editor.exportHtml((data) => {
      const { design, html } = data;
      $("input[name=data_html]").val(html);
    });
  };

  setMergeTags = (mergeTags) => {
    this.editor.setMergeTags(mergeTags);
  };
}

(function($) {
    "use strict";

    loadScript(loadEditor);

    appValidateForm($('#email-template-form'), {
      name: 'required',
      type: 'required',
    },template_form_handler);
    
})(jQuery);

function save_template(){
  saveDesign();
  exportHtml();

  $('#btn-submit').attr('disabled', true);

  setTimeout(function(){
      $('#email-template-form').submit();
    }, 1000);
}


function template_form_handler(form) {
    "use strict";

    var formURL = form.action;
    var formData = new FormData($(form)[0]);

    var data_design = new File([$("input[name=data_design]").val()], "data_design.txt", { type: "text/plain" });
    var data_html = new File([$("input[name=data_html]").val()], "data_html.txt", { type: "text/plain" });
    formData.append("data_html", data_html);
    formData.append("data_design", data_design);

    $.ajax({
        type: $(form).attr('method'),
        data: formData,
        mimeType: $(form).attr('enctype'),
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response) {
        response = JSON.parse(response);
        if (response.success === true || response.success == 'true' || $.isNumeric(response.success)) {
          alert_float('success', response.message);
          if(response.url){
            window.location.assign(response.url);
          }
        }else {
          alert_float('danger', response.message);
          $('#btn-submit').attr('disabled', false);
        }
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
        $('#btn-submit').attr('disabled', false);
    });

    return false;
}
</script>