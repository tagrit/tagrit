<script src = "//cdnjs.cloudflare.com/ajax/libs/require.js/2.3.2/require.min.js"> </script> 
<script type="text/javascript">
 var fnServerParams;
(function($){
	"use strict";

	appValidateForm($('#fs-share-form'), {
    },fs_share_form_handler);

    appValidateForm($('#fs-edit-sharing-form'), {
    },fs_edit_share_form_handler);

  	appValidateForm($('#fs-send-mail-form'), {
    	email: 'required'
    },fs_send_mail_form_handler);
	
	fnServerParams = {
	    "share_hash": '[name="share_hash"]',
	  };

	$('#share-modal input[name="type"]').change(function(){
	    var type = $(this).val();
	    if(type == 'fs_public'){
	      $('#share-modal #div_public').removeClass('hide');
	      $('#share-modal #div_client').addClass('hide');
	      $('#share-modal #div_staff').addClass('hide');
	      $('#share-modal #div_public_permisstion').removeClass('hide');
	      $('#share-modal #div_permisstion').addClass('hide');
	    }else if(type == 'fs_client'){
	      $('#share-modal #div_public').addClass('hide');
	      $('#share-modal #div_client').removeClass('hide');
	      $('#share-modal #div_staff').addClass('hide');
	      $('#share-modal #div_public_permisstion').addClass('hide');
	      $('#share-modal #div_permisstion').removeClass('hide');
	    }else{
	      $('#share-modal #div_public').addClass('hide');
	      $('#share-modal #div_client').addClass('hide');
	      $('#share-modal #div_staff').removeClass('hide');
	      $('#share-modal #div_public_permisstion').addClass('hide');
	      $('#share-modal #div_permisstion').removeClass('hide');
	    }
	});

	$('#share-modal input[name="expiration_date_apply"]').on('change', function() {
	    if($('#share-modal input[name="expiration_date_apply"]').is(':checked') == true){
	      $('#share-modal #div_expiration_date').removeClass('hide');
	    }else{
	      $('#share-modal #div_expiration_date').addClass('hide');
	    }
	});

	$('#share-modal input[name="download_limits_apply"]').on('change', function() {
	  if($('#share-modal input[name="download_limits_apply"]').is(':checked') == true){
	    $('#share-modal #div_download_limit').removeClass('hide');
	  }else{
	    $('#share-modal #div_download_limit').addClass('hide');
	  }
	});

	var // jQuery and jQueryUI version
		jqver = '3.4.1',
		uiver = '1.12.1',
		
		// Detect language (optional)
		lang = (function() {
			return '<?php echo get_media_locale($locale); ?>';
		})(),
		
		// Start elFinder (REQUIRED)
		start = function(elFinder, editors, config) {
			// load jQueryUI CSS
			elFinder.prototype.loadCss(site_url+'modules/file_sharing/assets/plugins/elFinder-2.1.57/css/jquery-ui.css');
			// load jQueryUI CSS
              elFinder.prototype.loadCss('//cdnjs.cloudflare.com/ajax/libs/jqueryui/' + uiver + '/themes/smoothness/jquery-ui.css');

			$(function() {
                var elfEditorCustomData = {};
				if (typeof(csrfData) !== 'undefined') {
	                elfEditorCustomData[csrfData['token_name']] = csrfData['hash'];
	            }
				var optEditors = {
						commandsOptions: {
							edit: {
								editors: Array.isArray(editors)? editors : []
							}
						}
					},
					opts = {
                        height: $(document).outerHeight(true) - 126,
                        customData: elfEditorCustomData,
					};
				
				// Interpretation of "elFinderConfig"
				if (config && config.managers) {
					$.each(config.managers, function(id, mOpts) {
						opts = Object.assign(opts, config.defaultOpts || {});
						// editors marges to opts.commandOptions.edit
						try {
							mOpts.commandsOptions.edit.editors = mOpts.commandsOptions.edit.editors.concat(editors || []);
						} catch(e) {
							Object.assign(mOpts, optEditors);
						}
						// Make elFinder
						$('#' + id).elfinder(
							// 1st Arg - options
							$.extend(true, { lang: lang }, opts, mOpts || {}),
							// 2nd Arg - before boot up function
							function(fm, extraObj) {
								// `init` event callback function
								fm.bind('init', function() {
									// Optional for Japanese decoder "encoding-japanese"
									if (fm.lang === 'ja') {
										require(
											[ 'encoding-japanese' ],
											function(Encoding) {
												if (Encoding && Encoding.convert) {
													fm.registRawStringDecoder(function(s) {
														return Encoding.convert(s, {to:'UNICODE',type:'string'});
													});
												}
											}
										);
									}
								});
							}
						);
					});
				} else {
					alert('"elFinderConfig" object is wrong.');
				}
			});
		},
		
		// JavaScript loader (REQUIRED)
		load = function() {
			require(
				[
					'elfinder'
					, site_url+'modules/file_sharing/assets/plugins/elFinder-2.1.57/js/extras/editors.default.min.js'               // load text, image editors
					, 'elFinderConfig'
				//	, 'extras/quicklook.googledocs.min'          // optional preview for GoogleApps contents on the GoogleDrive volume
				],
				start,
				function(error) {
					alert(error.message);
				}
			);
		},
		
		// is IE8 or :? for determine the jQuery version to use (optional)
		old = (typeof window.addEventListener === 'undefined' && typeof document.getElementsByClassName === 'undefined')
		       ||
		      (!window.chrome && !document.unqueID && !window.opera && !window.sidebar && 'WebkitAppearance' in document.documentElement.style && document.body.style && typeof document.body.style.webkitFilter === 'undefined');

	// config of RequireJS (REQUIRED)
	require.config({
		baseUrl : 'js',
		paths : {
			'jquery'   : site_url+'modules/file_sharing/assets/plugins/elFinder-2.1.57/jquery.min',
			'jquery-ui': site_url+'modules/file_sharing/assets/plugins/elFinder-2.1.57/jquery-ui.min',
			'elfinder' : site_url+'modules/file_sharing/assets/plugins/elFinder-2.1.57/js/elfinder.min'
		},
		waitSeconds : 10 // optional
	});

	// check elFinderConfig and fallback
	// This part don't used if you are using elfinder.html, see elfinder.html
	if (! require.defined('elFinderConfig')) {
		define('elFinderConfig', {
			// elFinder options (REQUIRED)
			// Documentation for client options:
			// https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
			defaultOpts : {
				url : '<?php echo html_entity_decode($connector); ?>' // connector URL (REQUIRED)
				,commandsOptions : {
					edit : {
						extraOptions : {
							// set API key to enable Creative Cloud image editor
							// see https://console.adobe.io/
							creativeCloudApiKey : '',
							// browsing manager URL for CKEditor, TinyMCE
							// uses self location with the empty value
							managerUrl : ''
						}
					}
					,quicklook : {
						// to enable CAD-Files and 3D-Models preview with sharecad.org
						sharecadMimes : ['image/vnd.dwg', 'image/vnd.dxf', 'model/vnd.dwf', 'application/vnd.hp-hpgl', 'application/plt', 'application/step', 'model/iges', 'application/vnd.ms-pki.stl', 'application/sat', 'image/cgm', 'application/x-msmetafile'],
						// to enable preview with Google Docs Viewer
						googleDocsMimes : ['application/pdf', 'image/tiff', 'application/vnd.ms-office', 'application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/postscript', 'application/rtf'],
                    	// to enable preview with Microsoft Office Online Viewer
						// these MIME types override "googleDocsMimes"
						officeOnlineMimes : ['application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.oasis.opendocument.presentation']

					}
				},
				themes : {
                'dark-slim'     : 'https://johnfort.github.io/elFinder.themes/dark-slim/manifest.json',
                'material'      : 'https://nao-pon.github.io/elfinder-theme-manifests/material-default.json',
                'material-gray' : 'https://nao-pon.github.io/elfinder-theme-manifests/material-gray.json',
                'material-light': 'https://nao-pon.github.io/elfinder-theme-manifests/material-light.json',
                'bootstrap'     : 'https://nao-pon.github.io/elfinder-theme-manifests/bootstrap.json',
                'moono'         : 'https://nao-pon.github.io/elfinder-theme-manifests/moono.json',
                'win10'         : 'https://nao-pon.github.io/elfinder-theme-manifests/win10.json'
            },
				bootCallback: function(fm, extraObj) {
					/* any bind functions etc. */
		              fm.bind('init', function() {
		                  // any your code
		              });
		              // for example set document.title dynamically.
		              var title = document.title;
		              fm.bind('open', function() {
		                  var path = '',
		                      cwd = fm.cwd();
		                  if (cwd) {
		                      path = fm.path(cwd.hash) || null;
		                  }
		                  document.title = path ? path + ':' + title : title;
		              }).bind('destroy', function() {
		                  document.title = title;
		              });

	              	(fm.commands.share = function() {

		              	this.exec = function(hashes, data) {
		              		var files = fm.files();
		              		var file = $.map(fm.selected(), function(hash) { return files[hash] ? Object.assign({}, files[hash]) : null; });

		              		fm.request({
								data : {cmd : 'url', target : file[0]['hash']},
								preventDefault : true
							})
							.always(function() {
		              			$('#share-modal input[name="url"]').val('');
							})
							.done(function(data) {
								var rfile = fm.file(file[0]['hash']);
								file[0].url = rfile.url = data.url || '';
								if (file[0].url) {
		              				$('#share-modal input[name="url"]').val(file[0]['url']);

		              				if(file[0]['url'].indexOf("<?php echo FILE_SHARING_FOLDER_NAME; ?>/Public") > 0){
								    	$('#share-modal input:radio[name=type]').filter('[value=fs_client]').prop('checked', true);
								    	$('#share-modal input:radio[name=type]').filter('[value=fs_staff]').prop('disabled', true);
								    	$('#share-modal input:radio[name=type]').filter('[value=fs_client]').prop('disabled', false);

								    	$('#share-modal #div_public').addClass('hide');
									    $('#share-modal #div_client').removeClass('hide');
									    $('#share-modal #div_staff').addClass('hide');
				              		}else if(file[0]['url'].indexOf("<?php echo FILE_SHARING_FOLDER_NAME; ?>/Client") > 0){
								    	$('#share-modal input:radio[name=type]').filter('[value=fs_client]').prop('disabled', true);

				              			$('#share-modal input:radio[name=type]').filter('[value=fs_staff]').prop('checked', true);
								    	$('#share-modal input:radio[name=type]').filter('[value=fs_staff]').prop('disabled', false);

								    	$('#share-modal #div_public').addClass('hide');
								        $('#share-modal #div_client').addClass('hide');
								        $('#share-modal #div_staff').removeClass('hide');
				              		}else{
				              			
									    	$('#share-modal input:radio[name=type]').filter('[value=fs_client]').prop('disabled', false);
									    	$('#share-modal input:radio[name=type]').filter('[value=fs_staff]').prop('disabled', false);
									    	$('#share-modal input:radio[name=type]').filter('[value=fs_staff]').prop('checked', true);

									    	$('#share-modal #div_public').addClass('hide');
								        $('#share-modal #div_client').addClass('hide');
								        $('#share-modal #div_staff').removeClass('hide');
				              		}
								}
							});

		              		$('#share-modal input[name="isowner"]').val(file[0]['isowner']);
		              		$('#share-modal input[name="hash"]').val(file[0]['hash']);
		              		$('input[name="share_hash"]').val(file[0]['hash']);
		              		$('#share-modal input[name="locked"]').val(file[0]['locked']);
		              		$('#share-modal input[name="mime"]').val(file[0]['mime']);
		              		$('#share-modal input[name="name"]').val(file[0]['name']);
		              		$('#share-modal input[name="phash"]').val(file[0]['phash']);
		              		$('#share-modal input[name="read"]').val(file[0]['read']);
		              		$('#share-modal input[name="size"]').val(file[0]['size']);
		              		$('#share-modal input[name="ts"]').val(file[0]['ts']);
		              		$('#share-modal input[name="write"]').val(file[0]['write']);

		              		

		              		init_sharing_table();
	                    	$('#sharing-list-modal').modal('show');
	                  	};

		                this.getstate = function(sel) {
		                    var sel = this.files(sel);
		                    var cnt = sel? sel.length : 0;
		                    return cnt === 1 ? 0 : -1; 
		                };

	                }).prototype = {
	                	forceLoad : true // Force load this command
	              	};

	                fm.options.contextmenu.files.push('share');
	          	},
			},
			managers : {
				'elfinder': {},
			}
		});
	}

	// load JavaScripts (REQUIRED)
	load();

	$('#edit-sharing-modal input[name="expiration_date_apply"]').on('change', function() {
	      if($('#edit-sharing-modal input[name="expiration_date_apply"]').is(':checked') == true){
	        $('#edit-sharing-modal #div_expiration_date').removeClass('hide');
	      }else{
	        $('#edit-sharing-modal #div_expiration_date').addClass('hide');
	      }
	  });

	  $('#edit-sharing-modal input[name="download_limits_apply"]').on('change', function() {
	    if($('#edit-sharing-modal input[name="download_limits_apply"]').is(':checked') == true){
	      $('#edit-sharing-modal #div_download_limit').removeClass('hide');
	    }else{
	      $('#edit-sharing-modal #div_download_limit').addClass('hide');
	    }
	  });
}(jQuery));

