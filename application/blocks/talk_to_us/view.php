<?php defined("C5_EXECUTE") or die("Access Denied."); ?>

<? //php if (isset($hideBlock) && trim($hideBlock) != "") { 
?>
<? //php echo $hideBlock == 1 ? "yes" : "no"; 
?>
<? //php } 
?>
<? //php if (isset($bgColor) && trim($bgColor) != "") { 
?>
<? //php echo h($bgColor); 
?>
<? //php } 
?>




<section <?php if (isset($bgColor) && trim($bgColor) != "") { ?> style="background-color: <?php echo h($bgColor); ?>;" <?php } ?> class="<?php if (isset($hideBlock) && trim($hideBlock) == 1) { ?>hide-block<?php } ?> <?php if (isset($removePaddingTop) && trim($removePaddingTop) == 1) { ?>pt-0<?php } ?> <?php if (isset($removePaddingBottom) && trim($removePaddingBottom) == 1) { ?>pb-0<?php } ?> section  talk-to-us xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] text-white" id="talk-to-us">
    <div class="flex  flex-col lg:flex-row justify-between gap-12 lg:gap-[8.6rem]">


        <div class="title lg:w-1/2  w-full fade-text" data-duration="1" data-delay="0.2">
            <?php if (isset($title) && trim($title) != "") { ?>


                <h2 class="mb-[2rem]"><?php echo h($title); ?></h2>
            <?php } ?>
            <?php if (isset($Desc_1) && trim($Desc_1) != "") { ?>


                <p class="">
                    <?php echo h($Desc_1); ?>
                </p>
            <?php } ?>
        </div>


        <?php if (isset($selectForm) && !empty($selectForm)) { ?>
            <div class="form-container lg:w-1/2 w-full ">


                <?php foreach ($selectForm as $selectForm_stack) {
                    $selectForm_stack->display();
                } ?>

            </div>
        <?php } ?>
    </div>
</section>

