<style type="text/css">

 .fa-hand-o-right {
  position: relative;
}
.fa-hand-o-right:before {
  content: "\f009";
  font-family: FontAwesome;
  font-style: normal;
  font-weight: normal;
  text-decoration: inherit;
/*--adjust as necessary--*/
color: #000;
font-size: 18px;
padding-right: 0.5em;
position: absolute;
/*top: 10px;*/
left: 0;
}
.underline{
 border-bottom: 1px dotted #333;
}

.number { 
   font-family: Allerta Stencil; 
}
div { 
   font-family: 'Helvetica', 'Arial', sans-serif;
}
table{
   width: 100%;
   font-size:15px;
}
.card-item{
   width: 100%; 
   line-height: 2; 
   box-sizing: border-box;
   /*height: 319px;*/
   height: 310px;
   
}
.card-item-border{
   border: 1px solid #000;
}

.align-right{
   text-align: right;
}
.align-center{
   text-align: center;
}
@media print{
  @page 
  {
     size: auto;   /* auto is the initial value */
     margin: 2mm;  /* this affects the margin in the printer settings */
  }
  .card-item {
   margin-top: 0 !important;
}
.page-break-before{
   page-break-before: always;
}
}
.padding-top-10{
   padding-top: 10px;
}
.padding-bottom-10{
   padding-bottom: 10px;
}

.company-infor-box{
   height: 50px;
}

.bank-infor-box{
   height: 50px;
}
.vendor-infor-box{
   height: 50px;
}
.signature-infor-box{
   width:150px; 
   height:50px;
}
.text-center{
   text-align: center;
}
.company-font-size{
   font-size: 13px ;
}

.bank-font-size{
   font-size: 11px ;
}
.date-font-size{
   font-size: 13px ;
}
.content-font-size{
   font-size: 12px;
}

</style>
<?php 

$routing_number_icon_a = 'a';
$routing_number_icon_b = 'a';

$bank_account_icon_a = 'a';
$bank_account_icon_b = 'a';

$current_check_no_icon_a = 'a';
$current_check_no_icon_b = 'a';

$check_type = 'type_1';


$acc_routing_number_icon_a = acc_get_option('acc_routing_number_icon_a', $company_id);
if($acc_routing_number_icon_a != ''){
   $routing_number_icon_a = $acc_routing_number_icon_a;
}
$acc_routing_number_icon_b = acc_get_option('acc_routing_number_icon_b', $company_id);
if($acc_routing_number_icon_b != ''){
   $routing_number_icon_b = $acc_routing_number_icon_b;
}

$acc_bank_account_icon_a = acc_get_option('acc_bank_account_icon_a', $company_id);
if($acc_bank_account_icon_a != ''){
   $bank_account_icon_a = $acc_bank_account_icon_a;
}
$acc_bank_account_icon_b = acc_get_option('acc_bank_account_icon_b', $company_id);
if($acc_bank_account_icon_b != ''){
   $bank_account_icon_b = $acc_bank_account_icon_b;
}

$acc_current_check_no_icon_a = acc_get_option('acc_current_check_no_icon_a', $company_id);
if($acc_current_check_no_icon_a != ''){
   $current_check_no_icon_a = $acc_current_check_no_icon_a;
}
$acc_current_check_no_icon_b = acc_get_option('acc_current_check_no_icon_b', $company_id);
if($acc_current_check_no_icon_b != ''){
   $current_check_no_icon_b = $acc_current_check_no_icon_b;
}

$acc_check_type = acc_get_option('acc_check_type', $company_id);
if($acc_check_type != ''){
   $check_type = $acc_check_type;
}


$i = 0;

