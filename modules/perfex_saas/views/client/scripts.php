<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$clientid = get_client_user_id();
if ($clientid)
    $invoice =  get_instance()->perfex_saas_model->get_company_invoice($clientid);

$alternative_host = perfex_saas_get_saas_alternative_host();
$perfex_saas_default_host = empty($alternative_host) ? perfex_saas_get_saas_default_host() : $alternative_host;
?>

<script>
"use strict";

const SAAS_MODULE_NAME = '<?= PERFEX_SAAS_MODULE_WHITELABEL_NAME; ?>';
const SAAS_MAGIC_AUTH_BASE_URL = '<?= base_url('clients/ps_magic/'); ?>';
const SAAS_DEFAULT_HOST = '<?= $perfex_saas_default_host; ?>';
const SAAS_ACTIVE_SEGMENT = (window.location
        .search.startsWith("?subscription") || window.location
        .search.startsWith("?companies")) ? window.location.search.split('&')[0] :
    '<?= empty($invoice) ? "?subscription" : "?companies"; ?>';

const SAAS_CONTROL_CLIENT_MENU = <?= (int)get_option('perfex_saas_control_client_menu'); ?>;
const SAAS_MAX_SLUG_LENGTH = <?= PERFEX_SAAS_MAX_SLUG_LENGTH; ?>;
</script>

<!-- Load client panel script and style -->
<script src="<?= perfex_saas_asset_url('js/client.js'); ?>"></script>
<script>
const appFormatMoney = new AppFormatMoney({
    removeDecimalsOnZero: <?= (int)get_option('remove_decimals_on_zero'); ?>,
    decimalPlaces: <?= (int)get_decimal_places(); ?>,
    currency: <?= json_encode(get_base_currency()); ?>,
});
</script>
<link rel="stylesheet" type="text/css" href="<?= perfex_saas_asset_url('css/client.css'); ?>" />

<!-- style control for client menu visibility -->
<?php if ((int)get_option('perfex_saas_control_client_menu')) : ?>
<style>
.section-client-dashboard>dl:first-of-type,
.projects-summary-heading,
.submenu.customer-top-submenu {
    display: none;
}
</style>
<?php endif; ?>

<?php $CI = &get_instance(); ?>

<!-- load client widgets -->
<?php require_once(__DIR__ . '/widgets/index.php'); ?>


<?php
$portal_message = $CI->input->get('portal-message', true);
$portal_message_value = $CI->input->get('portal-message-value');
if (!empty($portal_message_value))
    $portal_message_value = urldecode(base64_decode($portal_message_value));
$has_magic_auth = $CI->session->has_userdata('magic_auth');
$portal_origin = $CI->session->userdata('magic_auth')['source_url'] ?? '';

// Sett custom message
if (isset($GLOBALS['has_outstanding']) && $GLOBALS['has_outstanding'] && empty($portal_message)) {

    $portal_message = 'closedBridge';
}

if (!empty($portal_message)) {

    // Remove any magic auth before redirecting away through message
    $CI->session->unset_userdata('magic_auth');
}
?>


<?php if ($has_magic_auth || !empty($portal_message)) : ?>
<style>
#wrapper>#content {
    margin-top: 30px
}
</style>

<script>
const SAAS_IS_MAGIC_AUTH = true;
const SINGLE_PORTAL_PARENT = window?.self !== window?.top ? window.parent : null;
const SINGLE_PORTAL_TARGET_ORIGIN = "<?= $portal_origin ?>";
const SINGLE_PORTAL_MESSAGE = "<?= $portal_message; ?>";
const SINGLE_PORTAL_MESSAGE_VALUE = "<?= $portal_message_value; ?>";
const SAAS_SINGLE_PORTAL_ORIGIN = "<?= parse_url(perfex_saas_default_base_url())['host'] ?? ''; ?>";

const SAAS_SINGLE_PORTAL_TARGET_IS_CUSTOM_DOMAIN = !SINGLE_PORTAL_TARGET_ORIGIN.includes(SAAS_SINGLE_PORTAL_ORIGIN);
const SAAS_CUSTOM_DOMAIN_CAN_USE_SINGLE_PORTAL = "<?= get_option('perfex_saas_enable_cross_domain_bridge'); ?>" == "1";
</script>

<script>
if (SINGLE_PORTAL_PARENT && SINGLE_PORTAL_PARENT.postMessage && SINGLE_PORTAL_MESSAGE.length) {

    SINGLE_PORTAL_PARENT.postMessage({
        message: SINGLE_PORTAL_MESSAGE,
        value: SINGLE_PORTAL_MESSAGE_VALUE
    }, SINGLE_PORTAL_TARGET_ORIGIN);
}

// Handle orphaned client window
if (SAAS_IS_MAGIC_AUTH && !SINGLE_PORTAL_PARENT && !SINGLE_PORTAL_MESSAGE.length && SINGLE_PORTAL_TARGET_ORIGIN) {

    // Get redirect count
    const singlePortalRedStorageKey = "sprc";
    const SAAS_SINGLE_PORTAL_REDIRECTION_COUNT = parseInt(sessionStorage.getItem(singlePortalRedStorageKey) || 1);

    let saasShouldNotRedirectOrphanedPage = (SAAS_SINGLE_PORTAL_TARGET_IS_CUSTOM_DOMAIN && !
            SAAS_CUSTOM_DOMAIN_CAN_USE_SINGLE_PORTAL) ||
        SAAS_SINGLE_PORTAL_REDIRECTION_COUNT > 3;

    if (saasShouldNotRedirectOrphanedPage) {
        // reset the redirect counter
        sessionStorage.setItem(singlePortalRedStorageKey, 0);

    } else {

        // track redirection for limit
        sessionStorage.setItem(singlePortalRedStorageKey, SAAS_SINGLE_PORTAL_REDIRECTION_COUNT + 1);

        // redirect
        window.location.href = SINGLE_PORTAL_TARGET_ORIGIN + '?redirect=' + window.location.pathname + window.location
            .search;
    }
}


if (SINGLE_PORTAL_PARENT) {
    // Function to handle navigation attempts
    function SaaSHandleCrossOriginNavigation(event, url = '') {

        if (!url.length)
            url = event.target.href || event.target.src || window.location.href;

        // Check if the URL is a third-party URL (like Stripe)
        if (!url.includes(SAAS_SINGLE_PORTAL_ORIGIN)) {
            event.preventDefault(); // Prevent the default navigation
            SINGLE_PORTAL_PARENT.postMessage({
                message: 'openInParent',
                value: url
            }, SINGLE_PORTAL_TARGET_ORIGIN); // Send a message to the parent
            return;
        }
    }

    // Attach the event listener for links and forms
    document.addEventListener('click', (event) => {
        if (event.target.tagName === 'A' || event.target.tagName === 'FORM') {
            SaaSHandleCrossOriginNavigation(event);
        }
    });

    // Alternatively, for window.location changes
    window.addEventListener('beforeunload', (event) => {
        SaaSShowLoadingIndicator(); // Show the spinner when leaving the page
        SaaSHandleCrossOriginNavigation(event);
    });
}
</script>
<?php endif; ?>