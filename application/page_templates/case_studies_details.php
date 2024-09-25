<?php

use  \Application\Concrete\Helpers\GeneralHelper;

$c = Page::getCurrentPage();

$themePath = $this->getThemePath();

use  \Application\Concrete\Helpers\ImageHelper;

$ih = new ImageHelper();

$banner_title = $c->getAttribute('banner_title');
$title = $c->getCollectionName();


$banner_attribute = $c->getAttribute('banner_image');
$banner_image = $ih->getThumbnail($banner_attribute, 2000, 2000, false);

$banner_bg = $c->getAttribute('banner_bg');

$banner_mobile_image = $c->getAttribute('mobile_banner');
$banner_mobile_image = $ih->getThumbnail($banner_mobile_image, 1000, 1000, false);

$description = $c->getCollectionDescription();

$cs_desc = $c->getAttribute('cs_desc');

$cs_specs = implode(',',iterator_to_array($c->getAttribute('specifications')));

$category = $c->getAttribute('cs_categories');
$year = $c->getAttribute('year');
$location = $c->getAttribute('cs_location');
$client = $c->getAttribute('cs_client');
$length = $c->getAttribute('cs_length');
$project_name = $c->getAttribute('cs_project_name');
$diameter = $c->getAttribute('cs_diameter');

$relatedProductIds = $c->getAttribute('related_products');

$relatedProducts = [];

if (!is_null($relatedProductIds) && !empty($relatedProductIds)) {
    $relatedProducts = GeneralHelper::getProductPages($relatedProductIds);
}

//$relatedCaseStudies = \Application\Concrete\Models\CaseStudies\CaseStudiesList::getRelatedCaseStudiesBasedOnLocation($c->getCollectionID(), $relatedProductIds);

?>

<section style="background-color: <?php echo $banner_bg ?>; " class="banner-v2 cs-banner  pt-[13rem] md:pt-[15rem] xl:pt-[25rem] px-[2rem] md:px-[4rem] xl:px-[6rem] xxl:px-[10rem] text-rustic-red">
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

        <?php if ($category) { ?>
            <div class="flex justify-center md:mt-[4rem] mt-[2rem]">
                <span class="batch relative z-2 bg-red-berry text-white rounded-[0.6rem]"><?php echo $category ?></span>
            </div>
        <?php } ?>
    </div>

    <div class="img-wrap relative rounded-[2rem] overflow-hidden mt-[4rem]">

        <img class="desktop absolute inset-0 md:block hidden size-full object-cover" src="<?php echo $banner_image ?>" alt="<?php echo $title ?>">

        <img class="mobile absolute  md:hidden inset-0 size-full object-cover" src="<?php echo $banner_mobile_image ?>" alt="<?php echo $title ?>">


    </div>
</section>

<?php $a = new Area('Page Content 1');

$a->display($c); ?>

