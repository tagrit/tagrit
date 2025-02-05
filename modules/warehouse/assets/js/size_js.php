<script> 

var size_type_value = {};
    function new_size_type(){
      "use strict";
      
        $('#size_type').modal('show');
        $('.edit-title').addClass('hide');
        $('.add-title').removeClass('hide');
        $('#size_type_id').html('');

        var handsontable_html ='<div id="hot_size_type" class="hot handsontable htColumnHeaders"></div>';
        if($('#add_handsontable').html() != null){
          $('#add_handsontable').empty();

          $('#add_handsontable').html(handsontable_html);
        }else{
          $('#add_handsontable').html(handsontable_html);

        }

      setTimeout(function(){
        "use strict";
        var hotElement1 = document.querySelector('#hot_size_type');


         var size_type = new Handsontable(hotElement1, {
          contextMenu: true,
          manualRowMove: true,
          manualColumnMove: true,
          stretchH: 'all',
          autoWrapRow: true,
          rowHeights: 30,
          defaultRowHeight: 100,
          maxRows: 22,
          minRows:9,
          width: '100%',
          height: 330,
          rowHeaders: true,
          autoColumnSize: {
            samplingRatio: 23
          },

          licenseKey: 'non-commercial-and-evaluation',
          filters: true,
          manualRowResize: true,
          manualColumnResize: true,
          allowInsertRow: true,
          allowRemoveRow: true,
          columnHeaderHeight: 40,

          colWidths: [40, 40, 100, 30,30, 30, 140],
          rowHeights: 30,
          // colWidths: 55,
          rowHeaderWidth: [44],
          hiddenColumns: {
            columns: [0],
            indicators: true
          },

          columns: [
                      {
                        type: 'text',
                        data: 'size_type_id'
                      },
                      {
                        type: 'text',
                        data: 'size_code'
                      },
                       {
                        type: 'text',
                        data: 'size_name',
                        // set desired format pattern and
                      },
                       {
                        type: 'text',
                        data: 'size_symbol',
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
                            "1":"<?php echo _l('size_type_id') ?>",
                            "2":"<?php echo _l('size_code') ?>",
                            "3":"<?php echo _l('size_name') ?>",
                            "4":"<?php echo _l('size_symbol') ?>",
                            "5":"<?php echo _l('order') ?>",
                           "6":"<?php echo _l('display') ?>",
                           "7":"<?php echo _l('note') ?>",
                          }],

          data: [
          {"size_code":"","size_name":"","size_symbol":"","order":"","display":"yes","note":""},
          {"size_code":"","size_name":"","size_symbol":"","order":"","display":"yes","note":""},
          {"size_code":"","size_name":"","size_symbol":"","order":"","display":"yes","note":""},
          {"size_code":"","size_name":"","size_symbol":"","order":"","display":"yes","note":""},
          {"size_code":"","size_name":"","size_symbol":"","order":"","display":"yes","note":""},
          {"size_code":"","size_name":"","size_symbol":"","order":"","display":"yes","note":""},
          {"size_code":"","size_name":"","size_symbol":"","order":"","display":"yes","note":""},
          {"size_code":"","size_name":"","size_symbol":"","order":"","display":"yes","note":""},
          {"size_code":"","size_name":"","size_symbol":"","order":"","display":"yes","note":""},
          ],

        });
         size_type_value = size_type;
        },300);


    }

  function edit_size_type(invoker,id){
    
    "use strict";

    var size_code = $(invoker).data('size_code');
    var size_name = $(invoker).data('size_name');
    var size_symbol = $(invoker).data('size_symbol');

    var order = $(invoker).data('order');
    if($(invoker).data('display') == 0){
      var display = 'no';
    }else{
      var display = 'yes';
    }
    var note = $(invoker).data('note');

        $('#size_type_id').html('');
        $('#size_type_id').append(hidden_input('id',id));
        $('#size_type').modal('show');
        $('.edit-title').removeClass('hide');
        $('.add-title').addClass('hide');

        var handsontable_html ='<div id="hot_size_type" class="hot handsontable htColumnHeaders"></div>';
        if($('#add_handsontable').html() != null){
          $('#add_handsontable').empty();

          $('#add_handsontable').html(handsontable_html);
        }else{
          $('#add_handsontable').html(handsontable_html);

        }

    setTimeout(function(){
       "use strict";
      var hotElement1 = document.querySelector('#hot_size_type');

       var size_type = new Handsontable(hotElement1, {
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
                  data: 'size_type_id'
                },
                {
                  type: 'text',
                  data: 'size_code',
                  readOnly:true,
                  
                },
                 {
                  type: 'text',
                  data: 'size_name',
                  // set desired format pattern and
                },
                 {
                  type: 'text',
                  data: 'size_symbol',
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
                      "1":"<?php echo _l('size_type_id') ?>",
                      "2":"<?php echo _l('size_code') ?>",
                      "3":"<?php echo _l('size_name') ?>",
                      "4":"<?php echo _l('size_symbol') ?>",
                      "5":"<?php echo _l('order') ?>",
                      "6":"<?php echo _l('display') ?>",
                      "7":"<?php echo _l('note') ?>",
                    }],

        data: [{"size_type_id":id,"size_code":size_code,"size_name":size_name,"size_symbol":size_symbol,"order":order,"display":display,"note":note}],

      });
       size_type_value = size_type;
      },300);

    }
    

    function add_size_type(invoker){
        "use strict";
        var valid_size_type = $('#hot_size_type').find('.htInvalid').html();

        if(valid_size_type){
          alert_float('danger', "<?php echo _l('data_must_number') ; ?>");
        }else{

          $('input[name="hot_size_type"]').val(JSON.stringify(size_type_value.getData()));
          $('#add_size_type').submit(); 

        }
        
    }
</script>