<table class="table dt-table table-proposals" data-order-col="3" data-order-type="desc">
  <thead>
    <tr>
      <th class="th-proposal-number">
        <?= _l('proposal') . ' #'; ?>
      </th>
      <th class="th-proposal-subject">
        <?= _l('proposal_subject'); ?>
      </th>
      <th class="th-proposal-total">
        <?= _l('proposal_total'); ?></th>
      <th class="th-proposal-open-till">
        <?= _l('proposal_open_till'); ?>
      </th>
      <th class="th-proposal-date">
        <?= _l('proposal_date'); ?></th>
      <th class="th-proposal-status">
        <?= _l('proposal_status'); ?></th>
      <?php
      $custom_fields = get_custom_fields('proposal', ['show_on_client_portal' => 1]);

        foreach ($custom_fields as $field) { ?>
      <th><?= e($field['name']); ?></th>
      <?php } ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($proposals as $proposal) { ?>
    <tr>
      <td>
        <a href="<?= site_url('proposal/' . $proposal['id'] . '/' . $proposal['hash']); ?>"
          class="td-proposal-url">
          <?= e(format_proposal_number($proposal['id'])); ?>
          <?php
              if ($proposal['invoice_id']) {
                  echo '<br /><span class="text-success proposal-invoiced">' . _l('estimate_invoiced') . '</span>';
              }
        ?>
        </a>
      <td>
        <a href="<?= site_url('proposal/' . $proposal['id'] . '/' . $proposal['hash']); ?>"
          class="td-proposal-url-subject">
          <?= e($proposal['subject']); ?>
        </a>
        <?php
        if ($proposal['invoice_id'] != null && has_contact_permission('invoices')) {
            $invoice = $this->invoices_model->get($proposal['invoice_id']);
            echo '<br /><a href="' . site_url('invoice/' . $invoice->id . '/' . $invoice->hash) . '" target="_blank" class="td-proposal-invoice-url">' . e(format_invoice_number($invoice->id)) . '</a>';
        } elseif ($proposal['estimate_id'] != null && has_contact_permission('estimates')) {
            $estimate = $this->estimates_model->get($proposal['estimate_id']);
            if (get_option('exclude_estimate_from_client_area_with_draft_status') == 0 || $estimate->status != 1) {
                echo '<br /><a href="' . site_url('estimate/' . $estimate->id . '/' . $estimate->hash) . '" target="_blank" class="td-proposal-estimate-url">' . e(format_estimate_number($estimate->id)) . '</a>';
            }
        }
        ?>
      </td>
      <td
        data-order="<?= e($proposal['total']); ?>">
        <?php
          if ($proposal['currency'] != 0) {
              echo e(app_format_money($proposal['total'], get_currency($proposal['currency'])));
          } else {
              echo e(app_format_money($proposal['total'], get_base_currency()));
          }
        ?>
      </td>
      <td
        data-order="<?= e($proposal['open_till']); ?>">
        <?= e(_d($proposal['open_till'])); ?>
      </td>
      <td
        data-order="<?= e($proposal['date']); ?>">
        <?= e(_d($proposal['date'])); ?>
      </td>
      <td>
        <?= format_proposal_status($proposal['status']); ?>
      </td>
      <?php foreach ($custom_fields as $field) { ?>
      <td>
        <?= get_custom_field_value($proposal['id'], $field['id'], 'proposal'); ?>
      </td>
      <?php } ?>
    </tr>
    <?php } ?>
  </tbody>
</table>