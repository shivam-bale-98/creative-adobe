<?php defined("C5_EXECUTE") or die("Access Denied.");

$ih = new \Application\Concrete\Helpers\ImageHelper();
?>

















<section id="banner" class="<?php if (isset($hideBlock) && trim($hideBlock) == 1) { ?>hide-block<?php } ?> banner-v1 | text-white px-[2rem] md:px-[4rem] xl:px-[6rem] xxl:px-[10rem] pt-[8rem] md:pt-[12rem] md:pb-[7.3rem] pb-[4rem] relative h-screen z-3 flex items-start flex-col justify-end">



    <?php if (!empty($bannerItems_items)) { ?>

        <div class="home_banner_slider absolute inset-0 size-full | swiper ">
            <div class="swiper-wrapper">
                <?php foreach ($bannerItems_items as $bannerItems_item_key => $bannerItems_item) { ?>
                    <div class="swiper-slide">
                        <?php if ($bannerItems_item["Dimage"]) { ?>
                            <img class="desktop hidden md:block absolute size-full inset-0 object-cover z-1" src="<?php echo $ih->getThumbnail($bannerItems_item["Dimage"], 2000, 2000) ?>" alt="channeline" />
                        <?php } ?>
                        <?php if ($bannerItems_item["Mimage"]) { ?>
                            <img class="mobile block md:hidden  absolute size-full inset-0 object-cover z-1" src="<?php echo $ih->getThumbnail($bannerItems_item["Mimage"], 2000, 2000) ?>" alt="channeline" />
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($bannerItems_items)) { ?>
        <div class="content z-3 relative max-w-[89rem] w-full">
            <div class="swiper banner_conent overflow-visible">
                <div class="swiper-wrapper">
                    <?php foreach ($bannerItems_items as $bannerItems_item_key => $bannerItems_item) { ?>
                        <div class="swiper-slide">
                            <?php if (isset($bannerItems_item["subTitle"]) && trim($bannerItems_item["subTitle"]) != "") { ?>
                                <h6 class="md:mb-[3rem] mb-[2rem]"><?php echo h($bannerItems_item["subTitle"]); ?></h6>
                            <?php } ?>
                            <?php if (isset($bannerItems_item["title"]) && trim($bannerItems_item["title"]) != "") { ?>
                                <?php echo $bannerItems_item["title"]; ?>
                            <?php } ?>

                            <?php if (!empty($bannerItems_item["ctaLink"]) && ($bannerItems_item["ctaLink_c"] = Page::getByID($bannerItems_item["ctaLink"])) && !$bannerItems_item["ctaLink_c"]->error && !$bannerItems_item["ctaLink_c"]->isInTrash()) { ?>
                                <?php echo '<a class="channeline-btn text-center channeline-btn--white relative z-2 overflow-hidden mt-8" href="' . $bannerItems_item["ctaLink_c"]->getCollectionLink() . '">' . (isset($bannerItems_item["ctaLink_text"]) && trim($bannerItems_item["ctaLink_text"]) != "" ? $bannerItems_item["ctaLink_text"] : $bannerItems_item["ctaLink_c"]->getCollectionName()) . '</a>'; ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>


    <div class="navigation | sm:pt-16 pt-8  flex  z-3">
        <div class=" swiper-buttons  | white flex ">
            <div class="swiper-button swiper-button__prev  | h-20 w-20 border border-white rounded-full flex items-center justify-center">
                <svg width="15" height="8" viewBox="0 0 15 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.646446 3.64645C0.451184 3.84171 0.451184 4.15829 0.646446 4.35355L3.82843 7.53553C4.02369 7.7308 4.34027 7.7308 4.53553 7.53553C4.7308 7.34027 4.7308 7.02369 4.53553 6.82843L1.70711 4L4.53553 1.17157C4.7308 0.976311 4.7308 0.659728 4.53553 0.464466C4.34027 0.269204 4.02369 0.269204 3.82843 0.464466L0.646446 3.64645ZM15 3.5L1 3.5V4.5L15 4.5V3.5Z" fill="white"></path>
                </svg>
            </div>
            <div class="swiper-button swiper-button__next  | h-20 w-20 border border-white rounded-full flex items-center justify-center">
                <svg width="15" height="8" viewBox="0 0 15 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.3536 3.64645C14.5488 3.84171 14.5488 4.15829 14.3536 4.35355L11.1716 7.53553C10.9763 7.7308 10.6597 7.7308 10.4645 7.53553C10.2692 7.34027 10.2692 7.02369 10.4645 6.82843L13.2929 4L10.4645 1.17157C10.2692 0.976311 10.2692 0.659728 10.4645 0.464466C10.6597 0.269204 10.9763 0.269204 11.1716 0.464466L14.3536 3.64645ZM0 3.5L14 3.5V4.5L0 4.5L0 3.5Z" fill="white"></path>
                </svg>
            </div>
        </div>
    </div>
</section>