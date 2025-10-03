<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (isset($multiple)) echo '<input name="' . $name . '" value="" type="hidden" />';
if (!is_array($value)) $value = [$value];
?>
<select name="<?= $name; ?>" class="form-control selectpicker <?= $class ?? ''; ?>" <?= isset($id) ? "id='$id'" : ""; ?>
    <?= isset($multiple) ? "multiple='$multiple'" : ""; ?>>
    <option value=""></option>
    <?php
    $tenants = get_instance()->perfex_saas_model->companies('', true);
    foreach ($tenants as $tenant) {
        $selected = in_array($tenant->slug, $value) ? 'selected' : '';
        echo '<option value="' . $tenant->slug . '" ' . $selected . '>' . $tenant->name . ' (' . $tenant->slug . ')</option>';
    } ?>
</select>