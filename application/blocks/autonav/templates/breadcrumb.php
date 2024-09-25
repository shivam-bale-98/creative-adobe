<?php defined('C5_EXECUTE') or die("Access Denied.");

$navItems = $controller->getNavItems(true); // Ignore exclude from nav
$c = Page::getCurrentPage();

if (count($navItems) > 0) {
    echo '<div class="breadcrumb-wrap mb-[2rem]">'; //opens the top-level menu
    echo '<ul class="mb-0 flex justify-center ed breadcrumb-list list-unstyled d-flex flex-wrap align-items-center">';

    $firstLevel = true;
    foreach ($navItems as $ni) {
        if ($ni->isCurrent) {
            echo '<li class="active uppercase">' . $ni->name . '</li>';
        } else {
            if($ni->level == 1 && $firstLevel) {
                echo '<li>
                <a class="uppercase" href="' . $ni->url . '" target="' . $ni->target . '">
                <i class="icon-home"></i>
                </a>
                </li>
                <li class="sep relative">
                    <span></span> 
                  </li>';
                $firstLevel = false;
            } else {
            echo '<li>
                    <a class="uppercase" href="' . $ni->url . '" target="' . $ni->target . '">
                    '. $ni->name . '
                    </a>
                  </li>
                  <li class="sep relative">
                    <span></span> 
                  </li>';
            }
        }
    }
    // <i class="icon-home"></i>
    echo '</ul>';
    echo '</div>'; //closes the top-level menu
} elseif (is_object($c) && $c->isEditMode()) {
    ?>
    <div class="ccm-edit-mode-disabled-item"><?=t('Empty Auto-Nav Block.')?></div>
<?php 
}