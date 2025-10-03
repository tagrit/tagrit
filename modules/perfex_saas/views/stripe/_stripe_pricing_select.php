<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<select name="<?= $name; ?>" class="selectpicker" data-live-search="true" data-width="100%"
    data-none-selected-text="<?php echo _l('stripe_subscription_select_plan'); ?>">
    <option value=""></option>
    <?php if (isset($stripe_plans->data)) { ?>
    <?php foreach ($stripe_plans->data as $plan) {
            if ($plan->usage_type == 'metered') continue;
            if (!$plan->active) {

                if ($selected_value != $plan->id) {
                    continue;
                }
            }

            $selected = '';
            if ($selected_value == $plan->id) {
                $selected = ' selected';
            }
            $subtext = app_format_money(strcasecmp($plan->currency, 'JPY') == 0 ? $plan->amount : $plan->amount / 100, strtoupper($plan->currency));
            if ($plan->interval_count == 1) {
                $subtext .= ' / ' . $plan->interval;
            } else {
                $subtext .= ' (every ' . $plan->interval_count . ' ' . $plan->interval . 's)';
            } ?>
    <option value="<?php echo e($plan->id); ?>" data-interval-count="<?php echo e($plan->interval_count); ?>"
        data-interval="<?php echo e($plan->interval); ?>" data-amount="<?php echo e($plan->amount); ?>"
        data-subtext="<?php echo e($subtext); ?>" <?php echo e($selected); ?>>
        <?php
                if (!empty($plan->nickname)) {
                    echo $plan->nickname;
                } elseif (isset($plan->product->name)) {
                    echo $plan->product->name;
                } else {
                    echo '[Plan Name Not Set in Stripe, ID:' . $plan->id . ']';
                }

                if (!$plan->active) {
                    echo ' (Inactive)';
                }
                ?>
    </option>
    <?php
        } ?>
    <?php } ?>
</select>