<?php defined('BASEPATH') or exit('No direct script access allowed');
$ma_form_style = get_option('ma_form_style');
?>

<?php echo form_open(admin_url('ma/update_form_style')); ?>
<div class="form-group">
    <label class="bold" for="ma_form_style">
        <?php echo _l('form_custom_css'); ?>
    </label>
    <textarea name="ma_form_style"
        id="ma_form_style" rows="15"
        class="form-control"><?php echo clear_textarea_breaks($ma_form_style); ?></textarea>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
</div>
<?php echo form_close(); ?>
