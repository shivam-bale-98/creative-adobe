<?php defined('C5_EXECUTE') or die('Access Denied.');
if (isset($pages) && $pages) {
    $page = reset($pages);
    $title = $page->getCollectionName();
    $url = ($page->isExternalLink()) ? $page->getCollectionPointerExternalLink() : $page->getCollectionLink();
    $menuImage = $page->getImageByAttributeHandle('menu_image'); ?>
    <a tabindex="0" role="link" aria-label="<?php echo $title; ?>" class="menu-card bg-white block mt-[3rem] rounded-[2rem] overflow-hidden" href="<?php echo $url; ?>">
        <div class="img-wrap relative rounded-[1rem] overflow-hidden">
            <img class="absolute inset-0 size-full object-cover" src="<?php echo $menuImage; ?>" alt="<?php echo $title; ?>">
        </div>
        <div class="content mt-[2rem]">
            <h4><?php echo $title; ?></h4>
        </div>
    </a>
<?php } ?>