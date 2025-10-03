<script> 

var style_type_value = {};
    function new_style_type(){

      "use strict";
        $('#style_type').modal('show');
        $('.edit-title').addClass('hide');
        $('.add-title').removeClass('hide');
        $('#style_type_id').html('');

        var handsontable_html ='<div id="hot_style_type" class="hot handsontable htColumnHeaders"></div>';
        if($('#add_handsontable').html() != null){
          $('#add_handsontable').empty();

          $('#add_handsontable').html(handsontable_html);
        }else{
          $('#add_handsontable').html(handsontable_html);

        }

      setTimeout(function(){
        "use strict";
        var hotElement1 = document.querySelector('#hot_style_type');

         var style_type = new Handsontable(hotElement1, {
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
          rowHeaders: true,
          autoColumnstyle: {
            samplingRatio: 23
          },
          licenseKey: 'non-commercial-and-evaluation',
          filters: true,
          manualRowRestyle: true,
          manualColumnRestyle: true,
          allowInsertRow: true,
          allowRemoveRow: true,
          columnHeaderHeight: 40,

          colWidths: [40, 40, 100, 30,30, 30, 140],
          rowHeights: 30,

          rowHeaderWidth: [44],
          hiddenColumns: {
            columns: [0],
            indicators: true
          },

          columns: [
                      {
                        type: 'text',
                        data: 'style_type_id'
                      },
                      {
                        type: 'text',
                        data: 'style_code'
                      },
                       {
                        type: 'text',
                        data: 'style_barcode',
                        
                      },
                       {
                        type: 'text',
                        data: 'style_name',
                       
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
                          "1":"<?php echo _l('style_type_id') ?>",
                          "2":"<?php echo _l('style_code') ?>",
                          "3":"<?php echo _l('Barcode') ?>",
                          "4":"<?php echo _l('style_name') ?>",
                          "5":"<?php echo _l('order') ?>",
                          "6":"<?php echo _l('display') ?>",
                          "7":"<?php echo _l('note') ?>",
                          }],

          data: [
          {"style_code":"","style_barcode":"","style_name":"","order":"","display":"yes","note":""},
          {"style_code":"","style_barcode":"","style_name":"","order":"","display":"yes","note":""},
          {"style_code":"","style_barcode":"","style_name":"","order":"","display":"yes","note":""},
          {"style_code":"","style_barcode":"","style_name":"","order":"","display":"yes","note":""},
          {"style_code":"","style_barcode":"","style_name":"","order":"","display":"yes","note":""},
          {"style_code":"","style_barcode":"","style_name":"","order":"","display":"yes","note":""},
          {"style_code":"","style_barcode":"","style_name":"","order":"","display":"yes","note":""},
          {"style_code":"","style_barcode":"","style_name":"","order":"","display":"yes","note":""},
          {"style_code":"","style_barcode":"","style_name":"","order":"","display":"yes","note":""},
          ],

        });
         style_type_value = style_type;
        },300);


    }

  function edit_style_type(invoker,id){
    
    "use strict";

    var style_code = $(invoker).data('style_code');
    var style_name = $(invoker).data('style_name');
    var style_barcode = $(invoker).data('style_barcode');

    var order = $(invoker).data('order');
    if($(invoker).data('display') == 0){
      var display = 'no';
    }else{
      var display = 'yes';
    }
    var note = $(invoker).data('note');

        $('#style_type_id').html('');
        $('#style_type_id').append(hidden_input('id',id));

        $('#style_type').modal('show');
        $('.edit-title').removeClass('hide');
        $('.add-title').addClass('hide');


        var handsontable_html ='<div id="hot_style_type" class="hot handsontable htColumnHeaders"></div>';
        if($('#add_handsontable').html() != null){
          $('#add_handsontable').empty();

          $('#add_handsontable').html(handsontable_html);
        }else{
          $('#add_handsontable').html(handsontable_html);

        }

    setTimeout(function(){
      "use strict";
      var hotElement1 = document.querySelector('#hot_style_type');

       var style_type = new Handsontable(hotElement1, {
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
        autoColumnstyle: {
          samplingRatio: 23
        },
        // rowHeaderWidth: 150,
        licenseKey: 'non-commercial-and-evaluation',
        filters: true,
        manualRowRestyle: true,
        manualColumnRestyle: true,
        columnHeaderHeight: 40,

        colWidths: [40, 40, 100, 30,30, 30, 140],
        rowHeights: 30,
        rowHeaderWidth: [44],
        hiddenColumns: {
          columns: [0],
          indicators: true
        },

        columns: [
                {
                  type: 'text',
                  data: 'style_type_id',
                  readOnly:true,
                  
                },
                {
                  type: 'text',
                  data: 'style_code',
                  readOnly:true,
                  
                },
                 {
                  type: 'text',
                  data: 'style_barcode',
                  
                },
                 {
                  type: 'text',
                  data: 'style_name',
                 
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
                        "1":"<?php echo _l('style_type_id') ?>",
                        "2":"<?php echo _l('style_code') ?>",
                        "3":"<?php echo _l('Barcode') ?>",
                        "4":"<?php echo _l('style_name') ?>",
                        "5":"<?php echo _l('order') ?>",
                        "6":"<?php echo _l('display') ?>",
                        "7":"<?php echo _l('note') ?>",
                    }],

        data: [{"style_type_id":id,"style_code":style_code,"style_barcode":style_barcode,"style_name":style_name,"order":order,"display":display,"note":note}],

      });
       style_type_value = style_type;
      },300);

    }


    function add_style_type(invoker){
      "use strict";
      var valid_style_type = $('#hot_style_type').find('.htInvalid').html();

      if(valid_style_type){
        alert_float('danger', "<?php echo _l('data_must_number') ; ?>");
      }else{

        $('input[name="hot_style_type"]').val(JSON.stringify(style_type_value.getData()));
        $('#add_style_type').submit(); 

      }
        
    }
    
</script>