<?php defined("C5_EXECUTE") or die("Access Denied."); ?>

<section <?php if (isset($bgColor) && trim($bgColor) != "") { ?> style="background-color: <?php echo h($bgColor); ?>;" <?php } ?> class="<?php if (isset($hideBlock_1) && trim($hideBlock_1) == 1) { ?>hide-block<?php } ?> <?php if (isset($removePaddingTop) && trim($removePaddingTop) == 1) { ?>pt-0<?php } ?> <?php if (isset($removePaddingBottom) && trim($removePaddingBottom) == 1) { ?>pb-0<?php } ?> w-full text-center md:text-left md:flex flex-wrap xl:items-center justify-between bg-[#1A0A0C] px-[2rem] md:px-[6rem] py-[3.7rem] md:py-[7.5rem]">
    <?php if (isset($title) && trim($title) != "") { ?>
        <h2 class="text-white w-full xl:mb-[0rem] mb-[3.4rem] text-[4rem] lg:text-[8rem]  xl:w-[75rem]"><?php echo h($title); ?></h2>
    <?php } ?>
    <?php if (!empty($button) && ($button_c = Page::getByID($button)) && !$button_c->error && !$button_c->isInTrash()) { ?>

        <div class="xl:w-auto w-full">
            <?php echo ' <a tabindex="0" role="link" aria-label="'.t($button_text) .'"  class="channeline-btn channeline-btn--arrow channeline-btn--border border-[#F2F1EF]/[0.5] text-white hover:border-transparent b channeline-btn--red relative md:mt-0 mt-5 no-underline " href="' . $button_c->getCollectionLink() . '">
		<div class="shape absolute">
			<i class="icon-right_button_arrow absolute z-2"></i>
			<span class="bg absolute inset-0 size-full"></span>
		</div>
		<span class="text-white z-2 relative">' . (isset($button_text) && trim($button_text) != "" ? $button_text : $button_c->getCollectionName()) . '</span></a>'; ?>
        </div>

    <?php } ?>
</section>