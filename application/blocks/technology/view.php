<? //php if (isset($removePaddingTop_1) && trim($removePaddingTop_1) != "") {
?>
<? //php echo $removePaddingTop_1 == 1 ? "yes" : "no ";
?>
<? //php }
?>




<?php defined("C5_EXECUTE") or die("Access Denied.");
$ih = new \Application\Concrete\Helpers\ImageHelper();

$desktopThumbnail = "";
$mobileThumbnail = "";

if ($desktopImage) {
    $desktopThumbnail = $ih->getThumbnail($desktopImage, 2000, 1600);
    $desktopLow = $ih->getThumbnail($desktopImage, 1, 1);
}

if ($mobileImage) {
    $mobileThumbnail = $ih->getThumbnail($mobileImage, 800, 1200);
    $mobileLow = $ih->getThumbnail($mobileImage, 1, 1);
}
?>





<section class="technology <?php if ($mobileImage) { ?>mobile-image<?php } ?> <?php if (isset($removePaddingTop) && trim($removePaddingTop) == 1) { ?>pt-0<?php } ?> <?php if (isset($removePaddingBottom) && trim($removePaddingBottom) == 1) { ?>pb-0<?php } ?>  relative  text-white flex justify-center items-center z-2">
    <!-- add a switch for overlay    -->
    <?php if (isset($vimeoLink) && trim($vimeoLink) != "") { ?>
        <div class="img-wrap relative video-wrapper overlay z-1 ">

            <?php if ($desktopImage) { ?>
                <div style="background-image: url('<?php echo $desktopLow; ?>');" class="desktop parallax-inner absolute blurred-img" data-parallax="12">
                    <img class=" absolute inset-0 size-full object-cover |  lazy" loading="lazy" src="<?php echo $desktopThumbnail; ?>" alt="technology at channeline">
                </div>

            <?php } ?>
            <?php if ($mobileImage) { ?>
                <div class="mobile img-wrap absolute mobile size-full blurred-img top-0 left-0" style="background-image: url('<?php echo $mobileLow; ?>');">
                    <img class=" absolute inset-0 size-full object-cover | lazy" loading="lazy" src="<?php echo $mobileThumbnail; ?>" alt="technology at channeline">

                </div>
            <?php } ?>


            <a class="play-btn z-3 flex flex-col absolute justify-center items-center glightbox_video" href="<?php echo h($vimeoLink); ?>">
                <div class="logo flex justify-center items-center h-[6rem] w-[6rem] rounded-[1.2rem] overflow-hidden">
                    <svg width="18" height="24" viewBox="0 0 18 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16.255 10.3786C17.3604 11.177 17.3604 12.823 16.255 13.6214L3.67098 22.7098C2.34833 23.6651 0.5 22.72 0.5 21.0885L0.5 2.91151C0.5 1.27997 2.34833 0.334903 3.67098 1.29015L16.255 10.3786Z" fill="white" />
                    </svg>
                </div>
                <h5 class="mt-[1.5rem]">Play video</h5>
            </a>
        </div>
    <?php } else { ?>
        <div class="img-wrap relative  overlay z-1 ">
            <?php if ($desktopImage) { ?>
                <div class="desktop parallax-inner absolute blurred-img" style="background-image: url('<?php echo $desktopLow; ?>');" data-parallax="12">
                    <img class=" absolute inset-0 size-full object-cover |  lazy" loading="lazy" src="<?php echo $desktopThumbnail; ?>" alt="technology at channeline">
                </div>

            <?php } ?>
            <?php if ($mobileImage) { ?>
                <div class=" mobile absolute mobile-img-wrapper size-full blurred-img top-0 left-0" style="background-image: url('<?php echo $mobileLow; ?>');">

                    <img class=" absolute inset-0 size-full object-cover | lazy" loading="lazy" src="<?php echo $mobileThumbnail; ?>" alt="technology at channeline">
                </div>
            <?php } ?>
        </div>
    <?php } ?>


    <?php if (isset($title) && trim($title) != "") { ?>
        <div class="tech-crd absolute bg-rustic-red z-2 rounded-[2rem] text-center overflow-hidden fadeBottom" data-y="10" data-duration="0.5">
            <?php if (isset($title) && trim($title) != "") { ?>

                <h3 class="mb-[3rem]"><?php echo h($title); ?></h3>
            <?php } ?>

            <?php if (isset($desc_1) && trim($desc_1) != "") { ?>

                <p class=""><?php echo h($desc_1); ?></p>

            <?php } ?>

            <?php
            if (trim($link_URL) != "") { ?>
                <?php
                $link_Attributes = [];
                $link_Attributes['href'] = $link_URL;
                if (in_array($link, ['url', 'relative_url'])) {
                    $link_Attributes['target'] = '_blank';
                }
                $link_AttributesHtml = join(' ', array_map(function ($key) use ($link_Attributes) {
                    return $key . '="' . $link_Attributes[$key] . '"';
                }, array_keys($link_Attributes)));
                echo sprintf('<a tabindex="0" role="link" arai-label="' . $link_Title . '" class="channeline-btn channeline-btn--rounded-red md:mt-[3rem] mt-[2rem] relative z-2 overflow-hidden" %s>
	     <span class="z-2 relative ">%s</span>
		 <span class="circle absolute z-1"></span>
		 </a>', $link_AttributesHtml, $link_Title); ?><?php
                                                    } ?>

                <?php if ($svg) { ?>
                    <img class="svg absolute" src="<?php echo $svg->getURL(); ?>" alt="technology At Channeline">

                <?php } ?>
        </div>
    <?php } ?>
</section>