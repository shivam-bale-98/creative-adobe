<?php
use Application\Concrete\Helpers\GeneralHelper;
if (GeneralHelper::pagesExist($pages)) {
    foreach ($pages as $page) {
        $title = $page->getName();
        $designation = $page->getDesignation();
        $profile = $page->getImage();
        $bgProfile = $page->getImage( 1,1, regenerate:true);
        ?>

        <div class="swiper-slide">
            <div class="img-wrap relative blurred-img rounded-[2rem] overflow-hidden" style="background-image: url('<?php echo $bgProfile ?>')">
                <img class="inset-0 lazy" loading="lazy" src="<?php echo $profile ?>" alt="Corey Rosser">
            </div>
            <div class="content mt-[3rem]">
                <h3 class="mb-[2.8rem]"><?php echo $title ?></h3>
                <h6 class="uppercase"><?php echo $designation ?></h6>
            </div>
        </div>

        <?php
    }
}
?>