function fs_share_form_handler(form) {

    $('#share-modal').find('button[type="submit"]').prop('disabled', true);

    var formURL = form.action;
    var formData = new FormData($(form)[0]);

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
        if (response.success === true || response.success == 'true') { alert_float('success', response.message); }
        $('#share-modal').modal('hide');
    	$('#share-modal').find('button[type="submit"]').prop('disabled', false);
		init_sharing_table();
		$('#sharing-list-modal').modal('show');

    }).fail(function(error) {
    	$('#share-modal').find('button[type="submit"]').prop('disabled', false);
        alert_float('danger', JSON.parse(error.responseText));
    });

    return false;
}

function copy_public_link(add){
  "use strict";
    var link = $('#public_link').val();

    if(add){
    	var copyText = document.getElementById("add_public_link");
    }else{
	    var copyText = document.getElementById("public_link");
    }
    copyText.select();
    copyText.setSelectionRange(0, 99999)
    document.execCommand("copy");
    alert_float('success','Copied!');
}

function init_sharing_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-sharing')) {
    $('.table-sharing').DataTable().destroy();
  }
  initDataTable('.table-sharing', admin_url + 'file_sharing/sharing_detail_table', false, false, fnServerParams);
}

function new_share(){
  	"use strict";

	$('#sharing-list-modal').modal('hide');
	requestGetJSON("file_sharing/get_hash_share").done(
    function (hash) {
			$('#share-modal #add_public_link').val(site_url+'file_sharing/'+hash);
			$('#share-modal input[name=hash_share]').val(hash);
			$('#share-modal').modal('show');
    }
  );
}

