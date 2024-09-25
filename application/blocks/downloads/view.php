<?php defined("C5_EXECUTE") or die("Access Denied."); 
$themePath = $this->getThemePath();


?>
<section style="background-color: #F2F1EF;" class="download--block xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] text-rustic-red">

    <?php if (isset($title) && trim($title) != "") { ?>
        <div class="title fade-text" data-duration="1" data-delay="0.2">
            <h2><?php echo h($title); ?></h2>
        </div>
    <?php } ?>


    <div class="filters | mt-[4rem] flex flex-wrap justify-between items-center fade-text" data-duration="1" data-delay="0.2">
        <div class="swiper filter-swiper">
            <div class="swiper-wrapper filter--section">
                <?php if (!empty($categoryOptions)) { ?>
                    <div class="swiper-slide">
                        <a class="flex px-[2rem] py-[1.6rem] rounded-[20rem] uppercase <?php echo $selectedCategory == '' ? 'active' : ''; ?>" category-news" id="tab1">
                            <span><?php echo t('All') ?></span>
                            <i class="bg"></i>
                        </a>
                    </div>

                    <?php foreach ($categoryOptions as $key => $option) { ?>
                        <div class="swiper-slide">
                            <a class="flex px-[2rem] py-[1.6rem] rounded-[20rem] uppercase <?php echo $selectedCategory == $key ? 'active' : ''; ?>" id="tab2" data-value="<?php echo $option; ?>">
                                <span><?php echo $option ?></span>
                                <i class="bg"></i>
                            </a>
                        </div>
                    <?php } ?>

                    <!-- products  -->
                    <div class="swiper-slide">
                        <a class="flex px-[2rem] py-[1.6rem] rounded-[20rem] uppercase <?php echo $selectedCategory == 'products' ? 'active' : ''; ?>" data-value="products">
                            <span><?php echo t('Products') ?></span>
                            <i class="bg"></i>
                        </a>
                    </div>

                <?php } ?>
            </div>
        </div>

        <div class="swiper-buttons js-desktop |  rustic-red hidden md:flex z-2">
            <div tabindex="0" aria-label="swiper button previous" role="button" class="swiper-button swiper-button-prev relative z-3">
                <i class="icon-right_button_arrow absolute z-3"></i>
                <span class="bg absolute inset-0 size-full"></span>
                <span class="bg-scale absolute  size-full z-2"></span>
            </div>
            <div tabindex="0" aria-label="swiper button next" role="button" class="swiper-button swiper-button-next relative z-3">
                <i class="icon-right_button_arrow absolute z-3"></i>
                <span class="bg absolute inset-0 size-full"></span>
                <span class="bg-scale absolute  size-full z-2"></span>
            </div>
        </div>
    </div>


    <div class="download-cards-list mt-[4rem] md:mt-[6rem] swiper">
        <div class="swiper-wrapper downloads--listing">

            
            <?php if (is_array($filesData) && count($filesData) > 0) {

                View::element('downloads/filesView', ['files' => $filesData]);
            } ?>
        </div>

        <div class="swiper-buttons js-mobile mt-[3rem] |  rustic-red flex md:hidden z-2 fade-text" data-duration="1" data-delay="0.2">
            <div tabindex="0" aria-label="swiper button previous" role="button" class="swiper-button swiper-button-prev relative z-3">
                <i class="icon-right_button_arrow absolute z-3"></i>
                <span class="bg absolute inset-0 size-full"></span>
                <span class="bg-scale absolute  size-full z-2"></span>
            </div>
            <div tabindex="0" aria-label="swiper button next" role="button" class="swiper-button swiper-button-next relative z-3">
                <i class="icon-right_button_arrow absolute z-3"></i>
                <span class="bg absolute inset-0 size-full"></span>
                <span class="bg-scale absolute  size-full z-2"></span>
            </div>
        </div>
    </div>
</section>