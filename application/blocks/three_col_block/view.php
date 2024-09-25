<?php defined("C5_EXECUTE") or die("Access Denied.");
$ih = new \Application\Concrete\Helpers\ImageHelper();
$thumbnail = "";
?>

<section <?php if (isset($bgColor) && trim($bgColor) != "") { ?>style="background-color: <?php echo h($bgColor); ?>;" <?php } ?> class="<?php if (isset($hideBlock) && trim($hideBlock) == 1) { ?>hide-block<?php } ?> <?php if (isset($removePaddingTop) && trim($removePaddingTop) == 1) { ?>pt-0<?php } ?> <?php if (isset($removePaddingBottom) && trim($removePaddingBottom) == 1) { ?>pb-0<?php } ?> three-col-block relative xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] text-rustic-red" id="design">
    <?php if (isset($sectionTitle) && trim($sectionTitle) != "") { ?>
        <div class="title">
            <div class="fade-text" data-duration="1" data-delay="0.2">
                <h2><?php echo $sectionTitle; ?></h2>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($addCards_items)) { ?>
        <div class="xl:mt-[10rem] md:mt-[8rem] mt-[4rem]">
            <?php foreach ($addCards_items as $addCards_item_key => $addCards_item) { ?>
                <div class="three-col-card flex flex-wrap justify-between relative">
                    <?php if ($addCards_item["image"]) {
                        if ($addCards_item["image"]) {
                            $thumbnail = $ih->getThumbnail($addCards_item["image"], 400, 400);
                        }
                    ?>
                        <div class="img-wrap relative size-full inset-0 blurred-img rounded-[2rem] overflow-hidden" style="background-image: url('<?php echo $thumbnail ?>');">
                            <img class="inset-0 lazy object-cover z-1" loading="lazy" src="<?php echo $thumbnail ?>" alt="">
                        </div>
                    <?php } ?>
                    <div class="content flex  justify-between">
                        <?php if (isset($addCards_item["title"]) && trim($addCards_item["title"]) != "") { ?>
                            <h4><?php echo h($addCards_item["title"]); ?></h4>
                        <?php } ?>
                        <?php if (isset($addCards_item["desc_1"]) && trim($addCards_item["desc_1"]) != "") { ?>
                            <p><?php echo h($addCards_item["desc_1"]); ?></p>
                        <?php } ?>

                    </div>

                    <div class="line absolute"></div>
                </div>

            <?php } ?>
        </div>
    <?php } ?>
</section>