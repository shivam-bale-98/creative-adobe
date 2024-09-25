<?php defined('C5_EXECUTE') or die('Access Denied.');
if (isset($pages) && $pages) {
    foreach ($pages as $page) {
        $title = $page->getCollectionName();
        $url = ($page->isExternalLink()) ? $page->getCollectionPointerExternalLink() : $page->getCollectionLink();
        $menuImage = $page->getImageByAttributeHandle('menu_image');
        $displayImage = $page->getAttribute('display_menu_image');
?>
        <a tabindex="0" role="link" aria-label="<?php echo $title; ?>" class="menu-card bg-white flex items-center gap-[2rem] rounded-[1rem] overflow-hidden <?php echo ($page->isExternalLink()) ? 'view-all-link' : ''; ?>" href="<?php echo $url; ?>">
            <? //php if (!is_null($displayImage) && $displayImage) {
            ?>
            <div class="img-wrap rounded-[1rem] overflow-hidden relative  transition-all">
                <img class="absolute object-contain" src="<?php echo $menuImage; ?>" alt="<?php echo $title; ?>">
            </div>
            <? //php }
            ?>
            <div class="content">
                <p class=""><?php echo $title; ?></p>
            </div>
        </a>
<?php
    }
}
?>