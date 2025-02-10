<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('authentication/includes/head.php'); ?>

<?php
echo '<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />';

?>

<style>
    .select2-container .select2-selection--single {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        color: #111827;
        font-size: 0.875rem;
        border-radius: 0.375rem;
        padding: 5px 10px 10px 10px;
        width: 100%;
        height: 35px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .select2-selection__arrow {
        transform: translateY(30%);
    }
</style>

<body class="tw-bg-neutral-100 login_admin">

    <div class="tw-max-w-md tw-mx-auto tw-pt-24 authentication-form-wrapper tw-relative tw-z-20">
        <div class="company-logo text-center">
            <?php get_dark_company_logo(); ?>
        </div>

        <h1 class="tw-text-2xl tw-text-neutral-800 text-center tw-font-bold tw-mb-5">
            <?= _l('admin_auth_login_heading'); ?>
        </h1>

        <div
            class="tw-bg-white tw-mx-2 sm:tw-mx-6 tw-py-8 tw-px-6 sm:tw-px-8 tw-shadow-sm tw-rounded-lg tw-border tw-border-solid tw-border-neutral-600/10">

            <?php $this->load->view('authentication/includes/alerts'); ?>

            <?= form_open($this->uri->uri_string()); ?>

            <?= validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>

            <?php hooks()->do_action('after_admin_login_form_start'); ?>

              <!-- Country selection will be hidden by default -->
        <div style="margin-bottom:20px; display:none;" id="country-select" class="form-group">
            <label for="country_id">Select Country</label><br>
            <select style="width: 100%;" name="country_id" id="country_id" class="form-control">
                <option value="">Select Country</option>
                <?php foreach (get_all_countries() as $country) { ?>
                    <option value="<?= $country['country_id']; ?>"><?= $country['short_name']; ?></option>
                <?php } ?>
            </select>
        </div>

        <!-- Hidden input to control whether country_id should be validated -->
        <input type="hidden" name="validate_country" id="validate_country" value="0">

            <div class="form-group">
                <label for="email" class="control-label">
                    <?= _l('admin_auth_login_email'); ?>
                </label>
                <input type="email" id="email" name="email" class="form-control" autofocus="1">
            </div>

            <div class="form-group">
                <label for="password" class="control-label">
                    <?= _l('admin_auth_login_password'); ?>
                </label>
                <input type="password" id="password" name="password" class="form-control">
            </div>

            <?php if (show_recaptcha()) { ?>
            <div class="g-recaptcha tw-mb-4"
                data-sitekey="<?= get_option('recaptcha_site_key'); ?>">
            </div>
            <?php } ?>

            <div class="form-group">
                <div class="checkbox checkbox-inline">
                    <input type="checkbox" value="estimate" id="remember" name="remember">
                    <label for="remember">
                        <?= _l('admin_auth_login_remember_me'); ?></label>
                </div>
            </div>

            <div class="form-group tw-mt-6">
                <button type="submit" class="btn btn-primary btn-block">
                    <?= _l('admin_auth_login_button'); ?>
                </button>
            </div>

            <div class="form-group tw-text-center">
                <a href="<?= admin_url('authentication/forgot_password'); ?>"
                    class="text-muted">
                    <?= _l('admin_auth_login_fp'); ?>
                </a>
            </div>

            <?php hooks()->do_action('before_admin_login_form_close'); ?>

            <?= form_close(); ?>
        </div>
    </div>

</body>

</html>


<!-- Scripts should be at the end of the body -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>

    $('#country_id').select2({});

    document.addEventListener('DOMContentLoaded', function () {
        const urls = ['goshipping'];
        const currentUrl = window.location.href;
        if (urls.some(url => currentUrl.includes(url))) {
            document.getElementById('country-select').style.display = 'block';
            document.getElementById('validate_country').value = '1';
        }
    });
</script>
