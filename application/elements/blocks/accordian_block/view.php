<?php defined("C5_EXECUTE") or die("Access Denied.");
$themePath = $view->getThemePath();
?>


<section style="background-color:#F2F1EF;" class="section accordion-block xxl:px-40 xl:px-[6rem] md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] text-rustic-red">
    <div class="flex  flex-col lg:flex-row justify-between gap-12 lg:gap-[8.6rem]">
        <?php if (isset($blockTitle) && trim($blockTitle) != "") { ?>
            <!-- <h2>Our Corporate Social Responsibility</h2> -->
            <div class="title lg:w-1/2 w-full fade-text" data-duration="1" data-delay="0.2">
                <h2><?php echo h($blockTitle); ?></h2>
            </div>

        <?php } ?>

        <?php if (!empty($accordions_items)) { ?>
            <div class="acc-container lg:w-1/2 w-full">
                <?php foreach ($accordions_items as $accordions_item_key => $accordions_item) { ?>
                    <div class="acc active:bg-red-berry active:text-white group">
                        <?php if (isset($accordions_item["accordionTitle"]) && trim($accordions_item["accordionTitle"]) != "") { ?>
                            <div class="acc-head">
                                <p class="p1 group active:text-white"><?php echo h($accordions_item["accordionTitle"]); ?></p>
                                <!-- <p class="p1">Overcrowded and Misaligned Teeth </p> -->
                                <div class="button group active:bg-white"></div>
                            </div>
                        <?php } ?>
                        <?php if (isset($accordions_item["accordionContent"]) && trim($accordions_item["accordionContent"]) != "") { ?>
                            <div class="acc-content">
                                <p class="p41">
                                    <?php echo h($accordions_item["accordionContent"]); ?>
                                    <!-- I find that most men would rather have their bellies opened for five hundred dollars than have a tooth pulled for five -->
                                </p>
                                <div class="button"></div>

                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>


            </div>
        <?php } ?>

    </div>
</section>

<section class="video">
    <div> <img class="" src="<?php echo $themePath ?>/assets/images/video.jpg" alt="load-more"></div>
</section>


<section class="vacancy-section  text-black">
    <div class="head pt-10 pb-5 flex justify-between px-[6rem] pb-[2.6rem]">
        <div class="vacancy-type left ">
            <h5 class="tab-title">Vacancy</h5>
        </div>
        <div class="vacancy-dept center ">
            <h5 class="tab-title">Department</h5>
        </div>
        <div class="vacancy-type right ">
            <h5 class="tab-title">Type</h5>
        </div>
        <div class="vacancy-closing-date right ">
            <h5 class="tab-title">Closing Date</h5>
        </div>

    </div>
    <div class="flex flex-wrap vacancy-section-list vacancy--listing">
        <a href="" class="vacancy-list-card flex justify-between items-center w-full px-[6rem] py-[5rem]">
            <div class="first-div left flex items-center gap-[10.5rem]">
                <span class="number text-[2rem] leading-[1.9rem] font-normal">01</span>
                <h3 class="">Sales associate</h3>
            </div>
            <div class="second-div center">
                <h5 class="tab-title"> Management</h5>
            </div>
            <div class="third-div right">
                <h5 class="tab-title">Dubai</h5>
            </div>
            <div class="fourth-div right">
                <h5 class="tab-title">Part Time</h5>
            </div>
            <div class="five-div right ">
                <div class="channeline-btn--arrow">
                    <div class="shape absolute">
                        <i class="icon-right_button_arrow absolute z-2"></i>
                        <span class="bg absolute inset-0 size-full"></span>
                    </div>
                </div>

            </div>
        </a>







    </div>
    <div class="no-result-found error-message pb-xl-12 pb-mb-10 pb-7" style="display:none">
        <div class="w-full error-message no-results text-center mt-xl-16 mt-md-12 mt-10">
            <h2 class="sub-title">No results found</h2>
        </div>
    </div>
    <div class="load-more w-auto justify-content-center text-align-center my-7 my-xl-12 px-3 px-sm-4 px-md-9 px-xl-15-h load-more-wrapper" style="display: none;">
        <div class="load-more-btn text-center">
            <div class="logo position-relative">
                <img class="brown-logo position-absolute inset-0" src="/application/themes/seddiqi/assets/images/load-more-brown.svg" alt="load-more">
                <img class="blue-logo position-absolute inset-0" src="/application/themes/seddiqi/assets/images/load-more-blue.svg" alt="load-more">
            </div>
            <h3 class="mt-5 w-auto">Load more</h3>
        </div>
    </div>
</section>