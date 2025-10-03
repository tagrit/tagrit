<script> 

    appValidateForm($('#add_warehouse'), {
        warehouse_code: 'required',
        warehouse_name: 'required',
        order: 'required',
        
    });
    
    var warehouseServerParams = {
      "assign_staff_filter": "[name='assign_staff_filter[]']",
    };

    var table_warehouse_name = $('table.table-table_warehouse_name');
    var _table_api = initDataTable(table_warehouse_name, admin_url+'warehouse/table_warehouse_name', [0], [0], warehouseServerParams,  [3, 'asc']);
    $.each(warehouseServerParams, function(i, obj) {
      $('select' + obj).on('change', function() {  
        table_warehouse_name.DataTable().ajax.reload();
      });
    });


var warehouse_type_value = {};
    function new_warehouse_type(){
      "use strict";
        $('#warehouse_type').modal('show');
        $('.edit-title').addClass('hide');
        $('.add-title').removeClass('hide');
        $('#warehouse_type_id').html('');

        var handsontable_html ='<div id="hot_warehouse_type" class="hot handsontable htColumnHeaders"></div>';
        if($('#add_handsontable').html() != null){
          $('#add_handsontable').empty();

          $('#add_handsontable').html(handsontable_html);
        }else{
          $('#add_handsontable').html(handsontable_html);

        }

setTimeout(function(){
  "use strict";
  var hotElement1 = document.querySelector('#hot_warehouse_type');


   var warehouse_type = new Handsontable(hotElement1, {
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

    colWidths: [20, 50,120,30, 30,120],
    rowHeights: 30,
    rowHeaderWidth: [44],

    columns: [
                {
                  type: 'text',
                  data: 'warehouse_code'
                },
                 {
                  type: 'text',
                  data: 'warehouse_name',
                  // set desired format pattern and
                },
                 {
                  type: 'text',
                  data: 'warehouse_address',
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
    nestedHeaders: [{"1":"<?php echo _l('warehouse_code') ?>",
                      "2":"<?php echo _l('warehouse_name') ?>",
                      "3":"<?php echo _l('warehouse_address') ?>",
                      "4":"<?php echo _l('order') ?>",
                     "5":"<?php echo _l('display') ?>",
                     "6":"<?php echo _l('note') ?>",
                    }],

    data: [
    {"warehouse_code":"","warehouse_name":"","warehouse_address":"","order":"","display":"yes","note":""},
    {"warehouse_code":"","warehouse_name":"","warehouse_address":"","order":"","display":"yes","note":""},
    {"warehouse_code":"","warehouse_name":"","warehouse_address":"","order":"","display":"yes","note":""},
    {"warehouse_code":"","warehouse_name":"","warehouse_address":"","order":"","display":"yes","note":""},
    {"warehouse_code":"","warehouse_name":"","warehouse_address":"","order":"","display":"yes","note":""},
    {"warehouse_code":"","warehouse_name":"","warehouse_address":"","order":"","display":"yes","note":""},
    {"warehouse_code":"","warehouse_name":"","warehouse_address":"","order":"","display":"yes","note":""},
    {"warehouse_code":"","warehouse_name":"","warehouse_address":"","order":"","display":"yes","note":""},
    {"warehouse_code":"","warehouse_name":"","warehouse_address":"","order":"","display":"yes","note":""},
    ],

  });
   warehouse_type_value = warehouse_type;
  },300);


    }

  function edit_warehouse_type(invoker,id){
      "use strict";

    var warehouse_code = $(invoker).data('warehouse_code');
    var warehouse_name = $(invoker).data('warehouse_name');
    var warehouse_address = $(invoker).data('warehouse_address');

    var order = $(invoker).data('order');
    if($(invoker).data('display') == 0){
      var display = 'no';
    }else{
      var display = 'yes';
    }
    var note = $(invoker).data('note');

        $('#warehouse_type_id').html('');
        $('#warehouse_type_id').append(hidden_input('id',id));

        $('#warehouse_type').modal('show');
        $('.edit-title').addClass('hide');
        $('.add-title').removeClass('hide');

        var handsontable_html ='<div id="hot_warehouse_type" class="hot handsontable htColumnHeaders"></div>';
        if($('#add_handsontable').html() != null){
          $('#add_handsontable').empty();

          $('#add_handsontable').html(handsontable_html);
        }else{
          $('#add_handsontable').html(handsontable_html);

        }

    setTimeout(function(){
      "use strict";
      var hotElement1 = document.querySelector('#hot_warehouse_type');

       var warehouse_type = new Handsontable(hotElement1, {
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

        colWidths: [40, 100, 30,30, 30, 140],
        rowHeights: 30,
        rowHeaderWidth: [44],

        columns: [
                {
                  type: 'text',
                  data: 'warehouse_code',
                  readOnly:true,
                  
                },
                 {
                  type: 'text',
                  data: 'warehouse_name',
                  // set desired format pattern and
                },
                 {
                  type: 'text',
                  data: 'warehouse_address',
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
        nestedHeaders: [{"1":"<?php echo _l('warehouse_code') ?>",
                      "2":"<?php echo _l('warehouse_name') ?>",
                      "3":"<?php echo _l('warehouse_address') ?>",
                      "4":"<?php echo _l('order') ?>",
                      "5":"<?php echo _l('display') ?>",
                      "6":"<?php echo _l('note') ?>",
                    }],

        data: [{"warehouse_code":warehouse_code,"warehouse_name":warehouse_name,"warehouse_address":warehouse_address,"order":order,"display":display,"note":note}],

      });
       warehouse_type_value = warehouse_type;
      },300);

    }

    function add_warehouse_type(invoker){
      "use strict";
      var valid_warehouse_type = $('#hot_warehouse_type').find('.htInvalid').html();

      if(valid_warehouse_type){
        alert_float('danger', "<?php echo _l('data_must_number') ; ?>");
      }else{

        $('input[name="hot_warehouse_type"]').val(warehouse_type_value.getData());
        $('#add_warehouse_type').submit(); 

      }
        
    }

  
  function add_one_warehouse(){
    "use strict";

    $('#a_warehouse').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#warehouse_id').html('');

    $('#a_warehouse input[name="warehouse_code"]').val('');
    $('#a_warehouse input[name="warehouse_name"]').val('');
    $('#a_warehouse input[name="order"]').val('');

    $('#a_warehouse textarea[name="warehouse_address"]').val('');
    $('#a_warehouse textarea[name="note"]').val('');

    $('#a_warehouse input[name="display"]').prop("checked", true);

    requestGetJSON('warehouse/get_warehouse_custom_fields_html/' + 0).done(function (response) {
      $('#custom_fields_items').html(response.custom_fields_html);

      $("select[name='assign_to_staffs[]']").html(response.assign_to_staff);
      $("select[name='assign_to_staffs[]']").selectpicker('refresh');

        init_selectpicker();
    });

       
  }

  function edit_warehouse_type(invoker,id){
      "use strict";

      var $warehouseModal = $('#a_warehouse');

     $warehouseModal.find('input[name="warehouse_code"]').val('');
     $warehouseModal.find('input[name="warehouse_name"]').val('');
     $warehouseModal.find('input[name="order"]').val('');

     $warehouseModal.find('textarea[name="warehouse_address"]').val('');
     $warehouseModal.find('textarea[name="note"]').val('');

     $warehouseModal.find('input[name="display"]').prop("checked", false);
     $warehouseModal.find('input[name="hide_warehouse_when_out_of_stock"]').prop("checked", false);

      
      $('#a_warehouse').modal('show');
      $('.edit-title').removeClass('hide');
      $('.add-title').addClass('hide');

      $('#warehouse_id').html('');
      $('#warehouse_id').append(hidden_input('id',id));

        // If id found get the text from the datatable
        if (typeof (id) !== 'undefined') {

            requestGetJSON('warehouse/get_warehouse_by_id/' + id).done(function (response) {

                $warehouseModal.find('input[name="warehouse_code"]').val(response.warehouse_code);
                $warehouseModal.find('input[name="warehouse_name"]').val(response.warehouse_name);
                $warehouseModal.find('input[name="order"]').val(response.order);
                $warehouseModal.find('input[name="city"]').val(response.city);
                $warehouseModal.find('input[name="state"]').val(response.state);
                $warehouseModal.find('input[name="zip_code"]').val(response.zip_code);

                if(response.country != ''){
                  $("select[name='country']").val(response.country).change();
                }else{
                  $("select[name='country']").val('').change();

                }

                if(response.display == 1){
                    $warehouseModal.find('input[name="display"]').prop("checked", true);
                  }else{
                    $warehouseModal.find('input[name="display"]').prop("checked", false);

                  }

                  if(response.hide_warehouse_when_out_of_stock == 1){
                    $warehouseModal.find('input[name="hide_warehouse_when_out_of_stock"]').prop("checked", true);
                  }else{
                    $warehouseModal.find('input[name="hide_warehouse_when_out_of_stock"]').prop("checked", false);

                  }

                $warehouseModal.find('textarea[name="warehouse_address"]').val(response.warehouse_address.replace(/(<|<)br\s*\/*(>|>)/g, " "));
                $warehouseModal.find('textarea[name="note"]').val(response.note.replace(/(<|<)br\s*\/*(>|>)/g, " "));

                $('#custom_fields_items').html(response.custom_fields_html);

                $("select[name='assign_to_staffs[]']").html(response.assign_to_staff);
                $("select[name='assign_to_staffs[]']").selectpicker('refresh');

                init_selectpicker();

            });

        }
   
       
  }

  $('#warehouse_code').on('keypress', function() {
    var warehouse_code = $('input[name="warehouse_code"]').val();
    if(warehouse_code.length >= 100){
        alert_float('warning', "<?php echo _l('Maximum_length_warehouse_code_is_100_words') ; ?>", 200);
    }
  });
  

</script>