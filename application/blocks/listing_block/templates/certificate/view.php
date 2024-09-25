<?php defined("C5_EXECUTE") or die("Access Denied.");
$themePath = $this->getThemePath();

use Application\Concrete\Helpers\GeneralHelper;

use Application\Concrete\Models\Certificates\Certificates;


$counter = "";
?>
<div class="certificate-main-container">
    <section class="listing--block bg-romance  certificates-listing-block xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:pb-[10rem] md:pb-[8rem] pb-[5rem] text-rustic-red border-b border-solid" id="certificates">
        <?php if (isset($title) && trim($title) != "") { ?>
            <?php echo h($title); ?>
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


        <div class="bind--data certificates-grid flex  flex-wrap justify-between gap-[4rem] w-full item-list-js certificate--popup">
            <?php View::element("certificate/view", ["pages" => $pages, 'counter' => 0]); ?>
        </div>

        <!-- Load More Button -->
        <?php if (isset($enablePagination) && $enablePagination && isset($paginationStyle)) { ?>

            <div class="load-more--wrapper flex flex-col mt-[6rem] justify-center items-center">
                <?php if (isset($enablePagination) && $enablePagination && isset($paginationStyle)) { ?>


                    <a data-pagination-style="<?php echo $paginationStyle; ?>" data-load-more="<?php echo $loadMore; ?>" <?php if (!isset($loadMore) || $paginationStyle == "on_scroll") { ?>style="display: none" <?php } ?> class="load--more | channeline-btn channeline-btn--rounded-red text-center relative z-2 overflow-hidden group">
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
        <?php echo $form->hidden('itemsCounter', $itemsCounter); ?>
    </section>
    <!-- POP UP -->
    <section class="certificate-popup-block fixed inset-0 size-full transition-all-600">
        <div class="t-overlay"></div>
        <span role="button" aria-label="close certificates popup" class="popup-close absolute flex justify-center rounded-full items-center transition-all-600 w-[6rem] h-[6rem]">
        </span>
        <div class="certificate-popup-slider rounded-tl-[2rem] h-full relative bg-rustic-red text-white">

            <div class="swiper">
                <div class="swiper-wrapper popUpItem-list-js certificate--popup">
                    <?php View::element("certificate/popup", ["pages" => $pages, 'counter' => 0]); ?>
                </div>
                <!-- Swiper Buttons -->
                <div class="btn-wrap  flex justify-end gap-15 absolute">


                    <div class="swiper-buttons flex z-3">
                        <div tabindex="0" aria-label="swiper button previous" role="button" class="swiper-button swiper-button-prev relative" aria-label="left slide arrow">
                            <i class="icon-right_button_arrow absolute z-3"></i>
                            <span class="bg absolute inset-0 size-full z-1"></span>
                            <span class="bg-scale absolute  size-full z-2"></span>
                        </div>
                        <div tabindex="0" aria-label="swiper button next" role="button" class="swiper-button swiper-button-next relative" aria-label="left slide arrow">
                            <i class="icon-right_button_arrow absolute z-3"></i>
                            <span class="bg absolute inset-0 size-full z-1"></span>
                            <span class="bg-scale absolute  size-full z-2"></span>
                        </div>
                    </div>

                </div>
            </div>



        </div>
    </section>

</div>