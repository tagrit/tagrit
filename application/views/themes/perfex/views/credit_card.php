<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Stripe Credit Cards UPDATE
 */
?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700 section-text section-heading-credit-card">
    <?= _l('update_credit_card'); ?>
</h4>

<div class="panel_s">
    <div class="panel-body credit-card">
        <?php if (! empty($payment_method)) { ?>
        <h4><?= _l('credit_card_update_info'); ?>
        </h4>

        <a href="<?= site_url('clients/update_credit_card'); ?>"
            class="btn btn-primary">
            <?= _l('update_card_btn'); ?>
            (<?= e($payment_method->card->brand); ?>
            <?= e($payment_method->card->last4); ?>
        </a>

        <div<?php if (! customer_can_delete_credit_card()) { ?>
            data-toggle="tooltip"
            title="<?= _l('delete_credit_card_info'); ?>"
            <?php } ?> class="inline-block">
            <a class="btn btn-danger<?php if (! customer_can_delete_credit_card()) { ?> disabled<?php } ?>"
                href="<?= site_url('clients/delete_credit_card'); ?>">
                <?= _l('delete_credit_card'); ?>
            </a>
    </div>
    <?php } else { ?>
    <?= _l('no_credit_card_found'); ?>
    <?php } ?>
</div>
</div>