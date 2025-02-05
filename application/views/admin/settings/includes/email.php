<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="horizontal-scrollable-tabs panel-full-width-tabs">
    <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
    <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
    <div class="horizontal-tabs">
        <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
            <li role="presentation" class="active">
                <a href="#email_config" aria-controls="email_config" role="tab"
                    data-toggle="tab"><?= _l('settings_smtp_settings_heading'); ?></a>
            </li>
            <li role="presentation">
                <a href="#email_queue" aria-controls="email_queue" role="tab"
                    data-toggle="tab"><?= _l('email_queue'); ?></a>
            </li>
        </ul>
    </div>
</div>
<div class="tab-content mtop15">
    <div role="tabpanel" class="tab-pane active" id="email_config">
        <?php
        if (! empty(get_option('smtp_email'))) {
            if (get_option('email_protocol') !== 'google' && preg_match('/gmail.com/', get_option('smtp_email'))) {
                ?>
        <div class="alert alert-warning">
            <p class="bold">
                Starting from May 30, 2022, Google will no longer support sign in to your Google Account using your
                email/username and account password.
            </p>
            <p>
                If you are using your Google Account password to connect to SMTP, it's highly recommended to <span
                    class="bold">update your password with an App Password</span> or use <span class="bold">Google
                    OAuth2</span> to avoid any email sending
                disruptions, find more information on how to generate App Password for your Google Account at the
                following link: <a href="https://support.google.com/accounts/answer/185833?hl=en"
                    class="alert-link">https://support.google.com/accounts/answer/185833?hl=en</a>
            </p>
        </div>
        <?php
            }
        }
