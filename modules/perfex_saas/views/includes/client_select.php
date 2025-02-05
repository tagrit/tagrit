<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$value = (array)(is_string($value) ? json_decode($value ?? '') : $value);
$value = array_unique($value);
get_instance()->load->helper('string');
?>

<!-- Set this input to ensure the $key value can be cleared i.e emptied -->
<input name="<?= $name; ?>" value="" type="hidden" />
<div class="form-group select-placeholder clientid reversed-list">
    <label for="clientid" class="control-label"><?php echo $label; ?></label>
    <select id="clientid" name="<?= $name; ?>" data-live-search="true" data-width="100%"
        <?php if (stripos($name, '[]') !== false) echo 'multiple="true"'; ?> class="ajax-search selectpicker"
        data-selected="<?= strip_quotes(json_encode($value)); ?>"
        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
        data-selected="<?= json_encode($value); ?>">
        <?php $selected = $value;
        foreach ($selected as $key => $value) {
            if (!empty($value)) {
                $rel_data = get_relation_data('customer', $value);
                $rel_val  = get_relation_values($rel_data, 'customer');
                echo '<option value="' . $rel_val['id'] . '" selected>' . $rel_val['name'] . '</option>';
            }
        } ?>
    </select>
</div>