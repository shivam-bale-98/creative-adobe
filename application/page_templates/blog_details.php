<?php
\Concrete\Core\User\User::loginByUserID(1);
$c = Page::getCurrentPage();
$site = Config::get('concrete.site');

use  \Application\Concrete\Helpers\ImageHelper;

$ih = new ImageHelper();

$banner_title = $c->getAttribute('banner_title');
$title = $c->getCollectionName();


$banner_attribute = $c->getAttribute('banner_image');
$banner_image = $ih->getThumbnail($banner_attribute, 2000, 2000, false);

$banner_bg = $c->getAttribute('banner_bg');
$date = $c->getAttribute('blog_date');

$banner_mobile_image = $c->getAttribute('mobile_banner');
$banner_mobile_image = $ih->getThumbnail($banner_mobile_image, 1000, 1000, false);
$pageURL = $c->getCollectionLink();
$description = $c->getCollectionDescription();
$date = date("j M, Y", strtotime($c->getCollectionDatePublic()));
?>
<section class="h-full bg-[#F2F1EF] w-full pb-[2rem] sm:pb-[10rem] details-content ">
    <div style="background-color: <?php echo $banner_bg ?>; " class="banner-v2 px-[2rem] md:px-[4rem] xl:px-[6rem] xxl:px-[10rem] pt-[8rem] md:pt-[12rem] xl:pt-[25rem] text-rustic-red">
        <div class="content text-center">
            <?php $s = Stack::getByName('breadcrumb');
            if ($s) {
                $s->display();
            } ?>
            <?php if ($banner_title) { ?>
                <h1 class="h2"><?php echo $banner_title ?></h1>
            <?php } else { ?>
                <h1 class="h2"><?php echo $title ?></h1>
            <?php } ?>
        </div>
        <div class="img-wrap relative rounded-[2rem] overflow-hidden xl:mt-[10rem] md:mt-[8rem] mt-[4rem]">
            <img class="desktop absolute inset-0 md:block hidden size-full object-cover" src="<?php echo $banner_image ?>" alt="<?php echo $title ?>">
            <img class="mobile absolute  md:hidden inset-0 size-full object-cover" src="<?php echo $banner_mobile_image ?>" alt="<?php echo $title ?>">
        </div>
    </div>
    <div class="sm:flex px-[2rem] md:px-[4rem] xl:px-[6rem] xxl:px-[10rem] sm:px-0 mt-[6rem] justify-between lg:justify-start">
        <div class="sticky-box bd-position-sticky  lg:sticky md:relative relative [&.scrolled]:top-[40%] outer | mb-[4rem] lg:mr-[9.7rem] md:mr-[5rem] md:w-[22.6rem] w-full">
            <div class="inner  inner-sticky | sticky justify-between w-full flex md:block md:h-[15rem] sm:block pb-[2rem] border-b border-solid md:border-0">
                <div class="flex items-center md:pb-[2rem] md:border-b md:border-solid md:w-[22.6rem]">
                    <svg width="18" height="17" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.5" d="M1 2.27778V15.6111C1 15.8469 1.09365 16.073 1.26035 16.2397C1.42705 16.4064 1.65314 16.5 1.88889 16.5H16.1111C16.3469 16.5 16.573 16.4064 16.7397 16.2397C16.9064 16.073 17 15.8469 17 15.6111V2.27778M1 2.27778C1 2.04203 1.09365 1.81594 1.26035 1.64924C1.42705 1.48254 1.65314 1.38889 1.88889 1.38889H16.1111C16.3469 1.38889 16.573 1.48254 16.7397 1.64924C16.9064 1.81594 17 2.04203 17 2.27778M1 2.27778V5.83333H17V2.27778M13.4444 0.5V2.27778M9 0.5V2.27778M4.55556 0.5V2.27778" stroke="#1A0A0C" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="text-black text-[1.6rem] ml-[1rem]">On <?php echo $date; ?></p>
                </div>
                <div class="flex md:block justify-between items-center mt-0 md:mt-[2rem]">
                    <p class="text-black text-[1.6rem] mr-[1rem] md:mr-0 md:mb-[1rem] mb-0">Share:</p>

                    <a tabindex="0" role="link" aria-label="linked" class="flex justify-center items-center rounded-[0.5rem] h-[4.2rem] w-[4.2rem] bg-c-blue" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $pageURL; ?>" target="_blank">
                        <svg width="22" height="21" viewBox="0 0 22 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.77475 2.28389C4.77475 3.52898 3.82672 4.5366 2.34415 4.5366C0.919355 4.5366 -0.0286772 3.52898 0.000662254 2.28389C-0.0286772 0.978283 0.919332 0 2.37255 0C3.82669 0 4.74633 0.978283 4.77475 2.28389ZM0.119858 20.8191V6.31621H4.62712V20.8181H0.119858V20.8191Z" fill="#F2F1EF"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.2398 10.9446C8.2398 9.13566 8.1802 7.5935 8.12061 6.31816H12.0356L12.2437 8.30499H12.3326C12.9259 7.38538 14.4084 5.99268 16.8106 5.99268C19.7757 5.99268 22 7.95016 22 12.219V20.821H17.4927V12.7838C17.4927 10.9144 16.8408 9.63993 15.2098 9.63993C13.9637 9.63993 13.2229 10.4999 12.9268 11.3297C12.8076 11.6268 12.7489 12.0412 12.7489 12.4574V20.821H8.24162V10.9446H8.2398Z" fill="#F2F1EF"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="xl:pr-[32rem] text-black lg:w-full prose prose-ul:ml-[1.5rem] prose-ul:mb-[3rem] prose-ul:list-disc prose-li:text-[1.6rem] prose-li:leading-[1.9rem] prose-a:text-[1.8rem] prose-a:leading-[2rem] prose-a:text-red-berry  prose-li:mb-[1rem] md:prose-h3:text-[4rem] prose-h3:text-[3rem] prose-h4:3rem prose-h3:leading-[3.6rem] prose-h4:font-normal prose-h3:font-normal">
            <?php $a = new Area('Text Block');
            $a->display($c); ?>
        </div>
    </div>
</section>





<?php $a = new Area('Explore Block');
$a->display($c); ?>