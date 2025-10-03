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

           $('#commodity_list-add-edit').modal('hide');

           var table_commodity_list = $('table.table-table_commodity_list');
           table_commodity_list.DataTable().ajax.reload(null, false);

         }else{

          expenseDropzone.processQueue();

        }

      },

    }));
    }

    appValidateForm($("body").find('.commodity_list-add-edit'), {
      'commodity_code': 'required',
      'unit_id': 'required',
      'rate': 'required',
    },expenseSubmitHandler);

    $('input[name="commodity_id"]').val("<?php echo new_html_entity_decode($commodity_item->id) ;?>");

    var ProposalServerParams = {
      "warehouse_ft": "[name='warehouse_filter[]']",
      "commodity_ft": "[name='commodity_filter[]']",
      "alert_filter": "[name='alert_filter']",
      "item_filter": "[name='item_filter[]']",
      "parent_item": "[name='parent_item_filter']",
      "sub_commodity_ft": "[name='commodity_id']",
    };

    var table_commodity_list = $('table.table-table_commodity_list');
    var _table_api = initDataTable(table_commodity_list, admin_url+'warehouse/table_commodity_list', [0], [0], ProposalServerParams,  [1, 'desc']);
    $.each(ProposalServerParams, function(i, obj) {
      $('select' + obj).on('change', function() {  
        table_commodity_list.DataTable().ajax.reload();
      });
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


  Dropzone.options.expenseForm = false;
  var expenseDropzone;


   // var data_long_descriptions;
   function expenseSubmitHandler(form){
    "use strict";


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

       }else{

        $.post(form.action, data).done(function(response) {

         var response = JSON.parse(response);

         if (response.commodityid) {
           if(typeof(expenseDropzone) !== 'undefined'){
            if (expenseDropzone.getQueuedFiles().length > 0) {
              expenseDropzone.options.url = admin_url + 'warehouse/add_commodity_attachment/' + response.commodityid;
              expenseDropzone.processQueue();
            } else {
              $('#commodity_list-add-edit').modal('hide');

              var table_commodity_list = $('table.table-table_commodity_list');
              table_commodity_list.DataTable().ajax.reload(null, false);

            }
          } else {
            $('#commodity_list-add-edit').modal('hide');

            var table_commodity_list = $('table.table-table_commodity_list');
            table_commodity_list.DataTable().ajax.reload(null, false);

          }
        } else {
          $('#commodity_list-add-edit').modal('hide');

          var table_commodity_list = $('table.table-table_commodity_list');
          table_commodity_list.DataTable().ajax.reload(null, false);
        }
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

    if($(invoker).data('without_checking_warehouse') == '1'){
      $('#commodity_list-add-edit input[id="without_checking_warehouse"]').attr('checked', true);
    }else{
      $('#commodity_list-add-edit input[id="without_checking_warehouse"]').removeAttr("checked");
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
            $("select[id='parent_id']").html('');
            $("select[id='parent_id']").append(response.item_html).selectpicker('refresh');

            //flag_is_parent
            if(response.flag_is_parent == true){
              $(".parent_item_hide").addClass("hide"); 
            }else{
              $(".parent_item_hide").removeClass("hide"); 
            }


              init_selectpicker();
              $(".selectpicker").selectpicker('refresh');

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
        $("select[id='parent_id']").html('');
        $("select[id='parent_id']").append(response.item_html).selectpicker('refresh');

        //flag_is_parent
        $(".parent_item_hide").removeClass("hide");
        init_selectpicker(); 
        $(".selectpicker").selectpicker('refresh');

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

 $.post(admin_url + 'warehouse/caculator_profit_rate', data).done(function(response) {
  response = JSON.parse(response);

  $('#commodity_list-add-edit input[name="profif_ratio"]').val(response.profit_rate);

});

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
          var barcode = chars.join('');

          if($( "#commodity_list-add-edit" ).hasClass( "in" )){
            $('input[name="commodity_barcode"]').val('');
            $('input[name="commodity_barcode"]').focus().val(barcode);
          }else{
            $('#table-table_commodity_list_wrapper input[type="search"]').val('');
            $('#table-table_commodity_list_wrapper input[type="search"]').focus().val(barcode);
            $('#table-table_commodity_list_wrapper input[type="search"]').focusout();
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
$( "#parent_id" ).change(function() {
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

    });

});

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

    //add opening stock
    function add_opening_stock_modal(id, parent_id) {
      "use strict";

      $("#modal_wrapper").load("<?php echo admin_url('warehouse/warehouse/add_opening_stock_modal'); ?>", {
       slug: 'add',
       id:id,
       parent_id:parent_id,
     }, function() {

      $("body").find('#appointmentModal').modal({ show: true, backdrop: 'static' });

    });

      init_selectpicker();
      $(".selectpicker").selectpicker('refresh');
    }

</script>