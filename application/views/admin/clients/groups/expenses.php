<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (isset($client)) { ?>
<h4 class="customer-profile-group-heading">
    <?= _l('client_expenses_tab'); ?></h4>
<?php if (staff_can('create', 'expenses')) { ?>
<a href="<?= admin_url('expenses/expense?customer_id=' . $client->userid); ?>"
    class="btn btn-primary mbot15<?= $client->active == 0 ? ' disabled' : ''; ?>">
    <i class="fa-regular fa-plus tw-mr-1"></i>
    <?= _l('new_expense'); ?>
</a>
<?php } ?>
<div id="expenses_total" class="tw-mb-5"></div>
<?php $this->load->view('admin/expenses/table_html', [
    'class'           => 'expenses-single-client',
    'withBulkActions' => false,
]); ?>
<?php } ?>