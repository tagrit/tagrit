<script>
(function($) {
  "use strict";
   validate_debit_note_form();
       // Init accountacy currency symbol
    init_db_currency();

    <?php if(get_purchase_option('item_by_vendor') != 1){ ?>
      init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'purchase/pur_commodity_code_search');
    <?php } ?>

    $("body").on('change', 'select[name="item_select"]', function () {
      var itemid = $(this).selectpicker('val');
      if (itemid != '') {
        pur_add_item_to_preview(itemid);
      }
    });

    $('select[name="vendorid"]').on('change', function(){
    	var vendor = $(this).val();
    	if(vendor != '' && vendor != null && vendor != undefined){
	    	requestGetJSON('purchase/vendor_change_data/' + vendor ).done(function (response) {

	            for (var f in billingAndShippingFields) {
	                if (billingAndShippingFields[f].indexOf('billing') > -1) {
	                    if (billingAndShippingFields[f].indexOf('country') > -1) {
	                        $('select[name="' + billingAndShippingFields[f] + '"]').selectpicker('val', response['billing_shipping'][0][billingAndShippingFields[f]]);
	                    } else {
	                        if (billingAndShippingFields[f].indexOf('billing_street') > -1) {
	                            $('textarea[name="' + billingAndShippingFields[f] + '"]').val(response['billing_shipping'][0][billingAndShippingFields[f]]);
	                        } else {
	                            $('input[name="' + billingAndShippingFields[f] + '"]').val(response['billing_shipping'][0][billingAndShippingFields[f]]);
	                        }
	                    }
	                }
	            }

	            if (!empty(response['billing_shipping'][0]['shipping_street'])) {
	                $('input[name="include_shipping"]').prop("checked", true).change();
	            }

	            for (var fsd in billingAndShippingFields) {
	                if (billingAndShippingFields[fsd].indexOf('shipping') > -1) {
	                    if (billingAndShippingFields[fsd].indexOf('country') > -1) {
	                        $('select[name="' + billingAndShippingFields[fsd] + '"]').selectpicker('val', response['billing_shipping'][0][billingAndShippingFields[fsd]]);
	                    } else {
	                        if (billingAndShippingFields[fsd].indexOf('shipping_street') > -1) {
	                            $('textarea[name="' + billingAndShippingFields[fsd] + '"]').val(response['billing_shipping'][0][billingAndShippingFields[fsd]]);
	                        } else {
	                            $('input[name="' + billingAndShippingFields[fsd] + '"]').val(response['billing_shipping'][0][billingAndShippingFields[fsd]]);
	                        }
	                    }
	                }
	            }

	            init_billing_and_shipping_details();

	            var vendor_currency = response['vendor_currency'];
	            var s_currency = $("body").find('.accounting-template select[name="currency"]');
	            vendor_currency = parseInt(vendor_currency);
	            vendor_currency != 0 ? s_currency.val(vendor_currency) : s_currency.val(s_currency.data('base'));
	           
	            s_currency.selectpicker('refresh');

	            <?php if(get_purchase_option('item_by_vendor') == 1){ ?>
			        if(response.option_html != ''){
			         $('#item_select').html(response.option_html);
			         $('.selectpicker').selectpicker('refresh');
			        }else if(response.option_html == ''){
			          init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'purchase/pur_commodity_code_search/purchase_price/can_be_purchased/'+invoker.value);
			        }
			        
			    <?php } ?>


	            init_currency();
	        });
	    }
    });
})(jQuery);

// Add item to preview
function pur_add_item_to_preview(id) {
  requestGetJSON("purchase/get_item_by_id/" + id).done(function (
    response
  ) {
    clear_item_preview_values();

    $('.main textarea[name="description"]').val(response.code_description);
    $('.main textarea[name="long_description"]').val(
      response.long_description.replace(/(<|&lt;)br\s*\/*(>|&gt;)/g, " ")
    );

    //_set_item_preview_custom_fields_array(response.custom_fields);

    $('.main input[name="quantity"]').val(1);

    var taxSelectedArray = [];
    if (response.taxname && response.taxrate) {
      taxSelectedArray.push(response.taxname + "|" + response.taxrate);
    }
    if (response.taxname_2 && response.taxrate_2) {
      taxSelectedArray.push(response.taxname_2 + "|" + response.taxrate_2);
    }

    $(".main select.tax").selectpicker("val", taxSelectedArray);
    $('.main input[name="unit"]').val(response.unit);

    var $currency = $("body").find(
      '.accounting-template select[name="currency"]'
    );
    var baseCurency = $currency.attr("data-base");
    var selectedCurrency = $currency.find("option:selected").val();
    var $rateInputPreview = $('.main input[name="rate"]');

    if (baseCurency == selectedCurrency) {
      $rateInputPreview.val(response.purchase_price);
    } else {
      var itemCurrencyRate = response["rate_currency_" + selectedCurrency];
      if (!itemCurrencyRate || parseFloat(itemCurrencyRate) === 0) {
        $rateInputPreview.val(response.purchase_price);
      } else {
        $rateInputPreview.val(itemCurrencyRate);
      }
    }

    $(document).trigger({
      type: "item-added-to-preview",
      item: response,
      item_type: "item",
    });
  });
}

function validate_debit_note_form(selector) {
	"use strict";
    selector = typeof (selector) == 'undefined' ? '#debit-note-form' : selector;

    appValidateForm($(selector), {
        vendorid: 'required',
        date: 'required',
        currency: 'required',
        number: {
            required: true,
        }
    });

    $("body").find('input[name="number"]').rules('add', {
        remote: {
            url: admin_url + "purchase/validate_debit_note_number",
            type: 'post',
            data: {
                number: function () {
                    return $('input[name="number"]').val();
                },
                isedit: function () {
                    return $('input[name="number"]').data('isedit');
                },
                original_number: function () {
                    return $('input[name="number"]').data('original-number');
                },
                date: function () {
                    return $(".debit_note input[name='date']").val();
                },
            }
        },
        messages: {
            remote: app.lang.debit_note_number_exists,
        }
    });
}

function init_db_currency(id, callback) {
  var $accountingTemplate = $("body").find(".accounting-template");

  if ($accountingTemplate.length || id) {
    var selectedCurrencyId = !id
      ? $accountingTemplate.find('select[name="currency"]').val()
      : id;

    requestGetJSON("misc/get_currency/" + selectedCurrencyId).done(function (
      currency
    ) {
      // Used for formatting money
      accounting.settings.currency.decimal = currency.decimal_separator;
      accounting.settings.currency.thousand = currency.thousand_separator;
      accounting.settings.currency.symbol = currency.symbol;
      accounting.settings.currency.format =
        currency.placement == "after" ? "%v %s" : "%s%v";
      calculate_total();

      if (callback) {
        callback();
      }
    });
  }
}

</script>