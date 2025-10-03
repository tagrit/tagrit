<?php defined('BASEPATH') or exit('No direct script access allowed');
$ma_unlayer_custom_fonts = get_option('ma_unlayer_custom_fonts');
?>

<?php echo form_open(admin_url('ma/update_unlayer_custom_fonts')); ?>
<div class="form-group">
    <label class="bold" for="ma_unlayer_custom_fonts">
        <?php echo _l('unlayer_custom_fonts'); ?>
    </label>
    <textarea name="ma_unlayer_custom_fonts"
        id="ma_unlayer_custom_fonts" rows="15"
        class="form-control"><?php echo clear_textarea_breaks($ma_unlayer_custom_fonts); ?></textarea>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
</div>
<?php echo form_close(); ?>
