  var fnServerParams;
(function($) {
  "use strict";
  appValidateForm($('#fs-share-form'), {
    },fs_share_form_handler);

  appValidateForm($('#fs-send-mail-form'), {
    email: 'required'
    },fs_send_mail_form_handler);

  fnServerParams = {
    "member_filter": '[name="member_filter"]',
    "type": '[name="type"]',
    "status": '[name="status"]',
    "password": '[name="password"]',
    "from_date": '[name="from_date"]',
    "to_date": '[name="to_date"]',
  }

  $("body").on('change', '.fs-permisstion-switch input', function(event, state) {
      var switch_url = $(this).data('id');
      if (!switch_url) { return; }
      fs_switch_field(this);
  });

  $('input[name="expiration_date_apply"]').on('change', function() {
      if($('input[name="expiration_date_apply"]').is(':checked') == true){
        $('#div_expiration_date').removeClass('hide');
      }else{
        $('#div_expiration_date').addClass('hide');
      }
  });

  $('input[name="download_limits_apply"]').on('change', function() {
    if($('input[name="download_limits_apply"]').is(':checked') == true){
      $('#div_download_limit').removeClass('hide');
    }else{
      $('#div_download_limit').addClass('hide');
    }
  });

  $('select[name="member_filter"]').on('change', function() {
    init_sharing_table();
  });

  $('select[name="type"]').on('change', function() {
    init_sharing_table();
  });

  $('select[name="status"]').on('change', function() {
    init_sharing_table();
  });

  $('select[name="password"]').on('change', function() {
    init_sharing_table();
  });

  $('input[name="from_date"]').on('change', function() {
    init_sharing_table();
  });

  $('input[name="to_date"]').on('change', function() {
    init_sharing_table();
  });
  
  init_sharing_table();
})(jQuery);


function init_sharing_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-sharing')) {
    $('.table-sharing').DataTable().destroy();
  }
  initDataTable('.table-sharing', admin_url + 'file_sharing/sharing_table', false, false, fnServerParams);
}

// Switch field make request
function fs_switch_field(field) {
  "use strict";
    var status, url, id, name;
    status = 0;
    if ($(field).prop('checked') === true) { status = 1; }
    url = admin_url + 'file_sharing/update_sharing_permission';
    name = $(field).attr('name');
    id = $(field).data('id');

    requestGet(url + '/' + id + '/' + status + '/' + name).done(function(response){
        response = JSON.parse(response);
        alert_float('success', response.success);
    });
}

function edit_sharing(invoker){
  "use strict";
  $('#share-modal').find('button[type="submit"]').prop('disabled', false);

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

  $('input[name="id"]').val(id);
  $('input[name="password"]').val(password);
  $('input[name="public_link"]').val(site_url + 'file_sharing/' + hash_share);

  $('#is_read').attr('checked', is_read);
  $('#is_write').attr('checked', is_write);
  $('#is_delete').attr('checked', is_delete);
  $('#is_upload').attr('checked', is_upload);

  if(type == 'fs_public'){
    $('#fs_public').prop('checked',true);
    $('#div_public').removeClass('hide');
    $('#div_client').addClass('hide');
    $('#div_staff').addClass('hide');
    $('#public_is_download').attr('checked', is_download);
    $('#div_public_permisstion').removeClass('hide');
    $('#div_permisstion').addClass('hide');
  }else if(type == 'fs_client'){
    $('#fs_client').prop('checked',true);
    $('#div_public').addClass('hide');
    $('#div_client').removeClass('hide');
    $('#div_staff').addClass('hide');
    $('#is_download').attr('checked', is_download);
    $('#div_public_permisstion').addClass('hide');
    $('#div_permisstion').removeClass('hide');
  }else{
    $('#fs_staff').prop('checked',true);
    $('#div_public').addClass('hide');
    $('#div_client').addClass('hide');
    $('#div_staff').removeClass('hide');
    $('#is_download').attr('checked', is_download);
    $('#div_public_permisstion').addClass('hide');
    $('#div_permisstion').removeClass('hide');
  }
  $(':radio(:checked)').attr('disabled', false);
  $(':radio:not(:checked)').attr('disabled', true);

  if (!empty(staffs)) {
    if(staffs.toString().indexOf(",") > 0){
      var selected = staffs.split(',');
    }else{
      var selected = [staffs.toString()];
    }
    selected = selected.map(function(e) {
        return e.trim();
    });
    $('select[name="staff[]"]').selectpicker('val', selected);
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
    $('select[name="role[]"]').selectpicker('val', selected);
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
    $('select[name="customer[]"]').selectpicker('val', selected);
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
    $('select[name="customer_group[]"]').selectpicker('val', selected);
  }
  
  if(expiration_date_apply == 1){
    $('#expiration_date_apply').prop('checked', true);
    $('#expiration_date').val(expiration_date);
    if(expiration_date_delete == 1){
      $('#expiration_date_delete').prop('checked', true);
    }else{
      $('#expiration_date_delete').prop('checked', false);
    }
    $('#div_expiration_date').removeClass('hide');
  }else{
    $('#expiration_date_apply').prop('checked', false);
    $('#expiration_date_delete').prop('checked', false);
    $('#expiration_date').val('');
    $('#div_expiration_date').addClass('hide');
  }

  if(download_limits_apply == 1){
    $('#download_limits_apply').prop('checked', true);
    $('#download_limits').val(download_limits);
    if(download_limits_delete == 1){
      $('#download_limits_delete').prop('checked', true);
    }else{
      $('#download_limits_delete').prop('checked', false);
    }
    $('#div_download_limit').removeClass('hide');
  }else{
    $('#download_limits_apply').prop('checked', false);
    $('#download_limits_delete').prop('checked', false);
    $('#download_limits').val('');
    $('#div_download_limit').addClass('hide');
  }

  $('#share-modal').modal('show');
}

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
        if (response.success === true || response.success == 'true') { 
          alert_float('success', response.message); 
          init_sharing_table();
        }else{
          alert_float('danger', response.message); 
        }
        $('#share-modal').modal('hide');
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
function copy_public_link(){
  "use strict";
    var link = $('#public_link').val();
    var copyText = document.getElementById("public_link");
    copyText.select();
    copyText.setSelectionRange(0, 99999)
    document.execCommand("copy");
    alert_float('success','Copied!');
}

function send_mail(){
  $('#send-mail-modal').find('button[type="submit"]').prop('disabled', false);
  $('#send-mail-modal input[name="id"]').val($('#share-modal input[name="id"]').val());
  $('#share-modal').modal('hide');
  $('#send-mail-modal').modal('show');
}

function close_send_mail(){
  $('#send-mail-modal').modal('hide');
  $('#share-modal').modal('show');
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
        $('#share-modal').modal('show');
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
    copyText.select();
    document.execCommand("copy");
    document.body.removeChild(copyText);
    alert_float('success','Copied!');
}