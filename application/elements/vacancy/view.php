<?php

use Application\Concrete\Models\Vacancy\Vacancy;
use Application\Concrete\Helpers\GeneralHelper;
?>


<?php if (is_array($pages) && count($pages)) {
    foreach ($pages as $page) {
        ++$counter;
        $page = new Vacancy($page);
        $type = $page->getVacancyType();
        $url = $page->getUrl();
        $closing_date = $page->getClosingDate();
        $dept = $page->department();

?>
        <a href="<?php echo $page->getCollectionLink() ?>" class="vacancy-list-card flex justify-between items-center text-black w-full xxl:px-[10rem] lg:px-[6rem] md:px-[3rem] px-[2rem] py-[5rem] group relative">
            <div class="group first-div left flex items-center lg:gap-[10.5rem] sm:gap-[2rem] gap-[0]">
                <span class="number text-[2rem] leading-[1.9rem] font-normal"><?php echo GeneralHelper::getTwoDigitCount($counter);  ?></span>
                <h3 class="group-hover:text-red-berry"><?php echo $page->getCollectionName() ?></h3>
            </div>
            <div class="second-div center">
                <h5 class=" text-black text-[1.4rem] sm:text-[1.6rem] leading-[1.52rem] font-light">
                    <?php echo $dept ?></h5>
                <span class="lg:hidden text-black text-[1.6rem] leading-[1.52rem] font-light"> <?php echo $type ?></span>
            </div>
            <div class="third-div right ">
                <h5 class=" text-black text-[1.6rem] leading-[1.52rem] font-light"><?php echo $type ?></h5>
            </div>
            <div class="fourth-div right">
                <h5 class=" text-black text-[1.6rem] leading-[1.52rem] font-light"><?php echo $closing_date ?></h5>
            </div>

            <!-- Button -->
            <div class="five-div right">

                <div class="shape absolute">

                    <i class="icon-right_button_arrow absolute z-2"></i>
                    <span class="bg absolute inset-0 size-full"></span>

                </div>

            </div>

        </a>

    <?php }
} else { ?>
    <h4 class="w-full text-center mt-[3rem] h2"><?= t("No results found") ?></h4>
<?php } ?>