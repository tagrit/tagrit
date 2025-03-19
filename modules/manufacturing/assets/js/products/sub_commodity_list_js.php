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
           table_commodity_list.DataTable().ajax.reload(null, false)
           .columns.adjust()
           .responsive.recalc();

         }else{

          expenseDropzone.processQueue();

        }

      },

    }));
    }


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
    var _table_api = initDataTable(table_commodity_list, admin_url+'manufacturing/table_commodity_list', [0], [0], ProposalServerParams,  [1, 'desc']);
    $.each(ProposalServerParams, function(i, obj) {
      $('select' + obj).on('change', function() {  
        table_commodity_list.DataTable().ajax.reload()
        .columns.adjust()
        .responsive.recalc();
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



  $("body").on('click', '.tagit-close', function() {
    "use strict";

    var tag_id = $(this).parents('li').val();
    /*delete tag id*/
    if(tag_id){
      $.post(admin_url + 'warehouse/delete_item_tags/'+tag_id).done(function(response) {
        response = JSON.parse(response);

        if(response.status == 'true'){                  
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

</script>