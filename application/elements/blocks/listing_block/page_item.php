<?php if (is_array($pages) && count($pages)) {
    foreach ($pages as $page) { ?>
    <a href="<?= $page->getCollectionLink() ?>">
        <h4><?= $page->getCollectionName() ?></h4>
    </a>
<?php }
} else { ?>
    <h4><?= t("No Items Found") ?></h4>
<?php } ?>