<script>	

		var product_tabs;

	(function($) {
		"use strict";  

		$('li.menu-item-accounting_registers').addClass('active');

		var dataObject_pu = [];

		var hotElement1 = document.getElementById('product_tab_hs');

		product_tabs = new Handsontable(hotElement1, {
			licenseKey: 'non-commercial-and-evaluation',

			contextMenu: true,
			manualRowMove: true,
			manualColumnMove: true,
			stretchH: 'all',
			autoWrapRow: true,
			rowHeights: 30,
			defaultRowHeight: 100,
			// minRows: 100,
			// maxRows: 40,
			width: '100%',
    	height: 400,
			rowHeaders: true,
			colHeaders: true,
			autoColumnSize: {
				samplingRatio: 23
			},

			filters: true,
			manualRowResize: true,
			manualColumnResize: true,
			allowInsertRow: true,
			allowRemoveRow: true,
			columnHeaderHeight: 40,

			rowHeights: 30,
			rowHeaderWidth: [44],
			minSpareRows: 1,
			hiddenColumns: {
				columns: [0],
				indicators: true
			},

			columns: [
			{
				type: 'text',
				data: 'id',
			},

			{
				type: 'date',
				dateFormat: 'YYYY-MM-DD',
				correctFormat: true,
				defaultDate: "<?php echo date('Y-m-d'); ?>",
				data:'date'
			},
			{
				type: 'text',
				data: 'number',
			},
			
			{
				type: 'text',
				data: 'payee',
				renderer: customDropdownRenderer,
				editor: "chosen",
				chosenOptions: {
					data: <?php echo json_encode($payee); ?>
				},
			},
			{
				type: 'text',
				data: 'split',
				renderer: customDropdownRenderer,
				editor: "chosen",
				chosenOptions: {
					data: <?php echo json_encode($accounts); ?>
				},
				isRequired: true,
			},
			{
				data: 'debit',
				type: 'numeric',
			      numericFormat: {
			        pattern: '0,0.00',
			      },
			},
			{
				data: 'credit',
				type: 'numeric',
			      numericFormat: {
			        pattern: '0,0.00',
			      },
			},
			{
				data: 'balance',
				type: 'numeric',
			      numericFormat: {
			        pattern: '0,0.00',
			      },
			     readOnly: true,
			},

			
			],

			colHeaders: [
				'<?php echo _l('id'); ?>',
				'<?php echo _l('acc_date'); ?>',
				'<?php echo _l('number'); ?>',
				'<?php echo _l('payee'); ?>',
				'<?php echo _l('acc_account'); ?>',
				'<?php echo _l('acc_debit'); ?>', 
				'<?php echo _l('acc_credit'); ?>', 
				'<?php echo _l('balance'); ?>',
			],
			cells: function(row){
        let cp = {}
        if(row % 2 === 1){ cp.className = 'greyRow'}
        return cp
      },

			data: dataObject_pu,
		});

		product_tabs.addHook('afterChange', function(changes, src) {
			"use strict";

			if(changes !== null && changes !== undefined){
				changes.forEach(([row, col, prop, oldValue, newValue]) => {
					if(col == 'credit' && oldValue != ''){

						product_tabs.setDataAtCell(row,5,'');
						var date = product_tabs.getDataAtCell(row, 1);

						if(date == null){
							product_tabs.setDataAtCell(row,1, '<?php echo date('Y-m-d'); ?>');
						}

					}

					if(col == 'debit' && oldValue != ''){

						product_tabs.setDataAtCell(row,6,'');
						var date = product_tabs.getDataAtCell(row, 1);

						if(date == null){
							product_tabs.setDataAtCell(row,1, '<?php echo date('Y-m-d'); ?>');
						}

					}


				});
			}

		});


		$('input[name="from_date_filter"]').on('change', function() {
    	'use strict';

    	transaction_filter();
    });

    $('input[name="to_date_filter"]').on('change', function() {
    	'use strict';

    	transaction_filter();
    });

    $('input[name="number_filter"]').on('change', function() {
    	'use strict';

    	transaction_filter();
    });

    $('select[name="payee_filter[]"]').on('change', function() {
    	'use strict';

    	transaction_filter();

    });

    $('input[name="from_credit_filter"]').on('change', function() {
    	'use strict';

    	transaction_filter();
    });

    $('input[name="to_credit_filter"]').on('change', function() {
    	'use strict';

    	transaction_filter();
    });

    $('input[name="from_debit_filter"]').on('change', function() {
    	'use strict';

    	transaction_filter();
    });

    $('input[name="to_debit_filter"]').on('change', function() {
    	'use strict';

    	transaction_filter();
    });

    $('select[name="account_filter[]"]').on('change', function() {
    	'use strict';

    	transaction_filter();
    });
    

    $('.reset_filter').on('click', function() {
    	'use strict';

    	reset_filter();
    });

		transaction_filter();
		transaction_filter();

    
$('.add_user_register').on('click', function() {
	'use strict';

        var valid_product_tab_hs = $('#product_tab_hs').find('.htInvalid').html();

        $('input[name="save_and_send_request"]').val('false');

        if(valid_product_tab_hs){
          alert_float('danger', "<?php echo _l('data_must_number') ; ?>");

        }else{
          
          var warehouse_id = $('select[name="warehouse_id"]').val();

          var datasubmit = {};
          datasubmit.product_tabs = JSON.stringify(product_tabs.getData());
          datasubmit.account = $('input[name="account"]').val();
          datasubmit.company = $('input[name="company"]').val();

          datasubmit.from_date_filter = $('input[name="from_date_filter"]').val();
          datasubmit.to_date_filter = $('input[name="to_date_filter"]').val();
          datasubmit.number_filter = $('input[name="number_filter"]').val();
          datasubmit.payee_filter = $('select[name="payee_filter[]"]').val();
          datasubmit.from_credit_filter = $('input[name="from_credit_filter"]').val();
          datasubmit.to_credit_filter = $('input[name="to_credit_filter"]').val();
          datasubmit.from_debit_filter = $('input[name="from_debit_filter"]').val();
          datasubmit.to_debit_filter = $('input[name="to_debit_filter"]').val();
          datasubmit.account_filter = $('select[name="account_filter[]"]').val();

            $.post(admin_url + 'accounting/check_user_register_transaction', datasubmit).done(function(responsec){
              responsec = JSON.parse(responsec);

              if(responsec.status == true || responsec.status == 'true'){
                
              	$.post(admin_url + 'accounting/register_add_edit_transaction', datasubmit).done(function(response){
              		response = JSON.parse(response);

              		if(response.status == true || response.status == 'true'){
              			
              			product_tabs.updateSettings({
              				data: response.dataObject,

              			})

              			$('.ending_balance').html(format_money(response.ending_balance));

		            	alert_float('success', "<?php echo _l('acc_updated_successfully') ; ?>");
		            }else{
		            	alert_float('success', "<?php echo _l('acc_updated_successfully') ; ?>");
		            	
		            }
		        });

              	$('input[name="product_tab_hs"]').val(JSON.stringify(product_tabs.getData()));   
                // $('#add_update_transaction').submit(); 

              }else{
                alert_float('danger', "<?php echo _l('acc_please_select_account') ; ?>");
              }

            });



        }
});
})(jQuery);



