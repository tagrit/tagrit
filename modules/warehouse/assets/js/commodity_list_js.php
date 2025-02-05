<script>
  var hidden_columns = [2,6];
  var sub_group_value ='';

  (function($) {
    "use strict";

    $('input[name="description"]' ).change(function() {
      if($( 'input[name="sku_name"]' ).val() == ''){
        $( 'input[name="sku_name"]' ).val($('input[name="description"]' ).val());
      }

    });


    var gallery = new SimpleLightbox('.gallery a', {});

    if($('#dropzoneDragArea').length > 0){
      expenseDropzone = new Dropzone(".commodity_list-add-edit", appCreateDropzoneOptions({
        autoProcessQueue: false,
        clickable: '#dropzoneDragArea',
        previewsContainer: '.dropzone-previews',
        addRemoveLinks: true,
        maxFiles: 10,

        success:function(file,response){
         response = JSON.parse(response);
         if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {

          if(response.add_variant == 'add_variant'){
            $.get(admin_url + 'warehouse/copy_product_image/' +response.id, function (response1) {
              response1 = JSON.parse(response1);

              var check_id = $('#commodity_item_id').html();
              if(check_id){
                alert_float('success', "<?php echo _l('updated_successfully') ?>");
              }else{
                alert_float('success', "<?php echo _l('added_successfully') ?>");
              }

              $('#commodity_list-add-edit').modal('hide');
              var table_commodity_list = $('table.table-table_commodity_list');
              table_commodity_list.DataTable().ajax.reload(null, false);

            });
          }else{
            var check_id = $('#commodity_item_id').html();
            if(check_id){
              alert_float('success', "<?php echo _l('updated_successfully') ?>");
            }else{
              alert_float('success', "<?php echo _l('added_successfully') ?>");
            }
              
            $('#commodity_list-add-edit').modal('hide');
            var table_commodity_list = $('table.table-table_commodity_list');
            table_commodity_list.DataTable().ajax.reload(null, false);

          }

         }else{

          expenseDropzone.processQueue();

        }

      },

    }));
    }

    appValidateForm($("body").find('.commodity_list-add-edit'), {
      // 'commodity_code': 'required',
      commodity_code: {
        required: true,
        // email: true,
        remote: {
          url: site_url + "warehouse/wh_check_commodity_code_exit",
          type: 'post',
          data: {
            commodity_code: function() {
              return $('input[name="commodity_code"]').val();
            },
            commodity_item_id: function() {
              return $('input[name="id"]').val();
            }
          }
        }
      },

      'unit_id': 'required',
      'rate': 'required',
    },expenseSubmitHandler);

    $(".checkbox #filter_all_simple_variation").change(function() {
        if(this.checked) {
          $('input[name="filter_all_simple_variation_value"]').val('true');
        }else{
          $('input[name="filter_all_simple_variation_value"]').val('false');
        }
    });


    var ProposalServerParams = {
      "warehouse_ft": "[name='warehouse_filter[]']",
      "commodity_ft": "[name='commodity_filter[]']",
      "alert_filter": "[name='alert_filter']",
      "item_filter": "[name='item_filter[]']",
      "parent_item": "[name='parent_item_filter']",
      "filter_all_simple_variation": "[name='filter_all_simple_variation_value']",
      "can_be_value_filter": "[name='can_be_value_filter[]']",
      "barcode_filter": "[name='barcode_filter']",
      "group_filter": "[name='group_filter[]']",
      "sub_group_filter": "[name='sub_group_filter[]']",
    };

    var table_commodity_list = $('table.table-table_commodity_list');
    var _table_api = initDataTable(table_commodity_list, admin_url+'warehouse/table_commodity_list', [0], [0], ProposalServerParams,  [1, 'desc']);
    $.each(ProposalServerParams, function(i, obj) {
      $('select' + obj).on('change', function() {  
        table_commodity_list.DataTable().ajax.reload();
      });
    });

    $('#filter_all_simple_variation').on('change', function() {
         table_commodity_list.DataTable().ajax.reload();
    });

    $('#barcode_filter').on('change', function() {
         table_commodity_list.DataTable().ajax.reload();
    });

    /**
     * department_change
     * @param  integer invoker 
     *     
     */
     $('select[name="group_id"]').on('change',function(){

      var data_select = {};
      data_select.group_id = $('select[name="group_id"]').val();


      $.post(admin_url + 'warehouse/get_subgroup_fill_data',data_select).done(function(response){
       response = JSON.parse(response);
       $("select[name='sub_group']").html('');

       $("select[name='sub_group']").append(response.subgroup);
       $("select[name='sub_group']").selectpicker('refresh');

       if(sub_group_value != ''){

        $("select[name='sub_group']").val(sub_group_value).change();
        sub_group_value = '';
      }



    });

    });

     $("input[data-type='currency']").on({
      keyup: function() {        
        formatCurrency($(this));
      },
      blur: function() { 
        formatCurrency($(this), "blur");
      }
    });
     init_selectpicker();



   })(jQuery); 



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

          var type_products ={};
          type_products['1'] ='<?php echo _l('materials') ; ?>';
          type_products['2'] ='<?php echo _l('tools') ; ?>';
          type_products['3'] ='<?php echo _l('service') ; ?>';
          type_products['4'] ='<?php echo _l('foods') ; ?>';

          function rendererDropdown(instance, td, row, col, prop, value, cellProperties) {
            "use strict";

            var selectid ='';
            var dataRender ={};
            switch(col) {
              case 3:
              selectid ='units';
              dataRender = units;
              break;
              case 4:
              selectid ='commodity_types';
              dataRender = commodity_types;
              break;
              case 5:
              selectid ='warehouses';
              dataRender = warehouses;
              break;
              case 6:
              selectid ='commodity_groups';
              dataRender = commodity_groups;
              break;
              case 7:
              selectid ='taxes';
              dataRender = taxes;
              break;
              case 9:
              selectid ='styles';
              dataRender = styles;
              break;
              case 10:
              selectid ='models';
              dataRender = models;
              break;
              case 11:
              selectid ='sizes';
              dataRender = sizes;
              break;
              case 17:
              selectid ='type_products';
              dataRender = type_products;
              break;

            }


            if (td.innerHTML === undefined || td.innerHTML === null || td.innerHTML === "") {

              if(row%2==1){
                var selectbox = " <select id=" + selectid +row+ col + "  >";
              }else{
                var selectbox = " <select id=" + selectid +row+ col + "  >";
              }
              selectbox +=    "<option value =''></option>";

              for (let elem in dataRender) {  
                selectbox +=  "<option value ="+elem+">"+dataRender[elem]+"</option>";
              };
              selectbox += "</select>";

              var $td = $(td);
              var $text = $(selectbox);
              $text.on('mousedown', function (event) {
                event.stopPropagation(); 
              });

              $td.empty().append($text);
              $('#'+selectid +row+ col).change(function () {

                var value = this[this.selectedIndex].value;
                instance.setDataAtCell(row, col, value);
              });
            }
          }

          var dataObject = [
          ];
          var hotElement1 = document.querySelector('#hot_warehouse_type');

          var warehouse_type = new Handsontable(hotElement1, {

            contextMenu: true,
            manualRowMove: true,
            manualColumnMove: true,
            stretchH: 'all',
            autoWrapRow: true,
            rowHeights: 30,
            defaultRowHeight: 100,
            minRows: 10,
            maxRows: 22,
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

            colWidths: [120, 100,150, 80,120, 120,120, 120,120, 120,120, 120,120, 120,120, 120,120,],
            rowHeights: 30,
            rowHeaderWidth: [44],

            columns: [
            {
              type: 'text',
              data: '<?php echo _l('commodity_code'); ?>',
            },
            {
              type: 'text',
              data: '<?php echo _l('commodity_barcode'); ?>',

            },
            {
              type: 'text',
              data: '<?php echo _l('description'); ?>',

            },
            {
              type: 'text',
              data: '<?php echo _l('unit_id'); ?>',
            },
            {
              type: 'text',
              data:'<?php echo _l('commodity_type'); ?>',

            },
            {
              type: 'text',
              data: '<?php echo _l('warehouse_id') ?>',

            },
            {
              type: 'text',
              data: '<?php echo _l('commodity_group'); ?>',
            },
            {
              type: 'text',
              data: '<?php echo _l('tax_rate'); ?>',
            },
            {
              type: 'text',
              data: '<?php echo _l('origin'); ?>',
            },
            {
              type: 'text',
              data: '<?php echo _l('style_id'); ?>',
            },
            {
              type: 'text',
              data: '<?php echo _l('model_id'); ?>',
            },
            {
              type: 'text',
              data: '<?php echo _l('size_id'); ?>',
            },
            {
              type: 'text',
              data: '<?php echo _l('commodity_images'); ?>',  
            },
            {
              type: 'text',
              data: '<?php echo _l('date_manufacture'); ?>',
            },
            {
              type: 'text',
              data: '<?php echo _l('expiry_date'); ?>',
            },
            {
              type: 'text',
              data: '<?php echo _l('rate'); ?>',
            },
            {
              type: 'text',
              data: '<?php echo _l('type_product'); ?>',
            },


            ],

            colHeaders: [
            "<?php echo _l('commodity_code'); ?>",
            "<?php echo _l('commodity_barcode'); ?>",
            "<?php echo _l('description'); ?>",
            "<?php echo _l('unit_id'); ?>",
            "<?php echo _l('commodity_type'); ?>",
            "<?php echo _l('warehouse_id'); ?>",
            "<?php echo _l('commodity_group'); ?>",
            "<?php echo _l('tax_rate'); ?>",
            "<?php echo _l('origin'); ?>",
            "<?php echo _l('style_id'); ?>",
            "<?php echo _l('model_id'); ?>",
            "<?php echo _l('size_id'); ?>",
            "<?php echo _l('commodity_images'); ?>",
            "<?php echo _l('date_manufacture'); ?>",
            "<?php echo _l('expiry_date'); ?>",
            "<?php echo _l('rate'); ?>",
            "<?php echo _l('type_product'); ?>",

            ],

            data: dataObject,

            cells: function (row, col, prop, value, cellProperties) {
              var cellProperties = {};
              var data = this.instance.getData();
              cellProperties.className = 'htMiddle ';
              if(col == 3 || col == 4|| col == 5|| col == 6|| col == 7|| col == 9|| col == 10|| col == 11|| col == 17){
           cellProperties.renderer = rendererDropdown; // uses function directly
         }

         return cellProperties;
       }

     });
