<script>
	if($('#dropzoneDragArea').length > 0){
		expenseDropzone = new Dropzone("#add_update_product", appCreateDropzoneOptions({
			autoProcessQueue: false,
			clickable: '#dropzoneDragArea',
			previewsContainer: '.dropzone-previews',
			addRemoveLinks: true,
			maxFiles: 10,

			success:function(file,response){
				response = JSON.parse(response);
				if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
				
					if(response.add_variant == 'add_variant'){
						$.get(admin_url + 'manufacturing/copy_product_image/' +response.id+'/'+response.rel_type, function (response1) {
							response1 = JSON.parse(response1);

							window.location.assign(response.url);
						});
					}else{
						window.location.assign(response.url);
					}

				}else{
					expenseDropzone.processQueue();

				}

			},

		}));
	}

	Dropzone.options.expenseForm = false;

	//variation
	var addMoreVendorsInputKey;
	addMoreVendorsInputKey = $('.list_approve').length;
	$("body").on('click', '.new_wh_approval', function() {
		'use strict';

		if ($(this).hasClass('disabled')) { return false; }

		var newattachment = $('.list_approve').find('#item_approve').eq(0).clone().appendTo('.list_approve');
		newattachment.find('button[data-toggle="dropdown"]').remove();
		newattachment.find('select').selectpicker('refresh');

		newattachment.find('button[data-id="name[0]"]').attr('data-id', 'name[' + addMoreVendorsInputKey + ']');
		newattachment.find('label[for="name[0]"]').attr('for', 'name[' + addMoreVendorsInputKey + ']');
		newattachment.find('input[name="name[0]"]').attr('name', 'name[' + addMoreVendorsInputKey + ']');
		newattachment.find('input[id="name[0]"]').attr('id', 'name[' + addMoreVendorsInputKey + ']').val('');

		newattachment.find('button[data-id="options[0]"]').attr('data-id', 'options[' + addMoreVendorsInputKey + ']');
		newattachment.find('label[for="options[0]"]').attr('for', 'options[' + addMoreVendorsInputKey + ']');
		newattachment.find('textarea[name="options[0]"]').attr('name', 'options[' + addMoreVendorsInputKey + ']');
		newattachment.find('textarea[id="options[0]"]').attr('id', 'options[' + addMoreVendorsInputKey + ']').val('');

		newattachment.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
		newattachment.find('button[name="add"]').removeClass('new_wh_approval').addClass('remove_wh_approval').removeClass('btn-success').addClass('btn-danger');
		addMoreVendorsInputKey++;

	});
	$("body").on('click', '.remove_wh_approval', function() {
		'use strict';

		$(this).parents('#item_approve').remove();
	});

	 appValidateForm($("body").find('#add_update_product'), {
	  	'description': 'required',
	  	'unit_id': 'required',
	  	'purchase_unit_measure': 'required',
	  }, productSubmitHandler); 


	$('input[name="can_be_sold"]').on('click', function() {
		'use strict';

		var can_be_sold =$('#can_be_sold').is(':checked');
		if(can_be_sold == true){
			$('.tab_sales_hide').removeClass('hide');
		}else{
			$('.tab_sales_hide').addClass('hide');
		}
	});


	$('input[name="can_be_purchased"]').on('click', function() {
		'use strict';

		var can_be_purchased =$('#can_be_purchased').is(':checked');
		if(can_be_purchased == true){
			$('.tab_purchase_hide').removeClass('hide');
		}else{
			$('.tab_purchase_hide').addClass('hide');
		}
	});


	function productSubmitHandler(form) {
		'use strict';
		
		var data={};
		data.formdata = $( form ).serializeArray();

		var sku_data ={};
		sku_data.sku_code =  $('input[name="sku_code"]').val();
		if($('input[name="id"]').val() != '' && $('input[name="id"]').val() != 0){
			sku_data.item_id =  $('input[name="id"]').val();
		}else{
			sku_data.item_id = '';
		}

		$.post(admin_url + 'manufacturing/check_sku_duplicate', sku_data).done(function(response) {
			response = JSON.parse(response);

			if(response.message == 'false' || response.message ==  false){

				alert_float('warning', "<?php echo _l('sku_code_already_exists') ?>");

			}else{

				//show box loading
				var html = '';
				html += '<div class="Box">';
				html += '<span>';
				html += '<span></span>';
				html += '</span>';
				html += '</div>';
				$('#box-loading').html(html);

				$('.submit_button').attr( "disabled", "disabled" );

				$.post(form.action, data).done(function(response) {
					var response = JSON.parse(response);
					if (response.commodityid) {
						if(typeof(expenseDropzone) !== 'undefined'){
							if (expenseDropzone.getQueuedFiles().length > 0) {
								if(response.add_variant){
									var add_variant = 'add_variant';
								}else{
									var add_variant = '';
								}
								expenseDropzone.options.url = admin_url + 'manufacturing/add_product_attachment/' + response.commodityid+'/'+response.rel_type+'/'+add_variant;
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
			}

		});

		return false;

	}


	function delete_product_attachment(wrapper, attachment_id, rel_type) {
	 	"use strict";  
		
		if (confirm_delete()) {
			$.get(admin_url + 'manufacturing/delete_product_attachment/' +attachment_id+'/'+rel_type, function (response) {
				if (response.success == true) {
					$(wrapper).parents('.dz-preview').remove();

					var totalAttachmentsIndicator = $('.dz-preview'+attachment_id);
					var totalAttachments = totalAttachmentsIndicator.text().trim();

					if(totalAttachments == 1) {
						totalAttachmentsIndicator.remove();
					} else {
						totalAttachmentsIndicator.text(totalAttachments-1);
					}
					alert_float('success', "<?php echo _l('deleted_product_image_successfully') ?>");

				} else {
					alert_float('danger', "<?php echo _l('deleted_product_image_failed') ?>");
				}
			}, 'json');
		}
		return false;
	}
	
</script>