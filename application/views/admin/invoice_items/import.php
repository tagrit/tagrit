<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="collapse" id="importHints">
                    <div class="panel_s">
                        <div class="panel-body tw-bg-gradient-to-l tw-from-transparent tw-to-neutral-50">
                            <?= $this->import->importGuidelinesInfoHtml(); ?>
                        </div>
                    </div>
                </div>
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-3">
                    <h4 class="tw-my-0 tw-font-bold tw-text-lg tw-text-neutral-700 tw-flex tw-items-center tw-gap-x-2">
                        <i class="fa fa-question-circle tw-cursor-pointer" data-toggle="collapse" href="#importHints"
                            aria-expanded="false" aria-controls="importHints"></i>
                        <?= _l('import_items'); ?>
                    </h4>
                    <?= $this->import->downloadSampleFormHtml(); ?>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <?= $this->import->maxInputVarsWarningHtml(); ?>
                        <?php if (! $this->import->isSimulation()) { ?>
                        <?= $this->import->createSampleTableHtml(); ?>

                        <?php } else { ?>

                        <div class="tw-mb-6">
                            <?= $this->import->simulationDataInfo(); ?>
                        </div>

                        <?= $this->import->createSampleTableHtml(true); ?>

                        <?php } ?>
                        <div class="row">
                            <div class="col-md-4">
                                <?= form_open_multipart($this->uri->uri_string(), ['id' => 'import_form']); ?>
                                <?= form_hidden('items_import', 'true'); ?>
                                <?= render_input('file_csv', 'choose_csv_file', '', 'file'); ?>
                                <div class="form-group">
                                    <button type="button"
                                        class="btn btn-primary import btn-import-submit"><?= _l('import'); ?></button>
                                    <button type="button"
                                        class="btn btn-default simulate btn-import-submit"><?= _l('simulate_import'); ?></button>
                                </div>
                                <?= form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script
    src="<?= base_url('assets/plugins/jquery-validation/additional-methods.min.js'); ?>">
</script>
<script>
    $(function() {
        appValidateForm($('#import_form'), {
            file_csv: {
                required: true,
                extension: "csv"
            }
        });
    });
</script>
</body>

</html>