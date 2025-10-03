<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="tw-max-w-4xl tw-mx-auto">
            <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700">
                <?= e($title); ?>
            </h4>
            <?= form_open($this->uri->uri_string()); ?>
            <div class="panel_s">
                <div class="panel-body">
                    <?php $value = (isset($announcement) ? $announcement->name : ''); ?>
                    <?= render_input('name', 'announcement_name', $value); ?>
                    <p class="bold">
                        <?= _l('announcement_message'); ?>
                    </p>
                    <?php $contents = ''; ?>
                    <?php if (isset($announcement)) {
                        $contents = $announcement->message;
                    } ?>
                    <?= render_textarea('message', '', $contents, [], [], '', 'tinymce'); ?>
                </div>
                <div class="panel-footer">
                    <div class="tw-flex tw-justify-between tw-items-center">
                        <div>
                            <div class="checkbox checkbox-primary checkbox-inline">
                                <input type="checkbox" name="showtostaff" id="showtostaff"
                                    <?= (! isset($announcement) || (isset($announcement) && $announcement->showtostaff == 1)) ? 'checked' : ''; ?>>
                                <label
                                    for="showtostaff"><?= _l('announcement_show_to_staff'); ?></label>
                            </div>
                            <div class="checkbox checkbox-primary checkbox-inline">
                                <input type="checkbox" name="showtousers" id="showtousers"
                                    <?= isset($announcement) && $announcement->showtousers == 1 ? 'checked' : ''; ?>>
                                <label
                                    for="showtousers"><?= _l('announcement_show_to_clients'); ?></label>
                            </div>
                            <div class="checkbox checkbox-primary checkbox-inline">
                                <input type="checkbox" name="showname" id="showname"
                                    <?= isset($announcement) && $announcement->showname == 1 ? 'checked' : ''; ?>>
                                <label
                                    for="showname"><?= _l('announcement_show_my_name'); ?></label>
                            </div>
                        </div>
                        <button type="submit"
                            class="btn btn-primary"><?= _l('submit'); ?></button>
                    </div>


                </div>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function() {
        appValidateForm($('form'), {
            name: 'required'
        });
    });
</script>
</body>

</html>