<?php defined("C5_EXECUTE") or die("Access Denied."); ?>
<section class="listing--block products-listing--page mt-[4rem] xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:pb-[10rem] md:pb-[8rem] pb-[5rem] text-rustic-red">
    <?php if (isset($title) && trim($title) != "") { ?>
        <?php echo h($title); ?>
    <?php } ?>

    <!-- link -->
    <?php if (isset($button_URL) && isset($button_Title)) { ?>
        <a href="<?php echo $button_URL; ?>"><?php echo $button_Title; ?></a>
    <?php } ?>

    <!-- Filters -->
    <div class="flex items-center justify-center search-wrapper">
        <?php if (isset($enableSearch) && $enableSearch) { ?>
            <div class="search-box z-2 relative">
                <!-- Search -->
                <?php if (isset($enableSearch) && $enableSearch) {
                    echo $form->text("keywords", null, ["placeholder" => isset($searchPlaceHolderText) ? $searchPlaceHolderText : t("Search")]);
                } ?>

                <i class="icon-search z-2 absolute"></i>
            </div>
        <?php } ?>


        <!-- sortBy -->
        <?php if (isset($enableSort) && $enableSort && isset($sortOptions) && $sortOptions) {
            echo $form->label("sortOrder", t("Sort By"));
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
    </div>

    <div class="xl:mt-[10rem] md:mt-[8rem] mt-[4rem]">
        <div class="bind--data flex flex-wrap">
            <?php View::element("products/view", ["pages" => $pages]); ?>
        </div>



        <?php if (isset($enablePagination) && $enablePagination && isset($paginationStyle)) { ?>

            <div class="load-more--wrapper flex flex-col  justify-center items-center">
                <?php if (isset($enablePagination) && $enablePagination && isset($paginationStyle)) { ?>


                    <a data-pagination-style="<?php echo $paginationStyle; ?>" data-load-more="<?php echo $loadMore; ?>" <?php if (!isset($loadMore) || $paginationStyle == "on_scroll") { ?>style="display: none" <?php } ?> class="load--more | mt-[6rem] channeline-btn channeline-btn--rounded-red text-center relative z-2 overflow-hidden">
                        <span class="z-2 relative "><?php echo isset($loadMoreText) ? $loadMoreText : t("Load more"); ?></span>
                        <span class="circle absolute z-1"></span>
                    </a>

                    <div class="loader mt-[4rem]" style="display : none;">
                        <h4><?= t("Loading...") ?></h4>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

    </div>

    <?php echo $token; ?>
</section>