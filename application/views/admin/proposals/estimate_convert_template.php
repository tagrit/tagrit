<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade proposal-convert-modal" id="convert_to_estimate" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xxl" role="document">
        <?= form_open('admin/proposals/convert_to_estimate/' . $proposal->id, ['id' => 'proposal_convert_to_estimate_form', 'class' => '_transaction_form disable-on-submit']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="close_modal_manually('#convert_to_estimate')"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span
                        class="edit-title"><?= _l('proposal_convert_to_estimate'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php $this->load->view('admin/estimates/estimate_template'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="close_modal_manually('#convert_to_estimate')">
                    <?= _l('close'); ?>
                </button>
                <button type="submit"
                    class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
        </div>
        <?= form_close(); ?>
    </div>
</div>
<?php $this->load->view('admin/invoice_items/item'); ?>
<script>
    init_ajax_search('customer', '#clientid.ajax-search');
    init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');
    custom_fields_hyperlink();
    init_selectpicker();
    init_datepicker();
    init_color_pickers();
    init_items_sortable();
    init_tags_inputs();
    validate_estimate_form('#proposal_convert_to_estimate_form');
    <?php if ($proposal->assigned != 0) { ?>
    $('#convert_to_estimate #sale_agent').selectpicker(
        'val', <?= e($proposal->assigned); ?> );
    <?php } ?>
    $('select[name="discount_type"]').selectpicker('val',
        '<?= e($proposal->discount_type); ?>');
    $('input[name="discount_percent"]').val(
        '<?= e($proposal->discount_percent); ?>');
    $('input[name="discount_total"]').val(
        '<?= e($proposal->discount_total); ?>');
    <?php if (is_sale_discount($proposal, 'fixed')) { ?>
    $('.discount-total-type.discount-type-fixed').click();
    <?php } ?>
    $('input[name="adjustment"]').val(
        '<?= e($proposal->adjustment); ?>');
    $('input[name="show_quantity_as"][value="<?= e($proposal->show_quantity_as); ?>"]')
        .prop('checked', true).change();
    <?php if (! isset($project_id) || ! $project_id) { ?>
    $('#convert_to_estimate #clientid').change();
    <?php } else { ?>
    init_ajax_project_search_by_customer_id('select#project_id')
    <?php } ?>
    // Trigger item select width fix
    $('#convert_to_estimate').on('shown.bs.modal', function() {
        $('#item_select').trigger('change')
    })
</script>