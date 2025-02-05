<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <?php $arrAtt = array();
                $arrAtt['data-type']='currency';
                ?>
          <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'journal-entry-form','autocomplete'=>'off')); ?>
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <hr />
          <div class="row">
            <div class="col-md-6">
              <?php $value = (isset($journal_entry) ? _d($journal_entry->journal_date) : _d(date('Y-m-d'))); ?>
              <?php echo render_date_input('journal_date','journal_date',$value); ?>
            </div>
            <div class="col-md-6">
              <?php $value = (isset($journal_entry) ? $journal_entry->number : $next_number); ?>
              <?php echo render_input('number','number',$value,'number'); ?>
            </div>
          </div>
          <div id="journal_entry_container"></div>
          <div class="col-md-8 col-md-offset-4">
         <table class="table text-right">
            <tbody>
                <tr>
                  <td></td>
                  <td class="text-right bold"><?php echo _l('debit'); ?></td>
                  <td class="text-right bold"><?php echo _l('credit'); ?></td>
                </tr>
               <tr>
                  <td><span class="bold"><?php echo _l('invoice_total'); ?> :</span>
                  </td>
                  <td class="total_debit">
                    <?php $value = (isset($journal_entry) ? $journal_entry->amount : 0); ?>
                    <?php echo app_format_money($value, $currency->name); ?>
                  </td>
                  <td class="total_credit">
                    <?php $value = (isset($journal_entry) ? $journal_entry->amount : 0); ?>
                    <?php echo app_format_money($value, $currency->name); ?>
                  </td>
               </tr>
            </tbody>
         </table>
        </div>
          <?php echo form_hidden('journal_entry'); ?>
          <?php echo form_hidden('amount'); ?>
          <div class="row">
            <div class="col-md-12">
              <p class="bold"><?php echo _l('dt_expense_description'); ?></p>
              <?php $value = (isset($journal_entry) ? $journal_entry->description : ''); ?>
              <?php echo render_textarea('description','',$value,array(),array(),'','tinymce'); ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">    
              <div class="modal-footer">
                <button type="button" class="btn btn-info journal-entry-form-submiter"><?php echo _l('submit'); ?></button>
              </div>
            </div>
          </div>
          <?php echo form_close(); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/accounting/assets/js/journal_entry/journal_entry_js.php';?>