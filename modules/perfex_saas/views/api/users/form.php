<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <?= form_open(admin_url(PERFEX_SAAS_ROUTE_NAME . '/api/' . (isset($api_user->id) ? 'edit_user/' . $api_user->id : 'create_user'))); ?>
        <input type="hidden" name="id" value="<?= $api_user->id ?? '' ?>" />
        <div class="row">
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <?= render_input('name', _l('perfex_saas_api_key_name'), $api_user->name ?? '', 'text', ['max' => 50, 'min' => '2', 'required' => 'required']); ?>

                        <div class="tw-mt-4 tw-mb-4">
                            <hr />
                        </div>

                        <?php $key = 'perfex_saas_api_key'; ?>
                        <div class="form-group tw-mb-2">
                            <label><?= perfex_saas_input_label_with_hint($key); ?></label>
                            <div class="tw-flex">
                                <div class="input-group col-md-12 <?= $key; ?>">
                                    <input type="text" minlength="32" maxlength="150" autocorrect="off"
                                        autocomplete="off" class="form-control" name="token"
                                        value="<?= $api_user->token ?? ''; ?>" />
                                    <span class="input-group-addon tw-px-2 tw-border-l-0 copy-to-clipboard"
                                        data-toggle="tooltip" data-title="<?= _l('perfex_saas_copy_to_clipboard'); ?>"
                                        data-text="<?= $api_user->token ?? ''; ?>"
                                        data-success-text="<?= _l('perfex_saas_copied'); ?>">
                                        <a href="javascript:;"><i class="fa fa-copy"></i></a>
                                    </span>
                                    <span class="input-group-addon tw-px-2 tw-border-l-0 _delete api-key-generator"
                                        data-toggle="tooltip" data-title="<?= _l('perfex_saas_generate_api_key'); ?>">
                                        <a href="javascript:;"><i class="fa fa-refresh"></i></a>
                                    </span>

                                </div>
                            </div>
                        </div>

                        <div class="tw-mt-4 tw-mb-4">
                            <hr />
                        </div>

                        <p class="alert alert-warning mtop20"><?= _l('perfex_saas_api_key_warning'); ?></p>


                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <p> <?= _l('perfex_saas_api_key_permissions'); ?></p>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <?php
                                    foreach (perfex_saas_api_endpoints_specs() as $feature => $endpoint) {
                                        $summary = $endpoint['summary'];
                                    ?>
                                    <tr data-name="<?= $feature; ?>">
                                        <td>
                                            <?php
                                                foreach ($endpoint['methods'] as $http_verb => $path) {
                                                    $checked  = '';
                                                    if (isset($api_user) && isset($api_user->permissions->{$feature}->{$http_verb})) {
                                                        $checked = ' checked ';
                                                    }
                                                ?>
                                            <div class="form-group tw-flex tw-mb-0">
                                                <span>
                                                    <i class="fa fa-question-circle" data-toggle="tooltip"
                                                        data-title="<?= $feature; ?> - <?= $summary; ?>"></i>
                                                </span>

                                                <div class="checkbox tw-ml-5 tw-mb-0">
                                                    <input type="checkbox" <?= $checked; ?> class="capability"
                                                        id="<?= $feature . '_' . $http_verb; ?>"
                                                        name="permissions[<?= $feature; ?>][<?= $http_verb; ?>]"
                                                        value="1">
                                                    <label for="<?= $feature . '_' . $http_verb; ?>">
                                                        <?= strtoupper($http_verb); ?> <?= $path; ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <?php
                                                }
                                                ?>
                                        </td>
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary pull-right">
                    <?= _l('submit'); ?>
                </button>
            </div>
        </div>

        <?= form_close(); ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function generateApiKey(length = 64) {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let apiKey = '';
        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * characters.length);
            apiKey += characters[randomIndex];
        }
        return apiKey;
    }

    let $apiKeyWrapper = $(".perfex_saas_api_key");
    $(".api-key-generator").on('click', function() {
        let apiKey = generateApiKey();
        $apiKeyWrapper.find('input').val(apiKey)
        $apiKeyWrapper.find('.copy-to-clipboard').data('text', apiKey);
    });

    if (!$apiKeyWrapper.find('input').val().length) {
        $(".api-key-generator").trigger('click');
    }

})
</script>

<?php init_tail(); ?>