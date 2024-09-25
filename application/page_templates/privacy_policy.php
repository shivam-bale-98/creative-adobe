<?php
use  \Application\Concrete\Helpers\GeneralHelper;
$c = Page::getCurrentPage();
$site = Config::get('concrete.site');

$title = $c->getCollectionName();
$description = $c->getCollectionDescription();
?>

<div class="privacy-policy | h-full">
    <div class="bg-[#80151A0D] flex-col text-black xl:h-[50rem] md:h-[40rem] h-[25rem] flex justify-center items-center pt-[4rem]">
        <?php $s = Stack::getByName('breadcrumb');
            if ($s) {
                $s->display();
            } ?>
        <h2 class="text-black"><?php echo $title ?></h2>
    </div>
    <div class="my-[5rem] text-black lg:my-[10rem] xl:mx-[27.5rem] lg:mx-[15rem] sm:mx-[10rem] mx-[2rem]">
        <?php $a = new Area('Text content');
        $a->display($c); ?>
    </div>
</div>