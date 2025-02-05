<script>
var signaturePad;

(function($) {
  "use strict";  
  //hansometable for purchase
<?php if(isset($goods_delivery_detail)){
 ?>
  var dataObject_pu = <?php echo new_html_entity_decode($goods_delivery_detail); ?>;
<?php }else{ ?>
  var dataObject_pu = [];
<?php } ?>

  var hotElement1 = document.querySelector('#hot_purchase');
   var purchase = new Handsontable(hotElement1, {
    licenseKey: 'non-commercial-and-evaluation',

    contextMenu: true,
    manualRowMove: true,
    manualColumnMove: true,
    stretchH: 'all',
    autoWrapRow: true,
    rowHeights: 30,
    defaultRowHeight: 100,
    minRows: 9,
    maxRows: 22,
    width: '100%',
    height: 330,

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

    colWidths: [110, 100,120,80, 100,80, 100,120,120,120,120,120,120,150],
    rowHeights: 30,
    rowHeaderWidth: [44],

    columns: [
                {
                  type: 'text',
                  data: 'commodity_code',
                  renderer: customDropdownRenderer,
                  editor: "chosen",
                  width: 150,
                  chosenOptions: {
                      data: <?php echo json_encode($commodity_code_name); ?>
                  }
                },
                 {
                  type: 'text',
                  data: 'warehouse_id',
                  renderer: customDropdownRenderer,
                  editor: "chosen",
                  width: 150,
                  chosenOptions: {
                      data: <?php echo json_encode($units_warehouse_name); ?>
                  }
                  // set desired format pattern and
                },
                 {
                  type: 'numeric',
                  data:'available_quantity',
                  numericFormat: {
                    pattern: '0,00',
                  },
                  readOnly: true
                },
                {
                  
                  type: 'text',
                  data: 'unit_id',
                  renderer: customDropdownRenderer,
                  editor: "chosen",
                  width: 150,
                  chosenOptions: {
                      data: <?php echo json_encode($units_code_name); ?>
                  },
                  readOnly: true

                },
                {
                  type: 'numeric',
                  data:'quantities',
                  numericFormat: {
                    pattern: '0,00',
                  }
                },
                {
                  type: 'numeric',
                  data: 'unit_price',
                  numericFormat: {
                    pattern: '0,00',
                  }
                      
                },
                {
                  data: 'tax_id',
                  renderer: customDropdownRenderer,
                  editor: "chosen",
                  width: 150,
                  chosenOptions: {
                      data: <?php echo json_encode($taxes); ?>
                  }
                },
                
                {
                  type: 'numeric',
                  data: 'total_money',
                  numericFormat: {
                    pattern: '0,00',
                  }
                      
                },
                
                {
                  data: 'discount',
                  type: 'numeric',
                  renderer: customRenderer
                },
                {
                  data: 'discount_money',
                  type: 'numeric',
                  numericFormat: {
                    pattern: '0,0'
                  },
                  readOnly: true
                },
                {
                  data: 'total_after_discount',
                  type: 'numeric',
                  numericFormat: {
                    pattern: '0,0'
                  },
                  readOnly: true
                },
                {
                  type: 'text',
                  data: 'guarantee_period',
                },
                {
                  type: 'text',
                  data: 'note',
                      
                },
                
               
                
              ],

          colHeaders: [
        "<?php echo _l('commodity_code'); ?>",
        "<?php echo _l('warehouse_name'); ?>",
        "<?php echo _l('available_quantity'); ?>",
        "<?php echo _l('unit_id'); ?>",
        "<?php echo _l('quantity'); ?>",
        "<?php echo _l('rate'); ?>",
        "<?php echo _l('tax_rate')._l(' %'); ?>",
        "<?php echo _l('subtotal'); ?>",
        "<?php echo _l('discount(%)').'(%)'; ?>",
        "<?php echo _l('discount(money)'); ?>",
        "<?php echo _l('total_money'); ?>",
        "<?php echo _l('guarantee_period'); ?>",
        "<?php echo _l('note'); ?>",

      ],
   
    data: dataObject_pu,

  });
   var purchase_value = purchase;


  purchase.addHook('afterChange', function(changes, src) {
      "use strict";
    changes.forEach(([row, col, prop, oldValue, newValue]) => {
      if(col == 'commodity_code' && oldValue != ''){
        
        $.post(admin_url + 'warehouse/commodity_code_change/'+oldValue ).done(function(response){
          response = JSON.parse(response);
            
            purchase.setDataAtCell(row,1, response.value.description);
            purchase.setDataAtCell(row,3, response.value.unit_name);
            purchase.setDataAtCell(row,4, '');
            purchase.setDataAtCell(row,5, response.value.rate);
            purchase.setDataAtCell(row,6, response.value.taxrate);
            purchase.setDataAtCell(row,7, '');
            purchase.setDataAtCell(row,8, '');
           
        });
      }
      if(col == 'commodity_code' && oldValue == ''){
          purchase.setDataAtCell(row,1,'');
          purchase.setDataAtCell(row,2,'');
          purchase.setDataAtCell(row,3,'');
          purchase.setDataAtCell(row,4,'');
          purchase.setDataAtCell(row,5,'');
          purchase.setDataAtCell(row,6,'');
          purchase.setDataAtCell(row,7,'');
          purchase.setDataAtCell(row,8,'');
      }
      if(col == 'quantity' && oldValue != ''){
            var total_tax_money =0;
            var total_goods_money =0;
            var value_of_inventory =0;
            var total_money =0;

          purchase.setDataAtCell(row,7,oldValue*purchase.getDataAtCell(row,5));
          purchase.setDataAtCell(row,8,oldValue*purchase.getDataAtCell(row,5)*(purchase.getDataAtCell(row,6)/100));

          for (var row_index = 0; row_index <= row; row_index++) {

            total_tax_money += (purchase.getDataAtCell(row_index, 4)*purchase.getDataAtCell(row_index, 5))*purchase.getDataAtCell(row_index, 6)/100;
            total_goods_money += purchase.getDataAtCell(row_index, 4)*purchase.getDataAtCell(row_index, 5);
            value_of_inventory += purchase.getDataAtCell(row_index, 4)*purchase.getDataAtCell(row_index, 5);

          }

            total_money = total_tax_money + total_goods_money;

            $('input[name="total_tax_money"]').val((total_tax_money).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
            $('input[name="total_goods_money"]').val((total_goods_money).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
            $('input[name="value_of_inventory"]').val((value_of_inventory).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
            $('input[name="total_money"]').val((total_money).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
      }

    });
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

function send_request_approve(id){
  "use strict";
    var data = {};
    data.rel_id = <?php echo new_html_entity_decode($goods_delivery->id); ?>;
    data.rel_type = '2';
    // data rel_type : 'stock_export';
    data.addedfrom = <?php echo new_html_entity_decode($goods_delivery->addedfrom); ?>;
  $("body").append('<div class="dt-loader"></div>');
    $.post(admin_url + 'warehouse/send_request_approve', data).done(function(response){
        response = JSON.parse(response);
        $("body").find('.dt-loader').remove();
        if (response.success === true || response.success == 'true') {
            alert_float('success', response.message);
            window.location.reload();
        }else{
          alert_float('warning', response.message);

        }
    });
}

    function accept_action() {
    "use strict";

      $('#add_action').modal('show');
    }

    (function($) {
    "use strict";

    var data_send_mail = {};
    <?php if(isset($send_mail_approve)){ 
      ?>
      data_send_mail = <?php echo json_encode($send_mail_approve); ?>;
      data_send_mail.rel_id = <?php echo new_html_entity_decode($goods_delivery->id); ?>;
      data_send_mail.rel_type = '2';
      // data_send_mail rel_type : '1' stock_export ;
      data_send_mail.addedfrom = <?php echo new_html_entity_decode($goods_delivery->addedfrom); ?>;

      $('.close_button').attr( "disabled", "disabled" );
      $.get(admin_url+'warehouse/send_mail', data_send_mail).done(function(response){
        response = JSON.parse(response);
        $('.close_button').removeAttr('disabled')

      }).fail(function(error) {

      });
    <?php } ?>

    SignaturePad.prototype.toDataURLAndRemoveBlanks = function() {
     var canvas = this._ctx.canvas;
       // First duplicate the canvas to not alter the original
       var croppedCanvas = document.createElement('canvas'),
       croppedCtx = croppedCanvas.getContext('2d');

       croppedCanvas.width = canvas.width;
       croppedCanvas.height = canvas.height;
       croppedCtx.drawImage(canvas, 0, 0);

       // Next do the actual cropping
       var w = croppedCanvas.width,
       h = croppedCanvas.height,
       pix = {
         x: [],
         y: []
       },
       imageData = croppedCtx.getImageData(0, 0, croppedCanvas.width, croppedCanvas.height),
       x, y, index;

       for (y = 0; y < h; y++) {
         for (x = 0; x < w; x++) {
           index = (y * w + x) * 4;
           if (imageData.data[index + 3] > 0) {
             pix.x.push(x);
             pix.y.push(y);

           }
         }
       }
       pix.x.sort(function(a, b) {
         return a - b
       });
       pix.y.sort(function(a, b) {
         return a - b
       });
       var n = pix.x.length - 1;

       w = pix.x[n] - pix.x[0];
       h = pix.y[n] - pix.y[0];
       var cut = croppedCtx.getImageData(pix.x[0], pix.y[0], w, h);

       croppedCanvas.width = w;
       croppedCanvas.height = h;
       croppedCtx.putImageData(cut, 0, 0);

       return croppedCanvas.toDataURL();
     };

    var canvas = document.getElementById("signature");
     signaturePad = new SignaturePad(canvas, {
      maxWidth: 2,
      onEnd:function(){
        signaturePadChanged();
      }
    });

    $('#identityConfirmationForm').submit(function() {
       signaturePadChanged();
     });

})(jQuery); 

     function signaturePadChanged() {
      "use strict";

       var input = document.getElementById('signatureInput');
       var $signatureLabel = $('#signatureLabel');
       $signatureLabel.removeClass('text-danger');

       if (signaturePad.isEmpty()) {
         $signatureLabel.addClass('text-danger');
         input.value = '';
         return false;
       }

       $('#signatureInput-error').remove();
       var partBase64 = signaturePad.toDataURLAndRemoveBlanks();
       partBase64 = partBase64.split(',')[1];
       input.value = partBase64;
     }


  
  function signature_clear(){
    "use strict";

    var canvas = document.getElementById("signature");
    var signaturePad = new SignaturePad(canvas, {
      maxWidth: 2,
      onEnd:function(){
        
      }
    });
    signaturePad.clear();
    $('input[name="signature"]').val('');
    
  }
  function sign_request(id){
    "use strict";
    var signature_val = $('input[name="signature"]').val();
    if(signature_val.length > 0){
      change_request_approval_status(id,1, true);
      $('.sign_request_class').prop('disabled', true);
      $('.sign_request_class').html('<?php echo _l('wait_text'); ?>');
      $('.clear').prop('disabled', true);
    }else{
      alert_float('warning', '<?php echo _l('please_sign_the_form'); ?>');
      $('.sign_request_class').prop('disabled', false);
      $('.clear').prop('disabled', false);
    }
  }

  function approve_request(id){
    "use strict";
    change_request_approval_status(id,1);
  }
  function deny_request(id){
    "use strict";
      change_request_approval_status(id,-1);
  }

  function change_request_approval_status(id, status, sign_code){
    "use strict";
      var data = {};
      data.rel_id = id;
      data.rel_type = '2';
      // data rel type = '2'; 2: stock_export
      data.approve = status;

    if(status == 1){
        $.post(admin_url + 'warehouse/check_approval_sign', data).done(function(response){
          response = JSON.parse(response); 
            if (response.success === true || response.success == 'true') {

                      if(sign_code == true){
                data.signature = $('input[name="signature"]').val();
              }else{
                data.note = $('textarea[name="reason"]').val();
              }
              $.post(admin_url + 'warehouse/approve_request/' + id, data).done(function(response){
                  response = JSON.parse(response); 
                  if (response.success === true || response.success == 'true') {
                      alert_float('success', response.message);
                      window.location.reload();
                  }
              });
            }else{
              //check approval false
              alert_float('success', response.message);
            }

        });

      }else{

        if(sign_code == true){
          data.signature = $('input[name="signature"]').val();
        }else{
          data.note = $('textarea[name="reason"]').val();
        }
        $.post(admin_url + 'warehouse/approve_request/' + id, data).done(function(response){
            response = JSON.parse(response); 
            if (response.success === true || response.success == 'true') {
                alert_float('success', response.message);
                window.location.reload();
            }
        });
      }

  }

  function customRenderer(instance, td, row, col, prop, value, cellProperties) {
  "use strict";
    Handsontable.renderers.TextRenderer.apply(this, arguments);
    if(td.innerHTML != ''){
      td.innerHTML = td.innerHTML + '%'
      td.className = 'htRight';
    }
  }

</script>