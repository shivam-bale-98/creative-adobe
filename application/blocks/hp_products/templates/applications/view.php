<?php defined("C5_EXECUTE") or die("Access Denied.");

use Application\Concrete\Helpers\GeneralHelper;

?>


<!-- fully completed block  -->
<section style="background-color: #fff;" class="application-list xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:pt-[10rem] md:pt-[8rem] pt-[5rem] xl:pb-[15rem] md:pb-[8rem] pb-[5rem] text-rustic-red">

  <?php if (isset($subTitle) && trim($subTitle) != "") { ?>
    <h6 class="mb-[2rem] fade-text" data-duration="1">
      <?php echo h($subTitle); ?>
    </h6>
  <?php } ?>

  <div class="title flex flex-wrap">
    <?php if (isset($title) && trim($title) != "") { ?>
      <div class="left xl:w-3/4 md:w-1/2 w-full">
        <div class="fade-text" data-duration="1.2" data-delay="0.2">
          <h2 class=""><?php echo h($title); ?></h2>
        </div>
      </div>
    <?php } ?>


    <div class="right flex md:justify-end items-center xl:w-1/4 md:w-1/2 w-full mt-[3rem] md:mt-[0rem] fade-text" data-duration="1">
      <?php
      if (trim($link_URL) != "") { ?>
        <?php
        $link_Attributes = [];
        $link_Attributes['href'] = $link_URL;
        $link_AttributesHtml = join(' ', array_map(function ($key) use ($link_Attributes) {
          return $key . '="' . $link_Attributes[$key] . '"';
        }, array_keys($link_Attributes)));
        echo sprintf('<a tabindex="0" role="link" aria-label="view ' . t($title) . '" class="channeline-btn channeline-btn--arrow channeline-btn--border channeline-btn--red relative" %s>
      <span class="text z-2 relative">%s</span>
      <div class="shape absolute">
        <i class="icon-right_button_arrow absolute z-2"></i>
        <span class="bg absolute inset-0 size-full"></span>
      </div>
    </a>', $link_AttributesHtml, $link_Title);
        ?>

      <?php
      } ?>
    </div>


  </div>

  <div class="hidden xl:block  all-media-wrapper mt-[10rem] relative">
    <div class="applications-content-wrapper flex flex-wrap  w-full">
      <?php
      if (GeneralHelper::pagesExist($pages)) {
        $count = 1;
        foreach ($pages as $page) {
          $title = $page->getTitle();
          $url = $page->getUrl();
          $description = $page->getCollectionDescription();
          $thumb = $page->getThumbnailImage(850, 1190);

      ?>
          <div id="<?php echo 'application-' . $count ?>" data-id="<?php echo $count ?>" class="z-2 application <?php echo $count == 1 ? 'active' : '' ?>  relative flex justify-between items-center w-full">
            <a tabindex="0" role="button" aria-label="<?php echo $title; ?>" class="absolute w-full h-full z-1 inset-0" href="<?php echo $url ?>"></a>
            <span class="h6"><?php echo GeneralHelper::getTwoDigitCount($count);  ?></span>
            <div class="content relative z-2">
              <a role="link" aria-label="<?php echo $title; ?>" href="<?php echo $url ?>">
                <div class="h2 application-title"><?php echo $title; ?></div>
                <p class="mt-[2.4rem] "><?php echo $description; ?></p>
              </a>
            </div>
            <div class="line absolute"></div>
          </div>
      <?php $count++;
        }
      }
      ?>
    </div>

    <div class="applications-media-wrapper rounded-[2rem] absolute overflow-hidden">
      <?php if (GeneralHelper::pagesExist($pages)) {
        $count = 1;
        foreach ($pages as $page) {
          $title = $page->getTitle();
          $url = $page->getUrl();
          $description = $page->getCollectionDescription();
          $thumb = $page->getThumbnailImage(650, 720);
          $low = $page->getThumbnailImage(1, 1);
          $video = $page->getVideoUrl();
      ?>
          <div id="media-<?php echo $count ?>" class="media absolute inset-0 blurred-img" style="background-image:url('<?php echo $low ?>')">
            <?php if (!is_null($video) && !empty($video)) { ?>
              <a tabindex="0" role="link" aria-label="<?php echo $title ?>" href="<?php echo $url ?>" class="absolute inset-0 size-full">
                <video loading="lazy" data-src="<?php echo $video ?>" class="absolute inset-0 size-full object-cover lazy" autoplay="" loop="" muted="" playsinline="">

                </video>
              </a>
            <?php } else { ?>
              <a href="#<?php echo 'sub-app-' . $count ?>">
                <img class="absolute inset-0 size-full object-cover | lazy" loading="lazy" src="<?php echo $thumb ?>" alt="<?php echo $title ?>">
              </a>
            <?php }
            ?>

          </div>


      <?php $count++;
        }
      } ?>
    </div>


  </div>

  <div class="application-listing-mobile block xl:hidden mt-[5rem]">
    <?php
    if (GeneralHelper::pagesExist($pages)) {
      $count = 1;
      foreach ($pages as $page) {
        $title = $page->getTitle();
        $url = $page->getUrl();
        $description = $page->getCollectionDescription();
        $thumb = $page->getThumbnailImage(600, 600);
        $low = $page->getThumbnailImage(1, 1);
        $video = $page->getVideoUrl();
    ?>

        <a tabindex="0" role="link" aria-label="<?php echo $title ?>" href="<?php echo $title ?>" class="application-crd flex flex-wrap justify-between  relative md:pb-[3rem] pb-[2rem] md:pt-[3rem] pt-[2rem] ">
          <div class="left text-left">
            <div class="h6 mb-[2rem]">
              <?php echo GeneralHelper::getTwoDigitCount($count);  ?>
            </div>
            <div class="img-wrap rounded-[2rem] overflow-hidden relative blurred-img" style="background-image: url('<?php echo $low ?>');">
              <?php if (!is_null($video) && !empty($video)) { ?>
                <video src="<?php echo $video ?>" class="absolute inset-0 size-full object-cover | lazy" loading="lazy" autoplay="" loop="" muted="" playsinline="">
                </video>
              <?php } else { ?>
                <img class="absolute inset-0 object-cover | lazy" loading="lazy" src="<?php echo $thumb ?>" alt="<?php echo $title ?>">
              <?php }
              ?>
            </div>
          </div>
          <div class="right mt-[3.6rem]">
            <div class="h2"><?php echo $title ?></div>
            <p class="md:mt-[3rem] mt-[2rem]"><?php echo $description ?></p>
          </div>
          <div class="line absolute"></div>
        </a>



    <?php $count++;
      }
    } ?>

  </div>
</section>