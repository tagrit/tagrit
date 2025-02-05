
var fnServerParams = {
     "bank_account": '[name="bank_account"]',
    };

var csrf, bankId; 

(function($) {
"use strict";
    init_banking_table();

    csrf = $('input[name="csrf_token"]').val();
    bankId = $('select[name=bank_account]').val();

    $('select').on('change', function() {

        var bank_id = this.value;

        let here = new URL(window.location.href);

        console.log(here);

        here.searchParams.append('id', bank_id);

        window.location.href = here

    });

})(jQuery);

function init_banking_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-banking')) {
   $('.table-banking').DataTable().destroy();
 }
 initDataTable('.table-banking', admin_url + 'accounting/import_banking_table', [], [], fnServerParams, [1, 'desc']);
}




(async function() {

const fetchLinkToken = async () => {

    const response = await fetch(admin_url + 'accounting/create_plaid_token');

    const responseJSON = await response.json();

    return responseJSON.link_token;
};



const configs = {

    // 1. Pass a new link_token to Link.

    token: await fetchLinkToken(),

    onSuccess: async function(public_token, metadata) {

    // 2a. Send the public_token to your app server.

    // The onSuccess function is called when the user has successfully

    // authenticated and selected an account to use.

    const successMsg = await fetch(admin_url+'accounting/update_plaid_bank_accounts?public_token='+ public_token + '&bankId='+ bankId, {

    });

    const successJSON = await successMsg.json();

    if(successJSON.error == ''){
        window.location.reload();
    }else{
        window.location.reload();
    }
    setTimeout(function() {
        location.reload();
    }, 2000);
    },

    onExit: async function(err, metadata) {

    // 2b. Gracefully handle the invalid link token error. A link token

    // can become invalidated if it expires, has already been used

    // for a link session, or is associated with too many invalid logins.

    if (err != null && err.error_code === 'INVALID_LINK_TOKEN') {

        linkHandler.destroy();

        linkHandler = Plaid.create({

        ...configs,

        token: await fetchLinkToken(),

        });

    }

    if (err != null) {

    // Handle any other types of errors.

    }

    alert_float('danger','Connection failed, please check your settings: Setting -> Plald environment');


    // metadata contains information about the institution that the

    // user selected and the most recent API request IDs.

    // Storing this information can be helpful for support.

    },

};

var linkHandler = Plaid.create(configs);

    document.getElementById('linkButton').onclick = function() {

    linkHandler.open();

};

})();



//submit form on click

function submitForm(){

  var from_date = $('#from_date').val();

  var bank_id = $('#bank_account').find(":selected").val();

  if(from_date == ''){
    alert_float('warning', 'Please choose a date!');

    return false;
  }

  $('#import_button').prop('disabled',true);



  $.ajax({

       url: admin_url + 'accounting/update_plaid_transaction',

       type: 'POST',

       data: {bank_id: bank_id, from_date : from_date},

       error: function() {

          alert('Something is wrong');

       },

       success: function(response) {

           window.location.reload();

       }

    });      

}

function updatePlaidStatus(){

    var bank_id = $('#bank_account').find(":selected").val();

      $('#delete_button').prop('disabled',true);

     $.ajax({

       url: admin_url + 'accounting/update_plaid_status',

       type: 'POST',

       data: {bank_id: bank_id},

       error: function() {

          alert('Something is wrong');

       },

       success: function(response) {

            window.location.reload();

       }

    });

}
