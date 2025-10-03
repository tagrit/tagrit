<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<a href="<?= site_url('clients/open_ticket?project_id=' . $project->id); ?>"
	class="btn btn-primary mbot15">
	<?= _l('clients_ticket_open_subject'); ?>
</a>
<?php get_template_part('tickets_table'); ?>