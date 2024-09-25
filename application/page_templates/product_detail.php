<?php

use  \Application\Concrete\Helpers\GeneralHelper;

$c = Page::getCurrentPage();
$downloads = $c->getAttribute("product_downloads");
$relatedApplications = \Application\Concrete\Models\Products\ProductList::getRelatedApplications($c->getCollectionID());

$relatedCaseStudies = \Application\Concrete\Models\Products\ProductList::getRelatedCaseStudies($c->getCollectionID());

$site = Config::get('concrete.site');
$themePath = $this->getThemePath();

use  \Application\Concrete\Helpers\ImageHelper;

$ih = new ImageHelper();

$banner_title = $c->getAttribute('banner_title');
$title = $c->getCollectionName();


$banner_attribute = $c->getAttribute('banner_image');
$banner_image = $ih->getThumbnail($banner_attribute, 2000, 2000, false);

$banner_bg = $c->getAttribute('banner_bg');

$banner_mobile_image = $c->getAttribute('mobile_banner');
$banner_mobile_image = $ih->getThumbnail($banner_mobile_image, 1000, 1000, false);

$category = $c->getAttribute('category');

$filesData = [];
if (!is_null($downloads)) {
    foreach ($downloads->getFileObjects() as $download) {
        $filesData[] = GeneralHelper::getFileDetails($download);
    }
}
?>


<section style="background-color: <?php echo $banner_bg ?>; " class="banner-v2 product-detail--banner  pt-[13rem] md:pt-[15rem] xl:pt-[25rem] px-[2rem] md:px-[4rem] xl:px-[6rem] xxl:px-[10rem] text-rustic-red">
  <div class="content text-center  ">
    <?php $s = Stack::getByName('breadcrumb');
    if ($s) {
      $s->display();
    } ?>

    <?php if ($banner_title) { ?>
      <h1 class="h2"><?php echo $banner_title ?></h1>
    <?php } else { ?>
      <h1 class="h2"><?php echo $title ?></h1>
    <?php } ?>
  </div>
  <?php if ($category == 'Structural lining systems') {  ?>
    <div class="img-wrap relative rounded-[2rem] overflow-hidden xl:mt-[10rem] md:mt-[8rem] mt-[4rem]">

      <img class="desktop absolute  md:block hidden  object-contain" src="<?php echo $banner_image ?>" alt="<?php echo $title ?>">

      <img class="mobile absolute  md:hidden object-contain" src="<?php echo $banner_mobile_image ?>" alt="<?php echo $title ?>">
    </div>
  <?php } else { ?>
    <div class="img-wrap resins relative rounded-[2rem] overflow-hidden xl:mt-[10rem] md:mt-[8rem] mt-[4rem]">

      <img class="desktop absolute inset-0 md:block hidden size-full object-cover" src="<?php echo $banner_image ?>" alt="<?php echo $title ?>">

      <img class="mobile absolute  md:hidden inset-0 size-full object-cover" src="<?php echo $banner_mobile_image ?>" alt="<?php echo $title ?>">
    </div>
  <?php } ?>
</section>




<?php $a = new Area('Page Content');

$a->display($c); ?>



<?php if (GeneralHelper::pagesExist($relatedApplications)) { ?>
  <?php View::element('applications/relatedApplications', ['pages' => $relatedApplications]); ?>
<?php } ?>

<?php $a = new Area('Page Content2');

$a->display($c); ?>

<?php if (GeneralHelper::pagesExist($relatedCaseStudies)) { ?>
  <?php View::element('case_studies/relatedCaseStudies', ['pages' => $relatedCaseStudies]); ?>
<?php } ?>

<?php $a = new Area('Page Content3');

$a->display($c); ?>

<?php if (!is_null($downloads)) { ?>
<section style="background-color: #F2F1EF;" class="download--block xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] text-rustic-red">

    <div class="title flex justify-between items-center">
      <div class="left xl:w-3/4 md:w-1/2 w-full fade-text" data-duration="1" data-delay="0.2">
        <h2>Downloads</h2>
      </div>

      <div class="right right flex md:justify-end items-center xl:w-1/4 md:w-1/2 w-full mt-[2rem] md:mt-[0rem]">
        <div class="swiper-buttons js-desktop |  rustic-red hidden md:flex z-2">
          <div tabindex="0" aria-label="swiper button previous" role="button" class="swiper-button swiper-button-prev relative z-3">
            <i class="icon-right_button_arrow absolute z-3"></i>
            <span class="bg absolute inset-0 size-full"></span>
            <span class="bg-scale absolute  size-full z-2"></span>
          </div>
          <div tabindex="0" aria-label="swiper button next" role="button" class="swiper-button swiper-button-next relative z-3">
            <i class="icon-right_button_arrow absolute z-3"></i>
            <span class="bg absolute inset-0 size-full"></span>
            <span class="bg-scale absolute  size-full z-2"></span>
          </div>
        </div>
      </div>
    </div>
    <div class="download-cards-list mt-[4rem] md:mt-[6rem] swiper">
      <div class="swiper-wrapper downloads--listing">

          <?php if (is_array($filesData) && count($filesData) > 0) {
              View::element('downloads/filesView', ['files' => $filesData]);
          } ?>

      </div>

      <div class="swiper-buttons js-mobile mt-[3rem] |  rustic-red flex md:hidden z-2 fade-text" data-duration="1" data-delay="0.2">
        <div tabindex="0" aria-label="swiper button previous" role="button" class="swiper-button swiper-button-prev relative z-3">
          <i class="icon-right_button_arrow absolute z-3"></i>
          <span class="bg absolute inset-0 size-full"></span>
          <span class="bg-scale absolute  size-full z-2"></span>
        </div>
        <div tabindex="0" aria-label="swiper button next" role="button" class="swiper-button swiper-button-next relative z-3">
          <i class="icon-right_button_arrow absolute z-3"></i>
          <span class="bg absolute inset-0 size-full"></span>
          <span class="bg-scale absolute  size-full z-2"></span>
        </div>
      </div>
    </div>

</section>
<?php } ?>
<?php $a = new Area('Page Content4');

$a->display($c); ?>
