<script type="text/javascript">
(function($) {
	$(".download_file").click(function (e) {
		var data = {};
		data.password = $('input[name="password"]').val();
		data.hash_share = $('input[name="hash_share"]').val();
   		data.csrf_token_name = $('input[name="<?php echo html_entity_decode($this->security->get_csrf_token_name()); ?>"]').val();


		$.post("<?php echo site_url('file_sharing/file_sharing_public/check_download'); ?>", data).done(function(response) {
	      	response = JSON.parse(response);
		    if(response.success){
		    	if(response.mime == 'directory'){
		    		e.preventDefault();
		    		window.location.href = '<?php echo site_url('file_sharing/download_directory'); ?>/'+data.hash_share;
		    	}else{
		    		e.preventDefault();
		    		window.location.href = '<?php echo site_url('file_sharing/download_file'); ?>/'+data.hash_share;
		    	}


				$.post("<?php echo site_url('file_sharing/file_sharing_public/download'); ?>", data).done(function(response) {
			      	response = JSON.parse(response);
			    	if(response.hidden){
			    		$(".view_file").html("<?php echo _l('file_or_folder_does_not_exist'); ?>");
			    	}

				    if(response.message){
		                alert_float('success', response.message);
			    	}

					$('#confirm-password-modal').modal('hide');
			    });
		    }else{
			    if(response.message){
	                alert_float('warning', response.message);
		    	}
				$('#confirm-password-modal').modal('hide');
		    }
	    });
	});

	$(".confirm_password").click(function (e) {
		$('#confirm-password-modal').modal('show');
	});
})(jQuery);

// Show password on hidden input field
function showPassword(name) {
    var target = $('input[name="' + name + '"]');
    if ($(target).attr('type') == 'password' && $(target).val() !== '') {
        $(target)
            .queue(function() {
                $(target).attr('type', 'text').dequeue();
            });
    } else {
        $(target).queue(function() {
            $(target).attr('type', 'password').dequeue();
        });
    }
}

// Generate float alert
function alert_float(type, message, timeout) {
    var aId, el;

    aId = $("body").find('float-alert').length;
    aId++;

    aId = 'alert_float_' + aId;

    el = $("<div></div>", {
        "id": aId,
        "class": "float-alert animated fadeInRight col-xs-10 col-sm-3 alert alert-" + type,
    });

    el.append('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
    el.append('<span class="fa fa-bell-o" data-notify="icon"></span>');
    el.append("<span class=\"alert-title\">" + message + "</span>");

    $("body").append(el);
    timeout = timeout ? timeout : 3500
    setTimeout(function() {
        $('#' + aId).hide('fast', function() {
            $('#' + aId).remove();
        });
    }, timeout);
}
</script>