warehouse_type_value = warehouse_type;
},300);


}

  //submit data
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


  init_commodity_detail();
  function init_commodity_detail(id) {
    "use strict";
    load_small_table_item_proposal(id, '#proposal_sm_view', 'proposal_id', 'warehouse/get_commodity_data_ajax', '.proposal_sm');
  }

  function load_small_table_item_proposal(pr_id, selector, input_name, url, table) {
    "use strict";
    var _tmpID = $('input[name="' + input_name + '"]').val();
    // Check if id passed from url, hash is prioritized becuase is last
    if (_tmpID !== '' && !window.location.hash) {
      pr_id = _tmpID;
        // Clear the current id value in case user click on the left sidebar credit_note_ids
        $('input[name="' + input_name + '"]').val('');
      } else {

        if (window.location.hash && !pr_id) {
          pr_id = window.location.hash.substring(1); 
        }
      }
      if (typeof(pr_id) == 'undefined' || pr_id === '') { return; }
      if (!$("body").hasClass('small-table')) { toggle_small_view_proposal(table, selector); }
      $('input[name="' + input_name + '"]').val(pr_id);
      do_hash_helper(pr_id);
      $(selector).load(admin_url + url + '/' + pr_id);
      if (is_mobile()) {
        $('html, body').animate({
          scrollTop: $(selector).offset().top + 150
        }, 600);
      }
    }

    function toggle_small_view_proposal(table, main_data) {
      "use strict";

      $("body").toggleClass('small-table');
      var tablewrap = $('#small-table');
      if (tablewrap.length === 0) { return; }
      var _visible = false;
      if (tablewrap.hasClass('col-md-5')) {
        tablewrap.removeClass('col-md-5').addClass('col-md-12');
        _visible = true;
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-right').addClass('fa fa-angle-double-left');
      } else {
        tablewrap.addClass('col-md-5').removeClass('col-md-12');
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-left').addClass('fa fa-angle-double-right');
      }
      var _table = $(table).DataTable();
    // Show hide hidden columns
    _table.columns(hidden_columns).visible(_visible, false);
    _table.columns.adjust();
    $(main_data).toggleClass('hide');
    $(window).trigger('resize');
  }





  function close_modal_preview(){
    "use strict";
    $('._project_file').modal('hide');
  }

  

  $(document).ready(function(){
    "use strict";
    $("#wizard-picture").change(function(){
      readURL(this);
    });
  });

  
  $('#hot-display-license-info').empty();
  Dropzone.options.expenseForm = false;
  var expenseDropzone;


   // var data_long_descriptions;
   function expenseSubmitHandler(form){
    "use strict";
    $('.submit_btn').attr( "disabled", "disabled" );

    var data ={};


    data.commodity_code = $('input[name="commodity_code"]').val();
    data.description = $('input[name="description"]').val();
    data.commodity_barcode = $('input[name="commodity_barcode"]').val();
    data.sku_code = $('input[name="sku_code"]').val();
    data.sku_name = $('input[name="sku_name"]').val();

    data.long_description = $('textarea[name="long_description"]').val();

    data.commodity_type = $('select[name="commodity_type"]').val();
    data.unit_id = $('select[name="unit_id"]').val();
    data.group_id = $('select[name="group_id"]').val();
    data.sub_group = $('select[name="sub_group"]').val();

    data.profif_ratio = $('input[name="profif_ratio"]').val();
    data.tax = $('select[name="tax"]').val();

    data.purchase_price = $('input[name="purchase_price"]').val();
    data.rate = $('input[name="rate"]').val();

    data.origin = $('input[name="origin"]').val();
    data.style_id = $('select[name="style_id"]').val();
    data.model_id = $('select[name="model_id"]').val();
    data.size_id = $('select[name="size_id"]').val();
    data.color = $('select[name="color"]').val();
    data.guarantee = $('input[name="guarantee"]').val();
    data.warehouse_id = $('select[name="warehouse_id"]').val();
    data.parent_id = $('select[name="parent_id"]').val();

    data.long_descriptions = tinymce.activeEditor.getContent();
    data.formdata = $( form ).serializeArray();

    var without_checking = $('input[id="without_checking_warehouse"]').is(":checked");
    if(without_checking == true ){
      data.without_checking_warehouse = 1;

    }else{
      data.without_checking_warehouse = 0;

    }

    if($('input[id="can_be_sold"]').is(":checked")){
      data.can_be_sold = 'can_be_sold';
    }else{
      data.can_be_sold = null;
    }
    if($('input[id="can_be_purchased"]').is(":checked")){
      data.can_be_purchased = 'can_be_purchased';
    }else{
      data.can_be_purchased = null;
    }
    if($('input[id="can_be_manufacturing"]').is(":checked")){
      data.can_be_manufacturing = 'can_be_manufacturing';
    }else{
      data.can_be_manufacturing = null;
    }
    if($('input[id="can_be_inventory"]').is(":checked")){
      data.can_be_inventory = 'can_be_inventory';
    }else{
      data.can_be_inventory = null;
    }  

    /*update*/
    var check_id = $('#commodity_item_id').html();
    if(check_id){
      data.id = $('input[name="id"]').val();
    }

       //check duplicate sku code
       var flag_duplicate_sku = 1;

       var sku_data ={};
       sku_data.sku_code =  $('input[name="sku_code"]').val();
       if(check_id){
        sku_data.item_id =  $('input[name="id"]').val();
      }else{
        sku_data.item_id = '';
      }

      $.post(admin_url + 'warehouse/check_sku_duplicate', sku_data).done(function(response) {
        response = JSON.parse(response);

        if(response.message == 'false' || response.message ==  false){

         alert_float('warning', "<?php echo _l('sku_code_already_exists') ?>");
         $('.submit_btn').removeAttr('disabled')


       }else{
        $('.submit_btn').attr( "disabled", "disabled" );

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
              expenseDropzone.options.url = admin_url + 'warehouse/add_commodity_attachment/' + response.commodityid+'/'+add_variant;
              expenseDropzone.processQueue();
            } else {
              if(check_id){
                alert_float('success', "<?php echo _l('updated_successfully') ?>");
              }else{
                alert_float('success', "<?php echo _l('added_successfully') ?>");
              }

              $('#commodity_list-add-edit').modal('hide');

              var table_commodity_list = $('table.table-table_commodity_list');
              table_commodity_list.DataTable().ajax.reload(null, false);

            }
          } else {
            if(check_id){
              alert_float('success', "<?php echo _l('updated_successfully') ?>");
            }else{
              alert_float('success', "<?php echo _l('added_successfully') ?>");
            }

            $('#commodity_list-add-edit').modal('hide');

            var table_commodity_list = $('table.table-table_commodity_list');
            table_commodity_list.DataTable().ajax.reload(null, false);

          }
        } else {
          alert_float('warning', "<?php echo _l('Add_commodity_type_false') ?>");

          $('#commodity_list-add-edit').modal('hide');

          var table_commodity_list = $('table.table-table_commodity_list');
          table_commodity_list.DataTable().ajax.reload(null, false);
        }
        $('.submit_btn').removeAttr('disabled');
      });
      }

    });

      return false;
    }

      //function delete contract attachment file 
      function delete_contract_attachment(wrapper, id) {
        "use strict";

        if (confirm_delete()) {
         $.get(admin_url + 'warehouse/delete_commodity_file/' + id, function (response) {
          if (response.success == true) {
           $(wrapper).parents('.dz-preview').remove();

           var totalAttachmentsIndicator = $('.dz-preview'+id);
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
  

     function readURL(input) {
      "use strict";
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          $('#wizardPicturePreview').attr('src', e.target.result).fadeIn('slow');
        }
        reader.readAsDataURL(input.files[0]);
      }
    }

    function edit_commodity_item(invoker){
      "use strict";
      $('#commodity_list-add-edit').modal('show');
      
      $('#commodity_item_id').empty();
      $('#commodity_item_id').append(hidden_input('id',$(invoker).data('commodity_id')));

      $('.edit-commodity-title').removeClass('hide');
      $('.add-commodity-title').addClass('hide');

      $('#commodity_list-add-edit').find('input').not('input[type="hidden"]').val('');


      $('#commodity_list-add-edit input[name="commodity_code"]').val($(invoker).data('commodity_code'));
      $('#commodity_list-add-edit input[name="commodity_barcode"]').val($(invoker).data('commodity_barcode'));
      $('#commodity_list-add-edit textarea[name="long_description"]').val($(invoker).data('long_description'));
      $('#commodity_list-add-edit input[name="description"]').val($(invoker).data('description'));

      $('#commodity_list-add-edit input[name="sku_code"]').val($(invoker).data('sku_code'));
      $('#commodity_list-add-edit input[name="sku_name"]').val($(invoker).data('sku_name'));
      $('#commodity_list-add-edit input[name="purchase_price"]').val($(invoker).data('purchase_price'));


      if($(invoker).data('tax') != 0){
        $('#commodity_list-add-edit select[name="tax"]').val($(invoker).data('tax')).change();
      }else{
        $('#commodity_list-add-edit select[name="tax"]').val('').change();
      }


      if($(invoker).data('unit_id') != 0 ){
        $('#commodity_list-add-edit select[name="unit_id"]').val($(invoker).data('unit_id')).change();
      }else{

       $('#commodity_list-add-edit select[name="unit_id"]').val('').change();
     }

     if($(invoker).data('commodity_type') != 0){
      $('#commodity_list-add-edit select[name="commodity_type"]').val($(invoker).data('commodity_type')).change();

    }else{

      $('#commodity_list-add-edit select[name="commodity_type"]').val('').change();
    }

    if($(invoker).data('sub_group') != 0){
      sub_group_value = $(invoker).data('sub_group');
    }

    if($(invoker).data('group_id') != 0){
      $('#commodity_list-add-edit select[name="group_id"]').val($(invoker).data('group_id')).change();

    }else{
      $('#commodity_list-add-edit select[name="group_id"]').val('').change();

    }

    if($(invoker).data('warehouse_id') != 0){
      $('#commodity_list-add-edit select[name="warehouse_id"]').val($(invoker).data('warehouse_id')).change();
    }else{
      $('#commodity_list-add-edit select[name="warehouse_id"]').val('').change();
    }

    if($(invoker).data('tax') != 0){
      $('#commodity_list-add-edit select[name="tax"]').val($(invoker).data('tax')).change();
    }else{
      $('#commodity_list-add-edit select[name="tax"]').val('').change();
    }

    if($(invoker).data('tax2') != 0){
      $('#commodity_list-add-edit select[name="tax2"]').val($(invoker).data('tax2')).change();
    }else{
      $('#commodity_list-add-edit select[name="tax2"]').val('').change();
    }

    $('#commodity_list-add-edit input[name="origin"]').val($(invoker).data('origin'));
    $('#commodity_list-add-edit input[name="rate"]').val($(invoker).data('rate'));
    $('#commodity_list-add-edit input[name="type_product"]').val($(invoker).data('type_product'));
    $('#commodity_list-add-edit input[name="guarantee"]').val($(invoker).data('guarantee'));
    $('#commodity_list-add-edit input[name="profif_ratio"]').val($(invoker).data('profif_ratio'));

    if($(invoker).data('style_id') != 0){
      $('#commodity_list-add-edit select[name="style_id"]').val($(invoker).data('style_id')).change();
    }else{
      $('#commodity_list-add-edit select[name="style_id"]').val('').change();
    }
    if($(invoker).data('model_id') != 0){
      $('#commodity_list-add-edit select[name="model_id"]').val($(invoker).data('model_id')).change();
    }else{
      $('#commodity_list-add-edit select[name="model_id"]').val('').change();
    }
    if($(invoker).data('size_id') != 0){
      $('#commodity_list-add-edit select[name="size_id"]').val($(invoker).data('size_id')).change();
    }else{
      $('#commodity_list-add-edit select[name="size_id"]').val('').change();
    }
    if($(invoker).data('sub_group') != 0){
      $('#commodity_list-add-edit select[name="sub_group"]').val($(invoker).data('sub_group')).change();
    }else{
      $('#commodity_list-add-edit select[name="sub_group"]').val('').change();
    }
    if($(invoker).data('color') != 0){
      $('#commodity_list-add-edit select[name="color"]').val($(invoker).data('color')).change();
    }else{
      $('#commodity_list-add-edit select[name="color"]').val('').change();
    }
    if($(invoker).data('date_manufacture') != 0){
      $('#commodity_list-add-edit select[name="date_manufacture"]').val($(invoker).data('date_manufacture')).change();
    }else{
      $('#commodity_list-add-edit select[name="date_manufacture"]').val('').change();
    }
    if($(invoker).data('expiry_date') != 0){
      $('#commodity_list-add-edit select[name="expiry_date"]').val($(invoker).data('expiry_date')).change();
    }else{
      $('#commodity_list-add-edit select[name="expiry_date"]').val('').change();
    }

    if($(invoker).data('without_checking_warehouse') == 1){
      $('#commodity_list-add-edit input[id="without_checking_warehouse"]').prop('checked', true);
    }else{
      $('#commodity_list-add-edit input[id="without_checking_warehouse"]').prop("checked", false);
    }

    if($(invoker).data('can_be_sold') == 'can_be_sold'){
      $('#commodity_list-add-edit input[id="can_be_sold"]').prop('checked', true);
    }else{
      $('#commodity_list-add-edit input[id="can_be_sold"]').prop("checked", false);
    }
    if($(invoker).data('can_be_purchased') == 'can_be_purchased'){
      $('#commodity_list-add-edit input[id="can_be_purchased"]').prop('checked', true);
    }else{
      $('#commodity_list-add-edit input[id="can_be_purchased"]').prop("checked", false);
    }
    
    if($(invoker).data('can_be_manufacturing') == 'can_be_manufacturing'){
      $('#commodity_list-add-edit input[id="can_be_manufacturing"]').prop('checked', true);
    }else{
      $('#commodity_list-add-edit input[id="can_be_manufacturing"]').prop("checked", false);
    }
    if($(invoker).data('can_be_inventory') == 'can_be_inventory'){
      $('#commodity_list-add-edit input[id="can_be_inventory"]').prop('checked', true);
    }else{
      $('#commodity_list-add-edit input[id="can_be_inventory"]').prop("checked", false);
    }

    tinyMCE.activeEditor.setContent("");


    $.post(admin_url + 'warehouse/get_commodity_file_url/'+$(invoker).data('commodity_id')).done(function(response) {
      response = JSON.parse(response);

      $('#images_old_preview').empty();

      if(response !=''){
        $('#images_old_preview').prepend(response.arr_images);

      }


    });

    /*get long descriptions*/

    $.post(admin_url + 'warehouse/get_item_longdescriptions/'+$(invoker).data('commodity_id')).done(function(response) {
      response = JSON.parse(response);
      if(response.long_descriptions != '' && response.long_descriptions != null){
        tinyMCE.activeEditor.setContent(response.long_descriptions);

      }

      $('#commodity_list-add-edit textarea[name="long_description"]').val(response.description);
      $('#commodity_list-add-edit input[name="description"]').val(response.item_des);
      $('#commodity_list-add-edit input[name="sku_name"]').val(response.item_sku_name);

      $('#custom_fields_items').html(response.custom_fields_html);

            //get tags

            $('#tags_value').find('ul li.tagit-choice').remove();
            $('#tags_value').find('ul').prepend(response.item_tags);

            init_tags_inputs();
            $('#commodity_list-add-edit input[id="tags"]').attr('value', response.item_value);

            //variation value
            $('.list_approve').html('');
            $('.list_approve').append(response.variation_html);
            addMoreVendorsInputKey = response.variation_index;

            //parent id
            // $("select[id='parent_id']").html('');
            // $("select[id='parent_id']").append(response.item_html).selectpicker('refresh');
            $("#parent_item_html").html(response.item_html);


            //flag_is_parent
            if(response.flag_is_parent == true){
              $(".parent_item_hide").addClass("hide"); 
            }else{
              $(".parent_item_hide").removeClass("hide"); 
            }

              init_selectpicker();
              $(".selectpicker").selectpicker('refresh');

              init_ajax_search('items','#parent_id.ajax-search',undefined,admin_url+'warehouse/wh_parent_item_search');

          });


    $("input[data-type='currency']").on({
      keyup: function() {        
        formatCurrency($(this));
      },
      blur: function() { 
        formatCurrency($(this), "blur");
      }
    });
    init_selectpicker();
    $('#commodity_list-add-edit').find('select').selectpicker('refresh');


  }


  function formatNumber(n) {
    return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
  }
  function formatCurrency(input, blur) {
    "use strict";
    var input_val = input.val();
    if (input_val === "") { return; }
    var original_len = input_val.length; 
    var caret_pos = input.prop("selectionStart");
    if (input_val.indexOf(".") >= 0) {
      var decimal_pos = input_val.indexOf(".");
      var left_side = input_val.substring(0, decimal_pos);
      var right_side = input_val.substring(decimal_pos);
      left_side = formatNumber(left_side);
      right_side = formatNumber(right_side);
      right_side = right_side.substring(0, 2);
      input_val = left_side + "." + right_side;
    } else {

      input_val = formatNumber(input_val);
      input_val = input_val;
    }
    input.val(input_val);
    var updated_len = input_val.length;
    caret_pos = updated_len - original_len + caret_pos;
    input[0].setSelectionRange(caret_pos, caret_pos);
  }




  function new_commodity_item(){
    "use strict";

    $.post(admin_url + 'warehouse/get_commodity_barcode').done(function(response) {
      response = JSON.parse(response);
      $('#commodity_list-add-edit input[name="commodity_barcode"]').val(response);
    });

    $.post(admin_url + 'warehouse/get_variation_html_add').done(function(response) {
      response = JSON.parse(response);
        //variation value
        $('.list_approve').html('');
        $('.list_approve').append(response.variation_html);
        addMoreVendorsInputKey = response.variation_index;

        //parent id
        // $("select[id='parent_id']").html('');
        // $("select[id='parent_id']").append(response.item_html).selectpicker('refresh');
        $("#parent_item_html").html(response.item_html);

        //flag_is_parent
        $(".parent_item_hide").removeClass("hide"); 
        init_selectpicker(); 
        $(".selectpicker").selectpicker('refresh');

        init_ajax_search('items','#parent_id.ajax-search',undefined,admin_url+'warehouse/wh_parent_item_search');
    });

    $('#commodity_list-add-edit').modal('show');

    $('#commodity_item_id').empty();

    $('.edit-commodity-title').addClass('hide');
    $('.add-commodity-title').removeClass('hide');

    $('.dropzone-previews').empty();
    $('#images_old_preview').empty();

    tinyMCE.activeEditor.setContent("");

    $('#commodity_list-add-edit').find('input').not('input[type="hidden"]').val('');

    $('#commodity_list-add-edit input[name="commodity_code"]').val('');

    $('#commodity_list-add-edit textarea[name="long_description"]').val('');

    $('#commodity_list-add-edit input[name="description"]').val('');
    $('#commodity_list-add-edit input[name="sku_code"]').val('');
    $('#commodity_list-add-edit input[name="sku_name"]').val('');
    $('#commodity_list-add-edit input[name="purchase_price"]').val('');
    $('#commodity_list-add-edit input[name="description"]').val('');

    $('#commodity_list-add-edit select[name="unit_id"]').val('').change();
    $('#commodity_list-add-edit select[name="commodity_type"]').val('').change();
    $('#commodity_list-add-edit select[name="group_id"]').val('').change();
    $('#commodity_list-add-edit select[name="warehouse_id"]').val('').change();
    $('#commodity_list-add-edit select[name="tax"]').val('').change();

    sub_group_value = '';
    $('#commodity_list-add-edit select[name="sub_group"]').val('').change();

    $('#commodity_list-add-edit input[name="origin"]').val('');
    $('#commodity_list-add-edit input[name="rate"]').val('');
    $('#commodity_list-add-edit input[name="type_product"]').val('');
    $('#commodity_list-add-edit input[name="guarantee"]').val('');
    $('#commodity_list-add-edit input[name="profif_ratio"]').val('<?php echo get_warehouse_option('warehouse_selling_price_rule_profif_ratio'); ?>');

    $('#commodity_list-add-edit select[name="style_id"]').val('').change();
    $('#commodity_list-add-edit select[name="model_id"]').val('').change();
    $('#commodity_list-add-edit select[name="size_id"]').val('').change();


    $('#commodity_list-add-edit input[name="date_manufacture"]').val('').change();
    $('#commodity_list-add-edit input[name="expiry_date"]').val('').change();
    $('#commodity_list-add-edit img[id="wizardPicturePreview"]').attr('src', '<?php echo site_url(WAREHOUSE_PATH.'nul_image.jpg'); ?>');

    $('#commodity_list-add-edit input[id="without_checking_warehouse"]').removeAttr("checked");

    $('#commodity_list-add-edit input[id="can_be_sold"]').prop('checked', true);
    $('#commodity_list-add-edit input[id="can_be_purchased"]').prop('checked', true);
    $('#commodity_list-add-edit input[id="can_be_manufacturing"]').prop('checked', true);
    $('#commodity_list-add-edit input[id="can_be_inventory"]').prop('checked', true);

    $('#tags_value').find('ul li.tagit-choice').remove();
    /*init tags input*/
    init_tags_inputs();
    init_selectpicker();

  }

  $("body").on('click', '.tagit-close', function() {
    "use strict";

    var tag_id = $(this).parents('li').val();
    /*delete tag id*/
    if(tag_id){
      $.post(admin_url + 'warehouse/delete_item_tags/'+tag_id).done(function(response) {
        response = JSON.parse(response);

        if(response.status == 'true'){
                  // $(this).parents('li').remove();
                  alert_float('success', response.message);
                }else{
                  alert_float('warning', response.message);
                }

              });
    }

    $(this).parents('li').remove();

  });


  function view_commodity_images(){
    "use strict";
    $('#commodity_list_carosel').modal('show');
  }

  function show_detail_item(el){
    "use strict";

    $('.add-title').text($(el).data('name'));
    $('#show_detail').modal('show');

    $('input[name="warehouse_id"]').val($(el).data('warehouse_id'));
    $('input[name="commodity_id"]').val($(el).data('commodity_id'));
    $('input[name="expiry_date"]').val($(el).data('expiry_date'));


    var ProposalServerParams1 = {
      "expiry_date1": "[name='expiry_date']",
      "commodity_id1": "[name='commodity_id']",
      "warehouse_id1": "[name='warehouse_id']",
    };

    $('.table-table_out_of_stock').DataTable().destroy();
    $('.table-table_expired').DataTable().destroy();

    var table_out_of_stock = $('table.table-table_out_of_stock');
    var _table_api = initDataTable(table_out_of_stock, admin_url+'warehouse/table_out_of_stock', true, '', ProposalServerParams1);
    
    var table_expired = $('table.table-table_expired');
    var _table_api = initDataTable(table_expired, admin_url+'warehouse/table_expired', true, '', ProposalServerParams1);


  }

  function staff_bulk_actions(){
    "use strict";
    $('#table_commodity_list_bulk_actions').modal('show');
  }


 // Leads bulk action
 function warehouse_delete_bulk_action(event) {
  "use strict";

  if (confirm_delete()) {
    var mass_delete = $('#mass_delete').prop('checked');
    var change_item_selling_price = $('#change_item_selling_price').prop('checked');
    var change_item_purchase_price = $('#change_item_purchase_price').prop('checked');
    var clone_items = $('#clone_items').prop('checked');

    var selling_price = $('input[name="selling_price"]').val();
    var purchase_price = $('input[name="b_purchase_price"]').val();


    if(mass_delete == true || ( change_item_selling_price == true && selling_price != '') || ( change_item_purchase_price == true && purchase_price != '') || clone_items == true){
      var ids = [];
      var data = {};

      if(change_item_selling_price){
        data.change_item_selling_price = true;
        data.rel_type = 'change_item_selling_price';
        data.selling_price = selling_price;
        data.clone_items = false;
        data.mass_delete = false;

      }else if(change_item_purchase_price){
       data.change_item_purchase_price = true;
       data.rel_type = 'change_item_purchase_price';
       data.purchase_price = purchase_price;
       data.clone_items = false; 
       data.mass_delete = false;

     }else if(clone_items){
      data.mass_delete = false;
      data.rel_type = 'commodity_list';
      data.clone_items = true;
      data.change_item_selling_price = false;
      data.change_item_purchase_price = false;
     }else{
      data.mass_delete = true;
      data.rel_type = 'commodity_list';
      data.clone_items = false;
      data.change_item_selling_price = false;
      data.change_item_purchase_price = false;
    }

      var rows = $('#table-table_commodity_list').find('tbody tr');
      $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input');
        if (checkbox.prop('checked') === true) {
          ids.push(checkbox.val());
        }
      });

      data.ids = ids;
      $(event).addClass('disabled');
      setTimeout(function() {
        $.post(admin_url + 'warehouse/warehouse_delete_bulk_action', data).done(function() {
          window.location.reload();
        }).fail(function(data) {
          $('#table_commodity_list_bulk_actions').modal('hide');
          alert_float('danger', data.responseText);
        });
      }, 200);
    }else{
      window.location.reload();
    }

  }
}

$('input[name="purchase_price"]').keyup(function(){
 "use strict";
 var data={};
 data.purchase_price = $('input[name="purchase_price"]').val();
 data.profit_rate = $('input[name="profif_ratio"]').val();

 $.post(admin_url + 'warehouse/caculator_sale_price', data).done(function(response) {
  response = JSON.parse(response);
  $('#commodity_list-add-edit input[name="rate"]').val(response.sale_price);
});

});

$('input[name="profif_ratio"]').keyup(function(){
 "use strict";
 var data={};
 data.purchase_price = $('input[name="purchase_price"]').val();
 data.profit_rate = $('input[name="profif_ratio"]').val();

 $.post(admin_url + 'warehouse/caculator_sale_price', data).done(function(response) {
  response = JSON.parse(response);
  $('#commodity_list-add-edit input[name="rate"]').val(response.sale_price);
});

});



$('input[name="rate"]').keyup(function(){
 "use strict";
 var data={};
 data.sale_price = $('input[name="rate"]').val();
 data.profit_rate = $('input[name="profif_ratio"]').val();
 data.purchase_price = $('input[name="purchase_price"]').val();

 if($('input[name="profif_ratio"]').val() != 0 && $('input[name="purchase_price"]').val() != 0){
   $.post(admin_url + 'warehouse/caculator_profit_rate', data).done(function(response) {
    response = JSON.parse(response);

    $('#commodity_list-add-edit input[name="profif_ratio"]').val(response.profit_rate);

  });
 }else if($('input[name="profif_ratio"]').val() == 0){
  $('input[name="purchase_price"]').val($('input[name="rate"]').val());

 }else if($('input[name="profif_ratio"]').val() != 0){

  $.post(admin_url + 'warehouse/caculator_purchase_price', data).done(function(response) {
    response = JSON.parse(response);

    $('#commodity_list-add-edit input[name="purchase_price"]').val(response.purchase_price);

  });

 }


});

function staff_export_item(){
  "use strict";
  var ids = [];
  var data = {};

  data.mass_delete = true;
  data.rel_type = 'commodity_list';

  var rows = $('#table-table_commodity_list').find('tbody tr');
  $.each(rows, function() {
    var checkbox = $($(this).find('td').eq(0)).find('input');
    if (checkbox.prop('checked') === true) {
      ids.push(checkbox.val());
    }
  });

  data.ids = ids;
  $(event).addClass('disabled');
  setTimeout(function() {
    $.post(admin_url + 'warehouse/warehouse_export_item_checked', data).done(function(response) {
      response = JSON.parse(response);
      if(response.success == true){
        alert_float('success', "<?php echo _l("create_export_file_success") ?>");

        $('#dowload_items').removeClass('hide');

        $('#dowload_items').attr({target: '_blank', 
         href  : site_url +response.filename});

      }else{
        alert_float('success', "<?php echo _l("create_export_file_false") ?>");

      }

    }).fail(function(data) {


    });
  }, 200);


}

/*scanner barcode*/
$(document).ready(function() {
  var pressed = false;
  var chars = [];
  $(window).keypress(function(e) {
    if (e.key == '%') {
      pressed = true;
    }
    chars.push(String.fromCharCode(e.which));
    if (pressed == false) {
      setTimeout(function() {
        if (chars.length >= 8) {

          if($( "#commodity_list-add-edit" ).hasClass( "in" )){
            var barcode = chars.join('');
            $('input[name="commodity_barcode"]').val('');
            $('input[name="commodity_barcode"]').focus().val(barcode);
          }else{
            var barcode = chars.join('');
            $('input[name="barcode_filter"]').val('');
            $('input[name="barcode_filter"]').focus().val(barcode).change();
            $('input[name="barcode_filter"]').focusout();
          }

        }
        chars = [];
        pressed = false;
      }, 200);
    }
    pressed = true;
  });
});


  function print_barcode_option(invoker) {
  "use strict";
   var data={};
      data.profit_rate_by_purchase_price_sale = invoker.value;

      if(invoker.value == 1){
        $('.display-select-item').removeClass('hide');
      }else if(invoker.value == 0){
        $('.display-select-item').addClass('hide');
      }
  }


/*print barcode*/
  function print_barcode_bulk_actions(){
    "use strict";
    $('.display-select-item').addClass('hide');
    $("#y_opt_1_").prop("checked", true);

    $("#table_commodity_list_print_barcode option:selected").prop("selected", false).change()
    $("table_commodity_list_print_barcode select[id='item_select_print_barcode']").selectpicker('refresh');

    $('#table_commodity_list_print_barcode').modal('show');
  }


 // Leads bulk action
 function warehouse_print_barcode_action(event) {
  "use strict";

  if (confirm_delete()) {
    var mass_delete = $('#mass_delete').prop('checked');

    if(mass_delete == true){
      var ids = [];
      var data = {};

      data.mass_delete = true;
      data.rel_type = 'commodity_list';

      var rows = $('#table-table_commodity_list').find('tbody tr');
      $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input');
        if (checkbox.prop('checked') === true) {
          ids.push(checkbox.val());
        }
      });

      data.ids = ids;
      $(event).addClass('disabled');
      setTimeout(function() {
        $.post(admin_url + 'warehouse/warehouse_delete_bulk_action', data).done(function() {
          window.location.reload();
        }).fail(function(data) {
          $('#table_commodity_list_print_barcode').modal('hide');
          alert_float('danger', data.responseText);
        });
      }, 200);
    }else{
      window.location.reload();
    }

  }
}


