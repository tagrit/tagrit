<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="panel_s">
			<div class="panel-body">
        <div class="row _buttons">
          <div class="col-md-6">
  				  <h4 class="no-margin text-bold ptop-15"><i class="fa fa-dashboard menu-icon"></i> <?php echo new_html_entity_decode($title); ?></h4>
          </div>
          <div class="col-md-6">
            
          <div class="_hidden_inputs _filters _tasks_filters">
              <?php
              echo form_hidden('all_time');
              echo form_hidden('last_30_days');
              echo form_hidden('this_month');
              echo form_hidden('this_quarter');
              echo form_hidden('this_year');
              echo form_hidden('last_month');
              echo form_hidden('last_quarter');
              echo form_hidden('last_year');
              ?>
          </div>

          <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="btn_filter">
              <i class="fa fa-filter" aria-hidden="true"></i> <?php echo _l('last_30_days'); ?>
            </button>
            <ul class="dropdown-menu width300">
              <li class="filter-group" data-filter-group="group-date">
                  <a href="#" data-cview="all_time" onclick="dashboard_custom_view('all_time','<?php echo _l("acc_all_time"); ?>','all_time'); return false;">
                      <?php echo _l('acc_all_time'); ?>
                  </a>
              </li>
              <li class="filter-group active" data-filter-group="group-date">
                  <a href="#" data-cview="last_30_days" onclick="dashboard_custom_view('last_30_days','<?php echo _l("last_30_days"); ?>','last_30_days'); return false;">
                      <?php echo _l('last_30_days'); ?>
                  </a>
              </li>
                <li class="filter-group" data-filter-group="group-date">
                  <a href="#" data-cview="this_month" onclick="dashboard_custom_view('this_month','<?php echo _l("this_month"); ?>','this_month'); return false;">
                      <?php echo _l('this_month'); ?>
                  </a>
              </li>
              <li class="filter-group" data-filter-group="group-date">
                  <a href="#" data-cview="this_quarter" onclick="dashboard_custom_view('this_quarter','<?php echo _l("this_quarter"); ?>','this_quarter'); return false;">
                      <?php echo _l('this_quarter'); ?>
                  </a>
              </li>
              <li class="filter-group" data-filter-group="group-date">
                  <a href="#" data-cview="this_year" onclick="dashboard_custom_view('this_year','<?php echo _l("this_year"); ?>','this_year'); return false;">
                      <?php echo _l('this_year'); ?>
                  </a>
              </li>
              <li class="filter-group" data-filter-group="group-date">
                  <a href="#" data-cview="last_month" onclick="dashboard_custom_view('last_month','<?php echo _l("last_month"); ?>','last_month'); return false;">
                      <?php echo _l('last_month'); ?>
                  </a>
              </li>
              <li class="filter-group" data-filter-group="group-date">
                  <a href="#" data-cview="last_quarter" onclick="dashboard_custom_view('last_quarter','<?php echo _l("last_quarter"); ?>','last_quarter'); return false;">
                      <?php echo _l('last_quarter'); ?>
                  </a>
              </li>
              <li class="filter-group" data-filter-group="group-date">
                  <a href="#" data-cview="last_year" onclick="dashboard_custom_view('last_year','<?php echo _l("last_year"); ?>','last_year'); return false;">
                      <?php echo _l('last_year'); ?>
                  </a>
              </li>
              <div class="clearfix"></div>
              <li class="divider"></li>
              <li class="dropdown-submenu pull-left filter-group" data-filter-group="group-date">
                 <a href="#" tabindex="-1"><?php echo _l('financial_year'); ?></a>
                 <?php $current_year = date('Y');
                    $y0 = (int)$current_year;
                    $y1 = (int)$current_year - 1;
                    $y2 = (int)$current_year - 2;
                    $y3 = (int)$current_year - 3;
                    $y4 = (int)$current_year - 4;
                 ?>
                 <ul class="dropdown-menu dropdown-menu-left">
                  <li class="filter-group" data-filter-group="group-date">
                      <a href="#" data-cview="financial_year_<?php echo new_html_entity_decode($y0); ?>" onclick="dashboard_custom_view('financial_year_<?php echo new_html_entity_decode($y0); ?>','<?php echo _l("financial_year").': '.$y0; ?>','financial_year_<?php echo new_html_entity_decode($y0); ?>'); return false;"><?php echo new_html_entity_decode($y0); ?></a>
                  </li>
                  <li class="filter-group" data-filter-group="group-date">
                      <a href="#" data-cview="financial_year_<?php echo new_html_entity_decode($y1); ?>" onclick="dashboard_custom_view('financial_year_<?php echo new_html_entity_decode($y1); ?>','<?php echo _l("financial_year").': '.$y1; ?>','financial_year_<?php echo new_html_entity_decode($y1); ?>'); return false;"><?php echo new_html_entity_decode($y1); ?></a>
                  </li>
                  <li class="filter-group" data-filter-group="group-date">
                      <a href="#" data-cview="financial_year_<?php echo new_html_entity_decode($y2); ?>" onclick="dashboard_custom_view('financial_year_<?php echo new_html_entity_decode($y2); ?>','<?php echo _l("financial_year").': '.$y2; ?>','financial_year_<?php echo new_html_entity_decode($y2); ?>'); return false;"><?php echo new_html_entity_decode($y2); ?></a>
                  </li>
                  <li class="filter-group" data-filter-group="group-date">
                      <a href="#" data-cview="financial_year_<?php echo new_html_entity_decode($y3); ?>" onclick="dashboard_custom_view('financial_year_<?php echo new_html_entity_decode($y3); ?>','<?php echo _l("financial_year").': '.$y3; ?>','financial_year_<?php echo new_html_entity_decode($y3); ?>'); return false;"><?php echo new_html_entity_decode($y3); ?></a>
                  </li>
                  <li class="filter-group" data-filter-group="group-date">
                      <a href="#" data-cview="financial_year_<?php echo new_html_entity_decode($y4); ?>" onclick="dashboard_custom_view('financial_year_<?php echo new_html_entity_decode($y4); ?>','<?php echo _l("financial_year").': '.$y4; ?>','financial_year_<?php echo new_html_entity_decode($y4); ?>'); return false;"><?php echo new_html_entity_decode($y4); ?></a>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
          </div>
        </div>
        <hr class="mtop-5">
        <div class="clearfix"></div>
        <div class="row mbot15">
          
        <div class="col-md-6 border-right">
            <div id="bank_accounts">
              
            </div>
        </div>

        <div class="col-md-6 border-right">
          <div class="horizontal-scrollable-tabs preview-tabs-top">
            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
            <div class="horizontal-tabs">
              <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                <?php foreach ($currencys as $value) { ?>
                  <li role="currency_<?php echo new_html_entity_decode($value['id']); ?>" class="<?php if($value['id'] == $currency->id){echo 'active tab_currency_default';}else{ echo 'tab_non_currency_default';} ?>">
                    <a href="#currency_<?php echo new_html_entity_decode($value['id']); ?>" onclick="change_currency_convert_status(<?php echo new_html_entity_decode($value['id']); ?>); return false;" aria-controls="currency_<?php echo new_html_entity_decode($value['id']); ?>" role="tab" data-toggle="tab">
                      <?php echo new_html_entity_decode($value['name'] .'('.$value['symbol'].')'); ?>
                    </a>
                  </li>
                <?php } ?>
              </ul>
            </div>
          </div>
          <div id="convert_status">
            
          </div>
        </div>
        </div>
        <div class="row">
          <div class="col-md-6 border-right">
            <div id="profit_and_loss" class="minwidth310">
            </div>
          </div>
          <div class="col-md-6 border-right">
            <div id="expenses_chart" class="minwidth310">
            </div>
          </div>
          <div class="col-md-6 border-right">
            <div class="horizontal-scrollable-tabs preview-tabs-top">
                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                <div class="horizontal-tabs">
                  <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                    <?php foreach ($currencys as $value) { ?>
                      <li role="currency_<?php echo new_html_entity_decode($value['id']); ?>" onclick="change_currency_income_chart(<?php echo new_html_entity_decode($value['id']); ?>); return false;" class="<?php if($value['id'] == $currency->id){echo 'active tab_currency_default';}else{ echo 'tab_non_currency_default';} ?>">
                        <a href="#currency_<?php echo new_html_entity_decode($value['id']); ?>" aria-controls="currency_<?php echo new_html_entity_decode($value['id']); ?>" role="tab" data-toggle="tab">
                          <?php echo new_html_entity_decode($value['name'] .'('.$value['symbol'].')'); ?>
                        </a>
                      </li>
                    <?php } ?>
                  </ul>
                </div>
              </div>
            <div id="income_chart" class="minwidth310">
            </div>
          </div>
          <div class="col-md-6 border-right">
            <div class="horizontal-scrollable-tabs preview-tabs-top">
                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                <div class="horizontal-tabs">
                  <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                    <?php foreach ($currencys as $value) { ?>
                      <li role="currency_<?php echo new_html_entity_decode($value['id']); ?>" onclick="change_currency_sales_chart(<?php echo new_html_entity_decode($value['id']); ?>); return false;" class="<?php if($value['id'] == $currency->id){echo 'active tab_currency_default';}else{ echo 'tab_non_currency_default';} ?>">
                        <a href="#currency_<?php echo new_html_entity_decode($value['id']); ?>" aria-controls="currency_<?php echo new_html_entity_decode($value['id']); ?>" role="tab" data-toggle="tab">
                          <?php echo new_html_entity_decode($value['name'] .'('.$value['symbol'].')'); ?>
                        </a>
                      </li>
                    <?php } ?>
                  </ul>
                </div>
              </div>
            <div id="sales_chart" class="minwidth310">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- box loading -->
<div id="box-loading"></div>
<?php init_tail(); ?>
<?php require('modules/accounting/assets/js/dashboard/manage_js.php'); ?>
</body>
</html>
