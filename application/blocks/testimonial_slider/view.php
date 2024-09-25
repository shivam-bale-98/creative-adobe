<?php defined("C5_EXECUTE") or die("Access Denied."); ?>


<section <?php if (isset($bgColor) && trim($bgColor) != "") { ?>style="background-color:<?php echo h($bgColor); ?>;" <?php } ?> class="<?php if (isset($hideBlock) && trim($hideBlock) == 1) { ?>hide-block<?php } ?> <?php if (isset($removePaddingTop) && trim($removePaddingTop) == 1) { ?>pt-0<?php } ?> <?php if (isset($removePaddingBottom) && trim($removePaddingBottom) == 1) { ?>pb-0<?php } ?> testimonials z-2 xxl:px-[10rem]  xl:px-[6rem] md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] text-rustic-red">
    <?php if (isset($subTitle) && trim($subTitle) != "") { ?>

        <h6 class="mb-[2rem] fade-text" data-duration="1"><?php echo h($subTitle); ?></h6>
    <?php } ?>
    <div class="title flex flex-wrap items-center justify-between">
        <div class="left xl:w-3/4 md:w-1/2 w-full">
            <?php if (isset($title) && trim($title) != "") { ?>
                <div class="title fade-text" data-duration="1.2" data-delay="0.2">
                    <?php echo $title; ?>
                </div>
            <?php } ?>

        </div>

        <div class="right flex md:justify-end items-center xl:w-1/4 md:w-1/2 w-full  fade-text" data-duration="1" data-delay="0.2">
            <div class="swiper-buttons | rustic-red flex z-3">
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
    </div>

    <?php if (!empty($testimonials_items)) { ?>

        <div class="swiper mt-[5rem] xl:mt-[10rem] overflow-visible z-2">
            <div class="swiper-wrapper">
                <?php foreach ($testimonials_items as $testimonials_item_key => $testimonials_item) { ?>
                    <div class="swiper-slide z-2">
                        <div class="testimony-card flex justify-between z-2 flex-wrap">
                            <div class="img-wrap relative rounded-[2rem] transition-all z-2">
                                <?php if ($testimonials_item["redLogo"]) { ?>
                                    <img class="red absolute object-contain transition-all" src="<?php echo $testimonials_item["redLogo"]->getURL(); ?>" alt="<?php echo $testimonials_item["redLogo"]->getTitle(); ?>" />
                                <?php } ?>
                                <?php if ($testimonials_item["whiteLogo"]) { ?>
                                    <img class="white absolute object-contain transition-all" src="<?php echo $testimonials_item["whiteLogo"]->getURL(); ?>" alt="<?php echo $testimonials_item["whiteLogo"]->getTitle(); ?>" />
                                <?php } ?>

                            </div>

                            <div class="content absolute z-1 sm:absolute sm:right-0 sm:top-0 sm:h-full p-[5rem] rounded-[2rem] overflow-hidden">
                                <div class="box size-full flex flex-col justify-between">
                                    <div class="top">
                                        <i class="icon-quote"></i>

                                        <?php if (isset($testimonials_item["description_1"]) && trim($testimonials_item["description_1"]) != "") { ?>
                                            <?php echo $testimonials_item["description_1"]; ?>
                                        <?php } ?>
                                    </div>

                                    <div class="botton">
                                        <?php if (isset($testimonials_item["authorName"]) && trim($testimonials_item["authorName"]) != "") { ?>


                                            <h4> <?php echo h($testimonials_item["authorName"]); ?></h4>
                                        <?php } ?>


                                        <?php if (isset($testimonials_item["authorDesignation"]) && trim($testimonials_item["authorDesignation"]) != "") { ?>


                                            <h6> <?php echo h($testimonials_item["authorDesignation"]); ?></h6>
                                        <?php } ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                <?php } ?>
            </div>
        </div>
    <?php } ?>
</section>