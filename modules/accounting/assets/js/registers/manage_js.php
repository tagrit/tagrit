<script type="text/javascript">
	var list_account_type_details, fnServerParams;
	(function($) {
		"use strict";

		appValidateForm($('#account-form'), {
			account_type_id: 'required',
			account_detail_type_id: 'required',
			name: 'required',
    	});

		fnServerParams = {
      "ft_type": '[name="ft_type"]',
      "ft_detail_type": '[name="ft_detail_type"]',
      "ft_parent_account": '[name="ft_parent_account"]',
      "ft_account": '[name="ft_account"]',
      "ft_active": '[name="ft_active"]',
      "from_date": '[name="from_date"]',
      "to_date": '[name="to_date"]',
    };
    $('select[name="ft_type"]').on('change', function() {
      init_account_table();
    });
    $('select[name="ft_active"]').on('change', function() {
      init_account_table();
    });
    $('select[name="ft_detail_type"]').on('change', function() {
      init_account_table();
    });

    $('select[name="ft_parent_account"]').on('change', function() {
      init_account_table();
    });
    
    $('select[name="ft_account"]').on('change', function() {
      init_account_table();
    });

    $('input[name="from_date"]').on('change', function() {
      init_account_table();
    });

    $('input[name="to_date"]').on('change', function() {
      init_account_table();
    });

	 	list_account_type_details = <?php echo json_encode($detail_types); ?>;

		  $('.add-new-account').on('click', function(){
          if($('select[name="account_type_id"]').val() <= 10 && $('select[name="account_type_id"]').val() != 1 && $('select[name="account_type_id"]').val() != 6){
            $('#div_balance').removeClass('hide');
          }else{
            $('#div_balance').addClass('hide');
          }

          $('#enable_subaccount_of').prop('checked', false);
          $('select[name="parent_account"]').attr('disabled',true);

          $('#account-modal').find('button[type="submit"]').prop('disabled', false);

          $('select[name="parent_account"]').val('').change();

          $('input[name="name"]').val('');
          $('input[name="balance"]').val('');
          $('input[name="balance_as_of"]').val('');

          tinyMCE.activeEditor.setContent('');
          $('textarea[name="description"]').val('');
          $('input[name="id"]').val('');
	        $('#account-modal').modal('show');
	    });

    var html = '';
      var note = 0;
        $.each(list_account_type_details, function( index, value ) {
          if(value.account_type_id == $('select[name="account_type_id"]').val()){
            if(note == 0){
              $('#detail_type_note').val(value.note);
              note = 1;
            }
            html += '<option value="'+value.id+'">'+value.name+'</option>';
          }
      });

      $('select[name="account_detail_type_id"]').html(html);
      $('select[name="account_detail_type_id"]').selectpicker('refresh');

      $.each(list_account_type_details, function( index, value ) {
          if(value.id == $('select[name="account_detail_type_id"]').val()){
            $('.detail_type_note').html(value.note);
          }
      });

	 	init_account_table();

		$('select[name="account_type_id"]').on('change', function() {

      if($(this).val() <= 10 && $(this).val() != 1 && $(this).val() != 6 && $('input[name="id"]').val() == ''){
        $('#div_balance').removeClass('hide');
      }else{
        $('#div_balance').addClass('hide');
      }

			var html = '';
			var note = 0;
		  	$.each(list_account_type_details, function( index, value ) {
		  		if(value.account_type_id == $('select[name="account_type_id"]').val()){
		  			if(note == 0){
			  			$('#detail_type_note').val(value.note);
			  			note = 1;
		  			}
			  		html += '<option value="'+value.id+'">'+value.name+'</option>';
		  		}
			});

			$('select[name="account_detail_type_id"]').html(html);
			$('select[name="account_detail_type_id"]').selectpicker('refresh');

      $.each(list_account_type_details, function( index, value ) {
          if(value.id == $('select[name="account_detail_type_id"]').val()){
            $('.detail_type_note').html(value.note);
          }
      });
		});

	  	$('select[name="account_detail_type_id"]').on('change', function() {
	  		$.each(list_account_type_details, function( index, value ) {
		  		if(value.id == $('select[name="account_detail_type_id"]').val()){
			  		$('.detail_type_note').html(value.note);
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

  $('input[name="mass_activate"]').on('change', function() {
    if($('#mass_activate').is(':checked') == true){
      $('#mass_delete').prop( "checked", false );
      $('#mass_deactivate').prop( "checked", false );
    }
  });

  $('input[name="mass_deactivate"]').on('change', function() {
    if($('#mass_deactivate').is(':checked') == true){
      $('#mass_delete').prop( "checked", false );
      $('#mass_activate').prop( "checked", false );
    }
  });

  $('input[name="mass_delete"]').on('change', function() {
    if($('#mass_delete').is(':checked') == true){
      $('#mass_activate').prop( "checked", false );
      $('#mass_deactivate').prop( "checked", false );
    }
  });

  $('#enable_subaccount_of').on('click', function(){
    
    if( $('#enable_subaccount_of').is(':checked')){
      $('select[name="parent_account"]').removeAttr('disabled');
      $('select[name="parent_account"]').selectpicker('refresh');
    }else{
      $('select[name="parent_account"]').attr('disabled',true);
      $('select[name="parent_account"]').selectpicker('refresh');
    }
  });
})(jQuery);

function acc_add_transaction(id) {
  "use strict";
      $('.account_id').html('');
      $('.account_id').html(hidden_input('account', id));

      $('select[name="account"]').val(id).change();
      $('#account-modal').modal('show');
  
}



function formatNumber(n) {
  "use strict";
  // format number 1000000 to 1,234,567
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}
function formatCurrency(input, blur) {
  "use strict";
  // appends $ to value, validates decimal side
  // and puts cursor back in right position.

  // get input value
  var input_val = input.val();

  // don't validate empty input
  if (input_val === "") { return; }

  // original length
  var original_len = input_val.length;

  // initial caret position
  var caret_pos = input.prop("selectionStart");

  // check for decimal
  if (input_val.indexOf(".") >= 0) {

    // get position of first decimal
    // this prevents multiple decimals from
    // being entered
    var decimal_pos = input_val.indexOf(".");
    var minus = input_val.substring(0, 1);
    if(minus != '-'){
      minus = '';
    }

    // split number by decimal point
    var left_side = input_val.substring(0, decimal_pos);
    var right_side = input_val.substring(decimal_pos);
    // add commas to left side of number
    left_side = formatNumber(left_side);

    // validate right side
    right_side = formatNumber(right_side);

    // Limit decimal to only 2 digits
    right_side = right_side.substring(0, 2);

    // join number by .
    input_val = minus+left_side + "." + right_side;

  } else {
    // no decimal entered
    // add commas to number
    // remove all non-digits
    var minus = input_val.substring(0, 1);
    if(minus != '-'){
      minus = '';
    }
    input_val = formatNumber(input_val);
    input_val = minus+input_val;

  }

  // send updated string to input
  input.val(input_val);

  // put caret back in the right position
  var updated_len = input_val.length;
  caret_pos = updated_len - original_len + caret_pos;
  //input[0].setSelectionRange(caret_pos, caret_pos);
}

function init_account_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-registers')) {
    $('.table-registers').DataTable().destroy();
  }
  initDataTable('.table-registers', admin_url + 'accounting/registers_table', [0], [0,1,2,3,4,5,6,7,8], fnServerParams, []);
  $('.dataTables_filter').addClass('hide');
  $('.table_select').selectpicker('refresh');

  //hide first column
  var hidden_columns = [6,7,8];
  $('.table-registers').DataTable().columns(hidden_columns).visible(false, false);
}

// General function for all datatables serverside
function initDataTable(selector, url, notsearchable, notsortable, fnserverparams, defaultorder) {
    var table = typeof (selector) == 'string' ? $("body").find('table' + selector) : selector;

    if (table.length === 0) {
        return false;
    }

    fnserverparams = (fnserverparams == 'undefined' || typeof (fnserverparams) == 'undefined') ? [] : fnserverparams;

    // If not order is passed order by the first column
    if (typeof (defaultorder) == 'undefined') {
        defaultorder = [
            [0, 'asc']
        ];
    } else {
        if (defaultorder.length === 1) {
            defaultorder = [defaultorder];
        }
    }

    var user_table_default_order = table.attr('data-default-order');

    if (!empty(user_table_default_order)) {
        var tmp_new_default_order = JSON.parse(user_table_default_order);
        var new_defaultorder = [];
        for (var i in tmp_new_default_order) {
            // If the order index do not exists will throw errors
            if (table.find('thead th:eq(' + tmp_new_default_order[i][0] + ')').length > 0) {
                new_defaultorder.push(tmp_new_default_order[i]);
            }
        }
        if (new_defaultorder.length > 0) {
            defaultorder = new_defaultorder;
        }
    }

    var length_options = [10, 25, 50, 100];
    var length_options_names = [10, 25, 50, 100];
    var tables_pagination_limit = 25;
    tables_pagination_limit = parseFloat(tables_pagination_limit);

    if ($.inArray(tables_pagination_limit, length_options) == -1 && tables_pagination_limit != -1) {
        length_options.push(tables_pagination_limit);
        length_options_names.push(tables_pagination_limit);
    }

    length_options.sort(function (a, b) {
        return a - b;
    });
    length_options_names.sort(function (a, b) {
        return a - b;
    });

    length_options.push(-1);
    length_options_names.push(app.lang.dt_length_menu_all);

    var dtSettings = {
        "language": app.lang.datatables,
        "processing": true,
        "retrieve": true,
        "serverSide": true,
        'paginate': true,
        'searchDelay': 750,
        "bDeferRender": true,
        "autoWidth": false,
        dom: "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i>><'row'<'#colvis'><'.dt-page-jump'>p>",
        "pageLength": tables_pagination_limit,
        "lengthMenu": [length_options, length_options_names],
        "columnDefs": [{
            "searchable": false,
            "targets": notsearchable,
        }, {
            "sortable": false,
            "targets": notsortable
        }],
        "fnDrawCallback": function (oSettings) {
            _table_jump_to_page(this, oSettings);
            if (oSettings.aoData.length === 0) {
                $(oSettings.nTableWrapper).addClass('app_dt_empty');
            } else {
                $(oSettings.nTableWrapper).removeClass('app_dt_empty');
            }
        },
        "fnCreatedRow": function (nRow, aData, iDataIndex) {
            // If tooltips found
            $(nRow).attr('data-title', aData.Data_Title);
            $(nRow).attr('data-toggle', aData.Data_Toggle);
        },
        "initComplete": function (settings, json) {
            var t = this;
            var $btnReload = $('.btn-dt-reload');
            $btnReload.attr('data-toggle', 'tooltip');
            $btnReload.attr('title', app.lang.dt_button_reload);

            var $btnColVis = $('.dt-column-visibility');
            $btnColVis.attr('data-toggle', 'tooltip');
            $btnColVis.attr('title', app.lang.dt_button_column_visibility);

            t.wrap('<div class="table-responsive"></div>');

            var dtEmpty = t.find('.dataTables_empty');
            if (dtEmpty.length) {
                dtEmpty.attr('colspan', t.find('thead th').length);
            }

            // Hide mass selection because causing issue on small devices
            if (is_mobile() && $(window).width() < 400 && t.find('tbody td:first-child input[type="checkbox"]').length > 0) {
                t.DataTable().column(0).visible(false, false).columns.adjust();
                $("a[data-target*='bulk_actions']").addClass('hide');
            }

            t.parents('.table-loading').removeClass('table-loading');
            t.removeClass('dt-table-loading');
            var th_last_child = t.find('thead th:last-child');
            var th_first_child = t.find('thead th:first-child');
            if (th_last_child.text().trim() == app.lang.options) {
                th_last_child.addClass('not-export');
            }
            if (th_first_child.find('input[type="checkbox"]').length > 0) {
                th_first_child.addClass('not-export');
            }
            mainWrapperHeightFix();
        },
        "order": defaultorder,
        "ajax": {
            "url": url,
            "type": "POST",
            "data": function (d) {
                if (typeof (csrfData) !== 'undefined') {
                    d[csrfData['token_name']] = csrfData['hash'];
                }
                for (var key in fnserverparams) {
                    d[key] = $(fnserverparams[key]).val();
                }
                if (table.attr('data-last-order-identifier')) {
                    d['last_order_identifier'] = table.attr('data-last-order-identifier');
                }
            }
        },
        buttons: get_datatable_buttons(table),
    };

    table = table.dataTable(dtSettings);
    var tableApi = table.DataTable();

    var hiddenHeadings = table.find('th.not_visible');
    var hiddenIndexes = [];

    $.each(hiddenHeadings, function () {
        hiddenIndexes.push(this.cellIndex);
    });

    setTimeout(function () {
        for (var i in hiddenIndexes) {
            tableApi.columns(hiddenIndexes[i]).visible(false, false).columns.adjust();
        }
    }, 10);

    if (table.hasClass('customizable-table')) {
        var tableToggleAbleHeadings = table.find('th.toggleable');
        var invisible = $('#hidden-columns-' + table.attr('id'));
        try {
            invisible = JSON.parse(invisible.text());
        } catch (err) {
            invisible = [];
        }

        $.each(tableToggleAbleHeadings, function () {
            var cID = $(this).attr('id');
            if ($.inArray(cID, invisible) > -1) {
                tableApi.column('#' + cID).visible(false);
            }
        });

        // For for not blurring out when clicked on the link
        // Causing issues hidden column still to be shown as not hidden because the link is focused
        /* $('body').on('click', '.buttons-columnVisibility a', function() {
             $(this).blur();
         });*/
        /*
                table.on('column-visibility.dt', function(e, settings, column, state) {
                    var hidden = [];
                    $.each(tableApi.columns()[0], function() {
                        var visible = tableApi.column($(this)).visible();
                        var columnHeader = $(tableApi.column($(this)).header());
                        if (columnHeader.hasClass('toggleable')) {
                            if (!visible) {
                                hidden.push(columnHeader.attr('id'))
                            }
                        }
                    });
                    var data = {};
                    data.id = table.attr('id');
                    data.hidden = hidden;
                    if (data.id) {
                        $.post(admin_url + 'staff/save_hidden_table_columns', data).fail(function(data) {
                            // Demo usage, prevent multiple alerts
                            if ($('body').find('.float-alert').length === 0) {
                                alert_float('danger', data.responseText);
                            }
                        });
                    } else {
                        console.error('Table that have ability to show/hide columns must have an ID');
                    }
                });*/
    }

    // Fix for hidden tables colspan not correct if the table is empty
    if (table.is(':hidden')) {
        table.find('.dataTables_empty').attr('colspan', table.find('thead th').length);
    }

    table.on('preXhr.dt', function (e, settings, data) {
        if (settings.jqXHR) settings.jqXHR.abort();
    });

    return tableApi;
}
</script>
