<section style="background-color: #fff;" class="related-blogs | w-full mt-[4rem] xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] md:mt-[10rem]  text-rustic-red">
    <div class="flex justify-between mb-[4rem] md:mb-[6rem]">
        <div class="left">

            <?php if (isset($title) && trim($title) != "") { ?>
                <div class="fade-text" data-duration="1.2" data-delay="0.2">
                    <h2><?php echo $title ?></h2>
                </div>
            <?php } ?>

        </div>
        <div class="right sm:mt-[2rem] md:mt-[1rem] xl:mt-[0rem]fade-text" data-duration="1" data-delay="0.2">
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
    <div class="swiper mb-[4rem] sm:mb-[10rem]">
        <div class="swiper-wrapper">
            <?php View::element("blocks/related_listing/blogs/relatedBlogs", ["pages" => $pages]); ?>
        </div>
    </div>
</section>