<section style="background-color: #F2F1EF;" class="cs-details relative  xl:py-[10rem] md:py-[8rem] py-[5rem] text-rustic-red">
    <div class="flex flex-wrap justify-between">
        <article class="left xl:w-1/2 w-full xxl:pl-[10rem] xl:pl-[6rem] md:pl-[4rem] pl-[2rem] xl:pr-[0rem] md:pr-[4rem] pr-[2rem]">
            <h3 class="md:mb-[4rem] mb-[3rem] fade-text" data-duration="1">About the project</h3>
            <div class="fade-text" data-duration="1">
                <!-- <p>
                    Diam in sit quam nibh sed. Tempus malesuada nunc quisque faucibus. Purus in nulla arcu phasellus hendrerit accumsan. In lorem eu egestas elit nunc. Nullam nec id iaculis lorem tortor at sed convallis. Netus id nulla urna viverra aliquam tellus. Amet vestibulum in volutpat habitant egestas sed dictum ac.
                </p>
                <p>
                    Magna ultricies in nulla odio diam sit nibh ut arcu. Quam porta sed ut feugiat. Commodo felis et volutpat diam aenean lectus at. Ut volutpat amet arcu blandit. Scelerisque volutpat vestibulum dictum nunc ut.
                </p> -->
                <?php echo $cs_desc ?>
            </div>
        </article>
        <article class="right xl:w-1/2 xl:gap-y-[6rem] gap-y-[4rem]   w-full xl:mt-[0rem] mt-[4rem]  flex flex-wrap items-start h-full fade-text" data-duration="1.2">

            <?php if ($client) { ?>
                <div class="content w-full  relative">
                    <h6 class="xl:px-[3rem] md:px-[4.5rem] uppercase font-[1.6rem]">Client</h6>
                    <p class="xl:pl-[3rem] md:pl-[4.5rem]  pb-[3rem] mt-[1.5rem] relative">
                        <!-- Gravida venenatis gravida lorem quiser query -->
                        <?php echo $client ?>
                        <!-- <span class="line absolute"></span> -->
                    </p>

                </div>
            <?php }  ?>

            <?php if ($location) { ?>
                <div class="content w-full md:w-1/2  relative">
                    <h6 class="xl:px-[3rem] md:px-[4.5rem] uppercase font-[1.6rem]">Location</h6>
                    <p class="xl:pl-[3rem] md:pl-[4.5rem] pb-[3rem] mt-[1.5rem] relative">
                        <!-- Dubai -->
                        <?php echo $location ?>
                        <!-- <span class="line absolute"></span> -->
                    </p>
                </div>
            <?php } ?>
            <?php if ($year) { ?>
                <div class="content w-full md:w-1/2 relative">
                    <h6 class="xl:px-[3rem] md:px-[4.5rem] uppercase font-[1.6rem]">Year</h6>
                    <p class="xl:pl-[3rem] md:pl-[4.5rem] pb-[3rem] mt-[1.5rem] relative">
                        <!-- 2023 -->
                        <?php echo $year ?>
                        <!-- <span class="line absolute"></span> -->
                    </p>
                </div>
            <?php } ?>
            <?php if ($location) { ?>
                <div class="content w-full md:w-1/2 relative">
                    <h6 class="xl:px-[3rem] md:px-[4.5rem] uppercase font-[1.6rem]">Length</h6>
                    <p class="xl:pl-[3rem] md:pl-[4.5rem] pb-[3rem] mt-[1.5rem] relative">
                        <!-- 4.34m -->
                        <?php echo $length ?>
                        <!-- <span class="line absolute"></span> -->
                    </p>
                </div>
            <?php } ?>
            <?php if ($diameter) { ?>
            <div class="content w-full md:w-1/2  relative">
                <h6 class="xl:px-[3rem] md:px-[4.5rem] uppercase font-[1.6rem]"><?php echo t('Diameter'); ?></h6>
                <p class="xl:pl-[3rem] md:pl-[4.5rem] pb-[3rem] mt-[1.5rem] relative">
                    <?php echo $diameter ?>
                    <!-- <span class="line absolute"></span> -->
                </p>
            </div>
            <?php } ?>
            <?php if ($project_name) { ?>
                <div class="content w-full md:w-1/2  relative">
                    <h6 class="xl:px-[3rem] md:px-[4.5rem] uppercase font-[1.6rem]"><?php echo t('Product'); ?></h6>
                    <p class="xl:pl-[3rem] md:pl-[4.5rem] pb-[3rem] mt-[1.5rem] relative">
                        <!-- Slip liner -->
                        <?php echo $project_name ?>
                        <!-- <span class="line absolute"></span> -->
                    </p>
                </div>
            <?php } ?>
            <?php if ($cs_specs) { ?>
                <div class="content w-full md:w-1/2  relative">
                    <h6 class="xl:px-[3rem] md:px-[4.5rem] uppercase font-[1.6rem]">Design Specification</h6>
                    <p class="xl:pl-[3rem] md:pl-[4.5rem] pb-[3rem] mt-[1.5rem] relative">
                        <!-- Fringilla, creative, elemnter -->
                        <?php echo $cs_specs ?>
                        <!-- <span class="line absolute"></span> -->
                    </p>
                </div>
            <?php } ?>
        </article>
    </div>
</section>

<?php $a = new Area('Page Content 3');

$a->display($c); ?>

<?php if (GeneralHelper::pagesExist($relatedProducts)) { ?>
    <section style="background-color: #1A0A0C;" class="products-list related-products text-white xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] overflow-hidden js-related-products">


        <h6 class="mb-[2rem] fade-text" data-duration="1">
            What we do
        </h6>


        <div class="title flex flex-wrap">
            <div class="left  xl:w-1/2 w-full flex justify-between items-center">
                <div class="fade-text" data-duration="1.2" data-delay="0.2">
                    <h2>Products</h2>
                </div>
            </div>

            <div class="right flex xl:justify-end justify-between items-center  xl:w-1/2 w-full mt-[2rem] xl:mt-[0rem] fade-text" data-duration="1" data-delay="0.2">

                <a href="<?php echo View::url('/products'); ?>" class="channeline-btn channeline-btn--arrow channeline-btn--border channeline-btn--white relative">
                    <span class="text z-2 relative">Read More</span>
                    <div class="shape absolute">
                        <i class="icon-right_button_arrow absolute z-2"></i>
                        <span class="bg absolute inset-0 size-full"></span>
                    </div>
                </a>


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
                    <?php View::element('products/view', ['pages' => $relatedProducts]); ?>
                </div>
            </div>
        </div>
    </section>

<?php } ?>

<?php $a = new Area('Page Content 2');

$a->display($c); ?>

<!-- <? //php if (GeneralHelper::pagesExist($relatedCaseStudies)) { 
        ?>
    
    <? //php View::element('case_studies/relatedCaseStudies', ['pages' => $relatedCaseStudies]); 
    ?>

<? //php } 
?> -->