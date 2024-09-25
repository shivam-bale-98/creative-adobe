<?php defined("C5_EXECUTE") or die("Access Denied."); ?>
<div class="listing--block main-news news-listing--page  bg-romance border-b border-solid">




    <!-- link -->
    <?php if (isset($button_URL) && isset($button_Title)) { ?>
        <a href="<?php echo $button_URL; ?>"><?php echo $button_Title; ?></a>
    <?php } ?>

    <!-- Filters -->
    <div class="search-wrapper | gap-[2rem] flex flex-wrap  flex-col sm:flex-row justify-between xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:pb-[9.5rem] md:pb-[8rem] pb-[5rem]">

        <!-- Search -->
        <div class="search-box flex justify-between items-center w-full bg-white">

            <?php if (isset($enableSearch) && $enableSearch) {
                echo $form->text("keywords", null, ["placeholder" => isset($searchPlaceHolderText) ? $searchPlaceHolderText : t("Search")]);
            } ?>
            <button class="search-btn flex items-center justify-center mr-[3rem]" id="search--btn">
                <i class="icon-search z-2 text-[2.2rem] [&:before]:text-black transition duration-300 ease-linear group hover:[&:before]:text-red-berry"></i>
            </button>

        </div>


        <!-- sortBy -->
        <div class="filters banner-filters | flex h-full filter--section">
            <div class="news-listing tabs ">
                <?php if (isset($enableSort) && $enableSort && isset($sortOptions) && $sortOptions) {
                    // echo $form->label("sortOrder", t("Sort By"));
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

    <section class="news-list  flex flex-wrap text-black xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem]  xl:pb-[10rem] md:pb-[8rem] pb-[5rem]">

        <!-- Blog Element  View -->
        <div class="bind--data news--listing flex flex-wrap w-full justify-between">
            <?php View::element("blog/view", ["pages" => $pages]); ?>
        </div>






        <!-- Load More -->
        <?php if (isset($enablePagination) && $enablePagination && isset($paginationStyle)) { ?>

            <div class="load-more--wrapper flex flex-col  justify-center items-center w-full">
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




        <?php echo $token; ?>

    </section>

</div>