function customDropdownRenderer(instance, td, row, col, prop, value, cellProperties) {
	"use strict";

	var selectedId;
	var optionsList = cellProperties.chosenOptions.data;

	if(typeof optionsList === "undefined" || typeof optionsList.length === "undefined" || !optionsList.length) {
		Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
		return td;
	}

	var values = (value + "").split("|");
	value = [];
	for (var index = 0; index < optionsList.length; index++) {

		if (values.indexOf(optionsList[index].id + "") > -1) {
			selectedId = optionsList[index].id;
			value.push(optionsList[index].label);
		}
	}
	value = value.join(", ");

	Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
	return td;
}



    //filter
    function transaction_filter (){
    	'use strict';

    	var data = {};

    	data.csrf_token_name = $('input[name="csrf_token_name"]').val();
    	data.account = $('input[name="account"]').val();
    	data.company = $('input[name="company"]').val();

    	data.from_date_filter = $('input[name="from_date_filter"]').val();
    	data.to_date_filter = $('input[name="to_date_filter"]').val();
    	data.number_filter = $('input[name="number_filter"]').val();
    	data.payee_filter = $('select[name="payee_filter[]"]').val();
    	data.from_credit_filter = $('input[name="from_credit_filter"]').val();
    	data.to_credit_filter = $('input[name="to_credit_filter"]').val();
    	data.from_debit_filter = $('input[name="from_debit_filter"]').val();
    	data.to_debit_filter = $('input[name="to_debit_filter"]').val();
    	data.account_filter = $('select[name="account_filter[]"]').val();

    	$.post(admin_url + 'accounting/transaction_filter', data).done(function(response) {
    		response = JSON.parse(response);

    		product_tabs.updateSettings({
    			data: response.dataObject,
    		});

    		$('.ending_balance').html(format_money(response.ending_balance));
    	});
    };

    

    function reset_filter() {
    	$('input[name="from_date_filter"]').val('');
    	$('input[name="to_date_filter"]').val('');
    	$('input[name="number_filter"]').val('');
    	$('select[name="payee_filter[]"]').val('').change();
    	$('input[name="from_credit_filter"]').val('');
    	$('input[name="to_credit_filter"]').val('');
    	$('input[name="from_debit_filter"]').val('');
    	$('input[name="to_debit_filter"]').val('');
    	$('select[name="account_filter[]"]').val('').change();
    }
</script>