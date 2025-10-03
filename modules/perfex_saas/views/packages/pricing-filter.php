<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="tw-flex tw-justify-center tw-mb-4 pricing-filter">

    <!-- Toggle for two items -->
    <?php if ($total_groups == 2) : ?>
    <div class="tw-flex">
        <label class="text-primary"
            data-package-group-toggle="<?= e($filter_groups[0]); ?>"><?= e($filter_groups[0]); ?></label>
        <div class="tw-px-2">
            <div class="onoffswitch">
                <input type="checkbox" class="onoffswitch-checkbox" value="1" name="package_group"
                    id="package_group_switch" onchange="perfexSaasSwitchPricingFilter();">
                <label class="onoffswitch-label" for="package_group_switch"></label>
            </div>
        </div>
        <label data-package-group-toggle="<?= e($filter_groups[1]); ?>"><?= e($filter_groups[1]); ?></label>
    </div>
    <?php endif; ?>


    <!-- Dropdown for more than two items -->
    <?php if ($total_groups > 2) : ?>
    <select id="package_group_switch" class="selectpicker" onchange="perfexSaasSwitchPricingFilter(this.value);">
        <?php foreach ($filter_groups as $group) : ?>
        <option data-package-group-toggle="<?= e($group); ?>" value="<?= e($group); ?>"><?= e($group); ?></option>
        <?php endforeach; ?>
    </select>
    <?php endif; ?>
</div>

<script>
// Use custom function to prefix the selector with a wrapping class to ensure unique instance of this component
var $pricingFilter = (selector) => {
    return $(`.<?= $filter_wrapper_class; ?> ${selector}`);
}

function perfexSaasSwitchPricingFilter(group = '') {
    if (!group?.length) {
        let index = $pricingFilter('#package_group_switch').prop('checked') ? 1 : 0;

        group = $($pricingFilter('[data-package-group-toggle]')[index]).attr('data-package-group-toggle');
    }

    $pricingFilter(`[data-package-group]:not([data-package-group="${group}"])`).hide();
    $pricingFilter(`[data-package-group="${group}"]`).show();
    $pricingFilter(`[data-package-group-toggle]`).removeClass('text-primary');
    $pricingFilter(`[data-package-group-toggle="${group}"]`).addClass('text-primary');
}
document.addEventListener('DOMContentLoaded', function() {
    let defaultGroup = $pricingFilter('[data-package-default-group]').attr('data-package-default-group');
    if (defaultGroup) {
        if ($pricingFilter("select#package_group_switch").length > 0)
            $pricingFilter("select#package_group_switch").val(defaultGroup).trigger('change');
        else {
            let check = defaultGroup != $($pricingFilter('[data-package-group-toggle]')[0]).attr(
                'data-package-group-toggle');
            if (check)
                $pricingFilter("[for='package_group_switch']").trigger('click');
            else
                perfexSaasSwitchPricingFilter(defaultGroup);
        }
    } else {
        perfexSaasSwitchPricingFilter(defaultGroup);
    }
    $pricingFilter('').show();
});
</script>
<style>
.pricing-filter .onoffswitch-checkbox+.onoffswitch-label,
.pricing-filter .onoffswitch-checkbox+.onoffswitch-label::before {
    --tw-border-opacity: 1 !important;
    border-color: rgb(59 130 246/var(--tw-border-opacity)) !important;
}

.pricing-filter .onoffswitch-checkbox+.onoffswitch-label {
    --tw-bg-opacity: 1 !important;
    background-color: rgb(59 130 246/var(--tw-bg-opacity)) !important;
}
</style>