<!-- Main listing page -->
<?php defined("C5_EXECUTE") or die("Access Denied."); ?>

<section class="listing--block  vacancy-listing--page vacancy-section bg-romance ">


    <!-- Main Title -->
    <div class="title-block flex flex-wrap justify-between xl:py-[10rem] md:py-[8rem] xxl:px-[10rem] lg:px-[6rem] md:px[4rem] px-[2rem] py-[5rem]">


        <!-- Title -->
        <?php if (isset($title) && trim($title) != "") { ?>
            <div class="title fade-text" data-duration="1">
                <h2 class="text-black"><?php echo h($title); ?></h2>
            </div>
        <?php } ?>


        <!-- link -->
        <?php if (isset($button_URL) && isset($button_Title)) { ?>


            <div class="fade-text" data-duration="0.6" data-delay="0.2">
                <a href="<?php echo $button_URL; ?>" class="channeline-btn channeline-btn--arrow channeline-btn--border channeline-btn--red relative " href="https://channeline.1020dev.com/index.php/services">
                    <span class="text z-2 relative"><?php echo $button_Title; ?></span>
                    <div class="shape absolute">
                        <i class="icon-right_button_arrow absolute z-2"></i>
                        <span class="bg absolute inset-0 size-full"></span>
                    </div>
                </a>
            </div>
        <?php } ?>


    </div>




    <!-- Vacancy Titles -->
    <div class="head  pb-5 flex justify-between xxl:px-[10rem] lg:px-[6rem] md:px-[3rem] px-[2rem] lg:pb-[2.6rem]">
        <!-- Vacancy name -->
        <div class="vacancy-name left ">
            <h5 class="tab-title ">Vacancy</h5>
        </div>

        <!-- Vacancy dept -->
        <div class="vacancy-dept center ">
            <h5 class="tab-title">Department</h5>
        </div>

        <!-- Vacancy type -->
        <div class="vacancy-type right ">
            <h5 class="tab-title">Type</h5>
        </div>

        <!-- Vacancy closing date -->
        <div class="vacancy-closing-date right ">
            <h5 class="tab-title">Closing Date</h5>
        </div>

    </div>


    <!-- Vacancy Data -->
    <div class="bind--data flex flex-wrap vacancy-section-list vacancy--listing">
        <?php View::element("vacancy/view", ["pages" => $pages, 'counter' => 0]); ?>

        <?php echo $token; ?>
        <?php echo $form->hidden('itemsCounter', $itemsCounter); ?>
    </div>



    <!-- Load More -->
    <?php if (isset($enablePagination) && $enablePagination && isset($paginationStyle)) { ?>

        <div class="load-more--wrapper flex flex-col  justify-center items-center w-full xl:mt-[6rem] md:mt-[3rem] mt-[2rem]">
            <?php if (isset($enablePagination) && $enablePagination && isset($paginationStyle)) { ?>


                <a data-pagination-style="<?php echo $paginationStyle; ?>" data-load-more="<?php echo $loadMore; ?>" <?php if (!isset($loadMore) || $paginationStyle == "on_scroll") { ?>style="display: none" <?php } ?> class="load--more | channeline-btn channeline-btn--rounded-red text-center relative z-2 overflow-hidden">
                    <span class="z-2 relative  !text-white"><?php echo isset($loadMoreText) ? $loadMoreText : t("Load more"); ?></span>
                    <span class="circle absolute z-1 !bg-rustic-red"></span>
                </a>

                <div class="loader mt-[4rem]" style="display : none;">
                    <h4><?= t("Loading...") ?></h4>
                </div>
            <?php } ?>
        </div>
    <?php } ?>



</section>