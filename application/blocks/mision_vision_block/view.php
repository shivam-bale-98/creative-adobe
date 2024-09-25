
<?php defined("C5_EXECUTE") or die("Access Denied.");
$ih = new \Application\Concrete\Helpers\ImageHelper();

$thumbnail1 = "";
$thumbnail2 = "";
$low1 = "";
$low2 = "";

if ($leftImage) {
    $thumbnail1 = $ih->getThumbnail($leftImage, 1440, 1800);
    $low1 = $ih->getThumbnail($leftImage, 10, 20);
}

if ($rightImage) {
    $thumbnail2 = $ih->getThumbnail($rightImage, 1440, 1800);
    $low2 = $ih->getThumbnail($rightImage, 10, 20);
}

?>



<section <?php if (isset($bgColor) && trim($bgColor) != "") { ?>style="background-color: <?php echo h($bgColor); ?>;" <?php } ?> class="<?php if (isset($hideBlock) && trim($hideBlock) == 1) { ?>hide-block<?php } ?> <?php if (isset($removePaddingTop) && trim($removePaddingTop) == 1) { ?>pt-0<?php } ?> <?php if (isset($removePaddingBottom) && trim($removePaddingBottom) == 1) { ?>pb-0<?php } ?> mission-vision z-3  text-rustic-red">
    <div class="flex flex-wrap h-full">
        <div class="left w-1/2 relative  cursor-pointer m-card z-2">

            <?php if ($leftImage) { ?>
                <div class="img-wrap relative size-full inset-0 blurred-img rounded-[2rem] overflow-hidden" style="background-image: url('<?php echo $low1 ?>');">
                    <img class="inset-0 lazy object-cover z-1" loading="lazy" src="<?php echo $thumbnail1 ?>" alt="<?php echo h($leftTitle); ?>">
                    <div class="overlay absolute z-2"></div>
                </div>
            <?php } ?>


            <div class="content relative z-3 text-fade" data-duration="1">
                <?php if (isset($leftTitle) && trim($leftTitle) != "") { ?>

                    <h2 class="md:mb-[3.5rem] mb-[2rem]"><?php echo h($leftTitle); ?></h2>
                <?php } ?>

                <?php if (isset($leftDesc) && trim($leftDesc) != "") { ?>

                    <p><?php echo h($leftDesc); ?></p>

                <?php } ?>
            </div>
        </div>

        <div class="right w-1/2 relative active cursor-pointer m-card z-2">
            
            <?php if ($rightImage) { ?>

                <div class="img-wrap size-full inset-0 relative blurred-img rounded-[2rem] overflow-hidden" style="background-image: url('<?php echo $thumbnail2; ?>');">
                    <img class="inset-0 lazy object-cover z-1" loading="lazy" src="<?php echo $thumbnail2; ?>" alt="<?php echo h($rightTitle); ?>">
                    <div class="overlay absolute z-2"></div>
                </div>
            <?php } ?>

            <div class="content relative z-3 text-fade" data-duration="1">
                <?php if (isset($rightTitle) && trim($rightTitle) != "") { ?>

                    <h2 class="md:mb-[3.5rem] mb-[2rem]"><?php echo h($rightTitle); ?></h2>
                <?php } ?>
                <?php if (isset($rightDesc) && trim($rightDesc) != "") { ?>

                    <p><?php echo h($rightDesc); ?></p>
                <?php } ?>

            </div>
        </div>
    </div>
</section>

