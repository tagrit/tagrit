<script> 
  var purchase;
  var purchase_value;

  (function($) {
    
    "use strict";

    <?php if(isset($inventory_min)){ ?>
      var dataObject_pu = <?php echo json_encode($inventory_min) ; ?>;
    <?php }else{ ?>
      var dataObject_pu = [];
    <?php } ?>
      var hotElement1 = document.querySelector('#inventory_min');
        purchase = new Handsontable(hotElement1, {
        licenseKey: 'non-commercial-and-evaluation',

        contextMenu: true,
        manualRowMove: true,
        manualColumnMove: true,
        stretchH: 'all',
        autoWrapRow: true,
        rowHeights: 30,
        defaultRowHeight: 100,
        width: '100%',
        height: 500,

        rowHeaders: true,
        hiddenColumns: {
          columns: [0, 1],
            indicators: true
        },
        autoColumnSize: {
          samplingRatio: 23
        },
        licenseKey: 'non-commercial-and-evaluation',
        filters: true,
        manualRowResize: true,
        manualColumnResize: true,
        allowInsertRow: false,
        allowRemoveRow: false,
        columnHeaderHeight: 40,

        rowHeights: 30,
        rowHeaderWidth: [44],
        columnSorting: true,
        columnSorting: {
          sortEmptyCells: true,
          initialConfig: {
            column: 2,
            sortOrder: 'asc'
          }
        },

        columns: [

                    {
                      type: 'text',
                      data: 'id',
                      readOnly: true
                    },
                    {
                      type: 'text',
                      data: 'commodity_id',
                      readOnly: true
                    },
                    {
                      type: 'text',
                      data: 'commodity_code',
                      readOnly: true
                    },
                     {
                      type: 'text',
                      data: 'commodity_name',
                      readOnly: true

                    
                    },{
                      type: 'text',
                      data: 'sku_code',
                      readOnly: true

                     
                    },
                     {
                      type: 'numeric',
                      data: 'inventory_number_min',
                       numericFormat: {
                        pattern: '0,00',
                      }
                      
                    },

                    {
                      type: 'numeric',
                      data: 'inventory_number_max',
                       numericFormat: {
                        pattern: '0,00',
                      }
                      
                    },

                    
                  ],

              colHeaders: [
            "<?php echo _l('id'); ?>",
            "<?php echo _l('commodity_id'); ?>",
            "<?php echo _l('commodity_code'); ?>",
            "<?php echo _l('commodity_name'); ?>",
            "<?php echo _l('sku_code'); ?>",
            "<?php echo _l('inventory_minimum'); ?>",
            "<?php echo _l('inventory_maximum'); ?>",
          ],
       
        data: dataObject_pu,
      });

})(jQuery); 

   purchase_value = purchase;
    function add_goods_receipt(invoker){
      "use strict";
        var valid_warehouse_type = $('#inventory_min').find('.htInvalid').html();

        if(valid_warehouse_type){
          alert_float('danger', "<?php echo _l('data_must_number') ; ?>");
        }else{

          $('input[name="inventory_min"]').val(JSON.stringify(purchase_value.getData()));
          $('#update_inventory').submit(); 

        }
        
    }
    
      //filter
  function maximum_minimum_inventory_filter(invoker){
    'use strict';

    var data = {};

    data.inventory_filter = $('input[name="inventory_filter"]').val();

    $.post(admin_url + 'warehouse/maximum_minimum_inventory_filter', data).done(function(response) {
      response = JSON.parse(response);
      console.log('data_object',response );
      purchase.updateSettings({
        data: response.data_object,

      })
      
    });
  };

  $('#inventory_filter').on('keyup', function() {
    'use strict';

    maximum_minimum_inventory_filter();

  });





</script>