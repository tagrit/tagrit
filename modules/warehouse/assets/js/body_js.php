<script>    

var body_type_value = {};

    function new_body_type(){
        "use strict";

        $('#body_type').modal('show');
        $('.edit-title').addClass('hide');
        $('.add-title').removeClass('hide');
        $('#body_type_id').html('');

        var handsontable_html ='<div id="hot_body_type" class="hot handsontable htColumnHeaders"></div>';
        if($('#add_handsontable').html() != null){
          $('#add_handsontable').empty();

          $('#add_handsontable').html(handsontable_html);
        }else{
          $('#add_handsontable').html(handsontable_html);

        }
        

  setTimeout(function(){
  "use strict";
    
    var hotElement1 = document.querySelector('#hot_body_type');
    

     var body_type = new Handsontable(hotElement1, {
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
      autoColumnbody: {
        samplingRatio: 23
      },
      

      filters: true,
      manualRowRebody: true,
      manualColumnRebody: true,
      allowInsertRow: true,
      allowRemoveRow: true,
      columnHeaderHeight: 40,
      licenseKey: 'non-commercial-and-evaluation',
      colWidths: [40, 100, 30,30, 30, 140],
      rowHeights: 30,
      
      rowHeaderWidth: [44],
      hiddenColumns: {
        columns: [0],
        indicators: true
      },

      columns: [
                  {
                    type: 'text',
                    data: 'body_type_id'
                  },
                  {
                    type: 'text',
                    data: 'body_code'
                  },
                   {
                    type: 'text',
                    data: 'body_name',
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
                      "1":"<?php echo _l('body_type_id') ?>",
                      "2":"<?php echo _l('model_code') ?>",
                      "3":"<?php echo _l('model_name') ?>",
                      "4":"<?php echo _l('order') ?>",
                      "5":"<?php echo _l('display') ?>",
                      "6":"<?php echo _l('note') ?>",
                      }],

      data: [
      {"body_code":"","body_name":"","order":"","display":"yes","note":""},
      {"body_code":"","body_name":"","order":"","display":"yes","note":""},
      {"body_code":"","body_name":"","order":"","display":"yes","note":""},
      {"body_code":"","body_name":"","order":"","display":"yes","note":""},
      {"body_code":"","body_name":"","order":"","display":"yes","note":""},
      {"body_code":"","body_name":"","order":"","display":"yes","note":""},
      {"body_code":"","body_name":"","order":"","display":"yes","note":""},
      {"body_code":"","body_name":"","order":"","display":"yes","note":""},
      {"body_code":"","body_name":"","order":"","display":"yes","note":""},
      {"body_code":"","body_name":"","order":"","display":"yes","note":""},
      ],

    });
     body_type_value = body_type;
    },300);


    }

  function edit_body_type(invoker,id){
    
    "use strict";

    var body_code = $(invoker).data('body_code');
    var body_name = $(invoker).data('body_name');

    var order = $(invoker).data('order');
    if($(invoker).data('display') == 0){
      var display = 'no';
    }else{
      var display = 'yes';
    }
    var note = $(invoker).data('note');

        $('#body_type_id').html('');
        $('#body_type_id').append(hidden_input('id',id));
       
        $('#body_type').modal('show');
        $('.edit-title').removeClass('hide');
        $('.add-title').addClass('hide');
        

        var handsontable_html ='<div id="hot_body_type" class="hot handsontable htColumnHeaders"></div>';
        if($('#add_handsontable').html() != null){
          $('#add_handsontable').empty();

          $('#add_handsontable').html(handsontable_html);
        }else{
          $('#add_handsontable').html(handsontable_html);

        }

    setTimeout(function(){
      "use strict";
      var hotElement1 = document.querySelector('#hot_body_type');

       var body_type = new Handsontable(hotElement1, {
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
        autoColumnbody: {
          samplingRatio: 23
        },
        
        licenseKey: 'non-commercial-and-evaluation',
        filters: true,
        manualRowRebody: true,
        manualColumnRebody: true,
        
        columnHeaderHeight: 40,

        colWidths: [40, 100, 30,30, 30, 140],
        rowHeights: 30,
        
        rowHeaderWidth: [44],
        hiddenColumns: {
          columns: [0],
          indicators: true
        },

        columns: [
                {
                  type: 'text',
                  data: 'body_type_id',
                  readOnly:true,
                  
                },
                {
                  type: 'text',
                  data: 'body_code',
                  readOnly:true,
                  
                },
                 {
                  type: 'text',
                  data: 'body_name',
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
                        "1":"<?php echo _l('body_type_id') ?>",
                        "2":"<?php echo _l('model_code') ?>",
                        "3":"<?php echo _l('model_name') ?>",
                        "4":"<?php echo _l('order') ?>",
                        "5":"<?php echo _l('display') ?>",
                        "6":"<?php echo _l('note') ?>",
                      }],

        data: [{"body_type_id":id,"body_code":body_code,"body_name":body_name,"order":order,"display":display,"note":note}],

      });
       body_type_value = body_type;
      },300);

    }

    function add_body_type(invoker){
        "use strict"; 
      var valid_body_type = $('#hot_body_type').find('.htInvalid').html();
      if(valid_body_type){
        alert_float('danger', "<?php echo _l('data_must_number') ; ?>");
      }else{

        $('input[name="hot_body_type"]').val(JSON.stringify(body_type_value.getData()));
        $('#add_body_type').submit(); 

      }
        
    }


</script>