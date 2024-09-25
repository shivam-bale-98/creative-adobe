<?php defined("C5_EXECUTE") or die("Access Denied.");

use Application\Concrete\Helpers\GeneralHelper;

$themePath = $this->getThemePath();

?>

<section style="background-color: #fff;" class="certificate-main-container certificates-accreditions xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] text-rustic-red" id="certificates">
    <div class="title flex flex-wrap justify-between">
        <div class="left xl:w-3/4 w-full">
            <?php if (isset($title) && trim($title) != "") { ?>

                <div class="fade-text" data-duration="1.2" data-delay="0.2">
                    <h2 class="">
                        <?php echo h($title); ?>
                    </h2>
                </div>
            <?php } ?>
        </div>
        <div class="right flex md:justify-end items-center xl:w-1/4  w-full mt-[2rem] md:mt-[0rem] fade-text" data-duration="1">
            <?php
            if (trim($link_URL) != "") { ?>
                <?php
                $link_Attributes = [];
                $link_Attributes['href'] = $link_URL;
                $link_AttributesHtml = join(' ', array_map(function ($key) use ($link_Attributes) {
                    return $key . '="' . $link_Attributes[$key] . '"';
                }, array_keys($link_Attributes)));
                echo sprintf('<a tabindex="0" role="link" aria-label="' . $title . '" class="channeline-btn channeline-btn--arrow channeline-btn--border channeline-btn--red relative" %s>
        <span class="text z-2 relative">%s</span>
        <div class="shape absolute">
            <i class="icon-right_button_arrow absolute z-2"></i>
            <span class="bg absolute inset-0 size-full"></span>
        </div>
    </a>', $link_AttributesHtml, $link_Title); ?>


            <?php } ?>



        </div>
    </div>


    <div class="certificates-listing-block certificates-list flex flex-wrap justify-between md:mt-[6rem] mt-[3rem] certificate--popup">
        <?php
        if (GeneralHelper::pagesExist($pages)) {
            $counter = 0;

            foreach ($pages as $page) {

                $title = $page->getTitle();
                $url = $page->getUrl();
                $description = $page->getCollectionDescription();
                $thumb = $page->getThumbnailImage(850, 1190);
                $date = date("Y", strtotime($page->getCollectionDatePublic()));
                $logoDesktop = $page->getCertificateLogo('certificate_logo_desktop');
                $logoMobile = $page->getCertificateLogo('certificate_logo_mobile');

                if ($description) {
                    $counter++;
                }
        ?>
                <div tabindex="0" role="button" aria-label="certification <?php echo $title ?>" class="certificate-card cursor-pointer block card-item  <?php if (!$description)  echo 'no-description'; ?>" data-slideIndex="<?php if ($description) {
                                                                                                                                        echo $counter;
                                                                                                                                    }
                                                                                                                                    ?>">
                    <div class="image rounded-[2rem] relative flex justify-center items-center">
                        <div class="svg">
                            <img class="object-contain desktop max-w-[20rem]" src="<?php echo $logoDesktop ?>" alt="<?php echo $title ?>">
                            <img class="object-contain mobile hidden max-w-[15rem]" src="<?php echo $logoMobile ?>" alt="<?php echo $title ?>">

                        </div>
                    </div>
                    <div class="content flex justify-between items-center mt-[3rem]">
                        <h4><?php echo $title ?></h4>

                        <h6><?php echo $date ?></h6>
                    </div>
                </div>

        <?php }
        }
        ?>
        <!-- <a href="" class="certificate-card block">
            <div class="image rounded-[2rem] relative flex justify-center items-center">
                <div class="svg">
                    <img class="object-contain desktop " src="https://channeline.1020dev.com/application/files/3917/1109/7865/bencor.svg" alt="">
                    <img class="object-contain mobile hidden" src="https://channeline.1020dev.com/application/files/3017/1110/0420/benor.svg" alt="">
                </div>
            </div>
            <div class="content flex justify-between items-center mt-[3rem]">
                <h4>BENOR</h4>

                <h6>2022</h6>
            </div>
        </a> -->
    </div>


</section>


<!-- POP UP -->
<section class="certificate-popup-block fixed inset-0 size-full transition-all-600">
    <div class="t-overlay"></div>
    <span role="button" aria-label="close certificates popup"  class="popup-close absolute flex justify-center rounded-full items-center transition-all-600 w-[6rem] h-[6rem]">
    </span>
    <div class="certificate-popup-slider rounded-tl-[2rem] h-full relative bg-rustic-red text-white">

        <div class="swiper">
            <div class="swiper-wrapper popUpItem-list-js certificate--popup">
                <?php View::element("certificate/popup", ["pages" => $pages, 'counter' => 0]); ?>
            </div>
            <!-- Swiper Buttons -->
            <div class="btn-wrap  flex justify-end gap-15 absolute">


                <div class="swiper-buttons  flex z-3">
                    <div tabindex="0" aria-label="swiper button previous" role="button" class="swiper-button swiper-button-prev relative" aria-label="left slide arrow">
                        <i class="icon-right_button_arrow absolute z-3"></i>
                        <span class="bg absolute inset-0 size-full z-1"></span>
                        <span class="bg-scale absolute  size-full z-2"></span>
                    </div>
                    <div tabindex="0" aria-label="swiper button next" role="button" class="swiper-button swiper-button-next relative" aria-label="left slide arrow">
                        <i class="icon-right_button_arrow absolute z-3"></i>
                        <span class="bg absolute inset-0 size-full z-1"></span>
                        <span class="bg-scale absolute  size-full z-2"></span>
                    </div>
                </div>

            </div>
        </div>



    </div>
</section>