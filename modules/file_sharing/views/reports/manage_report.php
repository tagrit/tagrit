<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" >
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
	                 <div class="row">
		                  <div class="col-md-4 border-right">
		                    <h4 class="no-margin font-medium"><i class="fa fa-area-chart" aria-hidden="true"></i> <?php echo _l('charts_based_report'); ?></h4>
		                    <hr />
		                    <p><a href="#" class="font-medium" onclick="init_report(this,'sharing_chart'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('sharing'); ?></a></p>
		                    <p><a href="#" class="font-medium" onclick="init_report(this,'download_chart'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('downloads'); ?></a></p>
		                 </div>
		                 <div class="col-md-4">
		                    <div class="hide" id="div_staff_filter">
	                     	<?php echo render_select('staff_filter', $staffs, array('staffid', 'firstname', 'lastname'), 'staff', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
	                     	</div>
	                     		
	                     	<div class="hide" id="div_type_filter">
					         <?php
					         $types = [['id' => 'fs_staff', 'label' => _l('fs_staff')],
					         ['id' => 'fs_client', 'label' => _l('fs_client')],
					         ['id' => 'fs_public', 'label' => _l('fs_public')]];
					          echo render_select('type', $types, array('id', 'label'), 'type', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
					      	</div>
		                 </div>
	                    <div class="col-md-4 border-right">
	                    	<div class="hide" id="div_hash_filter">
	                     	<?php echo render_select('hash_share', $hash_share, array('id', 'hash_share'), 'hash_share', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
	                     	</div>
		                     <?php $current_year = date('Y');
	                              $y0 = (int)$current_year;
	                              $y1 = (int)$current_year - 1;
	                              $y2 = (int)$current_year - 2;
	                              $y3 = (int)$current_year - 3;
	                           ?>
		                     <div class="form-group hide" id="year_requisition">
		                        <label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
		                        <select  name="year_requisition" id="year_requisition"  class="selectpicker"  data-width="100%" data-none-selected-text="<?php echo _l('filter_by').' '._l('year'); ?>">
		                              <option value="<?php echo html_entity_decode($y0); ?>" <?php echo 'selected' ?>><?php echo _l('year').' '. html_entity_decode($y0) ; ?></option>
		                              <option value="<?php echo html_entity_decode($y1); ?>"><?php echo _l('year').' '. html_entity_decode($y1) ; ?></option>
		                              <option value="<?php echo html_entity_decode($y2); ?>"><?php echo _l('year').' '. html_entity_decode($y2) ; ?></option>
		                              <option value="<?php echo html_entity_decode($y3); ?>"><?php echo _l('year').' '. html_entity_decode($y3) ; ?></option>

		                        </select>
		                     </div>
         				</div>
	                </div>
                	
			        <div class="row">
			          	<div class="col-md-12" id="container1" ></div>
			          	<div class="col-md-12" id="container2" ></div>
			        </div> 
			        <hr>
			        <div class="row">
		                <div class="col-md-6" id="container4" ></div>
			          	<div class="col-md-6" id="container3" ></div>
			        </div> 
			        <div id="report" class="hide">
			          	<div class="col-md-12">
		               		<?php $this->load->view('share_chart'); ?>
			          	</div>
			          	<div class="col-md-12">
		               		<?php $this->load->view('download_chart'); ?>
			          	</div>
		            </div>
	            </div>      
              </div>
           </div>
        </div>
     </div>
  </div>
</div>

<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/file_sharing/assets/js/reports_js.php';?>