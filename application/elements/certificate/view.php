<?php

use Application\Concrete\Models\Certificates\Certificates;

if (is_array($pages) && count($pages)) {
    foreach ($pages as $page) {
        $page = new Certificates($page);
        $thumb = $page->getThumbnailImage(262, 111);
        $year = $page->getYear();
        $title = $page->getTitle();
        $description = $page->getCollectionDescription();
        $thumb = $page->getThumbnailImage(850, 1190);
        $logoDesktop = $page->getCertificateLogo('certificate_logo_desktop');
        $logoMobile = $page->getCertificateLogo('certificate_logo_mobile');
        $date = date("Y", strtotime($page->getCollectionDatePublic()));
        $id = $page->getCollectionID();
        if ($description) {
            $counter++;
        }
?>
        <div class="card-item sm:w-[calc(50%-2rem)]  w-full text-rustic-red group hover transition duration-1000 ease-out opacity-0 translate-y-16 cursor-pointer
        <?php if (!$description)  echo 'no-description'; ?>" data-slideIndex="<?php if ($description) {
                                                                                    echo $counter;
                                                                                }
                                                                                ?>">
            <!-- Image -->
            <div class="img-wrap  bg-white flex items-center justify-center  aspect-[1.26] z-2 relative  rounded-[2rem]  overflow-hidden  group-hover:bg-red-berry transition duration-1000 ease-out">

                <img class="lg:block hidden object-contain lazy  filter  group-hover:invert transition-all duration-500 ease-out desktop max-w-[20rem]" loading="lazy" src="<?php echo $logoDesktop ?>" alt="<?php echo $title ?>">
                <img class="lg:hidden block object-contain lazy  filter  group-hover:invert transition-all duration-500 ease-out mobile  max-w-[15rem]" loading="lazy" src="<?php echo $logoMobile ?>" alt="<?php echo $title ?>">
            </div>
            <!-- details -->
            <div class="card-details flex justify-between mt-[3.5rem] items-center">
                <h4 class="text-[rustic-red] group-hover:text-red-berry transition duration-1000 ease-out"><?= $page->getCollectionName() ?></h4>
                <h6 class=" group-hover:text-red-berry transition duration-1000 ease-out text-[2rem] font-normal leading-5"><?php echo $year ?></h6>
            </div>

        </div>

    <?php

    }
} else { ?>
    <h2 class="w-full text-center"><?= t("No results found") ?></h2>
<?php } ?>