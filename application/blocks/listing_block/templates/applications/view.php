<?php defined("C5_EXECUTE") or die("Access Denied."); ?>
<section class="listing--block application-listing--page xl:mt-[10rem] mt-[6rem] xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:pb-[10rem] md:pb-[8rem] pb-[5rem] text-rustic-red">
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
        <?php if (is_array($pages) && count($pages)) { ?>
            <div class="filters normal-filters | flex flex-wrap justify-between items-center fade-text" data-duration="1" data-delay="0.2">
                <div class="swiper filter-swiper">
                    <div class="swiper-wrapper filter--section">
                        <?php
                        $count = 1;
                        foreach ($pages as $page) {

                        ?>
                            

                            <?php if ($count == 1) { ?>
                                <div class="swiper-slide">
                                <a class="active flex px-[2rem] py-[1.6rem] rounded-[20rem] " href="#application-<?php echo $count ?>">
                                    <span><?= $page->getCollectionName() ?></span>
                                    <i class="bg"></i>
                                </a>
                            </div>
                            <?php } else { ?>

                                <div class="swiper-slide">
                                <a class="flex px-[2rem] py-[1.6rem] rounded-[20rem] " href="#application-<?php echo $count ?>">
                                    <span><?= $page->getCollectionName() ?></span>
                                    <i class="bg"></i>
                                </a>
                            </div>
                            <?php } ?>



                            <?php $count++ ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (is_array($pages) && count($pages)) { ?>
            <div class="filters sticky-filters | z-3 top-0 left-0 w-full fixed flex flex-wrap justify-between items-center p-[2rem]">
                <div class="swiper filter-swiper">
                    <div class="swiper-wrapper filter--section">
                        <?php
                        $count = 1;
                        foreach ($pages as $page) {

                        ?>


                            <?php if ($count == 1) { ?>
                                <div class="swiper-slide">
                                    <a class="active flex px-[2rem] py-[1.6rem] rounded-[20rem] " href="#application-<?php echo $count ?>">
                                        <span><?= $page->getCollectionName() ?></span>
                                        <i class="bg"></i>
                                    </a>
                                </div>
                            <?php } else { ?>
                                <div class="swiper-slide">
                                    <a class="flex px-[2rem] py-[1.6rem] rounded-[20rem] " href="#application-<?php echo $count ?>">
                                        <span><?= $page->getCollectionName() ?></span>
                                        <i class="bg"></i>
                                    </a>
                                </div>
                            <?php } ?>

                            <?php $count++ ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="xl:mt-[10rem] md:mt-[8rem] mt-[4rem]">
            <div class="bind--data">

                <?php View::element("applications/view", ["pages" => $pages, 'counter' => 0]); ?>
            </div>
        </div>
    </div>






    <div class="loader" style="display : none;">
        <h4><?= t("loading...") ?></h4>
    </div>

    <?php if (isset($enablePagination) && $enablePagination && isset($paginationStyle) && $loadMoreFlag) { ?>
        <div class="load--more" <?php if (!isset($loadMore) || $paginationStyle == "on_scroll") { ?>style="display: none" <?php } ?> data-pagination-style="<?php echo $paginationStyle; ?>" data-load-more="<?php echo $loadMore; ?>"><span><?php echo isset($loadMoreText) ? $loadMoreText : t("Load more"); ?></span></div>
    <?php } ?>
    </div>

    <?php echo $token; ?>
    <?php echo $form->hidden('itemsCounter', $itemsCounter); ?>
</section>