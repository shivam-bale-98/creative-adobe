<?php

$c = Page::getCurrentPage();
$site = Config::get('concrete.site');

use  \Application\Concrete\Helpers\ImageHelper;

$ih = new ImageHelper();

$banner_title = $c->getAttribute('banner_title');
$title = $c->getCollectionName();
$description = $c->getCollectionDescription();
$banner_bg = $c->getAttribute('banner_bg');

?>


<section style="background-color: <?php echo $banner_bg ?>; " class="banner-v2 listing-wrapper px-[2rem] md:px-[4rem] xl:px-[6rem] xxl:px-[10rem] pt-[13rem] md:pt-[15rem] xl:pt-[25rem] text-rustic-red xl:pb-[10rem] md:pb-[8rem] pb-[4rem]">
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

        <?php if ($description) { ?>
            <p class="pt-[4rem] max-w-[74.6rem] mx-auto description text-rustic-red/50"><?php echo $description ?></p>
        <?php } ?>


    </div>

</section>

<?php $a = new Area('Page Content');
$a->display($c); ?>