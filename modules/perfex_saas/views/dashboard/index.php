<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php require_once 'alerts.php'; ?>

<script>
    "use strict";
    window.addEventListener("DOMContentLoaded", function() {
        var package_invoice_chart = $('#package_invoice_stats');
        if (package_invoice_chart.length > 0) {
            // Package invoice overview status
            new Chart(package_invoice_chart, {
                type: 'doughnut',
                data: <?php echo json_encode(get_instance()->perfex_saas_model->package_invoice_chart()); ?>,
                options: {
                    maintainAspectRatio: false,
                    onClick: function(evt) {
                        onChartClickRedirect(evt, this);
                    }
                }
            });
        }
    })
</script>