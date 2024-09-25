<?php

?>

<?php if (is_array($pages) && count($pages)) {
    foreach ($pages as $page) {
        $th = Core::make("helper/text");
        $desc =  $th->wordSafeShortText($page->getCollectionDescription(), 80);;
        $thumb = $page->getThumbnailImage(850, 1190);
        $low = $page->getThumbnailImage(10, 10);
        $date = $page->getCustomAttributeDate('blog_date', 'd/m/Y');
?>
        <!-- <a href="< /?= $page->getCollectionLink() ?>">
            <h4>< //?= $page->getCollectionName() ?></h4>
        </a>
        <h4>< //?= $desc ?></h4>
        <h4>< //?= $date ?></h4>
        <img src=< //?= $thumb ?> alt="img" /> -->
        <div class="swiper-slide">
            <a class="related-blogs-card hover:no-underline" href="<?= $page->getCollectionLink() ?>">
                <div class="image-wrap relative size-full inset-0 blurred-img rounded-[2rem] overflow-hidden" style="background-image: url('<?php echo $low ?>');">
                    <img loading="lazy" class="lazy | object-cover absolute inset-0 size-full" src="<?php echo $thumb; ?>" alt="<?= $page->getCollectionName() ?>">
                </div>
                <div class=" flex items-center"><span class=" flex items-center w-[0.8rem] h-[0.8rem] rounded-[0.2rem] bg-red-berry opacity-50 my-[2rem]"></span>
                    <p class="ml-[1.5rem]"><?php echo $date; ?></p>
                </div>
                <div class="content">
                    <h4 class="text-black mb-[2rem] "><?= $page->getCollectionName() ?></h4>
                    <p class="text-black"><?php echo $desc ?></p>
                </div>
            </a>
        </div>
    <?php }
} else { ?>
    <h4><?= t("No results found") ?></h4>
<?php } ?>