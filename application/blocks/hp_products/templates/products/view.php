<?php defined("C5_EXECUTE") or die("Access Denied.");

use Application\Concrete\Helpers\GeneralHelper;

?>




<section style="background-color: #1A0A0C;" class="products-list text-white xxl:px-[10rem]  xl:px-[6rem] md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] overflow-hidden js-main-products">

  <?php if (isset($subTitle) && trim($subTitle) != "") { ?>
    <h6 class="mb-[2rem] fade-text" data-duration="1"><?php echo h($subTitle); ?></h6>
  <?php } ?>

  <div class="title flex flex-wrap">
    <div class="left xl:w-3/4 md:w-1/2 w-full flex justify-between items-center">
      <?php if (isset($title) && trim($title) != "") { ?>
        <div class="fade-text" data-duration="1.2" data-delay="0.2">
          <h2><?php echo h($title); ?></h2>
        </div>
      <?php } ?>
      <div class="swiper-buttons | white md:hidden flex z-2 fade-text" data-duration="1" data-delay="0.2">
        <div tabindex="0" aria-label="swiper button previous" role="button" class="swiper-button swiper-button-prev relative">
          <i class="icon-right_button_arrow absolute z-2"></i>
          <span class="bg absolute inset-0 size-full"></span>
          <span class="bg-scale absolute  size-full z-2"></span>
        </div>
        <div tabindex="0" aria-label="swiper button next" role="button" class="swiper-button swiper-button-next relative">
          <i class="icon-right_button_arrow absolute z-2"></i>
          <span class="bg absolute inset-0 size-full"></span>
          <span class="bg-scale absolute  size-full z-2"></span>
        </div>
      </div>
    </div>

    <div class="right flex md:justify-end items-center xl:w-1/4 md:w-1/2 w-full mt-[3rem] md:mt-[0rem] fade-text" data-duration="1" data-delay="0.2">

      <?php
      if (trim($link_URL) != "") { ?>
        <?php
        $link_Attributes = [];
        $link_Attributes['href'] = $link_URL;
        $link_AttributesHtml = join(' ', array_map(function ($key) use ($link_Attributes) {
          return $key . '="' . $link_Attributes[$key] . '"';
        }, array_keys($link_Attributes)));
        echo sprintf('<a tabindex="0" role="link" aria-label="' . $title . '" class="channeline-btn channeline-btn--arrow channeline-btn--border channeline-btn--white relative" %s>
        <span class="text z-2 relative">%s</span>
        <div class="shape absolute">
          <i class="icon-right_button_arrow absolute z-2"></i>
          <span class="bg absolute inset-0 size-full"></span>
        </div>
      </a>', $link_AttributesHtml, $link_Title); ?>


      <?php } ?>
    </div>
  </div>

  <div class="products pt-[6rem]">
    <div class="swiper">
      <div class="swiper-wrapper">
        <?php
        if (GeneralHelper::pagesExist($pages)) {
          $th = Core::make("helper/text");
          foreach ($pages as $page) {
            $title = $page->getTitle();
            $url = $page->getUrl();
            $category = $page->getProductCategory();
            $description = $th->wordSafeShortText($page->getCollectionDescription(), 80);
            $thumb = $page->getThumbnailImage(850, 1190);

        ?>

            <div class="swiper-slide">
              <a tabindex="0" role="link" aria-label="<?php echo $title; ?>" aria-describedby="<?php echo $description; ?>" href="<?php echo $url; ?>" class="product-card <?php if (!($category == 'Structural lining systems')) { ?><?php echo 'resins' ?><?php } ?> | z-2 relative png rounded-[2rem] flex overflow-hidden">
                <img class="relative z-1 | lazy" loading="lazy" src="<?php echo $thumb; ?>" alt="<?php echo $title; ?>">

                <div class="content absolute p-[4rem] z-2">
                  <div class="h3 mb-[2rem]"><?php echo $title; ?></div>
                  <p><?php echo $description; ?></p>

                  <div class="shape absolute">
                    <i class="icon-right_button_arrow absolute z-2"></i>
                    <span class="bg absolute inset-0 size-full"></span>
                  </div>
                </div>
              </a>
            </div>

        <?php }
        }
        ?>
      </div>
    </div>
  </div>
</section>