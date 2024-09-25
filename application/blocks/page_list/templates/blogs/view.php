<?php
defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();

/** @var \Concrete\Core\Utility\Service\Text $th */
$th = Core::make('helper/text');
/** @var \Concrete\Core\Localization\Service\Date $dh */
$dh = Core::make('helper/date');

use Application\Concrete\Helpers\ImageHelper;

$ih = new ImageHelper();


if ($c->isEditMode() && $controller->isBlockEmpty()) {
?>
    <div class="ccm-edit-mode-disabled-item"><?php echo t('Empty Page List Block.') ?></div>
<?php
} else { ?>
    <section class="related-blogs | w-full mt-[4rem] md:mt-[10rem] ml-[2rem] md:ml-[6rem]">
        <div class="flex justify-between mb-[4rem] md:mb-[6rem] mr-[3rem] sm:mr-[12rem]">
            <div class="left">
                <?php if (isset($pageListTitle) && $pageListTitle) {
                ?>
                    <h2 class="text-black"><?php echo h($pageListTitle) ?></h2>
                <?php
                } ?>
            </div>
            <div class="right sm:mt-[2rem] md:mt-[1rem] xl:mt-[0rem]fade-text" data-duration="1" data-delay="0.2">
                <div class="swiper-buttons | rustic-red flex z-3">
                    <div tabindex="0" aria-label="swiper button previous" role="button" class="swiper-button swiper-button-prev relative">
                        <i class="icon-right_button_arrow absolute z-3"></i>
                        <span class="bg absolute inset-0 size-full z-1"></span>
                        <span class="bg-scale absolute  size-full z-2"></span>
                    </div>
                    <div tabindex="0" aria-label="swiper button next" role="button" class="swiper-button swiper-button-next relative">
                        <i class="icon-right_button_arrow absolute z-3"></i>
                        <span class="bg absolute inset-0 size-full z-1"></span>
                        <span class="bg-scale absolute  size-full z-2"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper mb-[4rem] sm:mb-[10rem]">
            <div class="swiper-wrapper">
                <?php foreach ($pages as $page) {

                    $title = $page->getCollectionName();
                    $url = $page->getCollectionLink();
                    $desc = $page->getCollectionName();
                    $thumbnail = $page->getAttribute('thumbnail');
                    if ($thumbnail) {
                        $thumbnail = $ih->getThumbnail($thumbnail, 2000, 2000);
                    }
                    $date = date("j M, Y", strtotime($page->getCollectionDatePublic()));
                ?>
                    <div class="swiper-slide">
                        <a class="related-blogs-card hover:no-underline" href="<?php echo $url ?>">
                            <img class="lazy" src="<?php echo $thumbnail; ?>" alt="<?php echo $title; ?>">
                            <div class=" flex items-center"><span class=" flex items-center w-[0.8rem] h-[0.8rem] rounded-[0.2rem] bg-red-berry opacity-50 my-[2rem]"></span>
                                <p class="ml-[1.5rem]"><?php echo $date; ?></p>
                            </div>
                            <div class="md:max-w-[43.6rem] max-w-full">
                                <h4 class="text-black mb-[2rem] "><?php echo $title; ?></h4>
                                <p class="text-black lg:w-[36rem] "><?php echo $desc ?></p>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>