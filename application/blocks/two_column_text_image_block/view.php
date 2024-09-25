<?php defined("C5_EXECUTE") or die("Access Denied.");
$ih = new \Application\Concrete\Helpers\ImageHelper();

?>







<section <?php if (isset($bgColor) && trim($bgColor) != "") { ?>style="background-color: <?php echo h($bgColor); ?>;" <?php } ?> class="<?php if (isset($hideBlock) && trim($hideBlock) == 1) { ?>hide-block<?php } ?> <?php if (isset($removePaddingTop) && trim($removePaddingTop) == 1) { ?>pt-0<?php } ?> <?php if (isset($removePaddingBottom) && trim($removePaddingBottom) == 1) { ?>pb-0<?php } ?> imageTextBlock xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] flex flex-col md:flex-col lg:flex-row  justify-between xl:gap-[15.9rem] lg:gap-[5.9rem] gap-[3rem] items-center" id="whygrp">
    <div class="bdFadeLeft content-block w-full md:w-full xl:w-[calc(53%-2.1rem)] ">
        <?php if (isset($title) && trim($title) != "") { ?>
            <div class="text-rustic-red"><?php echo $title; ?></div>
        <?php } ?>

        <?php if (isset($subtitle) && trim($subtitle) != "") { ?>
            <div class="mt-[3rem] mb-[4rem] text-rustic-red/50  prose-h4:text-[2.4rem] prose-h4:leading-[2.8rem] md:prose-h4:text-[3rem] md:prose-h4:leading-[3.9rem]  ">
                <?php echo $subtitle; ?>
            </div>
        <?php } ?>

        <?php if (isset($description_1) && trim($description_1) != "") { ?>
            <div class="text-rustic-red/50">
                <?php echo $description_1; ?>
            </div>

        <?php } ?>
    </div>
    <div class="bdFadeRight image-block w-full md:w-full xl:w-[calc(47%-2.1rem)]">
        <?php if ($image) { ?>
            <div class="rounded-[2rem] aspect-[0.71] size-full" style="background-image: url('<?php echo $ih->getThumbnail($image, 1, 1)  ?>')">


                <img class=" object-cover rounded-[2rem] w-full h-full" src="<?php echo $ih->getThumbnail($image, 1000, 1500) ?>" alt="<?php echo $image->getTitle(); ?>" />

            </div>
        <?php } ?>
    </div>
</section>