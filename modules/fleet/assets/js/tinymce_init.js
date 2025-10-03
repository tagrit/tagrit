if(tinymce.majorVersion + '.' + tinymce.minorVersion == '6.8.3'){
	initTinymceEditor();
}

function initTinymceEditor(selector, settings) {
  selector = typeof selector == "undefined" ? ".tinymce" : selector;
  var _editorSelectorCheck = document.querySelectorAll(selector);

  if (_editorSelectorCheck.length === 0) {
    return;
  }

  _editorSelectorCheck.forEach(function (el) {
    if (el.classList.contains("tinymce-manual")) {
      el.classList.remove("tinymce");
    }
  });

  // Original settings
  var _settings = {
    branding: false, // Ensure branding is set to false
    selector: selector,
    browser_spellcheck: true,
    height: 400,
    skin: "oxide", // Updated skin for TinyMCE 6.8.3
    language: app.tinymce_lang,
    relative_urls: false,
    inline_styles: true,
    verify_html: false,
    cleanup: false,
    autoresize_bottom_margin: 25,
    valid_elements: "+*[*]",
    valid_children: "+body[style], +style[type]",
    apply_source_formatting: false,
    remove_script_host: false,
    removed_menuitems: "newdocument restoredraft",
    forced_root_block: "p",
    autosave_restore_when_empty: false,
    fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
    setup: function (editor) {
      // Default fontsize is 12
      editor.on("init", function () {
        editor.getBody().style.fontSize = "12pt";
      });
    },
    table_default_styles: {
      // Default all tables width 100%
      width: "100%",
    },
    plugins: [
      "advlist autoresize autosave lists link image print hr codesample",
      "visualblocks code fullscreen",
      "media save table contextmenu",
      "paste textcolor colorpicker",
    ],
    toolbar:
      "fontselect fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | image link | bullist numlist | restoredraft",
    // file_picker_callback: elFinderBrowser,
    contextmenu:
      "link image inserttable | cell row column deletetable | paste copy",
  };

  // Add the rtl to the settings if is true
  if (isRTL == "true") {
    _settings.directionality = "rtl";
    _settings.plugins.push("directionality");
  }

  // Possible settings passed to be overwritten or added
  if (typeof settings != "undefined") {
    for (var key in settings) {
      if (key != "append_plugins") {
        _settings[key] = settings[key];
      } else {
        _settings["plugins"].push(settings[key]);
      }
    }
  }

  // Init the editor
  tinymce.init(_settings).then((editors) => {
    document.dispatchEvent(new Event("app.editor.initialized"));
  });

  // Add custom CSS to hide the branding
  var style = document.createElement('style');
  style.innerHTML = '.tox-promotion { display: none !important; }';
  document.head.appendChild(style);

  return tinymce.activeEditor;
}