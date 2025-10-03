<script type="text/javascript">

	appValidateForm($('#form_activity_log'),{
		description:'required',
	},expenseSubmitHandler);

	function expenseSubmitHandler(form){

		$.post(form.action, $(form).serialize()).done(function(response) {
			response = JSON.parse(response);
			if (response.shipment_log_id) {
				if(typeof(expenseDropzone) !== 'undefined'){
					console.log('111111111', expenseDropzone.getQueuedFiles().length);
					if (expenseDropzone.getQueuedFiles().length > 0) {
						expenseDropzone.options.url = admin_url + 'warehouse/add_shipment_attachment/' + response.shipment_log_id+'/'+response. cart_id;
						expenseDropzone.processQueue();
					} else {
						window.location.assign(response.url);
					}
				} else {
					window.location.assign(response.url);
				}
			} else {
				window.location.assign(response.url);
			}
		});
					console.log('2');

		return false;
	}


	if($('#dropzoneDragArea').length > 0){
		expenseDropzone = new Dropzone("#form_activity_log", appCreateDropzoneOptions({
			autoProcessQueue: false,
			clickable: '#dropzoneDragArea',
			previewsContainer: '.dropzone-previews',
			addRemoveLinks: true,
			maxFiles: 10,

			success:function(file,response){
				response = JSON.parse(response);
				if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
					window.location.assign(response.url);
				}else{
					expenseDropzone.processQueue();
				}
			},

		}));
	}

	function delete_product_attachment(wrapper, attachment_id, rel_type) {
		"use strict";  

		if (confirm_delete()) {
			$.get(admin_url + 'warehouse/delete_product_attachment/' +attachment_id+'/'+rel_type, function (response) {
				if (response.success == true) {
					$(wrapper).parents('.dz-preview').remove();

					var totalAttachmentsIndicator = $('.dz-preview'+attachment_id);
					var totalAttachments = totalAttachmentsIndicator.text().trim();

					if(totalAttachments == 1) {
						totalAttachmentsIndicator.remove();
					} else {
						totalAttachmentsIndicator.text(totalAttachments-1);
					}
					alert_float('success', "<?php echo _l('delete_commodity_file_success') ?>");

				} else {
					alert_float('danger', "<?php echo _l('delete_commodity_file_false') ?>");
				}
			}, 'json');
		}
		return false;
	}
</script>