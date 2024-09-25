<?php if (is_array($pages) && count($pages)) {
    foreach ($pages as $page) {
        $title = $page->getTitle();
        $phone = $page->getPhoneNumber();
        $email = $page->getEmail();
        $coverage_area = $page->getCoverageArea();
        $latitude = $page->getLatitude();
        $longitude = $page->getLongitude();
        $location = $page->getContactLocations();
        $name = $page->getName();
        $img = $page->getImage();
        $bgProfile = $page->getImage(1,1, regenerate:true);
?>
        <div data-lat="<?php echo $latitude ?>" data-long="<?php echo $longitude ?>" class="swiper-slide location-card flex justify-between items-start rounded-[2rem] overflow-hidden md:py-[3rem] py-[2.5rem] md:pl-[3rem] md:pr-[2rem] pl-[2.5rem] pr-[2.5rem] bg-white text-rustic-red">
            <div class="content">
                <h5 class="mb-[2rem]"><?php echo $title ?></h5>
                <?php if ($name) { ?>
                    <p class="mb-[1rem]"><?php echo t('Name') ?> : <?php echo $name ?></p>
                <?php } ?>
                <?php if ($phone) { ?>
                    <p class="mb-[1rem]"><?php echo t('Phone') ?> : <a href="tel:<?php echo $phone ?>"><?php echo $phone ?></a></p>
                <?php } ?>
                <?php if ($email) { ?>
                    <p class="mb-[1rem]"><?php echo t('Email') ?> : <a href="mailto:<?php echo $email ?>"><?php echo $email ?></a></p>
                <?php } ?>
                <?php if ($coverage_area) { ?>
                    <p><?php echo t('Coverage area') ?> : <span><?php echo  $coverage_area ?></span></p>
                <?php } ?>

                <!-- <a href="" class="channeline-btn channeline-btn--arrow channeline-btn--border channeline-btn--red relative xl:mt-[4rem] mt-[2rem] ">
            <span class="text z-2 relative">get directions</span>
            <div class="shape absolute">
                <i class="icon-right_button_arrow absolute z-2"></i>
                <span class="bg absolute inset-0 size-full"></span>
            </div>
        </a> -->
            </div>

            <!-- <div class="img-wrap relative rounded-[2rem] overflow-hidden" style="background-image: url(<?php echo $bgProfile ?>);">
                <img class="absolute inset-0 object-cover | lazy" loading="lazy" src="<? //php echo $img ?>" alt="<?php echo $title ?>">
            </div> -->
        </div>

    <?php }
} else { ?>
    <h4><?= t("No results found") ?></h4>
<?php } ?>