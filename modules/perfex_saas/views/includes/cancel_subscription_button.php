<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php if (empty($invoice->subscription_id)) : ?>
    <a href="<?= base_url('clients/my_account/cancel_subscription'); ?>" class="btn btn-danger mtop10">
        <i class="fa fa-times"></i>
        <?= _l('perfex_saas_pricing_cancel'); ?>
    </a>

<?php else : ?>

    <?php if (!empty($invoice->stripe_subscription_id) && $invoice->status != 'canceled' && !empty($invoice->subscription_ends_at)) : ?>
        <a class="btn btn-warning mtop10" href="<?php echo base_url('clients/my_account/resume_subscription'); ?>">
            <?= _l('resume_now'); ?>
        </a>
    <?php else : ?>
        <div class="btn-group">
            <a href="#" class="btn btn-danger mtop10 dropdown-toggle tw-w-full" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php echo _l('cancel'); ?> <span class="caret"></span></a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li><a onclick="return confirm('<?= perfex_saas_ecape_js_attr(_l('perfex_saas_pricing_cancel_confirmation')); ?>')" href="<?php echo base_url('clients/my_account/cancel_subscription?type=immediately'); ?>">
                        <?php echo _l('cancel_immediately'); ?></a></li>
                <li><a onclick="return confirm('<?= perfex_saas_ecape_js_attr(_l('perfex_saas_pricing_cancel_confirmation')); ?>')" href="<?php echo base_url('clients/my_account/cancel_subscription?type=at_period_end'); ?>">
                        <?php echo _l('cancel_at_end_of_billing_period'); ?>
                    </a>
            </ul>
        </div>
    <?php endif ?>
<?php endif ?>