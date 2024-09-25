<?php

use Application\Concrete\Helpers\ImageHelper;
$themePath = $this->getThemePath();
defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
$themePath = $this->getThemePath();
$detect   = new Mobile_Detect();
$isMobile = $detect->isMobile() && !$detect->isTablet();
/** @var \Concrete\Core\Utility\Service\Text $th */
$th = Core::make('helper/text');
/** @var \Concrete\Core\Localization\Service\Date $dh */
$dh = Core::make('helper/date');
$ih = new ImageHelper();
$page = reset($pages);
$i = 1;
if($page) {
    $parent = \Concrete\Core\Page\Page::getByID($page->getCollectionParentID());
    $parentPageTitle = $parent->getCollectionName();
    $parentURL = $parent->getCollectionLink();
}

    if ($c->isEditMode() && $controller->isBlockEmpty()) {
        ?>
        <div class="ccm-edit-mode-disabled-item"><?php echo t('Empty Page List Block.') ?></div>
        <?php
    } else { ?>
        <div class="flex flex-col items-start justify-center mt-[6rem] sm:mt-[11.8rem] xl:mt-[18.8rem] mb-[4.9rem] md:mb-0">
            <?php if (isset($pageListTitle) && $pageListTitle): ?>
                <h3 class="text-black w-96 md:w-auto mb-[4rem]"><?=h($pageListTitle)?></h3>
            <?php endif; ?>
            <ul>
                <?php
                    foreach ($pages as $page) {
                        // Prepare data for each page being listed...
                        $title = $page->getCollectionName();
                        if ($page->getCollectionPointerExternalLink() != '') {
                            $url = $page->getCollectionPointerExternalLink();
                            if ($page->openCollectionPointerExternalLinkInNewWindow()) {
                                $target = '_blank';
                            }
                        } else {
                            $url = $page->getCollectionLink();
                            $target = $page->getAttribute('nav_target');
                        }
                        $target = empty($target) ? '_self' : $target;
                        ?>

                        <li class="mb-[3rem]">
                            <a class="text-rustic-red transition-opacity md:opacity-30 md:hover:opacity-100 md:hover:text-black hover:no-underline text-[2rem] uppercase  md:text-[2.4rem]" href="<?php echo h($url); ?>"><span class="relative md:top-[-2.9rem] top:-[0.6rem] text-[1.6rem] md:text-[1.4rem] mr-[2.8rem]">0<?php echo h($i); ?></span><?php  echo h($title);?></a>
                        </li>
                <?php  $i = $i +1; } ?>
            </ul>
        </div>
<?php } ?>