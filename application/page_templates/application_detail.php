<?php

use  \Application\Concrete\Helpers\GeneralHelper;

$c = Page::getCurrentPage();
use  \Application\Concrete\Helpers\ImageHelper;

$ih = new ImageHelper();

$banner_title = $c->getAttribute('banner_title');
$title = $c->getCollectionName();


$banner_attribute = $c->getAttribute('banner_image');
$banner_image = $ih->getThumbnail($banner_attribute, 2000, 2000, false);

$banner_bg = $c->getAttribute('banner_bg');

$banner_mobile_image = $c->getAttribute('mobile_banner');
$banner_mobile_image = $ih->getThumbnail($banner_mobile_image, 1000, 1000, false);

$description = $c->getCollectionDescription();

$relatedCaseStudies = \Application\Concrete\Models\Application\ApplicationList::getRelatedCaseStudies($c->getCollectionID());

?>


<section style="background-color: <?php echo $banner_bg ?>; " class="banner-v2  pt-[13rem] md:pt-[15rem] xl:pt-[25rem] px-[2rem] md:px-[4rem] xxl:px-[10rem]  xl:px-[6rem] text-rustic-red">
    <div class="content text-center ">
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

    <div class="img-wrap relative rounded-[2rem] overflow-hidden xl:mt-[10rem] md:mt-[8rem] mt-[4rem]">

        <img class="desktop absolute inset-0 md:block hidden size-full object-cover" src="<?php echo $banner_image ?>" alt="<?php echo $title ?>">

        <img class="mobile absolute  md:hidden inset-0 size-full object-cover" src="<?php echo $banner_mobile_image ?>" alt="<?php echo $title ?>">


    </div>
</section>

<?php $a = new Area('Page Content');

$a->display($c); ?>

<?php if (GeneralHelper::pagesExist($relatedCaseStudies)) { ?>
        <?php View::element('case_studies/relatedCaseStudies', ['pages' => $relatedCaseStudies]); ?>
<?php } ?>