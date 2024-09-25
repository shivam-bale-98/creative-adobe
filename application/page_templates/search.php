<?php defined("C5_EXECUTE") or die("Access Denied.");
$themePath = $view->getThemePath();
$c = Page::getCurrentPage();
$site = Config::get('concrete.site');
$form = Core::make('helper/form');

use  \Application\Concrete\Helpers\ImageHelper;
use Application\Concrete\Helpers\GeneralHelper;

$ih = new ImageHelper();
$banner_title = $c->getAttribute('banner_title');
$title = $c->getCollectionName();
$search = $c->getAttribute('enable_search');
$banner_bg = $c->getAttribute('banner_bg');

?>


<div class="listing--block-main search-main-container main-search bg-romance pb-[5rem] sm:pb-[10rem]" style="background-color: <?php echo $banner_bg ?>;">
    <?php echo $tokenOutput;
    ?>

    <section class="search-banner search-page banner-v2  sm:pt-[15rem] xxl:px-[10rem]  xl:px-[6rem] md:px-[4rem] px-[2rem] xl:pt-[24.1rem] md:pt-[8rem] pt-[12rem] ">
        <!-- Breadcrumb -->
        <div class="content text-center prose-a:text-rustic-red prose-li:text-rustic-red">
            <?php $s = Stack::getByName('breadcrumb');
            if ($s) {
                $s->display();
            } ?>

            <?php if ($banner_title) { ?>
                <h1 class="h2 text-rustic-red"><?php echo $banner_title ?></h1>
            <?php } else { ?>
                <h1 class="h2 text-rustic-red"><?php echo $title ?></h1>
            <?php } ?>



        </div>

        <!-- Title -->
        <!-- <div class="fade-text" data-duration="1">
            <h2 class="text-rustic-red text-center mt-[2.1rem] "></?php echo $title ?></h2>
        </div> -->

        <!-- Search -->
        <div class="search-wrapper | gap-[2rem] flex flex-wrap flex-col items-center  justify-center w-full  sm:pt-[4rem] pt-[2rem]">
            <!-- Search -->
            <div class="search-box flex justify-between items-center w-full bg-white group">

                <?php echo $form->text('keywords', isset($keywords) ? $keywords : '', ["placeholder" => t('Search'), 'class' => "keywords_search"]) ?>

                <button class="search-btn flex items-center justify-center mr-[3rem]" id="search--btn">
                    <i class="icon-search z-2 text-[2.2rem] [&:before]:text-black transition duration-300 ease-linear group hover:[&:before]:text-red-berry"></i>
                </button>

            </div>

            <!-- Result -->
            <div class="search-result mt-[1rem]">
                <p class="search--count text-gunmetal opacity-0"><?php echo $countMessage; ?></p>
            </div>

        </div>
    </section>





    <section class="search-page-main search--grid">

        <!-- Tabs Button -->
        <!-- <div class="serach-btn | sm:pt-[6rem] sm:pb-[10rem] pt-[4rem] pb-[4rem] xl:px-[6rem] md:px-[4rem] px-[2rem]">
            
            <div class="searchSlider swiper">
                <div class="swiper-wrapper filter--options  items-center lg:justify-center filters !flex-nowrap">
                    
                    <?php
                    foreach ($searchTypeOptions as $key => $value) {
                        $selectedValue = !empty($selectedCategory) ? $selectedCategory : '';
                    ?>
                        <div class="swiper-slide  translate-y-[2rem]">
                            <a class="inline-block text-[1.6rem] font-normal border leading-[1.52rem] text-rustic-red
                             border-rustic-red
                            search-btn  channeline-btn--bordered_black  channeline-tab [&.active]:bg-red-berry [&.active]:bg-red-berry [&.active]:text-white [&.active]:border-red-berry  bg-transparent  px-[2rem] py-[1.6rem] rounded-[3rem]  !hover:text-decoration-none <?php echo ($selectedValue == $key) ? 'active' : '' ?>" data-value="<?php echo $key; ?>"><span class="relative"><?php echo $value; ?></span>
                                <i class="bg"></i>
                            </a>

                        </div>
                    <?php }
                    ?>
                </div>
            </div>
        </div> -->

        <div class="search_grid xl:mt-[10rem] md:mt-[8rem] mt-[4rem]">

            <div class="search--listing">
                <?php echo View::element($viewType, ['pages' => $pages]); ?>
            </div>

            <div class="no-result-found error-message" style="<?php echo $count > 0 ? 'display:none' : 'display:block'; ?>">
                <?php echo View::element($noResultsFound, []); ?>
            </div>

            <div class="flex items-center justify-center w-full mt-[6rem]">
                <a class=" load--more--btn channeline-btn channeline-btn--rounded-red relative z-2 overflow-hidden text-center">
                    <span class="z-2 relative  !text-white"><?php echo t('Load more') ?></span>
                    <span class="circle absolute z-1  !bg-rustic-red"></span>
                </a>
            </div>

            <div class="loader flex justify-center mt-[5rem]" style="display : none;">
                <h4><?= t("loading...") ?></h4>
            </div>

        </div>
    </section>
</div>