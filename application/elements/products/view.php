<?php if (is_array($pages) && count($pages)) {
    $th = Core::make("helper/text");
    foreach ($pages as $page) {
        $thumb = $page->getThumbnailImage(850, 1190);
        $category = $page->getProductCategory();
        $url = $page->getCollectionLink();
        $title = $page->getCollectionName();
        $description = $th->wordSafeShortText($page->getCollectionDescription(), 80);

        // dd($category);
?>




        <div class="swiper-slide">
            <a tabindex="0" role="link" aria-label="<?php echo $title; ?>" aria-describedby="<?php echo $description; ?>" href="<?php echo $url; ?>" class="product-card <?php if (!($category == 'Structural lining systems')) { ?><?php echo 'resins' ?><?php } ?> | z-2 relative png rounded-[2rem] flex overflow-hidden">
                <img class="relative z-1 | lazy" loading="lazy" src="<?php echo $thumb; ?>" alt="<?php echo $title; ?>">

                <div class="content absolute p-[4rem] z-2">
                    <div class="h3 mb-[2rem]"><?php echo $title; ?></div>
                    <p><?php echo $description; ?></p>

                    <div class="shape absolute">
                        <i class="icon-right_button_arrow absolute z-2"></i>
                        <span class="bg absolute inset-0 size-full"></span>
                    </div>
                </div>
            </a>
        </div>
    <?php }
} else { ?>
    <div class="no-results w-full text-center md:mt-[6rem] mt-[4rem]">
        <h2><?= t("No results found") ?></h2>
    </div>
<?php } ?>