<?php foreach ($ticket_replies as $reply) { ?>
<div class="panel_s">
    <div
        class="panel-body<?= $reply['admin'] == null ? ' client-reply' : ''; ?>">
        <div class="tw-flex tw-flex-wrap tw-mb-6">
            <div class="tw-grow">
                <div class="tw-flex tw-items-center tw-gap-3">
                    <p class="tw-my-0 tw-font-semibold">
                        <?php if ($reply['admin'] == null || $reply['admin'] == 0) { ?>
                        <?php if ($reply['userid'] != 0) { ?>
                        <a
                            href="<?= admin_url('clients/client/' . $reply['userid'] . '?contactid=' . $reply['contactid']); ?>">
                            <?= e($reply['submitter']); ?>
                        </a>
                        <?php } else { ?>
                        <span><?= e($reply['submitter']); ?></span>
                        <br />
                        <a
                            href="mailto:<?= e($reply['reply_email']); ?>">
                            <?= e($reply['reply_email']); ?>
                        </a>
                        <?php } ?>
                        <?php } else { ?>
                        <a
                            href="<?= admin_url('profile/' . $reply['admin']); ?>">
                            <?= e($reply['submitter']); ?>
                        </a>
                        <?php } ?>
                    </p>
                    <?php if ($reply['admin'] !== null || $reply['admin'] != 0) { ?>
                    <span
                        class="label label-default"><?= _l('ticket_staff_string'); ?></span>
                    <?php } elseif ($reply['userid'] != 0) { ?>
                    <span
                        class="label label-primary"><?= _l('ticket_client_string'); ?></span>
                    <?php } ?>
                </div>
            </div>
            <div class="tw-space-x-4 tw-flex tw-items-center rtl:tw-space-x-reverse">
                <p class="tw-text-neutral-600 tw-font-medium tw-text-sm tw-my-0">
                    <?= e(_l('ticket_posted', _dt($reply['date']))); ?>
                </p>
                <?php if (staff_can('create', 'tasks')) { ?>
                <a href="#"
                    class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-600 tw-text-sm tw-font-semibold"
                    onclick="convert_ticket_to_task(<?= e($reply['id']); ?>,'reply'); return false;">
                    <?= _l('convert_to_task'); ?>
                </a>
                <?php } ?>
                <?php if (! empty($reply['message'])) { ?>
                <a href="#" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-600"
                    onclick="print_ticket_message(<?= e($reply['id']); ?>, 'reply'); return false;">
                    <i class="fa fa-print"></i>
                </a>
                <?php } ?>
                <a href="#" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-600"
                    onclick="edit_ticket_message(<?= e($reply['id']); ?>,'reply'); return false;">
                    <i class="fa-regular fa-pen-to-square"></i>
                </a>
                <?php if (can_staff_delete_ticket_reply()) { ?>
                <a href="<?= admin_url('tickets/delete_ticket_reply/' . $ticket->ticketid . '/' . $reply['id']); ?>"
                    class="_delete tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-600">
                    <i class="fa-regular fa-trash-can"></i>
                </a>
                <?php } ?>
            </div>
        </div>
        <div data-reply-id="<?= e($reply['id']); ?>"
            class="tc-content">
            <?php
                if (empty($reply['admin'])) {
                    echo process_text_content_for_display($reply['message']);
                } else {
                    echo check_for_links($reply['message']);
                }
    ?>
        </div>
        <?php if (count($reply['attachments']) > 0) { ?>
        <hr />
        <?php foreach ($reply['attachments'] as $attachment) {
            $path     = get_upload_path_by_type('ticket') . $ticket->ticketid . '/' . $attachment['file_name'];
            $is_image = is_image($path);
            if ($is_image) { ?>
        <div class="preview_image">
            <?php } ?>
            <a href="<?= site_url('download/file/ticket/' . $attachment['id']); ?>"
                class="display-block mbot5" <?php if ($is_image) { ?>
                data-lightbox="attachment-reply-<?= e($reply['id']); ?>"
                <?php } ?>>
                <?= e($attachment['file_name']); ?>
                <?php if ($is_image) { ?>
                <img class="mtop5"
                    src="<?= site_url('download/preview_image?path=' . protected_file_url_by_path($path) . '&type=' . $attachment['filetype']); ?>">
                <?php } ?>
            </a>
            <?php if ($is_image) { ?>
        </div>
        <?php }
            if (is_admin() || (! is_admin() && get_option('allow_non_admin_staff_to_delete_ticket_attachments') == '1')) { ?>
        <a href="<?= admin_url('tickets/delete_attachment/' . $attachment['id']); ?>"
            class="text-danger _delete">
            <?= _l('delete'); ?>
        </a>
        <?php } ?>
        <hr />
        <?php } ?>
        <?php } ?>
    </div>
</div>
<?php } ?>