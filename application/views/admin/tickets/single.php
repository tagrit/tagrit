<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php set_ticket_open($ticket->adminread, $ticket->ticketid); ?>
<div id="wrapper">
    <div class="tw-h-full">
        <div class="sm:tw-flex tw-h-full">
            <div class="sm:tw-w-2/3">
                <div class="tw-h-full">
                    <div
                        class="tw-py-4 tw-px-6 tw-bg-gradient-to-r tw-from-neutral-50 tw-to-white tw-border-b tw-border-solid tw-border-neutral-300 tw-sticky tw-top-0 tw-z-20 sm:tw-h-[61px]">
                        <div class="sm:tw-flex sm:tw-items-center sm:tw-justify-between sm:tw-space-x-4 rtl:tw-space-x-reverse"
                            id="ticketLeftInformation">
                            <div
                                class="sm:tw-flex sm:tw-items-center sm:tw-space-x-3 tw-mb-2 sm:tw-mb-0 rtl:tw-space-x-reverse">
                                <h3 class="tw-font-bold tw-text-lg tw-my-0 tw-max-w-full sm:tw-max-w-lg sm:tw-truncate"
                                    title="<?= $ticket->subject; ?>">
                                    <span
                                        id="ticket_subject">#<?= e($ticket->ticketid); ?>
                                        -
                                        <?= e($ticket->subject); ?>
                                    </span>
                                </h3>
                                <div class="dropdown">
                                    <a href="#"
                                        class="dropdown-toggle single-ticket-status-label label tw-inline-flex tw-items-center tw-gap-1 tw-flex-nowrap hover:tw-opacity-80 tw-align-middle"
                                        style="color:<?= $ticket->statuscolor; ?>;border:1px solid <?= adjust_hex_brightness($ticket->statuscolor, 0.4); ?>;background: <?= adjust_hex_brightness($ticket->statuscolor, 0.04); ?>;"
                                        id="ticketStatusDropdown" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <?= ticket_status_translate($ticket->ticketstatusid); ?>
                                        <i class="chevron tw-shrink-0"></i>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="ticketStatusDropdown">
                                        <?php foreach ($statuses as $status) {
                                            if (! is_array($status)) {
                                                continue;
                                            } ?>
                                        <li>
                                            <a href="#" class="change-ticket-status"
                                                data-status="<?= $status['ticketstatusid']; ?>">
                                                <?= $status['name']; ?>
                                            </a>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>

                            <div class="tw-space-x-3 tw-inline-flex tw-items-center rtl:tw-space-x-reverse">
                                <?php if (get_option('disable_ticket_public_url') == '0') { ?>
                                <a href="<?= get_ticket_public_url($ticket); ?>"
                                    data-toggle="tooltip" data-placement="bottom"
                                    data-title="<?= _l('view_public_form'); ?>"
                                    class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700"
                                    target="_blank">
                                    <i class="fa-solid fa-up-right-from-square"></i>
                                </a>
                                <?php } ?>
                                <?php if (can_staff_delete_ticket()) { ?>
                                <a href="<?= admin_url('tickets/delete/' . $ticket->ticketid); ?>"
                                    data-toggle="tooltip" data-placement="bottom"
                                    data-title="<?= _l('delete', _l('ticket_lowercase')); ?>"
                                    class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
                                    <i class="fa-regular fa-trash-can"></i>
                                </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="tw-pb-8 tw-pt-4 tw-px-6">
                        <div class="!tw-space-y-3 tw-mb-6">
                            <div
                                class="alert alert-warning staff_replying_notice<?= ($ticket->staff_id_replying === null || $ticket->staff_id_replying === get_staff_user_id()) ? ' hide' : '' ?>">
                                <?php if ($ticket->staff_id_replying !== null && $ticket->staff_id_replying !== get_staff_user_id()) { ?>
                                <p class="tw-font-medium">
                                    <?= e(_l('staff_is_currently_replying', get_staff_full_name($ticket->staff_id_replying))); ?>
                                </p>
                                <?php } ?>
                            </div>

                            <?php if (count($merged_tickets) > 0) { ?>
                            <div class="alert alert-info">
                                <h4 class="alert-title">
                                    <?= _l('ticket_merged_tickets_header', count($merged_tickets)) ?>
                                </h4>
                                <ul>
                                    <?php foreach ($merged_tickets as $merged_ticket) { ?>
                                    <a href="<?= admin_url('tickets/ticket/' . $merged_ticket['ticketid']) ?>"
                                        class="alert-link">
                                        #<?= $merged_ticket['ticketid'] ?>
                                        -
                                        <?= $merged_ticket['subject'] ?>
                                    </a>
                                    <?php } ?>
                                </ul>
                            </div>
                            <?php } ?>

                            <?php if ($ticket->merged_ticket_id !== null) { ?>
                            <div class="alert alert-info" role="alert">
                                <div class="tw-flex tw-justify-between tw-items-center">
                                    <p class="tw-font-semibold tw-mb-0">
                                        <?= _l('ticket_merged_notice'); ?>:
                                        <?= e($ticket->merged_ticket_id); ?>
                                    </p>
                                    <a href="<?= admin_url('tickets/ticket/' . $ticket->merged_ticket_id); ?>"
                                        class="alert-link">
                                        <?= _l('view_primary_ticket'); ?>
                                    </a>
                                </div>
                            </div>
                            <?php } ?>
                        </div>

                        <div class="horizontal-scrollable-tabs tw-mb-3">
                            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-segmented nav-tabs-horizontal tw-mb-2" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#addreply" aria-controls="addreply" role="tab" data-toggle="tab">
                                            <i class="fa-solid fa-reply menu-icon"></i>
                                            <?= _l('ticket_single_add_reply'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#note" aria-controls="note" role="tab" data-toggle="tab">
                                            <i class="fa-regular fa-note-sticky menu-icon"></i>
                                            <?= _l('ticket_single_add_note'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#tab_reminders"
                                            onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?= $ticket->ticketid; ?> + '/' + 'ticket', undefined, undefined, undefined,[1,'asc']); return false;"
                                            aria-controls="tab_reminders" role="tab" data-toggle="tab">
                                            <i class="fa-regular fa-bell menu-icon"></i>
                                            <?= _l('ticket_reminders'); ?>
                                            <?php
                                                 $total_reminders = total_rows(
                                                     db_prefix() . 'reminders',
                                                     [
                                                         'isnotified' => 0,
                                                         'staff'      => get_staff_user_id(),
                                                         'rel_type'   => 'ticket',
                                                         'rel_id'     => $ticket->ticketid,
                                                     ]
                                                 ); ?>

                                            <?php if ($total_reminders > 0) { ?>
                                            <span class="badge">
                                                <?= $total_reminders; ?>
                                            </span>
                                            <?php } ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#othertickets" onclick="init_table_tickets(true);"
                                            aria-controls="othertickets" role="tab" data-toggle="tab">
                                            <i class="fa-regular fa-life-ring menu-icon"></i>
                                            <?= _l('ticket_single_other_user_tickets'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#tasks"
                                            onclick="init_rel_tasks_table(<?= e($ticket->ticketid); ?>,'ticket'); return false;"
                                            aria-controls="tasks" role="tab" data-toggle="tab">
                                            <i class="fa-regular fa-circle-check menu-icon"></i>
                                            <?= _l('tasks'); ?>
                                        </a>
                                    </li>
                                    <?php do_action_deprecated('add_single_ticket_tab_menu_item', $ticket, '3.0.7', 'after_admin_single_ticket_tab_menu_last_item'); ?>
                                    <?php hooks()->do_action('after_admin_single_ticket_tab_menu_last_item', $ticket); ?>
                                </ul>
                            </div>
                        </div>

                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="tab-content">
                                    <?php $this->load->view('admin/tickets/partials/ticket-tabpanel-add-reply'); ?>
                                    <?php $this->load->view('admin/tickets/partials/ticket-tabpanel-notes'); ?>
                                    <?php $this->load->view('admin/tickets/partials/ticket-tabpanel-reminders'); ?>
                                    <?php $this->load->view('admin/tickets/partials/ticket-tabpanel-other-tickets'); ?>
                                    <?php $this->load->view('admin/tickets/partials/ticket-tabpanel-tasks'); ?>

                                    <?php hooks()->do_action('after_admin_single_ticket_tab_menu_last_content', $ticket); ?>
                                </div>
                            </div>
                        </div>

                        <h4 class="tw-mt-6 tw-mb-4 tw-font-bold tw-text-lg">
                            <?= _l('ticket_request_history'); ?>
                        </h4>

                        <?php $this->load->view('admin/tickets/partials/ticket-history-message'); ?>
                        <?php $this->load->view('admin/tickets/partials/ticket-history-replies'); ?>
                    </div>
                </div>
            </div>
            <div class="sm:tw-flex-1 sm:tw-w-1/3 tw-relative">
                <div class="tw-px-6 sm:tw-px-0">
                    <div class="ticket-right-column tw-h-full tw-bg-white tw-overflow-y-auto tw-border ltr:sm:tw-border-l sm:tw-border-r-0 sm:tw-border-y-0 tw-border-solid tw-border-neutral-300 sm:tw-fixed sm:tw-top-[58px] tw-rounded-md sm:tw-rounded-none rtl:sm:tw-border-r"
                        id="ticketDetails">
                        <div
                            class="tw-py-4 tw-px-6 tw-bg-white tw-border-b tw-border-solid tw-border-neutral-300 tw-sticky tw-top-0 tw-z-20 tw-h-[59.5px] tw-flex tw-items-center tw-space-x-2 rtl:tw-space-x-reverse">
                            <h4
                                class="tw-font-bold tw-text-lg tw-my-0 sm:tw-truncate sm:tw-max-w-[240px] tw-flex tw-items-center tw-gap-x-2">
                                <?= _l('clients_single_ticket_information_heading'); ?>
                            </h4>
                            <a href="#" class="btn btn-primary btn-sm save_changes_settings_single_ticket">
                                <?= _l('submit'); ?>
                            </a>
                        </div>
                        <div class="tw-py-4 tw-px-6">
                            <?php if ($ticket->project_id) { ?>
                            <p class="tw-text-base tw-font-medium tw-mb-6">
                                <?= _l('ticket_linked_to_project', '<a href="' . admin_url('projects/view/' . $ticket->project_id) . '">' . e(get_project_name_by_id($ticket->project_id)) . '</a>'); ?>
                            </p>
                            <?php } ?>
                            <?php $this->load->view('admin/tickets/partials/ticket-settings'); ?>
                        </div> <!-- end padding -->
                    </div>
                </div>
            </div>
            <?php if (count($ticket_replies) > 1) { ?>
            <a href="#top" id="toplink">↑</a>
            <a href="#bot" id="botlink">↓</a>
            <?php } ?>
        </div>
    </div>

    <?php if (can_staff_edit_ticket_message()) {?>
    <!-- Edit Ticket Messsage Modal -->
    <div class="modal fade" id="ticket-message" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <?= form_open(admin_url('tickets/edit_message')); ?>
            <div class="modal-content">
                <div id="edit-ticket-message-additional"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <?= _l('ticket_message_edit'); ?>
                    </h4>
                </div>
                <div class="modal-body">
                    <?= render_textarea('data', '', '', [], [], '', 'tinymce-ticket-edit'); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?= _l('close'); ?></button>
                    <button type="submit"
                        class="btn btn-primary"><?= _l('submit'); ?></button>
                </div>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
    <?php } ?>
    <script>
        var _ticket_message;
    </script>
    <?php $this->load->view('admin/tickets/services/service'); ?>
    <?php init_tail(); ?>
    <?php hooks()->do_action('ticket_admin_single_page_loaded', $ticket); ?>
    <script>
        $(function() {
            var content = document.getElementById('ticketDetails');
            var header = document.getElementById('header');

            function makeTicketDetailsSticky() {
                var scrolledPixels = getHeaderScrolledPixels();

                if (scrolledPixels > 0) {
                    content.style.top = ((header.clientHeight + 1) - scrolledPixels) + 'px'
                } else {
                    content.style.top = null;
                }
            }

            function setTicketSubjectMaxWidthBasedOnFreeSpace() {
                const parent = document.getElementById(
                    'ticketLeftInformation');

                const children = parent.querySelectorAll('div');

                if (children.length < 3) {
                    console.error('There must be at least three child divs.');
                    return;
                }

                const firstSibling = children[1];
                const targetElement = children[0].firstChild.nextSibling;
                const lastSibling = children[2];

                const parentWidth = parent.clientWidth;

                // Calculate the width of the siblings
                const firstSiblingWidth = firstSibling.offsetWidth + getTotalHorizontalMargin(firstSibling);
                const lastSiblingWidth = lastSibling.offsetWidth + getTotalHorizontalMargin(lastSibling);

                // Calculate the max width for the target element
                const maxWidth = parentWidth - firstSiblingWidth - lastSiblingWidth;

                targetElement.style.maxWidth = `${maxWidth}px`;
            }

            if (!is_mobile()) {
                setTicketSubjectMaxWidthBasedOnFreeSpace()
                makeTicketDetailsSticky()
                window.addEventListener('resize', setTicketSubjectMaxWidthBasedOnFreeSpace);

                window.addEventListener('scroll', makeTicketDetailsSticky);
            }

            $('#single-ticket-form').appFormValidator();

            init_ajax_search('contact', '#contactid.ajax-search', {
                tickets_contacts: true
            });

            init_ajax_search('project', 'select[name="project_id"]', {
                customer_id: function() {
                    return $('input[name="userid"]').val();
                }
            });

            $('body').on('shown.bs.modal', '#_task_modal', function() {
                if (typeof(_ticket_message) != 'undefined') {
                    // Init the task description editor
                    if (!is_mobile()) {
                        $(this).find('#description').click();
                    } else {
                        $(this).find('#description').focus();
                    }
                    setTimeout(function() {
                        tinymce.get('description').execCommand('mceInsertContent', false,
                            _ticket_message);
                        $('#_task_modal input[name="name"]').val($('#ticket_subject').text()
                            .replace(/\s+/g, ' ').trim());
                    }, 100);
                }
            });

            var editorMessage = tinymce.get('message');

            if (typeof(editorMessage) != 'undefined') {
                var firstTypeCheckPerformed = false;

                editorMessage.on('change', function() {
                    if (!firstTypeCheckPerformed) {
                        // make AJAX Request
                        $.get(admin_url +
                            'tickets/check_staff_replying/<?= e($ticket->ticketid); ?>',
                            function(result) {
                                var data = JSON.parse(result)
                                if (data.is_other_staff_replying === true || data
                                    .is_other_staff_replying === 'true') {
                                    $('.staff_replying_notice').html('<p>' + data.message +
                                        '</p>');
                                    $('.staff_replying_notice').removeClass('hide');
                                } else {
                                    $('.staff_replying_notice').addClass('hide');
                                }
                            });

                        firstTypeCheckPerformed = true;
                    }

                    $.post(admin_url +
                        'tickets/update_staff_replying/<?= e($ticket->ticketid); ?>/<?= get_staff_user_id()?>'
                    );
                });

                $(document).on('pagehide, beforeunload', function() {
                    $.post(admin_url +
                        'tickets/update_staff_replying/<?= e($ticket->ticketid); ?>'
                    );
                })

                $(document).on('visibilitychange', function() {
                    if (document.visibilityState === 'visible' || (editorMessage.getContent()
                            .trim() !=
                            ''))
                        return;
                    $.post(admin_url +
                        'tickets/update_staff_replying/<?= e($ticket->ticketid); ?>'
                    );
                })
            }
        });


        var Ticket_message_editor;
        var edit_ticket_message_additional = $('#edit-ticket-message-additional');

        function edit_ticket_message(id, type) {
            edit_ticket_message_additional.empty();
            // type is either ticket or reply
            _ticket_message = $('[data-' + type + '-id="' + id + '"]').html();
            init_ticket_edit_editor();
            $('#ticket-message').modal('show');
            setTimeout(function() {
                tinyMCE.activeEditor.setContent(_ticket_message);
            }, 1000)
            edit_ticket_message_additional.append(hidden_input('type', type));
            edit_ticket_message_additional.append(hidden_input('id', id));
            edit_ticket_message_additional.append(hidden_input('main_ticket', $('input[name="ticketid"]').val()));
        }

        function init_ticket_edit_editor() {
            if (typeof(Ticket_message_editor) !== 'undefined') {
                return true;
            }
            Ticket_message_editor = init_editor('.tinymce-ticket-edit');
        }

        <?php if (staff_can('create', 'tasks')) { ?>
        function convert_ticket_to_task(id, type) {
            if (type == 'ticket') {
                _ticket_message = $('[data-ticket-id="' + id + '"]').html();
            } else {
                _ticket_message = $('[data-reply-id="' + id + '"]').html();
            }
            var new_task_url = admin_url +
                'tasks/task?rel_id=<?= e($ticket->ticketid); ?>&rel_type=ticket&ticket_to_task=true';
            new_task(new_task_url);
        }
        <?php } ?>
    </script>
    </body>

    </html>