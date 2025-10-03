<?php defined('BASEPATH') or exit('No direct script access allowed');?>

<?php init_head();?>
<div id="wrapper">
 <div class="content">
  <div class="row">
   <div class="col-md-12">
    <div class="panel_s">
      <div class="panel-body backdrop">
        <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
        <hr />
        <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'budget-form','autocomplete'=>'off')); ?>
        <div class="row">
          <div class="col-md-3">
            <?php echo render_select('budget', $budgets, array('id', 'name'), 'budget', '', array(), array(), '', '', false); ?>
          </div>
          <div class=" col-md-6 pull-right">
            <a href="<?php echo admin_url('accounting/budget_import'); ?>" class="btn btn-success mtop25 pull-right mleft5 <?php if(!has_permission('accounting_budget', '', 'create')){echo 'hide';} ?>"><?php echo _l('import_budget'); ?></a>
            <a href="#" onclick="creating_a_budget(); return false;" class="px-0 btn btn-info mtop25 pull-right <?php if(!has_permission('accounting_budget', '', 'create')){echo 'hide';} ?>"><?php echo _l('new'); ?></a>
          </div>
        </div>
        <div id="div_data">
          <div class="mx-auto mt-3 btn-group fc" role="group">
            <button type="button" class="button-text-transform fc-wl-monthly-button btn btn-sm btn-default active mright2"><?php echo _l('monthly') ?></button>
            <button type="button" class="button-text-transform fc-wl-quarterly-button btn btn-sm btn-default mright2"><?php echo _l('quarterly') ?></button>
            <button type="button" class="button-text-transform fc-wl-yearly-button btn btn-sm btn-default"><?php echo _l('yearly') ?></button>
            <?php echo form_hidden('view_type', 'monthly'); ?>
          </div>
          <br>
          <br>
          <div class="budget-notifi hide">
            <h4 class="text-danger"><?php echo _l('no_budget_has_been_created'); ?></h4>
          </div>
          <div id="workload"></div>
          <?php echo form_hidden('budget_data'); ?>
          <br>
          <div class="row">
            <div class=" col-md-12">
              <hr>
              <a href="#" onclick="save_budget(); return false;" class="px-0 btn btn-info display-block mleft5 pull-right"><?php echo _l('save'); ?></a>
              <a href="#" onclick="clear_budget(); return false;" class="px-0 btn btn-default display-block mleft5 pull-right"><?php echo _l('clear'); ?></a>
              <a href="#" onclick="delete_budget(); return false;" class="px-0 btn btn-danger display-block mleft5 pull-right"><?php echo _l('delete'); ?></a>
            </div>
          </div>
        </div>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<div class="modal fade" id="creating-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo _l('creating_a_budget')?></h4>
      </div>
      
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <p><?php echo _l('creating_a_budget_note_1'); ?></p>
            <?php echo _l('creating_a_budget_note_2'); ?>
            <br>
            <?php echo _l('creating_a_budget_note_3'); ?>
            <br>
            <?php echo _l('creating_a_budget_note_4'); ?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <a href="#" onclick="year_and_type(); return false;" class="px-0 btn btn-info"><?php echo _l('next'); ?></a>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="finish-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo _l('ready_to_create_your_budget')?></h4>
      </div>
      
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <p><?php echo _l('ready_to_create_your_budget_note_1'); ?></p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <a href="#" onclick="previous_year_and_type(); return false;" class="px-0 btn btn-default"><?php echo _l('previous'); ?></a>
        <a href="#" onclick="new_budget(); return false;" class="px-0 btn btn-info"><?php echo _l('fisnish'); ?></a>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="budget-exists-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo _l('budget_already_exists')?></h4>
      </div>
      
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <?php echo _l('budget_already_exists_note_1'); ?><br>
            <?php echo _l('budget_already_exists_note_2'); ?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <a href="#" onclick="previous_year_and_type(); return false;" class="px-0 btn btn-default"><?php echo _l('previous'); ?></a>
        <a href="#" onclick="update_budget(); return false;" class="px-0 btn btn-info"><?php echo _l('fisnish'); ?></a>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="year-and-type-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo _l('year_and_type')?></h4>
      </div>
      
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <?php echo render_input('fiscal_year_for_this_budget','fiscal_year_for_this_budget',date('Y'),'number'); ?>
            <?php echo _l('year_and_type_note_1'); ?>
            <br>
            
            <div class="form-group">
              <div class="radio radio-primary">
                <input type="radio" id="profit_and_loss_accounts" name="budget_type" value="profit_and_loss_accounts" checked>
                <label for="profit_and_loss_accounts"><?php echo _l('profit_and_loss_accounts'); ?></label>
              </div>

              <div class="radio radio-primary">
                <input type="radio" id="balance_sheet_accounts" name="budget_type" value="balance_sheet_accounts">
                <label for="balance_sheet_accounts"><?php echo _l('balance_sheet_accounts'); ?></label>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <a href="#" onclick="previous_creating_a_budget(); return false;" class="px-0 btn btn-default"><?php echo _l('previous'); ?></a>
        <a href="#" onclick="data_source(); return false;" class="px-0 btn btn-info"><?php echo _l('next'); ?></a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="data-source-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo _l('data_source')?></h4>
      </div>
      
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <p><?php echo _l('creating_a_budget_note_1'); ?></p>
            <div class="form-group">
              <div class="radio radio-primary">
                <input type="radio" id="create_budget_from_scratch" name="data_source" value="create_budget_from_scratch" checked>
                <label for="create_budget_from_scratch"><?php echo _l('create_budget_from_scratch'); ?></label>
                </br><?php echo _l('acc_create_budget_from_scratch_description'); ?>
              </div>

              <div class="radio radio-primary">
                <input type="radio" id="create_budget_from_prior_fiscal_year_transactions" name="data_source" value="create_budget_from_prior_fiscal_year_transactions">
                <label for="create_budget_from_prior_fiscal_year_transactions"><?php echo _l('create_budget_from_prior_fiscal_year_transactions'); ?></label>
                </br><?php echo _l('acc_create_budget_from_prior_fiscal_year_transactions_description'); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <a href="#" onclick="previous_year_and_type(); return false;" class="px-0 btn btn-default"><?php echo _l('previous'); ?></a>
        <a href="#" onclick="new_budget(); return false;" class="px-0 btn btn-info"><?php echo _l('done'); ?></a>
      </div>
    </div>
  </div>
</div>

<!-- box loading -->
<div id="box-loading"></div>
<?php init_tail();?>
<?php require 'modules/accounting/assets/js/budget/budget_js.php';?>
</body>
</html>