//variation
var addMoreVendorsInputKey;
(function($) {
  "use strict";

  addMoreVendorsInputKey = $('.list_approve').length;
  $("body").on('click', '.new_wh_approval', function() {
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
    $(this).parents('#item_approve').remove();
  });

  $('.account-template-form-submiter').on('click', function() {
    $('input[name="account_template"]').val(account_template.getData());
  });
})(jQuery);


//parent change
$("body").on('change', 'select[name="parent_id"]', function () {

  var parent_id = $('select[name="parent_id"]').val();

      var check_id = $('#commodity_item_id').html();
      var parent_data={};
          if(check_id){
            parent_data.item_id = $('input[name="id"]').val();
          }else{
            parent_data.item_id = '';
          }
          parent_data.parent_id = $('select[name="parent_id"]').val();
      $.post(admin_url + 'warehouse/get_variation_from_parent_item', parent_data).done(function(response) {
      response = JSON.parse(response);

      //variation value
      $('.list_approve').html('');
      $('.list_approve').append(response.variation_html);
      addMoreVendorsInputKey = response.variation_index;

      init_selectpicker();
      $("select[name='Size']").selectpicker('refresh');

      //get parent value use for child if is add new
      if(!check_id){


          $('#commodity_list-add-edit textarea[name="long_description"]').val(response.parent_value.long_description);
          $('#commodity_list-add-edit input[name="description"]').val(response.parent_value.description);
          $('#commodity_list-add-edit input[name="sku_name"]').val(response.parent_value.sku_name);
          $('#commodity_list-add-edit input[name="purchase_price"]').val(response.parent_value.purchase_price);


          if(response.parent_value.tax != 0){
            $('#commodity_list-add-edit select[name="tax"]').val(response.parent_value.tax).change();
          }else{
            $('#commodity_list-add-edit select[name="tax"]').val('').change();
          }

          if(response.parent_value.tax2 != 0){
            $('#commodity_list-add-edit select[name="tax2"]').val(response.parent_value.tax2).change();
          }else{
            $('#commodity_list-add-edit select[name="tax2"]').val('').change();
          }

          if(response.parent_value.unit_id != 0 ){
            $('#commodity_list-add-edit select[name="unit_id"]').val(response.parent_value.unit_id).change();
          }else{

           $('#commodity_list-add-edit select[name="unit_id"]').val('').change();
         }

         if(response.parent_value.commodity_type != 0){
          $('#commodity_list-add-edit select[name="commodity_type"]').val(response.parent_value.commodity_type).change();

          }else{

            $('#commodity_list-add-edit select[name="commodity_type"]').val('').change();
          }

          if(response.parent_value.sub_group != 0){
            sub_group_value = response.parent_value.sub_group;
          }

          if(response.parent_value.group_id != 0){
            $('#commodity_list-add-edit select[name="group_id"]').val(response.parent_value.group_id).change();

          }else{
            $('#commodity_list-add-edit select[name="group_id"]').val('').change();

          }

          if(response.parent_value.warehouse_id != 0){
            $('#commodity_list-add-edit select[name="warehouse_id"]').val(response.parent_value.warehouse_id).change();
          }else{
            $('#commodity_list-add-edit select[name="warehouse_id"]').val('').change();
          }

          if(response.parent_value.tax != 0){
            $('#commodity_list-add-edit select[name="tax"]').val(response.parent_value.tax).change();
          }else{
            $('#commodity_list-add-edit select[name="tax"]').val('').change();
          }

          $('#commodity_list-add-edit input[name="origin"]').val(response.parent_value.origin);
          $('#commodity_list-add-edit input[name="rate"]').val(response.parent_value.rate);
          $('#commodity_list-add-edit input[name="type_product"]').val(response.parent_value.type_product);
          $('#commodity_list-add-edit input[name="guarantee"]').val(response.parent_value.guarantee);
          $('#commodity_list-add-edit input[name="profif_ratio"]').val(response.parent_value.profif_ratio);

          if(response.parent_value.style_id != 0){
            $('#commodity_list-add-edit select[name="style_id"]').val(response.parent_value.style_id).change();
          }else{
            $('#commodity_list-add-edit select[name="style_id"]').val('').change();
          }
          if(response.parent_value.model_id != 0){
            $('#commodity_list-add-edit select[name="model_id"]').val(response.parent_value.model_id).change();
          }else{
            $('#commodity_list-add-edit select[name="model_id"]').val('').change();
          }
          if(response.parent_value.size_id != 0){
            $('#commodity_list-add-edit select[name="size_id"]').val(response.parent_value.size_id).change();
          }else{
            $('#commodity_list-add-edit select[name="size_id"]').val('').change();
          }
          if(response.parent_value.sub_group != 0){
            $('#commodity_list-add-edit select[name="sub_group"]').val(response.parent_value.sub_group).change();
          }else{
            $('#commodity_list-add-edit select[name="sub_group"]').val('').change();
          }
          if(response.parent_value.color != 0){
            $('#commodity_list-add-edit select[name="color"]').val(response.parent_value.color).change();
          }else{
            $('#commodity_list-add-edit select[name="color"]').val('').change();
          }
          if(response.parent_value.date_manufacture != 0){
            $('#commodity_list-add-edit select[name="date_manufacture"]').val(response.parent_value.date_manufacture).change();
          }else{
            $('#commodity_list-add-edit select[name="date_manufacture"]').val('').change();
          }
          if(response.parent_value.expiry_date != 0){
            $('#commodity_list-add-edit select[name="expiry_date"]').val(response.parent_value.expiry_date).change();
          }else{
            $('#commodity_list-add-edit select[name="expiry_date"]').val('').change();
          }

          if(response.parent_value.long_descriptions != '' && response.parent_value.long_descriptions != null){
            tinyMCE.activeEditor.setContent(response.parent_value.long_descriptions);
          }else{
            tinyMCE.activeEditor.setContent("");
          }

      }

    });

});

