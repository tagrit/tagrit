<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="horizontal-scrollable-tabs panel-full-width-tabs">
    <div class="scroller arrow-left tw-mt-px"><i class="fa fa-angle-left"></i></div>
    <div class="scroller arrow-right tw-mt-px"><i class="fa fa-angle-right"></i></div>
    <div class="horizontal-tabs tw-bg-neutral-100">
        <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
            <?php
            // Define tab data as an array for better maintainability
            $tabs = [
                ['id' => 'cpanel', 'label' => _l('perfex_saas_cpanel')],
                ['id' => 'plesk', 'label' => _l('perfex_saas_plesk')],
                ['id' => 'mysql_root', 'label' => _l('perfex_saas_mysql_root')],
            ];

            $activeTab = 'cpanel';

            // Loop through the tabs to generate navigation
            foreach ($tabs as $tab) {
                $isActive = ($tab['id'] === $activeTab) ? 'active' : '';
            ?>
            <li role="presentation" class="<?php echo $isActive; ?>">
                <a href="#<?php echo $tab['id']; ?>" aria-controls="<?php echo $tab['id']; ?>" role="tab"
                    data-toggle="tab">
                    <?php echo $tab['label']; ?>
                </a>
            </li>
            <?php
            }
            ?>
        </ul>
    </div>
</div>

<div class="tab-content mtop30">
    <?php
    // Loop through the tab content files
    foreach ($tabs as $tab) {
        $tabId = $tab['id'];
        $tabFile = !empty($tab['file']) ? $tab['file'] : 'integrations/' . $tabId . '.php';
        $isActive = ($tabId === $activeTab) ? 'active' : '';
    ?>
    <div role="tabpanel" class="tab-pane <?php echo $isActive; ?>" id="<?php echo $tabId; ?>">
        <?php require($tabFile); ?>
    </div>
    <?php
    }
    ?>
</div>