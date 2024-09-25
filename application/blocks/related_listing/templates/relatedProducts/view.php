



<section style="background-color: #1A0A0C;" class="products-list related-products text-white xl:px-[6rem] xxl:px-[10rem]  md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] overflow-hidden js-related-products">


    <h6 class="mb-[2rem] fade-text" data-duration="1">
        What we do
    </h6>


    <div class="title flex flex-wrap">
        <div class="left md:w-1/2 w-full flex justify-between items-center">



            <?php if (isset($title) && trim($title) != "") { ?>
                <div class="fade-text" data-duration="1.2" data-delay="0.2">
                    <h2><?php echo h($title); ?></h2>
                </div>
            <?php } ?>

            <div class="swiper-buttons | white flex z-3 fade-text" data-duration="1" data-delay="0.2">
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

        <div class="right flex md:flex-row flex-col xl:justify-end justify-between md:items-center  md:w-1/2 w-auto mt-[3rem] xl:mt-[0rem] fade-text" data-duration="1" data-delay="0.2">

            <?php
            if (trim($link_URL) != "") { ?>
                <?php
                $link_Attributes = [];
                $link_Attributes['href'] = $link_URL;
                $link_AttributesHtml = join(' ', array_map(function ($key) use ($link_Attributes) {
                    return $key . '="' . $link_Attributes[$key] . '"';
                }, array_keys($link_Attributes)));
                echo sprintf('<a class="channeline-btn channeline-btn--arrow channeline-btn--border channeline-btn--white relative" %s>
                               <span class="text z-2 relative">%s</span>
                               <div class="shape absolute">
                                  <i class="icon-right_button_arrow absolute z-2"></i>
                                   <span class="bg absolute inset-0 size-full"></span>
                               </div>
                             </a>', $link_AttributesHtml, $link_Title); ?>
            <?php } ?>



            <div class="swiper-buttons | white flex z-3 fade-text" data-duration="1" data-delay="0.2">
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

    <div class="products pt-[6rem]">
        <div class="swiper">
            <div class="swiper-wrapper">
            <?php View::element("blocks/related_listing/products/relatedProducts", ["pages" => $pages]); ?>
            </div>
        </div>
    </div>
</section>