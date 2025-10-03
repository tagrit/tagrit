<input type="hidden" name="id">
<?php echo render_input('name', 'name'); ?>
<?php echo render_textarea( 'address', 'client_address','', array('rows' => 7)); ?>
<?php echo render_input( 'city', 'client_city'); ?>
<?php echo render_input( 'state', 'client_state'); ?>

<?php echo render_input( 'zip', 'client_postal_code'); ?>
<?php 
$countries = get_all_countries();
echo render_select( 'country',$countries,array( 'country_id',array( 'short_name')), 'country','',array('data-none-selected-text'=>_l('dropdown_non_selected_tex'))); ?>
<?php echo render_textarea('notes','notes') ?>
