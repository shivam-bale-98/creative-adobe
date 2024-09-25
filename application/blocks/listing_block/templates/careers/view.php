<!-- Main listing page -->
<?php defined("C5_EXECUTE") or die("Access Denied."); ?>

<section class="listing--block careers vacancy-section bg-romance  vacancy-listing--page border-b border-solid xl:pb-[10rem] md:pb-[8rem] pb-[4rem]">

    <!-- Search -->
    <div class="search-wrapper | gap-[2rem] flex flex-wrap  flex-col sm:flex-row justify-between  sm:pt-[10rem] pt-[2rem] xxl:px-[10rem]  lg:px-[6rem] md:px-[3rem] px-[2rem] xl:pb-[10rem] md:pb-[8rem] pb-[5rem]">

        <!-- Search -->
        <div class="search-box flex justify-between items-center w-full bg-white group">

            <!-- Search -->
            <?php if (isset($enableSearch) && $enableSearch) {
                echo $form->text("keywords", null, ["placeholder" => isset($searchPlaceHolderText) ? $searchPlaceHolderText : t("Search")]);
            } ?>

            <button class="search-btn flex items-center justify-center mr-[3rem]" id="search--btn">
                <i class="icon-search z-2 text-[2.2rem] [&:before]:text-black transition duration-300 ease-linear group hover:[&:before]:text-red-berry"></i>
            </button>

        </div>
        <!-- sortBy -->
        <div class="filters banner-filters | flex h-full">
            <div class="news-listing tabs ">
                <!-- sortBy -->
                <?php if (isset($enableSort) && $enableSort && isset($sortOptions) && $sortOptions) {
                    //echo $form->label("sortOrder", t("Sort By"));
                    echo $form->select("sortOrder", $sortOptions, null, ["class" => "sort--filter"]);
                } ?>

                <!-- Dynamic Filters -->
                <?php if (isset($filters) && $filters) {
                    foreach ($filters as $filter) {
                        if ($filter["allowMultiple"]) {
                            $fieldType = "selectMultiple";
                        } else {
                            $fieldType = "select";
                        }

                        echo $form->label($filter["key"], $filter["label"]);
                        echo $form->{$fieldType}($filter["key"], $filter["options"], null, ["class" => "block--filter"]);
                    }
                } ?>
                <div class="dropdown-result" data-lenis-prevent></div>
            </div>

        </div>


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
    <!--  -->

    <!-- Vacancy Data -->
    <div class="bind--data flex flex-wrap vacancy-section-list vacancy--listing">
        <?php View::element("vacancy/view", ["pages" => $pages, 'counter' => 0]); ?>

        <?php echo $token; ?>
        <?php echo $form->hidden('itemsCounter', $itemsCounter); ?>
    </div>


    <!-- Load More -->
    <?php if (isset($enablePagination) && $enablePagination && isset($paginationStyle)) { ?>

        <div class=" load-more--wrapper flex flex-col  justify-center items-center w-full xl:mt-[6rem] md:mt-[3rem] mt-5">
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