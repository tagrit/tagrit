<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <hr />
          
          <div class="col-md-12"> 
            <div class="row">
              
              <div class="col-md-6">
            <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                   <i class="fa fa-filter" aria-hidden="true"></i> <?php echo _l('last_30_days'); ?>
               </button>
               <ul class="dropdown-menu width300">
                <li class="filter-group active" data-filter-group="group-date">
                    <a href="#" data-cview="last_30_days" onclick="dt_custom_view('last_30_days','','last_30_days'); return false;">
                        <?php echo _l('last_30_days'); ?>
                    </a>
                </li>
                  <li class="filter-group" data-filter-group="group-date">
                    <a href="#" data-cview="this_month" onclick="dt_custom_view('this_month','','this_month'); return false;">
                        <?php echo _l('this_month'); ?>
                    </a>
                </li>
                <li class="filter-group" data-filter-group="group-date">
                    <a href="#" data-cview="this_quarter" onclick="dt_custom_view('this_quarter','','this_quarter'); return false;">
                        <?php echo _l('this_quarter'); ?>
                    </a>
                </li>
                <li class="filter-group <?php echo (!has_permission('tasks','','view') ? ' active' : ''); ?>" data-filter-group="group-date">
                    <a href="#" data-cview="this_year" onclick="dt_custom_view('this_year','','this_year'); return false;">
                        <?php echo _l('this_year'); ?>
                    </a>
                </li>
                <li class="filter-group" data-filter-group="group-date">
                    <a href="#" data-cview="last_month" onclick="dt_custom_view('last_month','','last_month'); return false;">
                        <?php echo _l('last_month'); ?>
                    </a>
                </li>
                <li class="filter-group" data-filter-group="group-date">
                    <a href="#" data-cview="last_quarter" onclick="dt_custom_view('last_quarter','','last_quarter'); return false;">
                        <?php echo _l('last_quarter'); ?>
                    </a>
                </li>
                <li class="filter-group" data-filter-group="group-date">
                    <a href="#" data-cview="last_year" onclick="dt_custom_view('last_year','','last_year'); return false;">
                        <?php echo _l('last_year'); ?>
                    </a>
                </li>
            </ul>
            </div>
            <div class="btn-group pull-right">
               <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>
               <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                     <a href="#" onclick="printDiv(); return false;">
                     <?php echo _l('export_to_pdf'); ?>
                     </a>
                  </li>
                  <li>
                     <a href="#" onclick="printExcel(); return false;">
                     <?php echo _l('export_to_excel'); ?>
                     </a>
                  </li>
               </ul>
            </div>
              </div>
              <div class="col-md-3">
                <?php echo render_date_input('from_date','from_date'); ?>
              </div>
              <div class="col-md-3">
                <?php echo render_date_input('to_date','to_date'); ?>
              </div>
            </div>
          </div>
          <div class="row"> 
            <div class="col-md-12"> 
              <hr>
            </div>
          </div>
          <div class="page" id="DivIdToPrint">
            <?php $this->load->view('includes/'.$report); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
</body>
</html>
