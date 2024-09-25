<?php defined("C5_EXECUTE") or die("Access Denied.");
$ih = new \Application\Concrete\Helpers\ImageHelper();

$thumbnail = "";
$low = "";
?>











<!-- px-[2rem] md:px-[4rem] xl:px-[6rem] xxl:px-[10rem] -->
<section <?php if (isset($bgColor) && trim($bgColor) != "") { ?>style="background-color: <?php echo h($bgColor); ?>;" <?php } ?> class="<?php if (isset($removePaddingTop) && trim($removePaddingTop) == 1) { ?>pt-0<?php } ?> <?php if (isset($removePaddingBottom) && trim($removePaddingBottom) == 1) { ?>pb-0<?php } ?> relative not-animated timeline-slider py-[4rem] md:py-[8rem] xl:py-[10rem] text-rustic-red">

    <div class="line relative  left-0 w-full h-[0.1rem]">
        <div class="animate absolute"></div>
    </div>

    <?php if (!empty($addTimeline_items)) { ?>
        <div class="year-slider swiper mt-[5rem]">
            <div class="swiper-wrapper">
                <?php foreach ($addTimeline_items as $addTimeline_item_key => $addTimeline_item) { ?>
                    <?php if (isset($addTimeline_item["year"]) && trim($addTimeline_item["year"]) != "") { ?>

                        <div class="swiper-slide">
                            <h2> <?php echo h($addTimeline_item["year"]); ?></h2>
                        </div>
                    <?php } ?>

                <?php } ?>
            </div>
        </div>
    <?php } ?>
    <?php if (!empty($addTimeline_items)) { ?>
        <div class="card-slider swiper  md:mt-[6rem] mt-[4rem] px-[2rem] md:px-[0rem]">
            <div class="swiper-wrapper">
                <?php foreach ($addTimeline_items as $addTimeline_item_key => $addTimeline_item) { ?>

                    <div class="swiper-slide">
                        <?php if ($addTimeline_item["image"]) {
                            $thumbnail = $ih->getThumbnail($addTimeline_item["image"], 850, 900);
                            $low = $ih->getThumbnail($addTimeline_item["image"], 10, 10);
                        ?>
                            <div class="img-wrap relative blurred-img rounded-[2rem] overflow-hidden mb-[2rem]" style="background-image: url('<?php echo $low ?>');">
                                <img class="inset-0 lazy" loading="lazy" src="<?php echo $thumbnail ?>" alt="<?php echo h($addTimeline_item["title"]); ?>">
                            </div>
                        <?php } ?>


                        <div class="content">
                            <?php if (isset($addTimeline_item["title"]) && trim($addTimeline_item["title"]) != "") { ?>

                                <h3 class="mb-[2rem]"><?php echo h($addTimeline_item["title"]); ?></h3>
                            <?php } ?>
                            <?php if (isset($addTimeline_item["desc_1"]) && trim($addTimeline_item["desc_1"]) != "") { ?>
                                <p><?php echo h($addTimeline_item["desc_1"]); ?></p>
                            <?php } ?>
                        </div>
                    </div>


                <?php } ?>
            </div>

            <div class="swiper-buttons | rustic-red flex z-3  md:absolute relative mt-[4rem] md:mt-[0rem] justify-center">
                <div tabindex="0" aria-label="swiper button previous" role="button" class="swiper-button swiper-button-prev md:absolute relative z-3">
                    <i class="icon-right_button_arrow absolute z-3"></i>
                    <span class="bg absolute inset-0 size-full"></span>
                    <span class="bg-scale absolute  size-full z-2"></span>
                </div>
                <div tabindex="0" aria-label="swiper button next" role="button" class="swiper-button swiper-button-next md:absolute relative z-3">
                    <i class="icon-right_button_arrow absolute z-3"></i>
                    <span class="bg absolute inset-0 size-full"></span>
                    <span class="bg-scale absolute  size-full z-2"></span>
                </div>
            </div>
        </div>
    <?php } ?>
</section>