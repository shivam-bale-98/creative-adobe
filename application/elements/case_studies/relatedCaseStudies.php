<?php

use Concrete\Core\View\View;
use Application\Concrete\Helpers\AttributeHelper;
use Application\Concrete\Helpers\GeneralHelper;



?>

<?php if (GeneralHelper::pagesExist($pages)) { ?>

    <section style="background-color: #F2F1EF;" class="case-studies-listing related-cs-list xl:px-[6rem] md:px-[4rem] px-[2rem] xl:pt-[10rem] xl:pb-[15rem]  md:py-[8rem] py-[5rem] text-rustic-red">

        <div class="title flex flex-wrap items-center justify-between">

            <div class="left">
                <div class="fade-text" data-duration="1.2" data-delay="0.2">
                    <h2>Related case </br>
                        studies</h2>
                </div>

            </div>



            <div class="right flex md:justify-end items-center xl:w-1/4 md:w-1/2 w-full mt-[2rem] md:mt-[0rem] fade-text" data-duration="1" data-delay="0.2">
                <a href="<?php echo View::url('/case-studies'); ?>" class="channeline-btn channeline-btn--arrow channeline-btn--border channeline-btn--red relative">
                    <span class="text z-2 relative">View more</span>
                    <div class="shape absolute">
                        <i class="icon-right_button_arrow absolute z-2"></i>
                        <span class="bg absolute inset-0 size-full"></span>
                    </div>
                </a>

            </div>
        </div>


        <div class="case-studies-list flex flex-wrap xl:mt-[12rem] md:mt-[6rem] mt-[3rem] justify-between">

            <?php
            if (GeneralHelper::pagesExist($pages)) {
                $count = 1;
                foreach ($pages as $page) {
                    $title = $page->getTitle();
                    $url = $page->getUrl();
                    $description = $page->getCollectionDescription();
                    $thumb = $page->getThumbnailImage(1200, 800);
                    $low = $page->getThumbnailImage(1, 1);
                    $category = $page->getCategory();
                    $location = $page->getLocation();
            ?>
                    <?php if ($count == 5) { ?>
                        <div class="case-study-card w-full parallax" data-parallax="-90">
                            <a tabindex="0" role="link" aria-label="<?php echo $title; ?>" class="flex flex-col" href="<?php echo $url ?>">
                                <div class="subtitle relative md:mb-[3rem] mb-[2rem]">
                                    <span class="dot absolute h-[0.7rem] w-[0.7rem] bg-red-berry rounded-full"></span>
                                    <p class="relative"><?php echo GeneralHelper::getTwoDigitCount($count);  ?></p>
                                </div>

                                <div class="img-wrap relative rounded-[2rem] overflow-hidden md:mb-[3rem] mb-[2rem] z-2 blurred-img" style="background-image: url('<?php echo $low; ?>');">
                                    <img class="absolute inset-0 size-full z-1 object-cover | lazy" loading="lazy" src="<?php echo $thumb; ?>" alt="">
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
                    <?php } else { ?>
                        <div class="case-study-card  parallax" <?php if ($count == 1) { ?>data-parallax="-50" <?php } ?> <?php if ($count == 2) { ?>data-parallax="-15" <?php } ?> <?php if ($count == 3) { ?>data-parallax="-60" <?php } ?> <?php if ($count == 4) { ?>data-parallax="-20" <?php } ?>>
                            <a tabindex="0" role="link" aria-label="<?php echo $title; ?>" class="flex flex-col w-full" href="<?php echo $url ?>">
                                <div class="subtitle relative md:mb-[3rem] mb-[2rem]">
                                    <span class="dot absolute h-[0.7rem] w-[0.7rem] bg-red-berry rounded-full"></span>
                                    <p class="relative"><?php echo GeneralHelper::getTwoDigitCount($count);  ?></p>
                                </div>

                                <div class="img-wrap relative rounded-[2rem] overflow-hidden md:mb-[3rem] mb-[2rem] z-2 blurred-img" style="background-image: url('<?php echo $low; ?>');">
                                    <img class="absolute inset-0 size-full z-1 object-cover | lazy" loading="lazy" src="<?php echo $thumb; ?>" alt="">
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
                    <?php } ?>

            <?php $count++;
                }
            }
            ?>

        </div>
    </section>

<?php } ?>