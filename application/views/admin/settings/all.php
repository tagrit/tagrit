<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?php if ($this->session->flashdata('debug')) { ?>
        <div class="alert alert-warning">
            <?= $this->session->flashdata('debug'); ?>
        </div>
        <?php } ?>
        <div class="row">
            <div class="col-xs-12 col-md-12 col-lg-10">
                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <h4 class="tw-font-bold tw-mt-0 tw-text-neutral-800">
                            <?= _l('settings'); ?>
                        </h4>
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="tw-flex tw-flex-col tw-gap-6">
                                    <?php foreach ($sections as $sectionId => $section) { ?>
                                    <div>
                                        <h4 class="tw-mt-0 tw-mb-4 tw-text-sm tw-text-neutral-500 tw-font-medium">
                                            <?= $section['title']; ?>
                                        </h4>
                                        <ul class="tw-space-y-2">
                                            <?php foreach ($section['children'] as $child) { ?>
                                            <li
                                                class="settings-group-<?= e($child['id']); ?>">
                                                <a href="<?= admin_url('settings?group=' . $child['id']); ?>"
                                                    class="tw-group tw-flex tw-items-center tw-text-sm hover:tw-text-neutral-800 focus:tw-text-neutral-800 tw-font-medium tw-gap-2.5 <?= ($group['id'] === $child['id']) ? ' tw-text-neutral-800' : 'tw-text-neutral-600' ?>">
                                                    <i
                                                        class="<?= $child['icon'] ?? 'fa-regular fa-circle-question'; ?> fa-fw fa-lg tw-mr-0.5 group-hover:tw-text-neutral-800 <?= $group['id'] === $child['id'] ? 'tw-text-neutral-800' : 'tw-text-neutral-500'; ?>"></i>
                                                    <span>
                                                        <?= $child['name']; ?>
                                                    </span>
                                                    <?php if (isset($child['badge'], $child['badge']['value']) && ! empty($child['badge'])) { ?>
                                                    <span
                                                        class="badge tw-ml-auto
        <?= isset($child['badge']['type']) && $child['badge']['type'] != '' ? "bg-{$child['badge']['type']}" : 'bg-info' ?>"
                                                        <?= (isset($child['badge']['type']) && $child['badge']['type'] == '') || isset($child['badge']['color']) ? "style='background-color: {$child['badge']['color']}'" : '' ?>>
                                                        <?= $child['badge']['value'] ?>
                                                    </span>
                                                    <?php } ?>
                                                </a>
                                            </li>
                                            <?php } ?>
                                            <?php if ($sectionId === 'general') { ?>
                                            <li class="settings-group-system-update">
                                                <a href="<?= admin_url('settings?group=update'); ?>"
                                                    class="tw-group tw-flex tw-items-center tw-text-sm hover:tw-text-neutral-800 focus:tw-text-neutral-800 tw-font-medium tw-gap-2.5 <?= ($group['id'] === 'update') ? ' tw-text-neutral-800' : 'tw-text-neutral-600' ?>">
                                                    <i
                                                        class="fa-solid fa-hammer fa-fw fa-lg tw-mr-0.5 group-hover:tw-text-neutral-800 <?= $group['id'] === 'update' ? 'tw-text-neutral-800' : 'tw-text-neutral-500'; ?>"></i>
                                                    <span><?= _l('settings_update'); ?></span>
                                                </a>
                                            </li>

                                            <?php if (is_admin()) { ?>
                                            <li class="settings-group-system-info">
                                                <a href="<?= admin_url('settings?group=info'); ?>"
                                                    class="tw-group tw-flex tw-items-center tw-text-sm hover:tw-text-neutral-800 focus:tw-text-neutral-800 tw-font-medium tw-gap-2.5 <?= ($group['id'] === 'info') ? ' tw-text-neutral-800' : 'tw-text-neutral-600' ?>">
                                                    <i
                                                        class="fa-solid fa-question fa-fw fa-lg tw-mr-0.5 group-hover:tw-text-neutral-800 <?= $group['id'] === 'update' ? 'tw-text-neutral-800' : 'tw-text-neutral-500'; ?>"></i>

                                                    <span>System/Server Info</span>
                                                </a>
                                            </li>
                                            <?php } ?>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <?php } ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-9">
                        <h4 class="tw-font-bold tw-mt-0 tw-text-neutral-800">
                            <?= $group['name']; ?>
                        </h4>
                        <?php
$actionUrl = $group['update_url']
                            ?? $this->uri->uri_string() . '?group=' . $group['id'] . ($this->input->get('tab') ? '&active_tab=' . $this->input->get('tab') : '');

$formAttributes = [
    'id'    => 'settings-form',
    'class' => isset($group['update_url']) ? 'custom-update-url' : '',
];

echo form_open_multipart($actionUrl, $formAttributes);
?>
                        <div class="panel_s">
                            <div class="panel-body">
                                <?php hooks()->do_action('before_settings_group_view', $group); ?>
                                <?php $this->load->view($group['view']) ?>
                                <?php hooks()->do_action('after_settings_group_view', $group); ?>
                            </div>
                            <?php if ($group['without_submit_button'] ?? false !== true) { ?>
                            <div class="panel-footer text-right">
                                <button type="submit" class="btn btn-primary">
                                    <?= _l('settings_save'); ?>
                                </button>
                            </div>
                            <?php } ?>
                        </div>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="new_version"></div>
<?php init_tail(); ?>
<script>
    $(function() {
        var settingsForm = $('#settings-form');
        var slug = "<?= e($group['id']); ?>";
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            if (settingsForm.hasClass('custom-update-url')) {
                return;
            }

            var tab = $(this).attr('href').slice(1);
            settingsForm.attr('action',
                '<?= site_url($this->uri->uri_string()); ?>?group=' +
                slug +
                '&active_tab=' + tab);
        });

        settingsForm.on('submit', function() {
            var emailProtocol = $('input[name="settings[email_protocol]"]:checked').val();
            if (emailProtocol === 'microsoft' || emailProtocol === 'google') {
                $('input[name="settings[smtp_password]"]').val('')
                $('input[name="settings[smtp_username]"]').val('')
            }
        });

        $('input[name="settings[mail_engine]"]').on('change', function() {
            if ($(this).val() == 'codeigniter') {
                $('.protocol-microsoft').addClass('hide');
                $('.protocol-google').addClass('hide');

                if ($('input[name="settings[email_protocol]"]:checked').val() == 'microsoft') {
                    $('#smtp').prop('checked', true)
                    $('#microsoft').trigger('change')
                }

                if ($('input[name="settings[email_protocol]"]:checked').val() == 'google') {
                    $('#smtp').prop('checked', true)
                    $('#google').trigger('change')
                }
            } else {
                $('.protocol-microsoft').removeClass('hide');
                $('.protocol-google').removeClass('hide');
            }
        });

        $('input[name="settings[email_protocol]"]').on('change', function() {
            var $inputHost = $('input[name="settings[smtp_host]"]');
            var $inputPort = $('input[name="settings[smtp_port]"]');
            var $selectEnc = $('select[name="settings[smtp_encryption]"]');

            var resetFields = function() {
                if ($selectEnc.hasClass('_modified')) {
                    $selectEnc.selectpicker('val', '');
                    $selectEnc.removeClass('_modified');
                }

                if ($inputPort.hasClass('_modified')) {
                    $inputPort.val('');
                    $inputPort.removeClass('_modified');
                }

                if ($inputHost.hasClass('_modified')) {
                    $inputHost.val('');
                    $inputHost.removeClass('_modified');
                }
            }

            if ($(this).val() == 'mail') {
                $('.xoauth-hide').addClass('hide');
                $('.smtp-fields').addClass('hide');
                $('.xoauth-microsoft-show').addClass('hide');
                $('.xoauth-google-show').addClass('hide');
                resetFields();
            } else if ($(this).val() === 'microsoft' || $(this).val() === 'google') {
                $('.smtp-fields').removeClass('hide');
                $('.xoauth-hide').addClass('hide');
                $('.xoauth-microsoft-show').addClass('hide');
                $('.xoauth-google-show').addClass('hide');

                if ($(this).val() === 'microsoft') {
                    $('.xoauth-microsoft-show').removeClass('hide');
                    if ($inputHost.val() == '') {
                        $inputHost.val('smtp.office365.com')
                        $inputHost.addClass('_modified');
                    }
                }

                if ($(this).val() === 'google') {
                    $('.xoauth-google-show').removeClass('hide');
                    if ($inputHost.val() == '') {
                        $inputHost.val('smtp.gmail.com')
                        $inputHost.addClass('_modified');
                    }
                }

                if ($inputPort.val() == '') {
                    $inputPort.val('587')
                    $inputPort.addClass('_modified');
                    if ($selectEnc.selectpicker('val') == '') {
                        $selectEnc.selectpicker('val', 'tls');
                        $selectEnc.addClass('_modified');
                    }
                }
            } else {
                $('.smtp-fields').removeClass('hide');
                $('.xoauth-hide').removeClass('hide');
                $('.xoauth-microsoft-show').addClass('hide');
                $('.xoauth-google-show').addClass('hide');
                resetFields();
            }
        });

        $('.sms_gateway_active input').on('change', function() {
            if ($(this).val() == '1') {
                $('body .sms_gateway_active').not($(this).parents('.sms_gateway_active')[0]).find(
                    'input[value="0"]').prop('checked', true);
            }
        });

        <?php if ($group['id'] == 'pusher') {
            if (get_option('desktop_notifications') == '1') { ?>
        // Let's check if the browser supports notifications
        if (!("Notification" in window)) {
            $('#pusherHelper').html(
                '<div class="alert alert-danger">Your browser does not support desktop notifications, please disable this option or use more modern browser.</div>'
            );
        } else if (Notification.permission == "denied") {
            $('#pusherHelper').html(
                '<div class="alert alert-danger">Desktop notifications not allowed in browser settings, search on Google "How to allow desktop notifications for <?= $this->agent->browser(); ?>"</div>'
            );
        }
        <?php } ?>
        <?php if (get_option('pusher_realtime_notifications') == '0') { ?>
        $('input[name="settings[desktop_notifications]"]').prop('disabled', true);
        <?php } ?>
        <?php } ?>

        $('input[name="settings[pusher_realtime_notifications]"]').on('change', function() {
            if ($(this).val() == '1') {
                $('input[name="settings[desktop_notifications]"]').prop('disabled', false);
            } else {
                $('input[name="settings[desktop_notifications]"]').prop('disabled', true);
                $('input[name="settings[desktop_notifications]"][value="0"]').prop('checked', true);
            }
        });

        $('.test_email').on('click', function() {
            var email = $('input[name="test_email"]').val();
            if (email != '') {
                $(this).attr('disabled', true);
                $.post(admin_url + 'emails/sent_smtp_test_email', {
                    test_email: email
                }).done(function(data) {
                    window.location.reload();
                });
            }
        });

        $('#update_app').on('click', function(e) {
            e.preventDefault();
            $('input[name="settings[purchase_key]"]').parents('.form-group').removeClass('has-error');
            var purchase_key = $('input[name="settings[purchase_key]"]').val();
            var latest_version = $('input[name="latest_version"]').val();
            var upgrade_function = $('input[name="upgrade_function"]:checked').val();
            var update_errors;
            if (purchase_key != '') {
                var ubtn = $(this);
                ubtn.html(
                    '<?= _l('wait_text'); ?>'
                );
                ubtn.addClass('disabled');
                $.post(admin_url + 'auto_update', {
                    purchase_key: purchase_key,
                    latest_version: latest_version,
                    auto_update: true,
                    upgrade_function: upgrade_function
                }).done(function() {
                    window.location.reload();
                }).fail(function(response) {
                    update_errors = JSON.parse(response.responseText);
                    $('#update_messages').html('<div class="alert alert-danger"></div>');
                    for (var i in update_errors) {
                        $('#update_messages .alert').append('<p>' + update_errors[i] + '</p>');
                    }
                    ubtn.removeClass('disabled');
                    ubtn.html($('.update_app_wrapper').data('original-text'));
                });
            } else {
                $('input[name="settings[purchase_key]"]').parents('.form-group').addClass('has-error');
            }
        });
    });

    $('input[name="settings[reminder_for_completed_but_not_billed_tasks]"]').on('change', function() {
        if ($(this).val() == '1') {
            $('.staff_notify_completed_but_not_billed_tasks_fields').removeClass('hide');
        } else {
            $('.staff_notify_completed_but_not_billed_tasks_fields').addClass('hide');
        }
    });
</script>
<?php hooks()->do_action('settings_group_end', $group); ?>
</body>

</html>