function edit_sharing(invoker){
  "use strict";
	$('#sharing-list-modal').modal('hide');

  $('#edit-sharing-modal').find('button[type="submit"]').prop('disabled', false);

  var id = $(invoker).data('id');
  var is_read = $(invoker).data('is_read') == 1 ? true : false;
  var is_write = $(invoker).data('is_write') == 1 ? true : false;
  var is_delete = $(invoker).data('is_delete') == 1 ? true : false;
  var is_upload = $(invoker).data('is_upload') == 1 ? true : false;
  var is_download = $(invoker).data('is_download') == 1 ? true : false;
  var type = $(invoker).data('type');
  var staffs = $(invoker).data('staffs');
  var roles = $(invoker).data('roles');
  var hash_share = $(invoker).data('hash_share');
  var customers = $(invoker).data('customers');
  var password = $(invoker).data('password');
  var customer_groups = $(invoker).data('customer_groups');
  var expiration_date_apply = $(invoker).data('expiration_date_apply');
  var expiration_date = $(invoker).data('expiration_date');
  var expiration_date_delete = $(invoker).data('expiration_date_delete');
  var download_limits_apply = $(invoker).data('download_limits_apply');
  var download_limits = $(invoker).data('download_limits') == 0 ? '' : $(invoker).data('download_limits');
  var download_limits_delete = $(invoker).data('download_limits_delete');

  $('#edit-sharing-modal input[name="id"]').val(id);
  $('#edit-sharing-modal input[name="password"]').val(password);
  $('#edit-sharing-modal input[name="public_link"]').val(site_url + 'file_sharing/' + hash_share);

  $('#edit-sharing-modal #is_read').attr('checked', is_read);
  $('#edit-sharing-modal #is_write').attr('checked', is_write);
  $('#edit-sharing-modal #is_delete').attr('checked', is_delete);
  $('#edit-sharing-modal #is_upload').attr('checked', is_upload);

  if(type == 'fs_public'){
    $('#edit-sharing-modal #fs_public').prop('checked',true);
    $('#edit-sharing-modal #div_public').removeClass('hide');
    $('#edit-sharing-modal #div_client').addClass('hide');
    $('#edit-sharing-modal #div_staff').addClass('hide');
    $('#edit-sharing-modal #public_is_download').attr('checked', is_download);
    $('#edit-sharing-modal #div_public_permisstion').removeClass('hide');
	  $('#edit-sharing-modal #div_permisstion').addClass('hide');
  }else if(type == 'fs_client'){
    $('#edit-sharing-modal #fs_client').prop('checked',true);
    $('#edit-sharing-modal #div_public').addClass('hide');
    $('#edit-sharing-modal #div_client').removeClass('hide');
    $('#edit-sharing-modal #div_staff').addClass('hide');
    $('#edit-sharing-modal #is_download').attr('checked', is_download);
    $('#edit-sharing-modal #div_public_permisstion').addClass('hide');
	  $('#edit-sharing-modal #div_permisstion').removeClass('hide');
  }else{
    $('#edit-sharing-modal #fs_staff').prop('checked',true);
    $('#edit-sharing-modal #div_public').addClass('hide');
    $('#edit-sharing-modal #div_client').addClass('hide');
    $('#edit-sharing-modal #div_staff').removeClass('hide');
    $('#edit-sharing-modal #is_download').attr('checked', is_download);
    $('#edit-sharing-modal #div_public_permisstion').addClass('hide');
	  $('#edit-sharing-modal #div_permisstion').removeClass('hide');
  }
  $('#edit-sharing-modal :radio(:checked)').attr('disabled', false);
  $('#edit-sharing-modal :radio:not(:checked)').attr('disabled', true);

  if (!empty(staffs)) {
    if(staffs.toString().indexOf(",") > 0){
      var selected = staffs.split(',');
    }else{
      var selected = [staffs.toString()];
    }
    selected = selected.map(function(e) {
        return e.trim();
    });
    $('#edit-sharing-modal select[name="staff[]"]').selectpicker('val', selected);
  }
  if (!empty(roles)) {
    if(roles.toString().indexOf(",") > 0){
      var selected = roles.split(',');
    }else{
      var selected = [roles.toString()];
    }
    selected = selected.map(function(e) {
        return e.trim();
    });
    $('#edit-sharing-modal select[name="role[]"]').selectpicker('val', selected);
  }
  if (!empty(customers)) {
    if(customers.toString().indexOf(",") > 0){
      var selected = customers.split(',');
    }else{
      var selected = [customers.toString()];
    }
    selected = selected.map(function(e) {
        return e.trim();
    });
    $('#edit-sharing-modal select[name="customer[]"]').selectpicker('val', selected);
  }
  if (!empty(customer_groups)) {
    if(customer_groups.toString().indexOf(",") > 0){
      var selected = customer_groups.split(',');
    }else{
      var selected = [customer_groups.toString()];
    }
    selected = selected.map(function(e) {
        return e.trim();
    });
    $('#edit-sharing-modal select[name="customer_group[]"]').selectpicker('val', selected);
  }
  
  if(expiration_date_apply == 1){
    $('#edit-sharing-modal #expiration_date_apply').prop('checked', true);
    $('#edit-sharing-modal #expiration_date').val(expiration_date);
    if(expiration_date_delete == 1){
      $('#edit-sharing-modal #expiration_date_delete').prop('checked', true);
    }else{
      $('#edit-sharing-modal #expiration_date_delete').prop('checked', false);
    }
    $('#edit-sharing-modal #div_expiration_date').removeClass('hide');
  }else{
    $('#edit-sharing-modal #expiration_date_apply').prop('checked', false);
    $('#edit-sharing-modal #expiration_date_delete').prop('checked', false);
    $('#edit-sharing-modal #expiration_date').val('');
    $('#edit-sharing-modal #div_expiration_date').addClass('hide');
  }

  if(download_limits_apply == 1){
    $('#edit-sharing-modal #download_limits_apply').prop('checked', true);
    $('#edit-sharing-modal #download_limits').val(download_limits);
    if(download_limits_delete == 1){
      $('#edit-sharing-modal #download_limits_delete').prop('checked', true);
    }else{
      $('#edit-sharing-modal #download_limits_delete').prop('checked', false);
    }
    $('#edit-sharing-modal #div_download_limit').removeClass('hide');
  }else{
    $('#edit-sharing-modal #download_limits_apply').prop('checked', false);
    $('#edit-sharing-modal #download_limits_delete').prop('checked', false);
    $('#edit-sharing-modal #download_limits').val('');
    $('#edit-sharing-modal #div_download_limit').addClass('hide');
  }

  $('#edit-sharing-modal').modal('show');
}

