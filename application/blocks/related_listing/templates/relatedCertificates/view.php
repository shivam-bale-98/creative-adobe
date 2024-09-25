<?php defined("C5_EXECUTE") or die("Access Denied.");
$themePath = $this->getThemePath();

use Application\Concrete\Helpers\GeneralHelper;
?>
<section class="test certificates-slider bg-romance  xl:py-[10rem] md:py-[8rem] py-[5rem] xl:px-[0] sm:px-0 px-[2rem] text-rustic-red border-b  border-solid ">

    <div class="xl:px-[6rem] xxl:px-[10rem] md:px-[4rem] sm:px-[4rem] px-[0rem] flex items-start sm:items-center justify-between gap-[2rem] flex-col sm:flex-row ">
        <?php if (isset($title) && trim($title) != "") { ?>
            <h2 class="fade-text " data-duration="1"><?php echo h($title); ?></h2>
        <?php } ?>

        <div class=" swiper-buttons | rustic-red flex z-3 fade-text" data-duration="1" data-delay="0.2">
            <div tabindex="0" aria-label="swiper button previous" role="button" class="swiper-button swiper-button-prev relative">
                <i class="icon-right_button_arrow absolute z-3"></i>
                <span class="bg absolute inset-0 size-full z-1"></span>
                <span class="bg-scale absolute  size-full z-2"></span>
            </div>
            <div tabindex="0" aria-label="swiper button next" role="button" class="swiper-button swiper-button-next relative">
                <i class="icon-right_button_arrow absolute z-3"></i>
                <span class="bg absolute inset-0 size-full z-1"></span>
                <span class="bg-scale absolute  size-full z-2"></span>
            </div>
        </div>
    </div>




    <div class="bind--data certificates-grid-list flex  flex-wrap justify-between gap-[4rem] w-full pt-12 sm:pt-40 ">

        <div class="swiper">
            <div class="swiper-wrapper">

                <?php

                use Application\Concrete\Models\Certificates\Certificates;

                if (is_array($pages) && count($pages)) {
                    $counter = 0;
                    foreach ($pages as $page) {
                        $page = new Certificates($page);
                        $year = $page->getYear();
                        $thumb = $page->getThumbnailImage(500, 200);
                        $logoDesktop = $page->getCertificateLogo('certificate_logo_desktop');
                        $logoMobile = $page->getCertificateLogo('certificate_logo_mobile');
                        $description = $page->getCollectionDescription();

                        // $counter = "";
                        if ($description) {
                            $counter++;
                        }
                ?>
                        <div class="card-item swiper-slide text-rustic-red group hover transition duration-1000 ease-out <?php if (!$description)  echo 'no-description'; ?>" data-slideIndex="<?php if ($description) {
                                                                                                                                                                                                    echo $counter;
                                                                                                                                                                                                }
                                                                                                                                                                                                ?>">
                            <!-- Image -->
                            <div class="img-wrap text  bg-white flex items-center justify-center aspect-[1.2]  lg:aspect-[1.40] z-2 relative  rounded-[2rem]  overflow-hidden  group-hover:bg-red-berry transition duration-1000 ease-out px-10 ">
                                <img class="lg:block hidden object-contain lazy  filter  group-hover:invert transition-all duration-500 ease-out desktop max-w-[20rem]" loading="lazy" src="<?php echo $logoDesktop ?>" alt="<?php echo $title ?>">
                                <img class="lg:hidden block object-contain lazy  filter  group-hover:invert transition-all duration-500 ease-out mobile max-w-[15rem]" src="<?php echo $logoMobile ?>" alt="<?php echo $title ?>">

                            </div>
                        </div>

                    <?php

                    }
                } else { ?> <h4><?= t("No Items Found") ?></h4>
                <?php } ?>
            </div>
        </div>
    </div>







</section>


<!-- POP UP -->
<section class="certificate-popup-block certificate-popup-main fixed-full transition-all-600">
    <div class="t-overlay"></div>
    <div class="close-icon">
        <a href="javascript:void(0);" class="popup-close absolute flex justify-center rounded-full items-center transition-all-600 w-[6rem] h-[6rem]">
            <span class="icon-close"></span>

        </a>
    </div>
    <div class="certificate-popup-slider rounded-tl-[2rem] h-full relative bg-rustic-red text-white">

        <div class="swiper">
            <div class="swiper-wrapper popUpItem-list-js certificate--popup">
                <?php View::element("certificate/popup", ["pages" => $pages, 'counter' => 0]); ?>
            </div>
            <!-- Swiper Buttons -->
            <div class="btn-wrap  flex justify-end gap-15 absolute">


                <div class="swiper-buttons  flex z-3">
                    <div tabindex="0" aria-label="swiper button previous" role="button" class="swiper-button swiper-button-prev relative" >
                        <i class="icon-right_button_arrow absolute z-3"></i>
                        <span class="bg absolute inset-0 size-full z-1"></span>
                        <span class="bg-scale absolute  size-full z-2"></span>
                    </div>
                    <div tabindex="0" aria-label="swiper button next" role="button" class="swiper-button swiper-button-next relative" >
                        <i class="icon-right_button_arrow absolute z-3"></i>
                        <span class="bg absolute inset-0 size-full z-1"></span>
                        <span class="bg-scale absolute  size-full z-2"></span>
                    </div>
                </div>

            </div>
        </div>



    </div>
</section>