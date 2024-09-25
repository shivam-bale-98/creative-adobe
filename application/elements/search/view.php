<?php

use Application\Concrete\Helpers\GeneralHelper;

if (GeneralHelper::pagesExist($pages)) {
    foreach ($pages as $page) {
        $title = $page->getTitle();
        $description = $page->getCollectionDescription();
        $link = $page->getUrl();
        $breadCrumb = $page->getBreadCrumb();
        $searchImg = $page->getSearchImage();
?>


        <div class="search_card  relative">
            <a class="absolute w-full h-full left-0 right-0 top-0 " href="<?php echo $link; ?>">
            </a>

            <div class="search_label sm:pb-[9.8rem]">

                <ul class="mb-0 breadcrumb-list list-unstyled flex flex-wrap items-center">
                    <?php echo $breadCrumb; ?>
                </ul>
            </div>
            <div class="search_details flex flex-col gap-[2rem]">


                <div class="img-wrap relative rounded-[2rem] overflow-hidden z-2">
                    <?php if ($searchImg) { ?>
                        <img class="absolute inset-0 size-full z-1 object-cover | lazy" src="<?php echo $searchImg ?>" alt="load-more">
                    <?php } ?>
                </div>

                <h4><?php echo $title; ?></h4>
                <p><?php echo $description; ?></p>
            </div>
        </div>
<?php
    }
} ?>