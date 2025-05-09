<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="customer-profile-group-heading">
    <?= _l('vault'); ?>
</h4>
<button class="btn btn-primary mbot15" data-toggle="modal" data-target="#entryModal">
    <i class="fa-regular fa-plus tw-mr-1"></i>
    <?= _l('new_vault_entry'); ?>
</button>
<?php if (count($vault_entries) == 0) { ?>
<p class="tw-text-neutral-500 tw-mt-1 tw-mb-0">
    <?= _l('no_vault_entries'); ?>
</p>
<?php } ?>
<?php foreach ($vault_entries as $entry) { ?>
<div
    class="tw-border tw-border-solid tw-border-neutral-200 tw-rounded-md tw-overflow-hidden tw-mb-3 last:tw-mb-0 panel-vault">
    <div class="tw-flex tw-justify-between tw-items-center tw-px-6 tw-py-3 tw-border-b tw-border-solid tw-border-neutral-200 tw-bg-neutral-50"
        id="<?= 'vaultEntryHeading-' . e($entry['id']); ?>">
        <h4 class="tw-font-semibold tw-my-0 tw-text-lg">
            <?= e($entry['server_address']); ?>
        </h4>
        <div class="tw-flex-inline tw-items-center tw-space-x-2">
            <?php if ($entry['creator'] == get_staff_user_id() || is_admin()) { ?>
            <a href="#"
                onclick="edit_vault_entry(<?= e($entry['id']); ?>); return false;"
                class="text-muted">
                <i class="fa-regular fa-pen-to-square"></i>
            </a>
            <a href="<?= admin_url('clients/vault_entry_delete/' . $entry['id']); ?>"
                class="text-muted _delete">
                <i class="fa-regular fa-trash-can"></i>
            </a>
            <?php } ?>
        </div>
    </div>
    <div id="<?= 'vaultEntry-' . $entry['id']; ?>"
        class="tw-p-6">
        <div class="row">
            <div class="col-md-6 border-right">
                <p class="tw-mb-1">
                    <b><?= _l('server_address'); ?>:
                    </b><?= e($entry['server_address']); ?>
                </p>
                <p class="tw-mb-1">
                    <b><?= _l('port'); ?>:
                    </b><?= e(! empty($entry['port']) ? e($entry['port']) : _l('no_port_provided')); ?>
                </p>
                <p class="tw-mb-1">
                    <b><?= _l('vault_username'); ?>:
                    </b><?= e($entry['username']); ?>
                </p>
                <p class="tw-mb-1">
                    <b><?= _l('vault_password'); ?>:
                    </b><span class="vault-password-fake">
                        <?= str_repeat('&bull;', 10); ?>
                    </span><span class="vault-password-encrypted"></span> <a href="#"
                        class="vault-view-password mleft10" data-toggle="tooltip"
                        data-title="<?= _l('view_password'); ?>"
                        onclick="vault_re_enter_password(<?= e($entry['id']); ?>,this); return false;"><i
                            class="fa fa-lock" aria-hidden="true"></i></a>
                </p>
            </div>
            <div class="col-md-6 text-center">
                <?php if (! empty($entry['description'])) { ?>
                <p>
                    <b><?= _l('vault_description'); ?>:
                    </b><br /><?= process_text_content_for_display($entry['description']); ?>
                </p>
                <hr />
                <?php } ?>
                <p class="text-muted">
                    <?= e(_l('vault_entry_created_from', $entry['creator_name'])); ?>
                    -
                    <span class="text-has-action" data-toggle="tooltip"
                        data-title="<?= e(_dt($entry['date_created'])); ?>">
                        <?= e(time_ago($entry['date_created'])); ?>
                    </span>
                </p>
                <p>
                    <?php if (! empty($entry['last_updated_from'])) { ?>
                <p class="text-muted no-mbot">
                    <?= _l('vault_entry_last_update', $entry['last_updated_from']); ?>
                    -
                    <span class="text-has-action" data-toggle="tooltip"
                        data-title="<?= e(_dt($entry['last_updated'])); ?>">
                        <?= e(time_ago($entry['last_updated'])); ?>
                </p>
                </span>
                <p>
                    <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<div class="modal fade" id="entryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?= form_open(admin_url('clients/vault_entry_create/' . $client->userid), ['data-create-url' => admin_url('clients/vault_entry_create/' . $client->userid), 'data-update-url' => admin_url('clients/vault_entry_update')]); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?= _l('vault_entry'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
                <input type="text" class="fake-autofill-field" name="fakeusernameremembered" value='' tabindex="-1" />
                <input type="password" class="fake-autofill-field" name="fakepasswordremembered" value=''
                    tabindex="-1" />
                <?= render_input('server_address', 'server_address'); ?>
                <?= render_input('port', 'port', '', 'number'); ?>
                <?= render_input('username', 'vault_username'); ?>
                <?= render_input('password', 'vault_password', '', 'password'); ?>
                <div id="vault_password_change_notice" class="help-block text-muted vault_password_change_notice hide">
                    <span
                        class="text-muted tw-text-sm"><?= _l('password_change_fill_notice'); ?></span>
                </div>
                <?= render_textarea('description', 'vault_description'); ?>
                <hr />
                <div class="radio radio-info">
                    <input type="radio" name="visibility" value="1" id="only_creator_visible_all" checked>
                    <label
                        for="only_creator_visible_all"><?= _l('vault_entry_visible_to_all'); ?></label>
                </div>
                <div class="radio radio-info">
                    <input type="radio" name="visibility" value="2" id="only_creator_visible_administrators">
                    <label
                        for="only_creator_visible_administrators"><?= _l('vault_entry_visible_administrators'); ?></label>
                </div>
                <div class="radio radio-info">
                    <input type="radio" name="visibility" value="3" id="only_creator_visible_me">
                    <label
                        for="only_creator_visible_me"><?= _l('vault_entry_visible_creator'); ?></label>
                </div>
                <hr />
                <div class="checkbox checkbox-info">
                    <input type="checkbox" id="share_in_projects" name="share_in_projects">
                    <label
                        for="share_in_projects"><?= _l('vault_entry_share_on_projects'); ?></label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="submit"
                    class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
        <?= form_close(); ?>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php $this->load->view('admin/clients/vault_confirm_password'); ?>
<?php hooks()->add_action('app_admin_footer', 'vault_form_validate');
function vault_form_validate()
{ ?>
<script>
    var $entryModal = $('#entryModal');
    $(function() {

        appValidateForm($entryModal.find('form'), {
            server_address: 'required',
            username: 'required',
            password: 'required',
        });
        setTimeout(function() {
            $($entryModal.find('form')).trigger('reinitialize.areYouSure');
        }, 1000)
        $entryModal.on('hidden.bs.modal', function() {
            var $form = $entryModal.find('form');
            $form.attr('action', $form.data('create-url'));
            $form.find('input[type="text"]').val('');
            $form.find('input[type="radio"]:first').prop('checked', true);
            $form.find('textarea').val('');
            $('#vault_password_change_notice').addClass('hide');
            $form.find('#password').rules('add', {
                required: true
            });
            $form.find('#password').parents().find('.req').removeClass('hide');
            $form.find('#share_in_projects').prop('checked', false);
        });
    });

    function edit_vault_entry(id) {
        $.get(admin_url + 'clients/get_vault_entry/' + id, function(response) {
            var $form = $entryModal.find('form');
            $form.attr('action', $form.data('update-url') + '/' + id);
            $form.find('#server_address').val(response.server_address);
            $form.find('#port').val(response.port);
            $form.find('#username').val(response.username);
            $form.find('#description').val(response.description);
            $form.find('#password').rules('remove');
            $form.find('#password').parents().find('.req').addClass('hide');
            $form.find('input[value="' + response.visibility + '"]').prop('checked', true);
            $form.find('#share_in_projects').prop('checked', (response.share_in_projects == 1 ? true : false));
            $('#vault_password_change_notice').removeClass('hide');
            $entryModal.modal('show');
        }, 'json');
    }
</script>
<?php } ?>