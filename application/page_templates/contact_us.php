<?php
$themePath = $view->getThemePath();
$c = Page::getCurrentPage();
$site = Config::get('concrete.site');

use  \Application\Concrete\Helpers\ImageHelper;
use Application\Concrete\Helpers\GeneralHelper;

$ih = new ImageHelper();

$banner_title = $c->getAttribute('banner_title');
$title = $c->getCollectionName();

$banner_attribute = $c->getAttribute('banner_image');
$banner_image = $ih->getThumbnail($banner_attribute, 2000, 2000, false);

$banner_bg = $c->getAttribute('banner_bg');

$banner_mobile_image = $c->getAttribute('mobile_banner');
$banner_mobile_image = $ih->getThumbnail($banner_mobile_image, 1000, 1000, false);

$search = $c->getAttribute('enable_search');

$filters = $c->getAttribute('filters');

$tabs = $c->getAttribute('tabs');

?>

<section style="background-color: <?php echo $banner_bg ?>; " class="banner-v2  pt-[13rem] md:pt-[15rem] xl:pt-[25rem] px-[2rem] md:px-[4rem] xl:px-[6rem] xxl:px-[10rem] text-rustic-red">
    <div class="content text-center ">
        <?php $s = Stack::getByName('breadcrumb');
        if ($s) {
            $s->display();
        } ?>

        <?php if ($banner_title) { ?>
            <h1 class="h2"><?php echo $banner_title ?></h1>
        <?php } else { ?>
            <h1 class="h2"><?php echo $title ?></h1>
        <?php } ?>
    </div>

    <div class="img-wrap relative rounded-[2rem] overflow-hidden xl:mt-[10rem] md:mt-[8rem] mt-[4rem]">

        <img class="desktop absolute inset-0 md:block hidden size-full object-cover" src="<?php echo $banner_image ?>" alt="<?php echo $title ?>">

        <img class="mobile absolute  md:hidden inset-0 size-full object-cover" src="<?php echo $banner_mobile_image ?>" alt="<?php echo $title ?>">


    </div>
</section>

<?php $a = new Area('Page Content');

$a->display($c); ?>

<section id="locations" style="background-color: <?php echo $banner_bg ?>; " class="contact-us-main-container location-block relative  xl:pt-[10rem] md:pt-[8rem] pt-[5rem] text-rustic-red">

    <div class="xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem]">
        

        <div class="content flex  justify-between items-center md:pb-[4rem] pb-[3rem]">
            <div class="title fade-text md:w-1/2 max-w-[19rem] md:max-w-full" data-duration="1" data-delay="0.2">
                <h3><?php echo t('Our locations') ?></h3>
            </div>

            <div class="right flex justify-end items-center md:w-1/2   fade-text" data-duration="1" data-delay="0.2">
                <div  class="swiper-buttons | rustic-red flex z-3">
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

        <?php echo $tokenOutput; ?>
        <div class="filter--section search-wrapper | z-3 relative gap-[2rem] flex flex-wrap  items-center js-filter--section fade-text" data-duration="0.6" data-delay="0.2">
            <div class="search-box flex justify-between items-center w-full bg-white">
                <?php echo $form->text('keywords', isset($keywords) ? $keywords : '', ["placeholder" => t('Search'), 'class' => "keywords_search"]); ?>
                <button class="search-btn flex items-center justify-center mr-[3rem]" id="search--btn">
                    <i class="icon-search z-2 text-[2.2rem] [&:before]:text-black transition duration-300 ease-linear group hover:[&:before]:text-red-berry"></i>
                </button>
            </div>
            <div class="banner-filters filters desktop ">
                <div class="select-box tabs relative">
                    <?php echo $form->select("location", $locationsOptions, $selectedLocation, ["class" => "select--filters"]); ?>
                    <div class="dropdown-result" data-lenis-prevent></div>
                </div>
            </div>
        </div> 
        <div class="contact-us swiper md:pt-[4rem] pt-[3rem] fade-text" data-duration="1">
            <div class="contact-us--listing swiper-wrapper">
                <?php echo View::element($viewType, ['pages' => $pages]); ?>
            </div>

            <div class="no-result-found error-message" style="<?php echo $count > 0 ? 'display:none' : 'display:block'; ?>">
                <?php echo View::element($noResultsFound, []); ?>
            </div>
        </div>
    </div>


    <div class="map relative" id="location_map" data-key="AIzaSyA4-rcMDKAKddSn_kDdXppg4AJzbQJmVP8"></div>

</section>




<?php $a = new Area('Page Content 2');

$a->display($c); ?>