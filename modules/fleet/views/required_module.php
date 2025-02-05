<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
        <div class="panel_s">
           <div class="panel-body">
              <div>
                 <div class="tab-content">
                     <div class="col-md-4">
                      <div class="panel_s">
                        <div class="panel-heading">
                          <h4><?php echo _l('required'); ?></h4>
                        </div>
                        <div class="panel-body">
                          <table class="table table-striped  no-margin">
                            <tbody>
                              <?php foreach($required as $key => $active){ ?>
                                 <?php $value = (($active == 1) ? _l('yes') : _l('no')); ?>
                              <?php $text_class = (($active == 1) ? 'text-success' : 'text-danger'); ?>
                                <tr class="project-overview">
                                  <td width="60%"><a href="<?php echo site_url('admin/modules'); ?>" class="invoice-number"><?php echo _l($key); ?></a></td>
                                 <td class="text-right <?php echo new_html_entity_decode($text_class) ; ?>"><?php echo new_html_entity_decode($value) ; ?></td>
                               </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
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