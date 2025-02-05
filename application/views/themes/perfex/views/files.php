<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700 section-heading section-heading-files">
    <?= _l('customer_profile_files'); ?>
</h4>
<?php hooks()->do_action('after_customers_area_files_heading'); ?>
<div class="panel_s">
    <div class="panel-body">
        <?= form_open_multipart(site_url('clients/upload_files'), ['class' => 'dropzone', 'id' => 'files-upload']); ?>
        <input type="file" name="file" multiple class="hide" />
        <?= form_close(); ?>
        <?php hooks()->do_action('after_customers_area_files_dropzone'); ?>
        <div class="tw-mt-4 tw-flex tw-justify-end tw-items-center tw-space-x-2 tw-mb-5">
            <button class="gpicker" data-on-pick="customerFileGoogleDriveSave">
                <i class="fa-brands fa-google" aria-hidden="true"></i>
                <?= _l('choose_from_google_drive'); ?>
            </button>
            <?php if (get_option('dropbox_app_key') != '') { ?>
            <div id="dropbox-chooser-files"></div>
            <?php } ?>
        </div>
        <?php if (count($files) == 0) { ?>
        <hr class="hr-panel-heading" />
        <p class="tw-text-neutral-500">
            <?= _l('no_files_found'); ?>
        </p>
        <?php } else { ?>
        <table class="table dt-table mtop15 table-files" data-order-col="1" data-order-type="desc">
            <thead>
                <tr>
                    <th class="th-files-file">
                        <?= _l('customer_attachments_file'); ?>
                    </th>
                    <th class="th-files-date-uploaded">
                        <?= _l('file_date_uploaded'); ?>
                    </th>
                    <?php if (get_option('allow_contact_to_delete_files') == 1) { ?>
                    <th class="th-files-option">
                        <?= _l('options'); ?>
                    </th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file) { ?>
                <tr>
                    <td>
                        <?php
                      $url    = site_url() . 'download/file/client/';
                    $path     = get_upload_path_by_type('customer') . $file['rel_id'] . '/' . $file['file_name'];
                    $is_image = false;
                    if (! isset($file['external'])) {
                        $attachment_url = $url . $file['attachment_key'];
                        $is_image       = is_image($path);
                        $img_url        = site_url('download/preview_image?path=' . protected_file_url_by_path($path, true) . '&type=' . $file['filetype']);
                    } elseif (isset($file['external']) && ! empty($file['external'])) {
                        if (! empty($file['thumbnail_link'])) {
                            $is_image = true;
                            $img_url  = optimize_dropbox_thumbnail($file['thumbnail_link']);
                        }
                        $attachment_url = $file['external_link'];
                    }
                    if ($is_image) {
                        echo '<div class="preview_image">';
                    }
                    ?>
                        <a href="<?= e($attachment_url); ?>"
                            <?= isset($file['external']) && ! empty($file['external']) ? ' target="_blank"' : ''; ?>
                            class="display-block mbot5">
                            <?php if ($is_image) { ?>
                            <div class="table-image">
                                <div class="text-center"><i class="fa fa-spinner fa-spin mtop30"></i></div>
                                <img src="#" class="img-table-loading"
                                    data-orig="<?= e($img_url); ?>">
                            </div>
                            <?php } else { ?>
                            <i
                                class="<?= get_mime_class($file['filetype']); ?>"></i>
                            <?= e($file['file_name']); ?>
                            <?php } ?>
                        </a>
                        <?php if ($is_image) {
                            echo '</div>';
                        } ?>
                    </td>
                    <td
                        data-order="<?= e($file['dateadded']); ?>">
                        <?= e(_dt($file['dateadded'])); ?>
                    </td>
                    <?php if (get_option('allow_contact_to_delete_files') == 1) { ?>
                    <td>
                        <?php if ($file['contact_id'] == get_contact_user_id()) { ?>
                        <a href="<?= site_url('clients/delete_file/' . $file['id'] . '/general'); ?>"
                            class="btn btn-danger btn-icon _delete file-delete"><i class="fa fa-remove"></i></a>
                        <?php } ?>
                    </td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } ?>
        <?php hooks()->do_action('after_customers_area_files'); ?>
    </div>
</div>