<?php

$c = Page::getCurrentPage();
$site = Config::get('concrete.site');

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
?>


<section style="background-color: <?php echo $banner_bg ?>; " class="banner-v2  px-[2rem] md:px-[4rem] xl:px-[6rem] xxl:px-[10rem] pt-[8rem] md:pt-[12rem] xl:pt-[25rem] text-rustic-red xl:pb-[10rem] md:pb-[8rem] pb-[4rem]">
    <div class="content text-center">
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
</section>

<?php $a = new Area('Page Content');

$a->display($c); ?>





<?php $a = new Area('Page Content 2');

$a->display($c); ?>