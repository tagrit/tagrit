<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">

        <div class="row">
            <!-- Column -->
            <div class="col-md-5">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-2">
                    <h4 class="tw-my-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                        <?php echo $title; ?>
                    </h4>
                    <?php if (isset($role)) { ?>
                    <a href="<?php echo admin_url(CONTACT_ROLE_MODULE . '/form'); ?>"
                        class="btn btn-success btn-sm"><?php echo _l('add_new', strtolower(_l(CONTACT_ROLE_MODULE))); ?>
                    </a>
                    <?php } ?>
                </div>
                <div class="panel_s">
                    <div class="panel-body d-block d-md-flex justify-content-between" style="gap: 30px;">
                        <?= form_open(admin_url(CONTACT_ROLE_MODULE . '/form/' . (isset($role) ? $role->id : '')), ['method' => 'POST', 'id' => 'contact-role-form', 'class' => 'align-self-center w-full']) ?>
                        <?= render_input('name', 'name', $role->name ?? ''); ?>

                        <hr />
                        <?php require('includes/role_permissions.php'); ?>
                        <div class="clearfix"></div>
                        <hr />
                        <div>
                            <button type="submit" class="btn btn-primary btn-block"
                                data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off"
                                data-form="#contact-form"><?php echo _l('submit'); ?></button>

                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <?php if (isset($role)) : ?>
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                    <?php echo _l(CONTACT_ROLE_MODULE . '_contact_which_are_using_role'); ?>
                </h4>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive p-0">
                                <table class="table dt-table" data-order-col="0" data-order-type="desc">

                                    <thead>
                                        <tr>
                                            <th><?= _l('contact'); ?></th>
                                            <th><?= _l('company'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($contacts as $key => $contact) : ?>
                                        <tr>
                                            <td>
                                                <a target="_blank"
                                                    href="<?= admin_url('clients/client/' . $contact->userid . '?group=contacts&auto_click_contact_id=' . $contact->id . '&client_id=' . $contact->userid) ?>"><?= $contact->firstname . ' ' . $contact->lastname ?></a>
                                            </td>
                                            <td>
                                                <a target="_blank"
                                                    href="<?= admin_url('clients/client/' . $contact->userid) ?>"><?= $contact->company; ?></a>
                                            </td>
                                        </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>


    </div>
</div>
<?php init_tail(); ?>

<?php if (!isset($role)) { ?>
<script>
$(function() {
    // Guess auto email notifications based on the default contact permissios
    var permInputs = $('input[name="permissions[]"]');
    $.each(permInputs, function(i, input) {
        input = $(input);
        if (input.prop('checked') === true) {
            $('#contact_email_notifications [data-perm-id="' + input.val() + '"]').prop('checked',
                true);
        }
    });
});
</script>
<?php } ?>