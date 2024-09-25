<?php defined("C5_EXECUTE") or die("Access Denied."); ?>
<section class="listing--block" style="margin-top: 170px;">
    <?php if (isset($title) && trim($title) != "") { ?>
        <?php echo h($title); ?>
    <?php } ?>

    <!-- link -->
    <?php if (isset($button_URL) && isset($button_Title)) { ?>
        <a href="<?php echo $button_URL; ?>"><?php echo $button_Title; ?></a>
    <?php } ?>

    <!-- Filters -->
    <div>
        <!-- Search -->
        <?php if (isset($enableSearch) && $enableSearch) {
            echo $form->text("keywords", null, ["placeholder" => isset($searchPlaceHolderText) ? $searchPlaceHolderText : t("Search")]);
        } ?>

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

    <div>
        <div class="bind--data">
           
            <?php View::element("case_studies/view", ["pages" => $pages, 'counter' => 0]); ?>
        </div>

        <div class="loader" style="display : none;">
            <h4><?= t("loading...") ?></h4>
        </div>

        <?php if (isset($enablePagination) && $enablePagination && isset($paginationStyle)) { ?>
            <div class="flex items-center justify-center w-full" <?php if (!isset($loadMore) || $paginationStyle == "on_scroll") { ?>style="display: none" <?php } ?> data-pagination-style="<?php echo $paginationStyle; ?>" data-load-more="<?php echo $loadMore; ?>"><a class="channeline-btn channeline-btn--rounded-red relative z-2 overflow-hidden text-center"><span class="z-2 relative "><?php echo isset($loadMoreText) ? $loadMoreText : t("Load more"); ?></span>
                    <span class="circle absolute z-1"></span>
                </a>
            </div>
        <?php } ?>

        <!-- <div class="flex items-center justify-center w-full bdFadeUp">
            <a class="channeline-btn channeline-btn--rounded-red relative z-2 overflow-hidden text-center" href="https://channeline.1020dev.com/index.php/services">
                <span class="z-2 relative capitalize">Learn More</span>
                <span class="circle absolute z-1"></span>
            </a>
        </div> -->
    </div>

    <?php echo $token; ?>
</section>