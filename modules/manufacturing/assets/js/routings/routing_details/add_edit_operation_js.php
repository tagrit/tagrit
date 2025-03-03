<script>
	
	  init_selectpicker();
	  $(".selectpicker").selectpicker('refresh');

	  appValidateForm($("body").find('#add_edit_operation'), {
	  	'operation': 'required',
	  	'work_center_id': 'required',
	  });    

	// + button for adding more attachments
	var addMoreAttachmentsInputKey = 1;
		//button for adding more attachment in project
		$("body").on('click', '.add_more_attachments_file', function() {
			'use strict';

			if ($(this).hasClass('disabled')) {
				return false;
			}

			var total_attachments = $('.attachments input[name*="file"]').length;
			if ($(this).data('max') && total_attachments >= $(this).data('max')) {
				return false;
			}

			var newattachment = $('.attachments').find('.attachment').eq(0).clone().appendTo('.attachments');
			newattachment.find('input').removeAttr('aria-describedby aria-invalid');
			newattachment.find('input').attr('name', 'file[' + addMoreAttachmentsInputKey + ']').val('');
			newattachment.find($.fn.appFormValidator.internal_options.error_element + '[id*="error"]').remove();
			newattachment.find('.' + $.fn.appFormValidator.internal_options.field_wrapper_class).removeClass($.fn.appFormValidator.internal_options.field_wrapper_error_class);
			newattachment.find('i').removeClass('fa-plus').addClass('fa-minus');
			newattachment.find('button').removeClass('add_more_attachments_file').addClass('remove_attachment_file').removeClass('btn-success').addClass('btn-danger');
			addMoreAttachmentsInputKey++;
		});

		// Remove attachment
		$("body").on('click', '.remove_attachment_file', function() {
			'use strict';

			$(this).parents('.attachment').remove();
		}); 

	$('input[name="duration_computation"]').on('click', function() {
	 	"use strict";  
		
		var duration_computation =$(this).val();
		if(duration_computation == 'compute_based_on_real_time'){
			$('.based_on_hide').removeClass('hide');
			$('.default_duration_hide').addClass('hide');

		}else if(duration_computation == 'set_duration_manually'){
			$('.based_on_hide').addClass('hide');
			$('.default_duration_hide').removeClass('hide');

		}
	});

	$('input[name="start_next_operation"]').on('click', function() {
	 	"use strict";  
		
		var processed =$(this).val();
		if(processed == 'once_some_products_are_processed'){
			$('.quantity_process_hide').removeClass('hide');
		}else if(processed == 'once_all_products_are_processed'){
			$('.quantity_process_hide').addClass('hide');

		}
	});
	

	function delete_operation_attachment(wrapper, id) {
		'use strict';

		if (confirm_delete()) {
			$.get(admin_url + 'manufacturing/delete_operation_attachment_file/' + id, function (response) {
				if (response.success == true) {
					$(wrapper).parents('.contract-attachment-wrapper').remove();

					var totalAttachmentsIndicator = $('.attachments-indicator');
					var totalAttachments = totalAttachmentsIndicator.text().trim();
					if(totalAttachments == 1) {
						totalAttachmentsIndicator.remove();
					} else {
						totalAttachmentsIndicator.text(totalAttachments-1);
					}
				} else {
					alert_float('danger', response.message);
				}
			}, 'json');
		}
		return false;
	}

	function preview_file(invoker){
		'use strict';
        	$('#appointmentModal').modal('hide');

		var id = $(invoker).attr('id');
		var rel_id = $(invoker).attr('rel_id');
		view_file_file(id, rel_id);
	}

	function view_file_file(id, rel_id) {   
		'use strict';

		$('#contract_file_data').empty();
		$("#contract_file_data").load(admin_url + 'manufacturing/mrp_view_attachment_file/' + id + '/' + rel_id+'/'+'operation', function(response, status, xhr) {
			if (status == "error") {
				alert_float('danger', xhr.statusText);
			}
		});
	}



</script>