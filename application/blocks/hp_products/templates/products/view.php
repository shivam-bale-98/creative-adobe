<?php defined("C5_EXECUTE") or die("Access Denied.");

use Application\Concrete\Helpers\GeneralHelper;

?>




<section style="background-color: #171717;" class="products-list text-white xxl:px-[10rem]  xl:px-[6rem] md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] overflow-hidden js-main-products">

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
        <div class="swiper-button swiper-button__prev  | h-20 w-20 border border-white  flex items-center justify-center">
          <svg width="15" height="8" viewBox="0 0 15 8" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0.646446 3.64645C0.451184 3.84171 0.451184 4.15829 0.646446 4.35355L3.82843 7.53553C4.02369 7.7308 4.34027 7.7308 4.53553 7.53553C4.7308 7.34027 4.7308 7.02369 4.53553 6.82843L1.70711 4L4.53553 1.17157C4.7308 0.976311 4.7308 0.659728 4.53553 0.464466C4.34027 0.269204 4.02369 0.269204 3.82843 0.464466L0.646446 3.64645ZM15 3.5L1 3.5V4.5L15 4.5V3.5Z" fill="white"></path>
          </svg>
        </div>
        <div class="swiper-button swiper-button__next  | h-20 w-20 border border-white flex items-center justify-center">
          <svg width="15" height="8" viewBox="0 0 15 8" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14.3536 3.64645C14.5488 3.84171 14.5488 4.15829 14.3536 4.35355L11.1716 7.53553C10.9763 7.7308 10.6597 7.7308 10.4645 7.53553C10.2692 7.34027 10.2692 7.02369 10.4645 6.82843L13.2929 4L10.4645 1.17157C10.2692 0.976311 10.2692 0.659728 10.4645 0.464466C10.6597 0.269204 10.9763 0.269204 11.1716 0.464466L14.3536 3.64645ZM0 3.5L14 3.5V4.5L0 4.5L0 3.5Z" fill="white"></path>
          </svg>
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

                <div class="content absolute p-[4rem] z-2 bg-">
                  <div class="h3 mb-[2rem]"><?php echo $title; ?></div>
                  <p><?php echo $description; ?></p>

                  <div class="shape absolute   bg-white">
                    <i class="icon-right_button_arrow absolute z-2"></i>
                    <!-- <span class="bg absolute inset-0 size-full"></span> -->
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