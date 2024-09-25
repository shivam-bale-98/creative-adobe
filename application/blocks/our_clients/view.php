<?php defined("C5_EXECUTE") or die("Access Denied."); ?>


<section <?php if (isset($bgColor) && trim($bgColor) != "") { ?>style="background-color:<?php echo h($bgColor); ?>;" <?php } ?> class="<?php if (isset($hideBlock) && trim($hideBlock) == 1) { ?>hide-block<?php } ?> <?php if (isset($removePaddingTop) && trim($removePaddingTop) == 1) { ?>pt-0<?php } ?> <?php if (isset($removePaddingBottom) && trim($removePaddingBottom) == 1) { ?>pb-0<?php } ?> our-clients relative flex overflow-hidden  text-rustic-red ">
    <div class="flex w-full flex-wrap">
        <div class="left w-1/2 xl:py-[8rem] py-[5rem] flex flex-col justify-between">
            <div class="top">
                <?php if (isset($subTitle) && trim($subTitle) != "") { ?>

                    <h6 class="mb-[2rem] fade-text" data-duration="1"><?php echo h($subTitle); ?></h6>
                <?php } ?>

                <?php if (isset($title) && trim($title) != "") { ?>
                    <div class="title md:mb-[3rem] mb-[2rem] fade-text" data-duration="1.2" data-delay="0.2">
                        <?php echo $title; ?>
                    </div>
                <?php } ?>

                <?php if (isset($desc_1) && trim($desc_1) != "") { ?>
                    <div class="desc hidden md:block  fade-text" data-duration="1" data-delay="0.2">
                        <?php echo $desc_1; ?>
                    </div>
                <?php } ?>
            </div>

            <?php if (trim($link_URL) != "") { ?>
                <div class="bottom fade-text" data-duration="1">
                    <?php
                    $link_Attributes = [];
                    $link_Attributes['href'] = $link_URL;
                    if (in_array($link, ['url', 'relative_url'])) {
                        $link_Attributes['target'] = '_blank';
                    }
                    $link_AttributesHtml = join(' ', array_map(function ($key) use ($link_Attributes) {
                        return $key . '="' . $link_Attributes[$key] . '"';
                    }, array_keys($link_Attributes)));
                    echo sprintf('<a tabindex="0" role="link" aria-label="'.$link_Title.'" class="channeline-btn channeline-btn--arrow channeline-btn--border channeline-btn--red relative md:mt-[3rem]" %s>
	                               <span class="text z-2 relative">%s</span>
	                                <div class="shape absolute">
                                 	<i class="icon-right_button_arrow absolute z-2"></i>
                                    <span class="bg absolute inset-0 size-full"></span>
                                    </div></a>', $link_AttributesHtml, $link_Title); ?>
                </div>
            <?php } ?>
        </div>

        <?php if (!empty($logos_items)) { ?>
            <div class="right w-1/2 flex flex-wrap relative items-start h-fit">
                <?php
                $card_count = 0;
                foreach ($logos_items as $logos_item_key => $logos_item) {
                    // Open a new div every 3 cards
                    if ($card_count % 3 == 0) {
                        echo '<div class="card-group flex justify-between w-full">';
                    }
                ?>
                    <div class="card--1 flex justify-center items-center">
                        <?php if ($logos_item["desktopLogos"]) { ?>
                            <img class="desktop" loading="lazy" src="<?php echo $logos_item["desktopLogos"]->getURL(); ?>" alt="<?php echo $logos_item["desktopLogos"]->getTitle(); ?>" />
                        <?php } ?>
                        <?php if ($logos_item["mobileLogos"]) { ?>
                            <img class="mobile" src="<?php echo $logos_item["mobileLogos"]->getURL(); ?>" alt="<?php echo $logos_item["mobileLogos"]->getTitle(); ?>" />
                        <?php } ?>
                    </div>
                <?php
                    // Close the div every 3 cards
                    $card_count++;
                    if ($card_count % 3 == 0 || $card_count == count($logos_items)) {
                        echo '</div>';
                    }
                } ?>
            </div>

        <?php } ?>

    </div>
</section>