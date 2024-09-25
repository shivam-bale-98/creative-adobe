<?php defined("C5_EXECUTE") or die("Access Denied.");
$ih = new \Application\Concrete\Helpers\ImageHelper();
$thumbnail = "";
?>



<section <?php if (isset($bgColor) && trim($bgColor) != "") { ?>style="background-color: <?php echo h($bgColor); ?>;" <?php } ?> class="<?php if (isset($hideBlock) && trim($hideBlock) == 1) { ?>hide-block<?php } ?> <?php if (isset($removePaddingTop) && trim($removePaddingTop) == 1) { ?>pt-0<?php } ?> <?php if (isset($removePaddingBottom) && trim($removePaddingBottom) == 1) { ?>pb-0<?php } ?> techologies-slider relative xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] text-rustic-red">
    <div class="title flex flex-wrap items-center justify-between">
        <?php if (isset($blockTitle) && trim($blockTitle) != "") { ?>

            <div class="left xl:w-3/4 md:w-1/2 w-full">
                <div class="title fade-text" data-duration="1.2" data-delay="0.2">
                    <h2><?php echo $blockTitle; ?></h2>
                </div>
            </div>
        <?php } ?>


        <div class="right flex md:justify-end items-center mt-[3rem] md:mt-[0rem] xl:w-1/4 md:w-1/2 w-full  fade-text" data-duration="1" data-delay="0.2">
            <div tabindex="0" aria-label="swiper button previous" role="button" class="swiper-buttons | rustic-red flex z-3">
                <div class="swiper-button swiper-button-prev relative">
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

    <?php if (!empty($addCards_items)) { ?>
        <div class="technology-card--slider swiper xl:mt-[10rem] md:mt-[8rem] mt-[4rem]">
            <div class="swiper-wrapper">
                <?php foreach ($addCards_items as $addCards_item_key => $addCards_item) { ?>
                    <div class="swiper-slide">
                        <div class="technology-card rounded-[2rem] flex flex-col justify-end items-start h-full z-2 overflow-hidden md:p-[3rem] p-[2rem] bg-white text-rustic-red">


                            <?php if ($addCards_item["image"]) {
                                $thumbnail = $ih->getThumbnail($addCards_item["image"], 1000, 800);
                            ?>
                                <div class="hidden img-wrap absolute size-ful  z-1 inset-0 blurred-img  overflow-hidden" style="background-image: url('<?php echo $thumbnail ?>');">
                                    <img class="inset-0 lazy object-cover z-1" loading="lazy" src="<?php echo $thumbnail ?>" alt="">
                                </div>
                            <?php } ?>

                            <div class="content z-2 h-full">
                                <?php if ($addCards_item["icon"]) { ?>
                                    <span class="logo h-[6rem] w-[6rem] rounded-full overflow-hidden bg-red-berry relative flex items-center justify-center mb-[3rem]">
                                        <img src="<?php echo $addCards_item["icon"]->getURL(); ?>" alt="<?php echo $addCards_item["icon"]->getTitle(); ?>" />
                                    </span>
                                <?php } ?>

                                <?php if (isset($addCards_item["title"]) && trim($addCards_item["title"]) != "") { ?>

                                    <h4>
                                        <?php echo h($addCards_item["title"]); ?>
                                    </h4>
                                <?php } ?>

                                <?php if (isset($addCards_item["desc_1"]) && trim($addCards_item["desc_1"]) != "") { ?>

                                    <p class="mt-[1.5rem]"><?php echo h($addCards_item["desc_1"]); ?></p>
                                <?php } ?>

                            </div>

                        </div>
                    </div>
                <?php } ?>

            </div>
        </div>
    <?php } ?>
</section>