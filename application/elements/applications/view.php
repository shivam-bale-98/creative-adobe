<?php defined("C5_EXECUTE") or die("Access Denied.");

use Application\Concrete\Helpers\GeneralHelper;

?>

<?php if (is_array($pages) && count($pages)) {

    foreach ($pages as $page) {
        $counter++;
        $url = $page->getUrl();
        $title = $page->getTitle();
        $desc = $page->getCollectionDescription();
        $thumb = $page->getThumbnailImage(850, 1190);
        $video = $page->getAttribute('video_url');
?>


        <div id="application-<?php echo $counter ?>" class="application-crd flex flex-wrap justify-between  relative xl:py-[10rem] md:py-[6rem] py-[4rem] ">
            <div class="left text-left flex">
                <h6 class=""><?php echo \Application\Concrete\Helpers\GeneralHelper::getTwoDigitCount($counter) ?></h6>
                <div class="img-wrap rounded-[2rem] overflow-hidden relative">
                    <?php if (!is_null($video) && !empty($video)) { ?>
                        <video src="<?php echo $video ?>" class="absolute inset-0 size-full object-cover | lazy" loading="lazy" autoplay="" loop="" muted="" playsinline=""></video>
                    <?php } else { ?>
                        <img class="absolute inset-0 object-cover size-full | lazy" loading="lazy" src="<?php echo $thumb ?>" alt="<?php echo $title ?>">
                    <?php } ?>

                </div>
            </div>
            <div class="right ">
                <h2><?php echo $title ?></h2>
                <p class="md:mt-[3rem] mt-[2rem]"><?php echo $desc ?></p>
                <a href="<?php echo $url ?>" class="channeline-btn channeline-btn--arrow channeline-btn--border channeline-btn--red relative xl:mt-[5rem] md:mt-[3rem] mt-[2rem] ">
                    <span class="text z-2 relative">Learn more</span>
                    <div class="shape absolute">
                        <i class="icon-right_button_arrow absolute z-2"></i>
                        <span class="bg absolute inset-0 size-full"></span>
                    </div>
                </a>
            </div>
            <div class="line absolute"></div>
        </div>
    <?php
    }
} else { ?>
    <h4><?= t("No results found") ?></h4>
<?php } ?>