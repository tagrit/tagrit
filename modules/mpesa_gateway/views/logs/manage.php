<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-2 sm:tw-mb-4">
                    <h4> <?php echo _l('mpesa_payment_logs'); ?></h4>
                </div>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php render_datatable([
                            _l('invoice'),
                            _l('mpesa_table_log_ref_id'),
                            _l('phone_number'),
                            _l('mpesa_table_log_amount'),
                            _l('mpesa_table_log_status'),
                            _l('mpesa_table_log_description'),
                            _l('mpesa_table_log_date'),
                            _l('options')
                        ], 'mpesa_payment_logs'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function() {
        initDataTable('.table-mpesa_payment_logs', window.location.href, undefined, [4]);
    });
</script>
</body>

</html>