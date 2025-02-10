// Lead form validation
function validate_lead_form() {
    var validationObject = {
        name: 'required',
        phonenumber: 'required',
        source: 'required',
        status: {
            required: {
                depends: function (element) {
                    if ($('[lead-is-junk-or-lost]').length > 0) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        },
    };

    var messages = {};
    $.each(leadUniqueValidationFields, function (key, field) {
        validationObject[field] = {};

        if (field == 'email') {
            validationObject[field].email = true;
        }

        validationObject[field].remote = {
            url: admin_url + "leads/validate_unique_field",
            type: 'post',
            data: {
                field: field,
                lead_id: function () {
                    return $('#lead-modal').find('input[name="leadid"]').val();
                }
            }
        }

        if (typeof (app.lang[field + '_exists']) != 'undefined') {
            messages[field] = {
                remote: app.lang[field + '_exists']
            }
        }
    });

    appValidateForm($('#lead_form'), validationObject, lead_profile_form_handler, messages);
}