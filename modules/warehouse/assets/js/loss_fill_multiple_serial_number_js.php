<script>
	
	var transactions;

	(function($) {
		"use strict";  

		<?php if(isset($edit_serial_number_data)){ ?>
			var dataObject_pu = <?php echo json_encode($edit_serial_number_data) ; ?>;
		<?php }else{?>
			var dataObject_pu = [];
		<?php } ?>

		setTimeout(function(){

			var hotElement1 = document.getElementById('fill_multiple_serial_number_hs');

			transactions = new Handsontable(hotElement1, {

				contextMenu: false,
				manualRowMove: true,
				manualColumnMove: true,
				stretchH: 'all',
				autoWrapRow: true,
				rowHeights: 30,
				defaultRowHeight: 100,
				minRows: <?php echo new_html_entity_decode($min_row); ?>,
				maxsRows: <?php echo new_html_entity_decode($max_row); ?>,
				width: '100%',
				height: '350px',
				licenseKey: 'non-commercial-and-evaluation',
				rowHeaders: true,
				autoColumncommodity_group: {
					samplingRatio: 23
				},
				
				filters: true,
				manualRowRecommodity_group: true,
				manualColumnRecommodity_group: true,
				allowInsertRow: false,
				allowRemoveRow: false,
				columnHeaderHeight: 40,
				colWidths: [40, 50, 100,150, 150, 150,150,150,50,150,100,100,150,150,200,200,150,150],
				rowHeights: 30,
				
				rowHeaderWidth: [44],
				hiddenColumns: {
					columns: [],
					indicators: true
				},


				columns: [
				{
					type: 'text',
					data: 'serial_number',
					readOnly: false,
				},

				],

				colHeaders: [
				"<?php echo _l('wh_serial_number') ?>",
				],

				data: dataObject_pu,
			});

		},300);

	})(jQuery);


	$('.btn_submit_multiple_serial_number').on('click', function() {
		'use strict';

		var valid_edit_multiple_transaction = $('#fill_multiple_serial_number_hs').find('.htInvalid').html();

		if(valid_edit_multiple_transaction){
			alert_float('danger', "<?php echo _l('data_must_number') ; ?>");
		}else{
			var str_serial_number = '';
			var prefix_name = $('input[name="prefix_name"]').val();
			var arr_serial_number = transactions.getData();

			$.each(arr_serial_number, function(i, val){
				if(str_serial_number == ''){
					str_serial_number += val[0];
				}else{
					str_serial_number += ','+val[0];
				}
			});
			
			$('input[name="'+prefix_name+'[serial_number]'+'"]').val(str_serial_number);
			$('#deleteSerialNumberModal').modal('hide');
		}
	});

</script>