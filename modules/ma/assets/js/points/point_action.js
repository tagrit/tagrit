(function($) {
	"use strict";

    appValidateForm($('#point-action-form'), 
    {
      name: 'required', 
      category: 'required', 
      change_points: 'required', 
    });

    $('input[name="add_points_by_country"]').on('change', function() {
    if($('#add_points_by_country').is(':checked') == true){
      $('#div_add_points_by_country').removeClass('hide');
    }else{
      $('#div_add_points_by_country').addClass('hide');
    }
  });

  var addMoreLadderInputKey = $('.list_ladder_setting #item_ladder_setting').length;
  $("body").on('click', '.new_item_ladder', function() {
    if ($(this).hasClass('disabled')) { return false; }

    addMoreLadderInputKey++;
    var newItem = $('.list_ladder_setting').find('#item_ladder_setting').eq(0).clone().appendTo('.list_ladder_setting');
    newItem.find('button[role="combobox"]').remove();
    newItem.find('select').selectpicker('refresh');

    newItem.find('label[for="country[0]"]').attr('for', 'country[' + addMoreLadderInputKey + ']');
    newItem.find('select[name="country[0]"]').attr('name', 'country[' + addMoreLadderInputKey + ']');
    newItem.find('select[id="country[0]"]').attr('id', 'country[' + addMoreLadderInputKey + ']').selectpicker('refresh');

    newItem.find('input[id="list_change_points[0]"]').attr('name', 'list_change_points[' + addMoreLadderInputKey + ']').val('');
    newItem.find('input[id="list_change_points[0]"]').attr('id', 'list_change_points[' + addMoreLadderInputKey + ']').val('');

    newItem.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
    newItem.find('button[name="add"]').removeClass('new_item_ladder').addClass('remove_item_ladder').removeClass('btn-success').addClass('btn-danger');
  });

  $("body").on('click', '.remove_item_ladder', function() {
      $(this).parents('#item_ladder_setting').remove();
  });
})(jQuery);