?>
        <!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
        <input type="text" class="fake-autofill-field" name="fakeusernameremembered" value='' tabindex="-1" />
        <input type="password" class="fake-autofill-field" name="fakepasswordremembered" value='' tabindex="-1" />
        <h4 style="margin-top:-20px;" class="tw-font-semibold">
            <?= _l('settings_smtp_settings_heading'); ?>
            <small
                class="text-muted"><?= _l('settings_smtp_settings_subheading'); ?></small>
        </h4>
        <hr />
        <div class="form-group">

            <label
                for="mail_engine"><?= _l('mail_engine'); ?></label><br />
            <div class="radio radio-inline radio-primary">
                <input type="radio" name="settings[mail_engine]" id="phpmailer" value="phpmailer"
                    <?= get_option('mail_engine') == 'phpmailer' ? 'checked' : ''; ?>>
                <label for="phpmailer">PHPMailer</label>
            </div>

            <div class="radio radio-inline radio-primary">
                <input type="radio" name="settings[mail_engine]" id="codeigniter" value="codeigniter"
                    <?= get_option('mail_engine') == 'codeigniter' ? 'checked' : ''; ?>>
                <label for="codeigniter">CodeIgniter</label>
            </div>
            <hr />
            <?php if (get_option('email_protocol') == 'mail') { ?>
            <div class="alert alert-warning">
                The "mail" protocol is not the recommended protocol to send emails, you should strongly consider
                configuring the "SMTP" protocol to avoid any distruptions and delivery issues.
            </div>
            <?php } ?>
            <label
                for="email_protocol"><?= _l('email_protocol'); ?></label><br />
            <div class="radio radio-inline radio-primary">
                <input type="radio" name="settings[email_protocol]" id="smtp" value="smtp"
                    <?= get_option('email_protocol') == 'smtp' ? 'checked' : ''; ?>>
                <label for="smtp">SMTP</label>
            </div>

            <div
                class="radio radio-inline radio-primary protocol-microsoft<?= get_option('mail_engine') === 'codeigniter' ? ' hide' : ''; ?>">
                <input type="radio" name="settings[email_protocol]" id="microsoft" value="microsoft"
                    <?= get_option('email_protocol') == 'microsoft' ? 'checked' : ''; ?>>
                <label for="microsoft">Microsoft OAuth 2.0</label>
            </div>

            <div
                class="radio radio-inline radio-primary protocol-google<?= get_option('mail_engine') === 'codeigniter' ? ' hide' : ''; ?>">
                <input type="radio" name="settings[email_protocol]" id="google" value="google"
                    <?= get_option('email_protocol') == 'google' ? 'checked' : ''; ?>>
                <label for="google">Gmail OAuth 2.0</label>
            </div>

            <div class="radio radio-inline radio-primary">
                <input type="radio" name="settings[email_protocol]" id="sendmail" value="sendmail"
                    <?= get_option('email_protocol') == 'sendmail' ? 'checked' : ''; ?>>
                <label for="sendmail">Sendmail</label>
            </div>

            <div class="radio radio-inline radio-primary">
                <input type="radio" name="settings[email_protocol]" id="mail" value="mail"
                    <?= get_option('email_protocol') == 'mail' ? 'checked' : ''; ?>>
                <label for="mail">Mail</label>
            </div>
        </div>
        <div
            class="smtp-fields<?= get_option('email_protocol') == 'mail' ? ' hide' : ''; ?>">
            <div
                class="tw-my-8 tw-border tw-border-solid tw-rounded-md tw-border-neutral-200 tw-px-3 tw-py-4 xoauth-microsoft-show<?= get_option('email_protocol') !== 'microsoft' ? ' hide' : ''; ?>">
                <p>
                    These details are obtained by setting up an app in your Microsoft Azure <a
                        href="https://portal.azure.com/#view/Microsoft_AAD_RegisteredApps/ApplicationsListBlade"
                        target="_blank">developer portal</a>.
                </p>
                <p>
                    <span class="tw-font-semibold">Redirect URL:</span>
                    <span
                        class="tw-select-all"><?= admin_url('smtp_oauth_microsoft/token'); ?></span>
                </p>
                <div class="form-group">
                    <label for="ClientId">Client Id</label>
                    <input type="text" class="form-control" id="ClientId" name="settings[microsoft_mail_client_id]"
                        value="<?= get_option('microsoft_mail_client_id'); ?>" />
                </div>
                <div class="form-group">
                    <label for="clientSecret">Client Secret</label>
                    <input type="password" class="form-control" id="clientSecret"
                        name="settings[microsoft_mail_client_secret]"
                        value="<?= $this->encryption->decrypt(get_option('microsoft_mail_client_secret')); ?>" />
                </div>
                <div class="form-group">
                    <label for="tenantId">Tenant ID (only relevant for Azure)</label>
                    <input type="text" class="form-control" id="tenantId"
                        name="settings[microsoft_mail_azure_tenant_id]"
                        value="<?= get_option('microsoft_mail_azure_tenant_id'); ?>" />
                </div>
                <?php if (! empty(get_option('microsoft_mail_client_id')) && ! empty(get_option('microsoft_mail_client_secret'))) { ?>
                <a href="<?= admin_url('smtp_oauth_microsoft/token'); ?>"
                    class="btn btn-primary">
                    Authenticate
                </a>
                <?php } else { ?>
                <div class="alert alert-warning">
                    To authenticate, first add Client Id and Client Secret and save settings.
                </div>
                <?php } ?>
                </form>
            </div>

            <div
                class="tw-my-8 tw-border tw-border-solid tw-rounded-md tw-border-neutral-200 tw-px-3 tw-py-4 xoauth-google-show<?= get_option('email_protocol') !== 'google' ? ' hide' : ''; ?>">
                <p>
                    These details are obtained by setting up a project in your <a
                        href="https://console.developers.google.com/" target="_blank">Google API Console</a>.
                </p>
                <p>
                    <span class="tw-font-semibold">Redirect URL:</span>
                    <span
                        class="tw-select-all"><?= admin_url('smtp_oauth_google/token'); ?></span>
                </p>
                <div class="form-group">
                    <label for="ClientId">Client Id</label>
                    <input type="text" class="form-control" id="ClientId" name="settings[google_mail_client_id]"
                        value="<?= get_option('google_mail_client_id'); ?>" />
                </div>
                <div class="form-group">
                    <label for="clientSecret">Client Secret</label>
                    <input type="password" class="form-control" id="clientSecret"
                        name="settings[google_mail_client_secret]"
                        value="<?= $this->encryption->decrypt(get_option('google_mail_client_secret')); ?>" />
                </div>
                <?php if (! empty(get_option('google_mail_client_id')) && ! empty(get_option('google_mail_client_secret'))) { ?>
                <a href="<?= admin_url('smtp_oauth_google/token'); ?>"
                    class="btn btn-primary">
                    Authenticate
                </a>
                <?php } else { ?>
                <div class="alert alert-warning">
                    To authenticate, first add Client Id and Client Secret and save settings.
                </div>
                <?php } ?>
                </form>
            </div>

            <div class="form-group mtop15">
                <label
                    for="smtp_encryption"><?= _l('smtp_encryption'); ?></label><br />
                <select name="settings[smtp_encryption]" class="selectpicker" data-width="100%">
                    <option value="" <?= get_option('smtp_encryption') == '' ? 'selected' : '' ?>>
                        <?= _l('smtp_encryption_none'); ?>
                    </option>
                    <option value="ssl" <?= get_option('smtp_encryption') == 'ssl' ? 'selected' : '' ?>>SSL
                    </option>
                    <option value="tls" <?= get_option('smtp_encryption') == 'tls' ? 'selected' : '' ?>>TLS
                    </option>

                </select>
            </div>
            <?= render_input('settings[smtp_host]', 'settings_email_host', get_option('smtp_host')); ?>
            <?= render_input('settings[smtp_port]', 'settings_email_port', get_option('smtp_port')); ?>
        </div>
        <?= render_input(
            'settings[smtp_email]',
            'settings_email',
            get_option('smtp_email'),
            'text',
            [],
            [],
            empty(get_option('smtp_email')) && in_array(get_option('email_protocol'), ['microsoft', 'google']) ? 'has-error' : ''
        ); ?>
        <div class="xoauth-hide smtp-fields<?php if (in_array(get_option('email_protocol'), ['mail', 'microsoft', 'google'])) {
            echo ' hide';
        } ?>">
            <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                data-title="<?= _l('smtp_username_help'); ?>"></i>
            <?= render_input('settings[smtp_username]', 'smtp_username', get_option('smtp_username')); ?>
            <?php
            $ps = get_option('smtp_password');
