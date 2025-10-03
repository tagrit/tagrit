<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$contact_role = $roles[0] ?? null;
$contact_role_id = isset($contact) ? $contact->contact_role_id : null;
?>

<?php if (empty($contact_role)) { ?>
<div class="clearfix tw-full tw-mt-8 tw-mb-4 tw-text-center">
    <p><?= _l(CONTACT_ROLE_MODULE . '_no_role_found'); ?></p>
    <a href="<?php echo admin_url(CONTACT_ROLE_MODULE . '/form'); ?>" target="_blank"
        class="btn btn-danger btn-sm"><?php echo _l('add_new', strtolower(_l(CONTACT_ROLE_MODULE))); ?>
    </a>
</div>
<?php return;
} ?>

<div id='contact_role'>
    <input name="contact_role_id" type="hidden" value="<?= isset($contact) ? $contact_role_id : ''; ?>" />

    <div class="form-group">
        <div class="tw-flex tw-justify-between">
            <label><?= _l(CONTACT_ROLE_MODULE); ?></label>
            <a href="javscrit:;" class="custom_role text-danger tw-font-medium"><?= _l('contact_role_customize'); ?> <i
                    class="fa fa-eye-slash tw-font-medium"></i></a>
        </div>
        <select name="contact_role_id" id="contact_role_id" class="form-control select2">
            <?php foreach ($roles as $role) {
                if (isset($contact) && $contact_role_id == $role->id) $contact_role = $role; ?>
            <option data-permissions='<?= $role->permissions; ?>'
                data-email_notifications='<?= $role->email_notifications; ?>' value="<?= $role->id; ?>"
                <?= $contact_role->id === $role->id ? 'selected' : ''; ?>>
                <?= $role->name; ?>
            </option>
            <?php } ?>
        </select>
    </div>

    <div class="customize_contact_role" style="display: none;">
        <?php
        $email_notifications = (array)json_decode($contact_role->email_notifications);
        $role_permissions = (array)json_decode($contact_role->permissions);
        if (!isset($contact))
            $adding_new_contact =  true;
        require('role_permissions.php');
        ?>
    </div>
    <div class="clearfix"></div>
</div>

<script>
$(".custom_role").on('click', function() {
    $('.customize_contact_role').toggle();
});
$("#contact_role_id").on('change', function() {

    let value = $(this).val();
    let option = $(`#contact_role_id option[value=${value}]`);
    let permissions = JSON.parse(option.attr('data-permissions'));
    let emailNotifications = JSON.parse(option.attr('data-email_notifications'));

    let permInputs = $('input[name="permissions[]"]');
    $.each(permInputs, function(i, input) {
        input = $(input);
        if (permissions.includes(input.val())) {
            input.prop('checked', true);
        } else {
            input.prop('checked', false);
        }
    });

    let emailInputs = $('#contact_email_notifications input');
    $.each(emailInputs, function(i, input) {
        input = $(input);
        if (Object.values(emailNotifications).includes(input.val())) {
            input.prop('checked', true);
        } else {
            input.prop('checked', false);
        }
    });
});

$('#contact-form #contact_email_notifications').not('#contact-form #contact_role #contact_email_notifications')
    .remove();
$('#contact-form input[name="permissions[]"]').not('#contact-form #contact_role input[name="permissions[]"]').closest(
    '.col-md-6.row').remove();
$("#contact-form p:contains('<?= _l('customer_permissions'); ?>')").not(
    '#contact-form #contact_role p:contains("<?= _l('customer_permissions'); ?>")').remove();
$("#contact-form p:contains('<?= _l('contact_permissions_info'); ?>')").not(
    '#contact-form #contact_role p:contains("<?= _l('contact_permissions_info'); ?>")').remove();
$("#contact-form p:contains('<?= _l('email_notifications'); ?><?= is_sms_trigger_active() ? '/SMS' : '' ?>'):not(#contact_role p)")
    .remove();
$(".clearfix + .clearfix + hr").not('#contact-form #contact_role .clearfix + .clearfix + hr').remove();
</script>