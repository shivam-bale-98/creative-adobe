<?php defined("C5_EXECUTE") or die("Access Denied."); ?>




<section <?php if (isset($bgColor) && trim($bgColor) != "") { ?>style="background-color:<?php echo h($bgColor); ?>;" <?php } ?> class="<?php if (isset($hideBlock) && trim($hideBlock) == 1) { ?>hide-block<?php } ?> <?php if (isset($borders) && trim($borders) == 1) { ?>border-top<?php } else { ?>border-bottom<?php } ?> <?php if (isset($removePaddingTop) && trim($removePaddingTop) == 1) { ?>pt-0<?php } ?> <?php if (isset($removePaddingBottom) && trim($removePaddingBottom) == 1) { ?>pb-0<?php } ?>
 key-numbers  relative  text-rustic-red">
    <div class="key-numbers--wrappper flex mb-[6rem]">
        <?php if (!empty($keyNumbersLeft_items)) { ?>
            <div class="left relative w-1/2 ">
                <div class="swiper xl:py-[10rem] md:py-[8rem] py-[3.5rem]">
                    <div class="swiper-wrapper">
                        <?php foreach ($keyNumbersLeft_items as $keyNumbersLeft_item_key => $keyNumbersLeft_item) { ?>
                            <div class="swiper-slide">
                                <div class="number-card">
                                    <?php if (isset($keyNumbersLeft_item["leftValues"]) && trim($keyNumbersLeft_item["leftValues"]) != "") { ?>

                                        <h2 class="h15">
                                            <?php echo h($keyNumbersLeft_item["leftValues"]); ?>
                                        </h2>
                                    <?php } ?>

                                    <?php if (isset($keyNumbersLeft_item["leftDesc"]) && trim($keyNumbersLeft_item["leftDesc"]) != "") { ?>
                                        <h4 class="xl:mt-[5rem] md:mt-[3rem] mt-[2rem] ">
                                            <?php echo h($keyNumbersLeft_item["leftDesc"]); ?>
                                        </h4>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="pagination"></div>
                </div>
            </div>
        <?php } ?>
        <?php if (!empty($keyNumbersRight_items)) { ?>
            <div class="right relative w-1/2 ">

                <div class="swiper xl:py-[10rem] md:py-[8rem] py-[3.5rem]">
                    <div class="swiper-wrapper">
                        <?php foreach ($keyNumbersRight_items as $keyNumbersRight_item_key => $keyNumbersRight_item) { ?>

                            <div class="swiper-slide">
                                <div class="number-card">
                                    <?php if (isset($keyNumbersRight_item["rightValues"]) && trim($keyNumbersRight_item["rightValues"]) != "") { ?>
                                        <h2 class="h15">
                                            <?php echo h($keyNumbersRight_item["rightValues"]); ?>
                                        </h2>
                                    <?php } ?>
                                    <?php if (isset($keyNumbersRight_item["rightDesc"]) && trim($keyNumbersRight_item["rightDesc"]) != "") { ?>
                                        <h4 class="xl:mt-[5rem] md:mt-[3rem] mt-[2rem] ">
                                            <?php echo h($keyNumbersRight_item["rightDesc"]); ?>
                                        </h4>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>

                    </div>
                </div>

            </div>
        <?php } ?>
    </div>
    <?php if (!empty($keyNumbersLeft_items)) { ?>
        <div class="key-numbers--mobile">
            <div class="swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($keyNumbersLeft_items as $keyNumbersLeft_item_key => $keyNumbersLeft_item) { ?>
                        <div class="swiper-slide">
                            <div class="number-card">
                                <div class="line-left absolute"></div>
                                <!-- <div class="line-top absolute"></div> -->
                                <?php if (isset($keyNumbersLeft_item["leftValues"]) && trim($keyNumbersLeft_item["leftValues"]) != "") { ?>

                                    <h2 class="h15">
                                        <?php echo h($keyNumbersLeft_item["leftValues"]); ?>
                                    </h2>
                                <?php } ?>

                                <?php if (isset($keyNumbersLeft_item["leftDesc"]) && trim($keyNumbersLeft_item["leftDesc"]) != "") { ?>
                                    <h4 class="xl:mt-[5rem] md:mt-[3rem] mt-[2rem] ">
                                        <?php echo h($keyNumbersLeft_item["leftDesc"]); ?>
                                    </h4>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php foreach ($keyNumbersRight_items as $keyNumbersRight_item_key => $keyNumbersRight_item) { ?>
                        <div class="swiper-slide">
                            <div class="number-card">
                                <div class="line-left absolute"></div>
                                <!-- <div class="line-top absolute"></div> -->
                                <?php if (isset($keyNumbersRight_item["rightValues"]) && trim($keyNumbersRight_item["rightValues"]) != "") { ?>
                                    <h2 class="h15">
                                        <?php echo h($keyNumbersRight_item["rightValues"]); ?>
                                    </h2>
                                <?php } ?>
                                <?php if (isset($keyNumbersRight_item["rightDesc"]) && trim($keyNumbersRight_item["rightDesc"]) != "") { ?>
                                    <h4 class="xl:mt-[5rem] md:mt-[3rem] mt-[2rem] ">
                                        <?php echo h($keyNumbersRight_item["rightDesc"]); ?>
                                    </h4>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="pagination"></div>
            </div>
        
        </div>
    <?php } ?>
</section>