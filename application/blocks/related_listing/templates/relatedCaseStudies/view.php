<?php

use Application\Concrete\Helpers\GeneralHelper;
?>

<section style="background-color: #F2F1EF;" class="related-case-studies text-rustic-red xl:px-[6rem] xxl:px-[10rem] md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] overflow-hidden">

    <div class="title flex flex-wrap">
        <div class="left  xl:w-3/4 w-full flex justify-between items-center">
            <?php if (isset($title) && trim($title) != "") { ?>
                <div class="fade-text" data-duration="1.2" data-delay="0.2">
                    <h2><?php echo $title ?></h2>
                </div>
            <?php } ?>
        </div>

        <div class="right flex xl:justify-end justify-between items-center  xl:w-1/4 w-full md:mt-[4rem] mt-[3rem] xl:mt-[0rem] fade-text" data-duration="1" data-delay="0.2">

            <div class="swiper-buttons | rustic-red flex z-3 fade-text" data-duration="1" data-delay="0.2">
                <div tabindex="0" aria-label="swiper button previous" role="button" class="swiper-button swiper-button-prev relative">
                    <i class="icon-right_button_arrow absolute z-3"></i>
                    <span class="bg absolute inset-0 size-full z-2"></span>
                    <span class="bg-scale absolute  size-full z-2"></span>
                </div>
                <div tabindex="0" aria-label="swiper button next" role="button" class="swiper-button swiper-button-next relative">
                    <i class="icon-right_button_arrow absolute z-3"></i>
                    <span class="bg absolute inset-0 size-full z-2"></span>
                    <span class="bg-scale absolute  size-full z-2"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="case-studies-slider xl:pt-[10rem] md:pt-[6rem] pt-[4rem]">
        <div class="swiper">
            <div class="swiper-wrapper">

                <?php
                if (GeneralHelper::pagesExist($pages)) {
                    $count = 1;
                    foreach ($pages as $page) {
                        $page = new \Application\Concrete\Models\CaseStudies\CaseStudies($page);
                        $title = $page->getTitle();
                        $url = $page->getUrl();
                        $description = $page->getCollectionDescription();
                        $thumb = $page->getThumbnailImage(1200, 800);
                        $category = $page->getCategory();
                        $location = $page->getLocation();
                ?>
                        <div class="case-study-card swiper-slide">
                            <a tabindex="0" role="link" aria-label="<?php echo $title; ?>" href="<?php echo $url ?>" class="flex flex-col w-full">
                                <div class="subtitle relative md:mb-[3rem] mb-[2rem]">
                                    <span class="dot absolute h-[0.7rem] w-[0.7rem] bg-red-berry rounded-full"></span>
                                    <p class="relative"><?php echo GeneralHelper::getTwoDigitCount($count);  ?></p>
                                </div>

                                <div class="img-wrap relative rounded-[2rem] overflow-hidden md:mb-[3rem] mb-[2rem] z-2">
                                    <img class="absolute inset-0 size-full z-1 object-cover | lazy" loading="lazy" src="<?php echo $thumb; ?>" alt="45-meter culvert rehabilitation in Scotland">
                                    <span class="batch absolute z-2 bg-red-berry text-white rounded-[0.6rem]"><?php echo $category ?></span>
                                </div>

                                <div class="content">
                                    <div class="h3 md:mb-[2rem] mb-[1rem]"><?php echo $title; ?></div>
                                    <div class="location flex items-center">
                                        <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8 10C8.55 10 9.02083 9.80417 9.4125 9.4125C9.80417 9.02083 10 8.55 10 8C10 7.45 9.80417 6.97917 9.4125 6.5875C9.02083 6.19583 8.55 6 8 6C7.45 6 6.97917 6.19583 6.5875 6.5875C6.19583 6.97917 6 7.45 6 8C6 8.55 6.19583 9.02083 6.5875 9.4125C6.97917 9.80417 7.45 10 8 10ZM8 20C5.31667 17.7167 3.3125 15.5958 1.9875 13.6375C0.6625 11.6792 0 9.86667 0 8.2C0 5.7 0.804167 3.70833 2.4125 2.225C4.02083 0.741667 5.88333 0 8 0C10.1167 0 11.9792 0.741667 13.5875 2.225C15.1958 3.70833 16 5.7 16 8.2C16 9.86667 15.3375 11.6792 14.0125 13.6375C12.6875 15.5958 10.6833 17.7167 8 20Z" fill="#80151A" />
                                        </svg>
                                        <p><?php echo $location; ?></p>
                                    </div>
                                </div>
                            </a>
                        </div>


                <?php $count++;
                    }
                } ?>

            </div>
        </div>
    </div>
</section>