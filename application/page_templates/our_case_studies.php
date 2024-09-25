<?php
$themePath = $view->getThemePath();
$c = Page::getCurrentPage();
$site = Config::get('concrete.site');

use  \Application\Concrete\Helpers\ImageHelper;
use Application\Concrete\Helpers\GeneralHelper;

$ih = new ImageHelper();

$banner_title = $c->getAttribute('banner_title');
$title = $c->getCollectionName();

$search = $c->getAttribute('enable_search');

$filters = $c->getAttribute('filters');

$tabs = $c->getAttribute('tabs');
$banner_bg = $c->getAttribute('banner_bg');
$md = new Mobile_Detect();
$isMobileTablet = $md->isMobile() || $md->isTablet();
?>
<div class="case-studies-main-container" style="background-color: <?php echo $banner_bg ?>;">
    <section class="banner-v2  pt-[13rem] md:pt-[15rem] xl:pt-[25rem] text-rustic-red">
        <div class="content text-center px-[2rem] md:px-[4rem] xl:px-[6rem] xxl:px-[10rem]">
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

        <div class="search-wrapper |  flex flex-wrap group flex-col  justify-between items-center  xl:px-[6rem] xxl:px-[10rem] md:px-[4rem] px-[2rem] xl:pb-[10rem] md:pb-[8rem] pb-[5rem] js-filter--section <?php if($hasFiltersSelected) { echo "active"; } ?> ">
            <?php echo $tokenOutput;  ?>
            <?php echo $form->hidden('itemsCounter', $itemsCounter); ?>
            <div class="multi-wrap flex md:mt-[4rem] mt-[2rem] w-full justify-center items-center md:gap-[2rem] gap-[1.5rem]">
                <div class="search-box flex justify-between items-center w-full bg-white">
                    <?php echo $form->text('keywords', isset($keywords) ? $keywords : '', ["placeholder" => t('Search'), 'class' => "keywords_search"]) ?>
                    <button class="search-btn flex items-center justify-center md:mr-[3rem] mr-[2rem]" id="search--btn">
                        <i class="icon-search z-2 text-[2.2rem] [&:before]:text-black transition duration-300 ease-linear group hover:[&:before]:text-red-berry"></i>
                    </button>
                </div>
                <div class="open--filters h-[5.5rem] w-[5.5rem]   rounded-[1rem] overflow-hidden bg-white flex justify-center items-center">
                    <svg width="21" height="17" viewBox="0 0 21 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.670213 3.92484H4.53511C4.68893 4.66359 5.09991 5.32776 5.69842 5.80482C6.29693 6.28189 7.04616 6.54249 7.81915 6.54249C8.59214 6.54249 9.34137 6.28189 9.93988 5.80482C10.5384 5.32776 10.9494 4.66359 11.1032 3.92484H20.3298C20.5075 3.92484 20.678 3.85598 20.8037 3.73341C20.9294 3.61083 21 3.44459 21 3.27125C21 3.0979 20.9294 2.93166 20.8037 2.80909C20.678 2.68651 20.5075 2.61765 20.3298 2.61765H11.1032C10.9494 1.8789 10.5384 1.21473 9.93988 0.737669C9.34137 0.260609 8.59214 0 7.81915 0C7.04616 0 6.29693 0.260609 5.69842 0.737669C5.09991 1.21473 4.68893 1.8789 4.53511 2.61765H0.670213C0.492461 2.61765 0.32199 2.68651 0.196301 2.80909C0.0706114 2.93166 0 3.0979 0 3.27125C0 3.44459 0.0706114 3.61083 0.196301 3.73341C0.32199 3.85598 0.492461 3.92484 0.670213 3.92484ZM7.81915 1.31046C8.21682 1.31046 8.60555 1.42546 8.9362 1.64092C9.26685 1.85637 9.52456 2.1626 9.67674 2.52089C9.82892 2.87917 9.86873 3.27342 9.79115 3.65378C9.71357 4.03413 9.52208 4.38351 9.24088 4.65773C8.95969 4.93195 8.60143 5.1187 8.21141 5.19435C7.82138 5.27001 7.41711 5.23118 7.04971 5.08277C6.68232 4.93437 6.3683 4.68305 6.14736 4.3606C5.92643 4.03815 5.80851 3.65905 5.80851 3.27125C5.80851 2.75121 6.02035 2.25248 6.39741 1.88476C6.77448 1.51705 7.28589 1.31046 7.81915 1.31046ZM20.3298 13.0752H18.2521C18.0983 12.3364 17.6873 11.6722 17.0888 11.1952C16.4903 10.7181 15.7411 10.4575 14.9681 10.4575C14.1951 10.4575 13.4459 10.7181 12.8474 11.1952C12.2489 11.6722 11.8379 12.3364 11.684 13.0752H0.670213C0.492461 13.0752 0.32199 13.144 0.196301 13.2666C0.0706114 13.3892 0 13.5554 0 13.7288C0 13.9021 0.0706114 14.0683 0.196301 14.1909C0.32199 14.3135 0.492461 14.3823 0.670213 14.3823H11.684C11.8379 15.1211 12.2489 15.7853 12.8474 16.2623C13.4459 16.7394 14.1951 17 14.9681 17C15.7411 17 16.4903 16.7394 17.0888 16.2623C17.6873 15.7853 18.0983 15.1211 18.2521 14.3823H20.3298C20.5075 14.3823 20.678 14.3135 20.8037 14.1909C20.9294 14.0683 21 13.9021 21 13.7288C21 13.5554 20.9294 13.3892 20.8037 13.2666C20.678 13.144 20.5075 13.0752 20.3298 13.0752ZM14.9681 15.6895C14.5704 15.6895 14.1817 15.5745 13.851 15.3591C13.5204 15.1436 13.2627 14.8374 13.1105 14.4791C12.9583 14.1208 12.9185 13.7266 12.9961 13.3462C13.0737 12.9659 13.2652 12.6165 13.5463 12.3423C13.8275 12.0681 14.1858 11.8813 14.5758 11.8056C14.9659 11.73 15.3701 11.7688 15.7375 11.9172C16.1049 12.0656 16.4189 12.317 16.6399 12.6394C16.8608 12.9619 16.9787 13.3409 16.9787 13.7288C16.9787 14.2488 16.7669 14.7475 16.3898 15.1152C16.0128 15.483 15.5013 15.6895 14.9681 15.6895Z" fill="black" />
                    </svg>
                </div>
            </div>

            <div class="hidden clear-filter md:mt-[5rem] mt-[4rem] group-[.active]:flex justify-end  w-full">
               <p class="relative pr-[4rem] cursor-pointer clear--filter">Clear all filters</p>
               
            </div>

            <div class="md:pt-[10rem] pt-[6rem]  group-[.active]:md:pt-[3rem] group-[.active]:pt-[2rem] banner-filters filters desktop">
                <div class="flex flex-wrap gap-[2rem]">
                    <div class="select-box tabs relative">
                        <?php echo $form->select("range", $rangeOptions, $selectedRange, ["class" => "select--filters range"]); ?>
                        <div class="dropdown-result" data-lenis-prevent></div>
                    </div>
                    <div class="select-box tabs relative">
                        <?php echo $form->select("product", $productsOptions, $selectedProduct, ["class" => "select--filters product"]); ?>
                        <div class="dropdown-result" data-lenis-prevent></div>
                    </div>
                    <div class="select-box tabs relative">
                        <?php echo $form->select("applications", $applicationsOptions, $selectedApplication, ["class" => "select--filters applications"]); ?>
                        <div class="dropdown-result" data-lenis-prevent></div>
                    </div>
                    <div class="select-box tabs relative">
                        <?php echo $form->select("specification", $specificationOptions, $selectedSpecification, ["class" => "select--filters specification"]); ?>
                        <div class="dropdown-result" data-lenis-prevent></div>
                    </div>
                    <div class="select-box tabs relative">
                        <?php echo $form->select("year", $yearOptions, $selectedYear, ["class" => "select--filters year"]); ?>
                        <div class="dropdown-result" data-lenis-prevent></div>
                    </div>
                    <div class="select-box tabs relative">
                        <?php echo $form->select("location", $locationsOptions, $selectedLocation, ["class" => "select--filters location"]); ?>
                        <div class="dropdown-result" data-lenis-prevent></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <section class="case--studies xl:px-[6rem] xxl:px-[10rem] md:px-[4rem] px-[2rem] xl:pb-[0rem] md:pb-[8rem] pb-[4rem]  text-rustic-red">
       
            <div class="case-studies--listing flex-wrap flex  xl:mt-[8rem] mt-[0rem] justify-between">
                <?php echo View::element($viewType, ['pages' => $pages, 'counter' => 0]); ?>
            </div>
       

        <div class="no-result-found error-message" style="<?php echo $count > 0 ? 'display:none' : 'display:block'; ?>">
            <?php echo View::element($noResultsFound, []); ?>
        </div>

        <div class="load-more--wrapper flex flex-col  justify-center items-center ">
            <!-- <button class="load--more--btn"><? //php echo t("Load More"); 
                                                    ?></button> -->

            <a class="load--more--btn | channeline-btn channeline-btn--rounded-red text-center relative z-2 overflow-hidden xl:mb-[10rem]">
                <span class="z-2 relative "><?php echo t("Load more"); ?></span>
                <span class="circle absolute z-1"></span>
            </a>
        </div>

        <div class="loader flex flex-col  justify-center items-center md:mt-[4rem] mt-[2rem] xl:mb-[10rem]" style="display : none;">
            <h4><?= t("loading...") ?></h4>
        </div>

    </section>
    <? //php if ($isMobileTablet) { 
    ?>
    <div class="fixed banner-filters filters case-study--filters filter--section-mobile">
        <div class="">
            <div class="filter-wrap block">
                <div class="select-box tabs relative">
                    <?php echo $form->select("range", $rangeOptions, $selectedRange, ["class" => "select--filters range"]); ?>
                    <!-- <div class="dropdown-result" data-lenis-prevent></div> -->
                    <div class="selected-field">Diameter : <span>All</span>
                        <b class="absolute"></b>
                    </div>
                    <ul>
                        <?php foreach ($rangeOptions as $key => $option) { ?>
                            <li data-value="<?php echo $option ?>"><?php echo $option ?></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="select-box tabs relative">
                    <?php echo $form->select("product", $productsOptions, $selectedProduct, ["class" => "select--filters product"]); ?>
                    <!-- <div class="dropdown-result" data-lenis-prevent></div> -->
                    <div class="selected-field">Products : <span>All</span><b class="absolute"></b></div>
                    <ul>
                        <?php foreach ($productsOptions as $key => $option) { ?>
                            <li data-value="<?php echo $key ?>"><?php echo $option ?></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="select-box tabs relative">
                    <?php echo $form->select("applications", $applicationsOptions, $selectedApplication, ["class" => "select--filters applications"]); ?>
                    <!-- <div class="dropdown-result" data-lenis-prevent></div> -->
                    <div class="selected-field">Applications : <span>All</span><b class="absolute"></b></div>
                    <ul>
                        <?php foreach ($applicationsOptions as $key => $option) { ?>
                            <li data-value="<?php echo $key ?>"><?php echo $option ?></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="select-box tabs relative">
                    <?php echo $form->select("specification", $specificationOptions, $selectedSpecification, ["class" => "select--filters specification"]); ?>
                    <!-- <div class="dropdown-result" data-lenis-prevent></div> -->
                    <div class="selected-field">Specification : <span>All</span><b class="absolute"></b></div>
                    <ul>
                        <?php foreach ($specificationOptions as $key => $option) { ?>
                            <li data-value="<?php echo $key ?>"><?php echo $option ?></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="select-box tabs relative">
                    <?php echo $form->select("year", $yearOptions, $selectedYear, ["class" => "select--filters year"]); ?>
                    <!-- <div class="dropdown-result" data-lenis-prevent></div> -->

                    <div class="selected-field">Year : <span>All</span><b class="absolute"></b></div>
                    <ul>
                        <?php foreach ($yearOptions as $key => $option) { ?>
                            <li data-value="<?php echo $key ?>"><?php echo $option ?></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="select-box tabs relative">
                    <?php echo $form->select("location", $locationsOptions, $selectedLocation, ["class" => "select--filters location"]); ?>
                    <!-- <div class="dropdown-result" data-lenis-prevent></div> -->

                    <div class="selected-field">Location : <span>All</span><b class="absolute"></b></div>
                    <ul>
                        <?php foreach ($locationsOptions as $key => $option) { ?>
                            <li data-value="<?php echo $key ?>"><?php echo $option ?></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <span class="close absolute h-[4.5rem] w-[4.5rem] bg-romance rounded-full overflow-hidden md:top-[4rem] top-[2.5rem]"></span>
    </div>
    <? //php } 
    ?>
</div>