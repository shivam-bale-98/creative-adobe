<?php
$c = Page::getCurrentPage();

?>

<?php if (is_array($pages) && count($pages)) {
    $th = Core::make("helper/text");
    foreach ($pages as $page) {
        $title = $th->wordSafeShortText($page->getCollectionName(), 100);
        $desc = $th->wordSafeShortText($page->getCollectionDescription(), 100);
        $thumb = $page->getThumbnailImage(850, 1190);
        // $date = $page->getCustomAttributeDate('blog_date', 'd/m/Y');
        $date = $page->getPublicDate('d/m/Y');
?>
        <a class="news-listing-card flex flex-col xmd:mb-0 mb-[4rem] group lg:p-[0] md:p-[1rem] p-0 lg:w-[43%]  md:w-[50%] sm:w-full w-full opacity-0 translate-y-[1rem] lg:nth-[4n-2]:w-[39%] xmd:nth-[4n-2]:w-[50%] xmd:p-[1rem] xmd:nth-[4n-2]:pb-[1rem] lg:nth-[4n-2]:pb-[11%] lg:nth-[4n-1]:w-[39%] lg:nth-[4n-1]:pb-[11%]  lg:nth-[4n+3]:mt-[6%] xmd:mt-0 nth-[4n+3]:mt-0 last:!pb-0 lg:nth-[6]:mt-[2%] xmd:nth-[6]:mt-0 nth-[6]:mt-0     opacity-0 translate-y-[1rem] !no-underline " href="<?= $page->getCollectionLink() ?>">
            <div class="img-wrap relative rounded-[2rem] overflow-hidden z-2  ">
                <img class="absolute inset-0 size-full z-1 object-cover | lazy transition-transform duration-700 ease-out group-hover:scale-[1.2]" src="<?php echo $thumb; ?>" alt="load-more">

            </div>
            <div class=" flex flex-row items-center justify-start gap-[0.55rem]  mt-[1.4rem] sm:mb-[1rem] mb-[2rem] ">
                <div class="flex flex-col items-start justify-start">
                    <div class="w-[0.8rem] h-[0.8rem] relative rounded-[2px] bg-red-berry">
                    </div>
                </div>
                <div class="relative inline-block min-w-[6.125rem] text-[1.6rem] text-red-berry tracking-[0.1rem]"><?php echo $date ?></div>
            </div>

            <div class="md:max-w-[43.6rem] max-w-full">
                <h4 class="sm:text-[3rem] mb-[2rem] group-hover:text-red-berry transition duration-1000 ease-out"><?php echo $title ?></h4>
                <p class="text-[1.6rem]"><?php echo $desc ?></p>
            </div>
        </a>


    <?php }
} else { ?>
    <h4 class="text-rustic-red w-full text-center h2"><?= t("No results found") ?></h4>
<?php } ?>