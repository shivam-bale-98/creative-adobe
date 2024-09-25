<?php

use Application\Concrete\Models\Certificates\Certificates;

if (is_array($pages) && count($pages)) {
    $counter = 0;
    foreach ($pages as $page) {
        $page = new Certificates($page);
        $thumb = $page->getThumbnailImage(262, 111);
        $year = $page->getYear();
        $desc = $page->getCollectionDescription();
        // $counter = "";
        if ($desc) {
            $counter++;
        }
?>
        <div class="card-item sm:w-[calc(50%-2rem)]  w-full text-rustic-red group hover transition duration-1000 ease-out 
        <?php if (!$desc)  echo 'no-description'; ?>" data-slideIndex="<?php if ($desc) {
                                                                            echo $counter;
                                                                        } ?>">
            <!-- Image -->
            <div class="img-wrap  bg-white flex items-center justify-center  aspect-[1.26] z-2 relative  rounded-[2rem]  overflow-hidden  group-hover:bg-red-berry transition duration-1000 ease-out">
                <img class="object-contain lazy  filter  group-hover:invert " loading="lazy" src=<?= $thumb ?> alt="img" />
            </div>
            <!-- details -->
            <div class="card-details flex justify-between mt-[3.5rem]">
                <h4 class="text-[rustic-red] group-hover:text-red-berry"><?= $page->getCollectionName() ?></h4>
                <p class="text-[2rem] leading-[1.9rem] font-normal group-hover:text-red-berry"><?php echo $year ?></p>
            </div>

        </div>

    <?php

    }
} else { ?>
    <h4><?= t("No results found") ?></h4>
<?php } ?>