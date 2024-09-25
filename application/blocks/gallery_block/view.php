<?php defined("C5_EXECUTE") or die("Access Denied.");
$ih = new \Application\Concrete\Helpers\ImageHelper();

?>




<section class="gallery-slider   bg-romance <?php if (isset($hideBlock) && trim($hideBlock) == 1) { ?>hide-block<?php } ?> <?php if (isset($removePaddingTop) && trim($removePaddingTop) == 1) { ?>pt-0<?php } ?> <?php if (isset($removePaddingBottom) && trim($removePaddingBottom) == 1) { ?>pb-0<?php } ?>">
    <div class="gallery-slider-block relative z-10 overflow-hidden ">
        <!-- Gallery Top -->
        <div class="gallery-main-slider  ">
            <!-- Title -->
            <div class="hidden md:block title relative z-10">
                <?php if (isset($title) && trim($title) != "") { ?>
                    <div class="absolute top-[8.3rem] left-[6rem] text-white">
                        <?php echo $title; ?>
                    </div>
                <?php } ?>
            </div>
            <div class="swiper-wrapper">

                <?php if (!empty($gallery_items)) { ?>
                    <?php foreach ($gallery_items as $gallery_item_key => $gallery_item) { ?>
                        <div class="swiper-slide  ">
                            <?php if ($gallery_item["image"]) { ?>
                                <div class="w-full relative aspect-[1.8]">
                                    <div class="gallery-item size-full absolute top-0 left-0 blurred-img" style="background-image: url('<?php echo $ih->getThumbnail($gallery_item["image"], 1, 1)  ?>')">



                                        <img class="w-full h-full object-cover  lazy" loading="lazy" src="<?php echo $gallery_item["image"] ? $ih->getThumbnail($gallery_item["image"], 2000, 2000) : ""; ?>" loading="lazy" alt="<?php echo $gallery_item["image"]->getTitle(); ?>" />


                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } ?>

            </div>
        </div>

        <div class="swiper-buttons | gap-[1.9rem] rustic-red flex items-center  justify-center z-3 absolute    bottom-[1.6rem]   sm:bottom-[6.5rem] left-0 right-0 ms-auto ">
            <!-- Swiper Button Left -->
            <div tabindex="0" aria-label="swiper button previous" role="button" class=" swiper-button swiper-button-prev relative  !w-[5rem] !h-[3.4rem]">
                <i class="icon-right_button_arrow absolute z-3 !text-[1.6rem]  text-rustic-red "></i>
                <span class="bg absolute inset-0 size-full z-1 !bg-white"></span>
                <span class="bg-scale absolute  size-full z-2"></span>
            </div>
            <!-- Gallery Bottom -->
            <div class="gallery-thumb-slider swiper !m-0 hidden md:block">
                <div class="swiper-wrapper">
                    <?php if (!empty($gallery_items)) { ?>
                        <?php foreach ($gallery_items as $gallery_item_key => $gallery_item) { ?>
                            <div class="swiper-slide !w-[8.9rem] !h-[4.9rem] last:!mr-0">
                                <div class="cursor-pointer gallery-item absolute size-full top-0 left-0">
                                    <?php if ($gallery_item["image"]) { ?>

                                        <img class="rounded-[1rem] size-full object-cover lazy" loading="lazy" src="<?php echo $gallery_item["image"]->getURL(); ?>" alt="<?php echo $gallery_item["image"]->getTitle(); ?>" />
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <!-- Swiper button Right -->
            <div tabindex="0" aria-label="swiper button next" role="button" class="swiper-button swiper-button-next relative !w-[5rem] !h-[3.4rem]">
                <i class="icon-right_button_arrow absolute z-3 !text-[1.6rem] !before:!text-rustic-red"></i>
                <span class="bg absolute inset-0 size-full z-1 !bg-white"></span>
                <span class="bg-scale absolute  size-full z-2"></span>
            </div>
        </div>



    </div>
</section>