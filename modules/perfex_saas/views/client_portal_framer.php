<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="tw-h-screen" style="height: 100vh;">
        <iframe class="tw-w-full tw-h-full" src="<?= $url; ?>"></iframe>
    </div>
</div>
<script>
window.onmessage = function(event) {
    if (event.data.message === "closedBridge") {
        window.location.href = window.location.href.split('?')[0] + '?paying_outstanding';
    }

    if (event.data.message === "home") {
        window.location.href = "<?= perfex_saas_default_base_url('?subscription'); ?>";
    }

    if (event.data.message === "openInParent") {
        window.location.href = event.data.value;
    }
};
</script>



<?php init_tail(); ?>
</body>

</html>