if (! empty($ps)) {
    if ($this->encryption->decrypt($ps) == false) {
        $ps = $ps;
    } else {
        $ps = $this->encryption->decrypt($ps);
    }
}
echo render_input('settings[smtp_password]', 'settings_email_password', $ps, 'password', ['autocomplete' => 'off']); ?>
        </div>
        <?= render_input('settings[smtp_email_charset]', 'settings_email_charset', get_option('smtp_email_charset')); ?>
        <?= render_input('settings[bcc_emails]', 'bcc_all_emails', get_option('bcc_emails')); ?>
        <?= render_textarea('settings[email_signature]', 'settings_email_signature', get_option('email_signature'), ['data-entities-encode' => 'true']); ?>
        <hr />
        <?= render_textarea('settings[email_header]', 'email_header', get_option('email_header'), ['rows' => 15, 'data-entities-encode' => 'true']); ?>
        <?= render_textarea('settings[email_footer]', 'email_footer', get_option('email_footer'), ['rows' => 15, 'data-entities-encode' => 'true']); ?>
        <hr />
        <h4><?= _l('settings_send_test_email_heading'); ?>
        </h4>
        <p class="text-muted">
            <?= _l('settings_send_test_email_subheading'); ?>
        </p>
        <div class="form-group">
            <div class="input-group">
                <input type="email" class="form-control" name="test_email" data-ays-ignore="true"
                    placeholder="<?= _l('settings_send_test_email_string'); ?>">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-info test_email">Test</button>
                </div>
            </div>
        </div>
    </div>

    <div role="tabpanel" class="tab-pane" id="email_queue">
        <?php if (get_option('cron_has_run_from_cli') != '1') { ?>
        <div class="alert alert-danger">
            This feature requires a properly configured cron job. Before activating the feature, make sure that the <a
                href="<?= admin_url('settings?group=cronjob'); ?>">cron
                job</a> is configured as explanation in
            the documentation.
        </div>
        <?php } ?>
        <?php render_yes_no_option('email_queue_enabled', 'email_queue_enabled', 'To speed up the emailing process, the system will add the emails in queue and will send them via cron job, make sure that the cron job is properly configured in order to use this feature.'); ?>
        <hr />
        <?php render_yes_no_option('email_queue_skip_with_attachments', 'email_queue_skip_attachments', 'Most likely you will encounter problems with the email queue if the system needs to add big files to the queue. If you plan to use this option consult with your server administrator/hosting provider to increase the max_allowed_packet and wait_timeout options in your server config, otherwise when this option is set to yes the system won\'t add emails with attachments in the queue and will be sent immediately.'); ?>
        <?php
        $queueEmails = $this->email->get_queue_emails();
?>
        <hr />
        <h4 class="mbot15">
            <?= _l('email_queue'); ?>
        </h4>

        <table class="table dt-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>To</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($queueEmails as $email) {
                    $headers = unserialize($email->headers); ?>
                <tr>
                    <td><?= e($headers['subject']); ?>
                    </td>
                    <td><?= e($email->email); ?></td>
                    <td><?= e($email->status); ?></td>
                    <td>
                        <a href="<?= admin_url('emails/delete_queued_email/' . $email->id); ?>"
                            class="text-danger">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>