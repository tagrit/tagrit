<script>
(function($) {
  "use strict";
   $('li.sub-menu-item-accounting_checks').addClass('active');
})(jQuery);
    
 function print_sample_check() {

  var html_success = '<iframe id="content_print" class="w100" name="content_print"></iframe>';
  var ids = [];
  var data = {};
  data.count = 1;

  $.post(admin_url + 'accounting/print_sample_check', data).done(function(response){ 
    response = JSON.parse(response);
    if(navigator.userAgent.indexOf("Firefox") != -1 ){
        var mywindow = window.open('', 'Print check');
            mywindow.document.write(response.html);

            mywindow.document.close();
            mywindow.focus()
            mywindow.print();
            mywindow.close();
    }else{
        $('.content_cart').html(html_success);
        $("#content_print").contents().find('body').html(response.html);
        $("#content_print").contents().find('body').attr('style','text-align: center');
        $("#content_print").get(0).contentWindow.print();
    }
  });
}

function print_multiple_sample_check() {

  var html_success = '<iframe id="content_print" class="w100" name="content_print"></iframe>';
  var ids = [];
  var data = {};
  data.count = 6;

  $.post(admin_url + 'accounting/print_sample_check', data).done(function(response){ 
    response = JSON.parse(response); 
    if(navigator.userAgent.indexOf("Firefox") != -1 ){
        var mywindow = window.open('', 'Print check');
            mywindow.document.write(response.html);

            mywindow.document.close();
            mywindow.focus()
            mywindow.print();
            mywindow.close();
    }else{
        $('.content_cart').html(html_success);
        $("#content_print").contents().find('body').html(response.html);
        $("#content_print").contents().find('body').attr('style','text-align: center');
        $("#content_print").get(0).contentWindow.print();
    }
  });
}

</script>