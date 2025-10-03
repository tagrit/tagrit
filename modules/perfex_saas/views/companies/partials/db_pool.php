<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="tw-flex tw-items-start tw-mt-4 tw-justify-center">
    <div class="tw-flex tw-space-x-2 pool-template tw-items-center" id="single-pool-template">
        <?php echo render_input('db_pools[host]', 'perfex_saas_db_host', $_dsn['host'] ?? '', 'text', ['placeholder' => 'localhost']); ?>
        <?php echo render_input('db_pools[user]', 'perfex_saas_db_user', $_dsn['user'] ?? '', 'text', ['placeholder' => 'root']); ?>
        <?php echo render_input('db_pools[password]', 'perfex_saas_db_password', $_dsn['password'] ?? '', 'text', ['placeholder' => 'password']); ?>
        <?php echo render_input('db_pools[dbname]', 'perfex_saas_db_name', $_dsn['dbname'] ?? ''); ?>
        <div>
            <button class="btn btn-info test_db_row tw-mt-2" type="button" onclick="dbPoolTestDbRow();">
                <?= _l('perfex_saas_test'); ?>
            </button>
        </div>
    </div>
</div>

<script>
    // Test database connection
    function dbPoolTestDbRow() {
        let button = $(".test_db_row");
        button.addClass("disabled");

        let data = {};
        button
            .closest("#single-pool-template")
            .find("input, select")
            .each(function() {
                let thisInput = $(this);
                data[thisInput.attr("name")] = thisInput.val();
            });

        // Send AJAX request to test the database connection
        $.post(admin_url + SAAS_MODULE_NAME + "/packages/test_db", data)
            .done(function(response) {
                response = JSON.parse(response);
                if (response.status) {
                    alert_float(response.status, response.message);
                }
                button.removeClass("disabled");
            })
            .fail(function() {
                button.removeClass("disabled");
            });
    };
</script>