function fs_edit_share_form_handler(form) {
    $('#edit-sharing-modal').find('button[type="submit"]').prop('disabled', true);

    var formURL = form.action;
    var formData = new FormData($(form)[0]);

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
        if (response.success === true || response.success == 'true') { 
          alert_float('success', response.message); 
          init_sharing_table();
        }else{
          alert_float('danger', response.message); 
        }
        $('#edit-sharing-modal').modal('hide');
		$('#sharing-list-modal').modal('show');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}

function delete_sharing(id) {
  "use strict";
    if (confirm("Are you sure?")) {
      var url = admin_url + 'file_sharing/delete_sharing/'+id;

      requestGet(url).done(function(response){
          response = JSON.parse(response);
          if (response.success === true || response.success == 'true') { 
            alert_float('success', response.message); 
            init_sharing_table();
          }else{
            alert_float('danger', response.message); 
          }
      });
    }
    return false;
}

function close_share(){
  $('#share-modal').modal('hide');
  $('#sharing-list-modal').modal('show');
}

function close_edit_sharing(){
  $('#edit-sharing-modal').modal('hide');
  $('#sharing-list-modal').modal('show');
}

function send_mail(){
  $('#send-mail-modal').find('button[type="submit"]').prop('disabled', false);
  $('#send-mail-modal input[name="id"]').val($('#edit-sharing-modal input[name="id"]').val());
  $('#edit-sharing-modal').modal('hide');
  $('#send-mail-modal').modal('show');
}

function close_send_mail(){
  $('#send-mail-modal').modal('hide');
  $('#edit-sharing-modal').modal('show');
}

function fs_send_mail_form_handler(form) {
    $('#send-mail-modal').find('button[type="submit"]').prop('disabled', true);

    var formURL = form.action;
    var formData = new FormData($(form)[0]);

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
        if (response.success === true || response.success == 'true') { 
          alert_float('success', response.message); 
        }else{
          alert_float('danger', response.message); 
        }
        $('#send-mail-modal').modal('hide');
        $('#edit-sharing-modal').modal('show');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}

function copy_public_link_v2(hash){
  "use strict";
    var link = site_url+'file_sharing/'+hash;

    var copyText = document.createElement("input");
  	copyText.value = link;
		
		document.body.appendChild(copyText);
		copyText.focus();
    copyText.select();
    document.execCommand("copy");
    document.body.removeChild(copyText);
    alert_float('success','Copied!');
}
</script>