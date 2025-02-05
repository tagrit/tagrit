<div class="panel_s tw-mt-5">
    <div
        class="panel-body<?= $ticket->admin == null ? ' client-reply' : ''; ?> tw-bg-info-50/30">
        <div class="tw-flex tw-flex-wrap tw-mb-6">
            <div class="tw-grow">
                <div class="tw-flex tw-items-center tw-gap-3">
                    <p class="tw-my-0 tw-font-semibold">
                        <?php if ($ticket->admin == null || $ticket->admin == 0) { ?>
                        <?php if ($ticket->userid != 0) { ?>
                        <a
                            href="<?= admin_url('clients/client/' . $ticket->userid . '?contactid=' . $ticket->contactid); ?>">
                            <?= e($ticket->submitter); ?>
                        </a>
                        <?php } else { ?>
                        <?php if (! $sender_blocked) { ?>
                        <button type="button"
                            data-sender="<?= e($ticket->ticket_email); ?>"
                            data-toggle="tooltip"
                            title="<?= _l('block_sender'); ?>"
                            class="tw-bg-transparent tw-border-0 tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-600 tw-text-sm block-sender tw-p-0">
                            <i class="fa-solid fa-ban"></i>
                        </button>
                        <?php } ?>
                        <span><?= e($ticket->submitter); ?></span>
                        <a
                            href="mailto:<?= e($ticket->ticket_email); ?>">
                            <?= e($ticket->ticket_email); ?>
                        </a>
                        <?php } ?>
                        <?php } else {  ?>
                        <a
                            href="<?= admin_url('profile/' . $ticket->admin); ?>">
                            <?= e($ticket->opened_by); ?>
                        </a>
                        <?php } ?>
                    </p>

                    <?php if ($ticket->admin !== null || $ticket->admin != 0) { ?>
                    <span
                        class="label label-default"><?= _l('ticket_staff_string'); ?></span>
                    <?php } elseif ($ticket->userid != 0) { ?>
                    <span
                        class="label label-primary"><?= _l('ticket_client_string'); ?></span>
                    <?php } ?>

                    <?php if ($ticket->admin == null || $ticket->admin == 0 && $ticket->user_id == 0) { ?>
                    <?php if ($sender_blocked) { ?>
                    <span
                        class="label label-danger"><?= _l('sender_blocked'); ?></span>
                    <?php } ?>
                    <?php } ?>
                </div>
            </div>

            <div class="tw-space-x-4 tw-flex tw-items-center rtl:tw-space-x-reverse">
                <p class="tw-text-neutral-600 tw-font-medium tw-text-sm tw-my-0">
                    <?= e(_l('ticket_posted', _dt($ticket->date))); ?>
                </p>

                <?php if (staff_can('create', 'tasks')) { ?>
                <a href="#"
                    class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-600 tw-text-sm tw-font-semibold"
                    onclick="convert_ticket_to_task(<?= e($ticket->ticketid); ?>,'ticket'); return false;">
                    <?= _l('convert_to_task'); ?>
                </a>
                <?php } ?>
                <?php if (! empty($ticket->message)) { ?>
                <a href="#" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-600"
                    onclick="print_ticket_message(<?= e($ticket->ticketid); ?>, 'ticket'); return false;"
                    class="mright5">
                    <i class="fa fa-print"></i>
                </a>
                <?php } ?>
                <?php if (can_staff_edit_ticket_message()) { ?>
                <a href="#" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-600"
                    onclick="edit_ticket_message(<?= e($ticket->ticketid); ?>,'ticket'); return false;">
                    <i class="fa-regular fa-pen-to-square"></i>
                </a>
                <?php } ?>
            </div>
        </div>

        <div data-ticket-id="<?= e($ticket->ticketid); ?>"
            class="tc-content">
            <?php if (empty($ticket->admin)) {
                echo process_text_content_for_display($ticket->message);
            } else {
                echo check_for_links($ticket->message);
            } ?>
        </div>

        <?php if (count($ticket->attachments) > 0) { ?>
        <hr />
        <?php foreach ($ticket->attachments as $attachment) {
            $path     = get_upload_path_by_type('ticket') . $ticket->ticketid . '/' . $attachment['file_name'];
            $is_image = is_image($path);

            if ($is_image) { ?>
        <div class="preview_image">
            <?php } ?>
            <a href="<?= site_url('download/file/ticket/' . $attachment['id']); ?>"
                class="display-block mbot5" <?php if ($is_image) { ?>
                data-lightbox="attachment-ticket-<?= e($ticket->ticketid); ?>"
                <?php } ?>>
                <?= e($attachment['file_name']); ?>
                <?php if ($is_image) { ?>
                <img class="mtop5"
                    src="<?= site_url('download/preview_image?path=' . protected_file_url_by_path($path) . '&type=' . $attachment['filetype']); ?>">
                <?php } ?>
            </a>
            <?php if ($is_image) { ?>
        </div>
        <?php } ?>
        <?php if (is_admin() || (! is_admin() && get_option('allow_non_admin_staff_to_delete_ticket_attachments') == '1')) { ?>
        <a href="<?= admin_url('tickets/delete_attachment/' . $attachment['id']); ?>"
            class="text-danger _delete">
            <?= _l('delete'); ?>
        </a>
        <hr />
        <?php } ?>
        <?php } ?>
        <?php } ?>
    </div>
</div>