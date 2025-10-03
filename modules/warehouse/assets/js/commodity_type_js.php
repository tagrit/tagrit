<script> 

var commodity_type_value = {};
    function new_commodity_type(){
      "use strict";
        $('#commodity_type').modal('show');
        $('.edit-title').addClass('hide');
        $('.add-title').removeClass('hide');
        $('#commodity_type_id').html('');

        var handsontable_html ='<div id="hot_commodity_type" class="hot handsontable htColumnHeaders"></div>';
        if($('#add_handsontable').html() != null){
          $('#add_handsontable').empty();

          $('#add_handsontable').html(handsontable_html);
        }else{
          $('#add_handsontable').html(handsontable_html);

        }

      setTimeout(function(){
         "use strict";

        //hansometable for allowance_no_taxable
        var hotElement1 = document.querySelector('#hot_commodity_type');


         var commodity_type = new Handsontable(hotElement1, {
          contextMenu: true,
          manualRowMove: true,
          manualColumnMove: true,
          stretchH: 'all',
          autoWrapRow: true,
          rowHeights: 30,
          defaultRowHeight: 100,
          maxRows: 22,
          minRows: 9,
          width: '100%',
          height: 330,
          licenseKey: 'non-commercial-and-evaluation',
          rowHeaders: true,
          autoColumnSize: {
            samplingRatio: 23
          },


          filters: true,
          manualRowResize: true,
          manualColumnResize: true,
          allowInsertRow: true,
          allowRemoveRow: true,
          columnHeaderHeight: 40,

          colWidths: [40, 40, 100, 30, 30, 140],
          rowHeights: 30,
          rowHeaderWidth: [44],
          hiddenColumns: {
            columns: [0],
            indicators: true
          },

          columns: [
                      {
                        type: 'text',
                        data: 'commodity_type_id'
                      },
                      {
                        type: 'text',
                        data: 'commondity_code'
                      },
                       {
                        type: 'text',
                        data: 'commondity_name',
                        // set desired format pattern and
                      },
                      {
                        type: 'numeric',
                        data: 'order',
                      },
                      {
                        type: 'checkbox',
                        data: 'display',
                        checkedTemplate: 'yes',
                        uncheckedTemplate: 'no'
                      },
                      {
                        type: 'text',
                        data: 'note',
                      },
                    
                    ],


          colHeaders: true,
          nestedHeaders: [{
                          "1":"<?php echo _l('commodity_type_id') ?>",
                          "2":"<?php echo _l('commodity_type_code') ?>",
                          "3":"<?php echo _l('commodity_type_name') ?>",
                          "4":"<?php echo _l('order') ?>",
                          "5":"<?php echo _l('display') ?>",
                          "6":"<?php echo _l('note') ?>",
                          }],

          data: [
                  {"commondity_code":"","commondity_name":"","order":"","display":"yes","note":""},
                  {"commondity_code":"","commondity_name":"","order":"","display":"yes","note":""},
                  {"commondity_code":"","commondity_name":"","order":"","display":"yes","note":""},
                  {"commondity_code":"","commondity_name":"","order":"","display":"yes","note":""},
                  {"commondity_code":"","commondity_name":"","order":"","display":"yes","note":""},
                  {"commondity_code":"","commondity_name":"","order":"","display":"yes","note":""},
                  {"commondity_code":"","commondity_name":"","order":"","display":"yes","note":""},
                  {"commondity_code":"","commondity_name":"","order":"","display":"yes","note":""},
                  {"commondity_code":"","commondity_name":"","order":"","display":"yes","note":""},
                  ],

        });
         commodity_type_value = commodity_type;
        },300);


    }

  function edit_commodity_type(invoker,id){
    
    "use strict";
    var commondity_code = $(invoker).data('commondity_code');
    var commondity_name = $(invoker).data('commondity_name');
    var order = $(invoker).data('order');
    if($(invoker).data('display') == 1){
      var display = 'yes';
    }else{
      var display = 'no';
    }
    var note = $(invoker).data('note');

        $('#commodity_type_id').html('');
        $('#commodity_type_id').append(hidden_input('id',id));
        $('input[name="id_c"]').val(id);
        $('#commodity_type').modal('show');
        $('.edit-title').removeClass('hide');
        $('.add-title').addClass('hide');


        var handsontable_html ='<div id="hot_commodity_type" class="hot handsontable htColumnHeaders"></div>';
        if($('#add_handsontable').html() != null){
          $('#add_handsontable').empty();

          $('#add_handsontable').html(handsontable_html);
        }else{
          $('#add_handsontable').html(handsontable_html);

        }

    setTimeout(function(){
       "use strict";
        var hotElement1 = document.querySelector('#hot_commodity_type');

       var commodity_type = new Handsontable(hotElement1, {
        contextMenu: true,
        manualRowMove: true,
        manualColumnMove: true,
        stretchH: 'all',
        autoWrapRow: true,
        rowHeights: 30,
        defaultRowHeight: 100,
        maxRows: 1,
        width: '100%',
        height: 130,
        rowHeaders: true,
        autoColumnSize: {
          samplingRatio: 23
        },
        licenseKey: 'non-commercial-and-evaluation',
        filters: true,
        manualRowResize: true,
        manualColumnResize: true,
        columnHeaderHeight: 40,
        colWidths: [40, 40, 100, 30, 30, 140],
        rowHeights: 30,

        rowHeaderWidth: [44],
        hiddenColumns: {
          columns: [0],
          indicators: true
        },

        columns: [
                    {
                      type: 'text',
                      data: 'commodity_type_id'
                    },
                    {
                      type: 'text',
                      data: 'commondity_code',
                      readOnly:true,

                    },
                     {
                      type: 'text',
                      data: 'commondity_name',
                      // set desired format pattern and
                    },
                    {
                      type: 'numeric',
                      data: 'order',
                    },
                    {
                      type: 'checkbox',
                      data: 'display',
                      checkedTemplate: 'yes',
                      uncheckedTemplate: 'no'
                    },
                    {
                      type: 'text',
                      data: 'note',
                    },
                  
                  ],

        colHeaders: true,
        nestedHeaders: [{
                          "1":"<?php echo _l('commodity_type_id') ?>",
                          "2":"<?php echo _l('commodity_type_code') ?>",
                          "3":"<?php echo _l('commodity_type_name') ?>",
                          "4":"<?php echo _l('order') ?>",
                          "5":"<?php echo _l('display') ?>",
                          "6":"<?php echo _l('note') ?>",
                    }], 

        data: [{"commodity_type_id":id,"commondity_code":commondity_code,"commondity_name":commondity_name,"order":order,"display":display,"note":note}],

      });
       commodity_type_value = commodity_type;
      },300);

    }

    function add_commodity_type(invoker){
      "use strict";
      var valid_commodity_type = $('#hot_commodity_type').find('.htInvalid').html();

      if(valid_commodity_type){
        alert_float('danger', "<?php echo _l('data_must_number') ; ?>");
        
      }else{

        $('input[name="hot_commodity_type"]').val(JSON.stringify(commodity_type_value.getData()));
        $('#add_commodity_type').submit(); 

      }
        
    }
    
</script>