$(function(){
	"use strict";
	initDataTable('.table-fs-config-share', window.location.href);
	// Bootstrap switch active or inactive global function
    $("body").on('change', '.fs-permisstion-switch input', function(event, state) {
        var switch_url = $(this).data('id');
        if (!switch_url) { return; }
        fs_switch_field(this);
    });

    // New modal show 
    $('.add-new-config').on('click', function(){
        $('#config-modal .add-title').removeClass('hide');
        $('#config-modal .update-title').addClass('hide');
        $('#config-modal .update-title').addClass('hide');
        $('#config-modal input[type="number"], #config-modal input[type="text"], #config-modal input[name="id"], #config-modal select').val('').change();
        $('#config-modal input[type="checkbox"]').prop('checked', false);
        $('#config-modal').modal('show');
    });

    $('input[name="type"]').change(function(){
        var type = $(this).val();
        if(type == 'fs_public'){
          $('#div_public').removeClass('hide');
          $('#div_client').addClass('hide');
          $('#div_staff').addClass('hide');
        }else if(type == 'fs_client'){
          $('#div_public').addClass('hide');
          $('#div_client').removeClass('hide');
          $('#div_staff').addClass('hide');
        }else{
          $('#div_public').addClass('hide');
          $('#div_client').addClass('hide');
          $('#div_staff').removeClass('hide');
        }
    });

    // Edit modal show 
    $(document).on('click', '.edit-config', function(){
        var 
        id              = $(this).data('id'),
        type            = $(this).data('type'),
        staff           = $(this).data('staff-id').toString().indexOf(',') == -1 ? [$(this).data('staff-id')] : $(this).data('staff-id').split(','),
        role            = $(this).data('role-id').toString().indexOf(',') == -1 ? [$(this).data('role-id')] : $(this).data('role-id').split(','),
        customer        = $(this).data('customer-id').toString().indexOf(',') == -1 ? [$(this).data('customer-id')] : $(this).data('customer-id').split(','),
        customer_group  = $(this).data('customer-group-id').toString().indexOf(',') == -1 ? [$(this).data('customer-group-id')] : $(this).data('customer-group-id').split(','),
        read            = $(this).data('read'),
        write           = $(this).data('write'),
        fsDelete        = $(this).data('delete'),
        upload          = $(this).data('upload'),
        download        = $(this).data('download'),
        minSize         = $(this).data('min-size'),
        maxSize         = $(this).data('max-size');
        $('#fs-config-form input[name="id"]').val(id);

        if(type == "fs_staff"){
            $('#div_client').addClass('hide');
            $('#div_staff').removeClass('hide');
            $('input[name="type"]').eq(0).prop("checked", true);
            $('select[name="staff[]"]').val(staff).change();
            $('select[name="role[]"]').val(role).change();
        } else if(type == "fs_client"){
            $('#div_client').removeClass('hide');
            $('#div_staff').addClass('hide');
            $('input[name="type"]').eq(1).prop("checked", true);
            $('select[name="customer[]"]').val(customer).change();
            $('select[name="customer_group[]"]').val(customer_group).change();
        } 

        $('#fs-config-form input[name="min_size"]').val(minSize);
        $('#fs-config-form input[name="max_size"]').val(maxSize);
        
        read = read == 1 ? $('#fs-config-form input[name="is_read"]').prop("checked", true) : $('#fs-config-form input[name="is_read"]').prop("checked", false);    
        write = write == 1 ? $('#fs-config-form input[name="is_write"]').prop("checked", true) : $('#fs-config-form input[name="is_write"]').prop("checked", false);     
        fsDelete = fsDelete == 1 ? $('#fs-config-form input[name="is_delete"]').prop("checked", true) : $('#fs-config-form input[name="is_delete"]').prop("checked", false);   
        upload = upload == 1 ? $('#fs-config-form input[name="is_upload"]').prop("checked", true) : $('#fs-config-form input[name="is_upload"]').prop("checked", false);       
        download = download == 1 ? $('#fs-config-form input[name="is_download"]').prop("checked", true) : $('#config-modal input[name="is_download"]').prop("checked", false);  


        $('#fs-config-form .update-title').removeClass('hide');
        $('#fs-config-form .add-title').addClass('hide');
        $('#config-modal').modal('show');
    });


    $(".toggle-password").click(function() {
      $(this).toggleClass("fa-eye fa-eye-slash");
      var input = $($(this).attr("toggle"));
      if (input.attr("type") == "password") {
        input.attr("type", "text");
    } else {
        input.attr("type", "password");
    }
});

});

// Switch field make request
function fs_switch_field(field) {
	"use strict";
    var status, url, id, name;
    status = 0;
    if ($(field).prop('checked') === true) { status = 1; }
    url = admin_url + 'file_sharing/update_field';
    name = $(field).attr('name');
    id = $(field).data('id');

    requestGet(url + '/' + id + '/' + status + '/' + name).done(function(response){
        response = JSON.parse(response);
        alert_float('success', response.success);
    });
}