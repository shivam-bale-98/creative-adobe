<?php defined("C5_EXECUTE") or die("Access Denied.");
$ih = new \Application\Concrete\Helpers\ImageHelper();

$thumbnail = "";
$mthumbnail = "";

if ($Dimage) {
    $thumbnail = $ih->getThumbnail($Dimage, 2000, 2000);
}

if ($Mimage) {
    $mthumbnail = $ih->getThumbnail($Mimage, 700, 1400);
}
?>












<section id="banner" class="<?php if (isset($hideBlock) && trim($hideBlock) == 1) { ?>hide-block<?php } ?> banner-v1 | text-white px-[2rem] md:px-[4rem] xl:px-[6rem] xxl:px-[10rem] pt-[8rem] md:pt-[12rem] md:pb-[7.3rem] pb-[4rem] relative h-screen z-3 flex items-end">


    <!-- <svg class="absolute inset-0 size-full" x="0" y="0" width="100vw" height="100vh" viewBox="0 0 100vw 100vh" fill="none">

        <mask id="imageMask" viewBox="0 0 100 100">
            <g class="shape">
                <svg width="100%" height="100%">
                    <path xmlns="http://www.w3.org/2000/svg" d="M66.5382 141C44.3362 141 26.58 131.752 13.8355 113.504C-5.02544 86.5057 -4.56739 50.0102 14.9941 24.7248C27.6847 8.31427 45.0368 0 66.5382 0C124.818 0 179.919 34.2224 180 70.4689V70.5311C179.946 107.4 125.896 141 66.5382 141Z" fill="#fff" />
                </svg>
            </g>
        </mask>
        
        <?php if (isset($videoURL) && trim($videoURL) != "") { ?>
            <video xmlns="http://www.w3.org/1999/xhtml" class="absolute size-full inset-0 object-cover z-1" autoplay="" loop="" muted="" playsinline="">
                <source src="<?php echo h($videoURL); ?>" type="video/mp4" />
            </video>
        <?php } else { ?>
            <?php if ($Dimage) { ?>
                <image width="100vw" height="100vh" mask="url(#imageMask)" preserveAspectRatio="xMidYMid slice" class="desktop hidden md:block absolute size-full inset-0 object-cover z-1" xlink:href="<?php echo $thumbnail; ?>" />
            <?php } ?>
            <?php if ($Mimage) { ?>
                <image width="100vw" height="100vh" mask="url(#imageMask)" preserveAspectRatio="xMidYMid slice" class="mobile block md:hidden  absolute size-full inset-0 object-cover z-1" xlink:href="<?php echo $mthumbnail; ?>" />
            <?php } ?>
        <?php } ?>
    </svg> -->

    <div class="swiper ">
       
            <?php if ($Dimage) { ?>
                <img class="desktop hidden md:block absolute size-full inset-0 object-cover z-1" src="<?php echo $thumbnail; ?>" alt="channeline" />
            <?php } ?>
            <?php if ($Mimage) { ?>
                <img class="mobile block md:hidden  absolute size-full inset-0 object-cover z-1" src="<?php echo $mthumbnail; ?>" alt="channeline" />
            <?php } ?>
    </div>

    <div class="content z-3 relative max-w-[87.2rem]">
        <?php if (isset($subTitle) && trim($subTitle) != "") { ?>
            <h6 class="md:mb-[3rem] mb-[2rem]"><?php echo h($subTitle); ?></h6>
        <?php } ?>
        <?php if (isset($title) && trim($title) != "") { ?>
            <?php echo $title; ?>
        <?php } ?>
    </div>
</section>