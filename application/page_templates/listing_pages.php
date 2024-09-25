<?php

$c = Page::getCurrentPage();
$site = Config::get('concrete.site');
$themePath = $this->getThemePath();



$banner_title = $c->getAttribute('banner_title');
$title = $c->getCollectionName();
$banner_bg = $c->getAttribute('banner_bg');
$description = $c->getCollectionDescription();
?>

<div class="listing-wrapper" style="background-color: <?php echo $banner_bg ?>;">
    <section style="background-color: <?php echo $banner_bg ?>; " class="banner-v2  pt-[13rem] md:pt-[15rem] xl:pt-[25rem] text-rustic-red">
        <div class="content text-center px-[2rem] md:px-[4rem] xl:px-[6rem] xxl:px-[10rem]">
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
</div>