//add opening stock
function add_opening_stock_modal(id) {
  "use strict";

    $("#modal_wrapper").load("<?php echo admin_url('warehouse/warehouse/add_opening_stock_modal'); ?>", {
         slug: 'add',
         id:id,
    }, function() {

      $("body").find('#appointmentModal').modal({ show: true, backdrop: 'static' });

    });

    init_selectpicker();
    $(".selectpicker").selectpicker('refresh');
  }

  //update
  $('input[id="mass_delete"]').on('click', function() {
  "use strict";
    
    var mass_delete = $('input[id="mass_delete"]').is(":checked");


    if(mass_delete){

      $('input[id="change_item_selling_price"]').prop("checked", false);
      $('input[name="selling_price"]').val('');

      $('input[id="change_item_purchase_price"]').prop("checked", false);
      $('input[name="purchase_price"]').val('');
      $('input[id="clone_items"]').prop("checked", false);
    }

  });

  $('input[id="change_item_selling_price"]').on('click', function() {
  "use strict";
    
    var item_selling_price_checking = $('input[id="change_item_selling_price"]').is(":checked");


    if(item_selling_price_checking){
      $('input[id="mass_delete"]').prop("checked", false);

      $('input[id="change_item_purchase_price"]').prop("checked", false);
      $('input[name="purchase_price"]').val('');
      $('input[id="clone_items"]').prop("checked", false);
    }

  });

  $('input[id="change_item_purchase_price"]').on('click', function() {
  "use strict";
    
    var item_selling_purchase_checking = $('input[id="change_item_purchase_price"]').is(":checked");

    if(item_selling_purchase_checking){
      $('input[id="mass_delete"]').prop("checked", false);

      $('input[id="change_item_selling_price"]').prop("checked", false);
      $('input[name="selling_price"]').val('');
      $('input[id="clone_items"]').prop("checked", false);
    }

  });

  $('input[id="clone_items"]').on('click', function() {
  "use strict";
    
    var clone_items = $('input[id="clone_items"]').is(":checked");


    if(clone_items){

      $('input[id="change_item_selling_price"]').prop("checked", false);
      $('input[name="selling_price"]').val('');

      $('input[id="change_item_purchase_price"]').prop("checked", false);
      $('input[name="purchase_price"]').val('');

      $('input[id="mass_delete"]').prop("checked", false);
    }

  });
  
   // Maybe items ajax search
  init_ajax_search('items','#commodity_filter.ajax-search',undefined,admin_url+'warehouse/wh_commodity_code_search_all');

  init_ajax_search('items','#item_select_print_barcode.ajax-search',undefined,admin_url+'warehouse/wh_commodity_code_search_all');


</script>