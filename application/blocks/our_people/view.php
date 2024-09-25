<?php defined("C5_EXECUTE") or die("Access Denied.");

?>


<section <?php if (isset($bgColor) && trim($bgColor) != "") { ?> style="background-color: <?php echo h($bgColor); ?>;" <?php } ?> class="<?php if (isset($hideBlock) && trim($hideBlock) == 1) { ?>hide-block<?php } ?> <?php if (isset($paddingTop) && trim($paddingTop) == 1) { ?>pt-0<?php } ?> <?php if (isset($paddingBottom) && trim($paddingBottom) == 1) { ?>pb-0<?php } ?> our-team px-[2rem] md:px-[4rem]  xl:px-[6rem] xxl:px-[10rem] py-[5rem] md:py-[8rem] xl:py-[10rem] text-rustic-red">
    <div class="title fade-text" data-duration="1">
        <h2>
            <?php if (isset($title) && trim($title) != "") { ?>
                <?php echo h($title); ?>
            <?php } ?>
        </h2>
    </div>

    <div class="team-list swiper mt-[4rem] md:mt-[6rem] team-listing">
        <div class="swiper-wrapper team--listing">
            <?php View::element($viewType, ['pages' => $pages]); ?>
            <?php echo $token; ?>
            <?php if ($loadMoreFlag) { ?>
                <div class="swiper-slide relative load-more load--more--btn">
                    <div class="img-wrap relative  rounded-[2rem] overflow-hidden">

                    </div>
                    <div class="content mt-[3rem] absolute flex flex-col justify-center items-center gap-[3rem]">
                        <div class="plus relative"></div>
                        <h4>Show more</h4>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="swiper-buttons | mt-[4rem] rustic-red flex z-2 fade-text" data-duration="1" data-delay="0.2">
            <div tabindex="0" aria-label="swiper button previous" role="button" class="swiper-button swiper-button-prev relative">
                <i class="icon-right_button_arrow absolute z-2"></i>
                <span class="bg absolute inset-0 size-full"></span>
            </div>
            <div tabindex="0" aria-label="swiper button next" role="button" class="swiper-button swiper-button-next relative">
                <i class="icon-right_button_arrow absolute z-2"></i>
                <span class="bg absolute inset-0 size-full"></span>
            </div>
        </div>
    </div>
</section>