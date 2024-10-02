<?php defined("C5_EXECUTE") or die("Access Denied.");
$themePath = $view->getThemePath();

?>



<section <?php if (isset($bgColor) && trim($bgColor) != "") { ?>style="background-color: <?php echo h($bgColor); ?>;" <?php } ?> class="<?php if (isset($removePaddingTop) && trim($removePaddingTop) == 1) { ?>pt-0<?php } ?> <?php if (isset($removePaddingBottom) && trim($removePaddingBottom) == 1) { ?>pb-0<?php } ?> section accordion-block xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] text-rustic-red" id="whychanneline">
    <div class="flex  flex-col lg:flex-row justify-between gap-12 lg:gap-[8.6rem]">
        <?php if (isset($blockTitle) && trim($blockTitle) != "") { ?>
            <div class="title lg:w-1/2 w-full fade-text" data-duration="1" data-delay="0.2">

                <?php echo ($blockTitle); ?>
            </div>

        <?php } ?>

        <?php if (!empty($accordionsItem_items)) { ?>
            <div class="acc-container lg:w-1/2 w-full">
                <?php foreach ($accordionsItem_items as $accordionsItem_item_key => $accordionsItem_item) { ?>
                    <div class="acc active:bg-red-berry active:text-white group lg:opacity-0 lg:translate-x-[4rem] bg-white py-[3.5rem] px-[4rem] mb-[2rem] transition-colors duration-1000 ease-out hover:bg-red-berry hover:text-white group">
                        <?php if (isset($accordionsItem_item["accordionTitle"]) && trim($accordionsItem_item["accordionTitle"]) != "") { ?>
                            <div class="acc-head ">
                                <p class="p1 group active:text-white group-hover:text-white ">
                                    <?php echo h($accordionsItem_item["accordionTitle"]); ?>
                                </p>
                                <div class="button group active:bg-white"></div>
                            </div>
                        <?php } ?>
                        <?php if (isset($accordionsItem_item["accordionContent"]) && trim($accordionsItem_item["accordionContent"]) != "") { ?>
                            <div class="acc-content">
                                <p class="p41">
                                    <?php echo $accordionsItem_item["accordionContent"]; ?>

                                </p>
                                <div class="button"></div>

                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>


            </div>
        <?php } ?>

    </div>
</section>