for ($i=0; $i < $count; $i++) { 

   if($i != 0){
     ?>
     <!-- <div> -->
      <!-- <br> -->
      <br>
   <!-- </div> -->
<?php }
$page_break_before = '';
if($i % 3 == 0 && $i != 0){
   $page_break_before = ' page-break-before';
} 
$i++;?>
<?php 
   $class_card_item = ' card-item-border';
   $show_label_name = 1;
   $underline_class_name = 'underline';
   
   $class_card_item = '';
   $show_label_name = 0;
   $underline_class_name = '';

 ?>
<table cellpadding="15" class="card-item<?php echo  new_html_entity_decode($class_card_item); ?> <?php echo new_html_entity_decode($page_break_before); ?>">
   <tbody>
      <tr>
         <td>
            <table>
               <tbody>
                  <tr>
                     <td width="30%" colspan="3" class="company-infor-box text-center company-font-size">
                        <span style="font-weight:bold; font-family: Roboto;">XXXXX</span>
                        <br>
                        <span>XXXXX</span>
                        <br>
                        <span>XXXXX</span>
                     </td>
                     <td width="30%" colspan="3" style="vertical-align: top;" class="bank-infor-box text-center bank-font-size">
                        <span style="font-weight:bold; font-family: Roboto;">XXXXX</span>
                        <br>
                        <span>XXXXX</span>
                        <br>
                        <span>XXXXX</span>
                     </td>
                     <td style="vertical-align: top; text-align: right; font-size:20px; font-family: Roboto;" width="40%" colspan="4">
                        <span style=" font-weight:bold; font-size:15px;"></span><span>
                        XXXX</span>
                     </td>
                  </tr>
                  <tr>
                     <td width="10%">
                     </td>
                     <td width="10%">
                     </td>
                     <td width="10%">
                     </td>
                     <td width="10%">
                     </td>
                     <td width="10%">
                     </td>
                     <td width="10%">
                     </td>
                     <td width="10%">
                     </td>
                     <td width="10%" style="text-align: right;" class="date-font-size">
                        <?php if($show_label_name == 1){ ?>
                           <span style="text-align: right; font-weight:bold; font-family: Roboto;"><?php echo _l('acc_date'); ?>: </span>
                        <?php } ?>
                     </td>
                     <td style="text-align: right; font-family: Roboto;" width="20%" class =" content-font-size <?php echo new_html_entity_decode($underline_class_name); ?>" colspan="2">
                        <span style="white-space: nowrap;">
                        XXXXX</span>
                     </td>
                  </tr>
                  <tr>
                     <td width="15%" colspan="1" class="company-font-size">
                        <?php if($show_label_name == 1){ ?>
                           <span style="text-align: right; font-weight:bold; text-transform: uppercase; font-family: Roboto;"><?php echo _l('pay_to_the_order_of'); ?></span>
                        <?php } ?>
                     </td>
                     <td  width="55%" colspan="6" class=" content-font-size <?php echo new_html_entity_decode($underline_class_name); ?>"><br>
                        <span style="font-family: Roboto;">
                        XXXXX</span>
                     </td>
                     <td width="10%" style="text-align: right; font-family: Roboto;"  class=" company-font-size">
                        <?php if($show_label_name == 1){ ?>
                           <span style="font-weight:bold;"><?php echo new_html_entity_decode($currency->symbol); ?> </span>
                        <?php } ?>
                     </td>
                     <td style="text-align: right; font-family: Roboto;" width="20%" class="content-font-size <?php echo new_html_entity_decode($underline_class_name); ?>" colspan="2"><br><br>
                        <span style="font-family: Roboto;">
                           XXXXX
                        </span>
                     </td>
                  </tr>
                  <tr>
                     <td colspan="8" class=" content-font-size <?php echo new_html_entity_decode($underline_class_name); ?>" width="80%" style="line-height: 2;">
                        <span style="width: 200px; font-family: Roboto;">XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</span>
                     </td>
                     <td width="15%" colspan="2" class=" company-font-size">
                        <?php if($show_label_name == 1){ ?>
                           Dollars
                        <?php } ?>
                     </td>
                  </tr>
                  <tr>
                     <td width="10%">
                     </td>
                     <td width="50%" colspan="5" class="vendor-infor-box content-font-size">
                           <br>
                        <?php if($check_type == 'type_3' || $check_type == 'type_1' || $check_type == ''){ ?>
                           <span>XXXXXXXXXXXX</span>
                           <br>
                           <span>XXXXXXXXXXXXXXXXXXXXXX</span>
                           <br>
                           <span>XXXXXXXXXXXXXXXXXXXXXX</span>
                        <?php } ?>
                           <br>
                     </td>
                     <td width="10%">
                     </td>
                        <?php if($check_type == 'type_3' || $check_type == 'type_4'){ ?>
                           <td width="30%" colspan="3" class="signature-infor-box <?php echo new_html_entity_decode($underline_class_name); ?>">
                            <span style="font-family: Roboto;">XXXXXXXXXXXXXX</span>
                           </td>
                        <?php } ?>
                  </tr>
                  <tr>
                     <td width="10%" class="company-font-size">
                        <?php if($show_label_name == 1){ ?>
                           <span style="font-weight:bold; font-family: Roboto;"><?php echo _l('acc_memo') ?></span>
                        <?php } ?>
                     </td>
                     <td width="50%" colspan="5" class="content-font-size <?php echo new_html_entity_decode($underline_class_name); ?>">
                        <span style="font-family: Roboto;">XXXXX</span>
                     </td>
                     <td width="10%">
                     </td>
                     <td width="30%" colspan="3" class="signature-infor-box <?php echo new_html_entity_decode($underline_class_name); ?>">
                      <span style="font-family: Roboto;">XXXXXXXXXXXXXX</span>
                   </td>
                </tr>
                <tr>
                  <td width="100%" colspan="10" style="font-size:18px; font-family: monospace,Arial,serif;">
                     <?php if($current_check_no_icon_a != ''){ ?>
                        <img width="13" class="exam-icon exam-icon-a" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_'.$current_check_no_icon_a.'.svg'); ?>" alt="img">
                     <? } ?>
                     <span>XXXX</span>   
                     <?php if($current_check_no_icon_b != '' && $current_check_no_icon_b != 'e'){ ?>
                        <img width="13" class="exam-icon exam-icon-b" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_'.$current_check_no_icon_b.'.svg'); ?>" alt="img">
                     <? } ?>
                     <span>&nbsp;&nbsp;</span>
                     <?php if($routing_number_icon_a != '' && $routing_number_icon_a != 'e'){ ?>
                        <img width="13" class="exam-icon exam-icon-a" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_'.$routing_number_icon_a.'.svg'); ?>" alt="img">
                     <? } ?>
                     <span>XXXXXXXXX</span>
                     <?php if($routing_number_icon_b != '' && $routing_number_icon_b != 'e'){ ?>
                        <img width="13" class="exam-icon exam-icon-b" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_'.$routing_number_icon_b.'.svg'); ?>" alt="img">
                     <? } ?>
                     <span>&nbsp;&nbsp;</span>
                     <?php if($bank_account_icon_a != '' && $bank_account_icon_a != 'e'){ ?>
                     <img width="13" class="exam-icon exam-icon-a" data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_'.$bank_account_icon_a.'.svg'); ?>" alt="img">
                     <? } ?>
                     <span>XXXXXXXXXX</span>
                     <?php if($bank_account_icon_b != '' && $bank_account_icon_b != 'e'){ ?>
                     <img width="13" class="exam-icon exam-icon-b" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_'.$bank_account_icon_b.'.svg'); ?>" alt="img">
                     <? } ?>

                  </td>
               </tr>
            </tbody>
         </table>
      </td>
   </tr>
</tbody>
</table>
<?php } ?>