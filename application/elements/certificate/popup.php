<?php

use Application\Concrete\Models\Certificates\Certificates;

if (is_array($pages) && count($pages)) {
    foreach ($pages as $page) {
        $page = new Certificates($page);
        $year = $page->getYear();
        $title = $page->getTitle();
        $thumb = $page->getThumbnailImage(150, 52);
        $logoDesktop = $page->getCertificateLogo('certificate_logo_desktop');
        $logoMobile = $page->getCertificateLogo('certificate_logo_mobile');
        $description = $page->getCollectionDescription();
        $id = $page->getCollectionID();

        // Test  code
        if ($description) {
            $counter++;
        }
?>

        <?php if ($description) { ?>
            <div class="swiper-slide team-items color-white" data-slideIndex="<?php echo $counter; ?>">
                <div class="card-item flex flex-row flex-wrap flex-col justify-between <?php if (!$description)  echo 'no-description'; ?>">

                    <div class="card-content lg:pt-[0]">
                        <div class="card-title flex justify-between items-center lg:mb-0 md:mb-[4rem] mb-[4rem]">
                            <h4 class="capitalize lg:mb-[3rem] md:mb-0 sm:mb-0"><?= $page->getCollectionName() ?> </h4>
                            <div class=" lg:hidden md:block block max-w-[14rem]">

                                <!-- 
                                <img class="lg:block hidden object-contain lazy  filter  group-hover:invert transition-all duration-500 ease-out desktop" loading="lazy" src="</?php echo $logoDesktop ?>" alt="<?php echo $title ?>"> -->
                                <img class="lg:hidden block object-contain invert transition-all duration-500 ease-out mobile " src="<?php echo $logoMobile ?>" alt="<?php echo $title ?>">
                            </div>
                        </div>
                        <div class="content">
                            <p><?php echo  t($description) ?></p>
                        </div>
                    </div>

                    <div class="max-w-[15rem] lg:block bottom-logo hidden">
                        <img class="object-contain lazy invert filter" loading="lazy" src="<?php echo $logoMobile ?>" />
                    </div>




                </div>
            </div>
        <?php } ?>

    <?php }
} else { ?>
    <h2 class="w-full text-center"><?= t("No results found") ?></h2>
<?php } ?>