<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div id="invoice-template-packageid">
    <div class="form-group">
        <label for="<?= $col_name; ?>" class="control-label"><?php echo _l('perfex_saas_package'); ?></label>
        <select id="<?= $col_name; ?>" name="<?= $col_name; ?>" class="form-control">
            <?= $invoice_packageid ? '' : '<option value=""></option>'; ?>
            <?php foreach ($packages as $package) : ?>
                <option value="<?= $package->id; ?>" <?= !empty($invoice) && $invoice_packageid == $package->id ? 'selected' : ''; ?>>
                    <?= $package->name; ?> (<?= app_format_money($package->price, get_base_currency()); ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<script>
    "use strict";
    window.addEventListener('load', function() {
        document.querySelector('.f_client_id').append(document.querySelector('#invoice-template-packageid'));
        appValidateForm(document.querySelector('#invoice-form'), {
            "<?= $col_name; ?>": 'required'
        });
    })
</script>