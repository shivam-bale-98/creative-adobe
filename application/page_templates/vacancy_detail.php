<?php

$c = Page::getCurrentPage();
$site = Config::get('concrete.site');
$themePath = $view->getThemePath();

use  \Application\Concrete\Helpers\ImageHelper;
use  \Application\Concrete\Helpers\GeneralHelper;

/** @var \Concrete\Core\Localization\Service\Date $dh */
$dh = Core::make('helper/date');
$c = Page::getCurrentPage();

$ih = new ImageHelper();

use Concrete\Core\Localization\Service\Date;

$banner_title = $c->getAttribute('banner_title');
$title = $c->getCollectionName();
$desc = $c->getCollectionDescription();


$exclude_from_page_list = $c->getAttribute('exclude_page_list');
$id = $c->getCollectionID();
$dh = new Date();
// $closing_date = $dh->formatCustom('d/M/Y', $c->getAttribute('closing_date'));




$job_description = $c->getAttribute('job_description');
$job_summary = $c->getAttribute('job_summary');
$job_profile = $c->getAttribute('job_profile');
$job_short_description = $c->getAttribute('job_short_description');

$apply_button_url = $c->getAttribute('apply_button_url');
$banner_bg = $c->getAttribute('banner_bg');
$pageURL = $c->getCollectionLink();
$date = date("j M, Y", strtotime($c->getCollectionDatePublic()));
?>


<section style="background-color: <?php echo $banner_bg ?>; " class="px-[2rem] md:px-[4rem] xl:px-[6rem] xxl:px-[10rem] pt-[8rem] md:pt-[12rem] xl:pt-[25rem] text-rustic-red xl:pb-[10rem] md:pb-[8rem] pb-[4rem]">
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
</section>

<div class="vacancy-details">
    <section class="vacancy-detail-content details-content px-0 px-xl-15-h pb-xl-12  text-black bg-romance xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:py-[7.5rem] md:py-[5rem] py-[3rem] pb-0">
        <div class="flex  md:flex-row  flex-col">

            <!-- Sticky -->
            <div class="sticky-box bd-position-sticky lg:sticky md:relative relative top-0 [&.scrolled]:top-[40%] mb-[4rem] lg:mr-[9.7rem] md:mr-[5rem] md:w-[22.6rem] w-full ">
                <div class="inner-sticky flex flex-row lg:flex-col justify-between md:block   pb-[2rem] border-b border-solid md:border-0 ">
                    <div class="job-date flex items-center gap-[0.5rem] md:pb-[2rem] md:border-b md:border-solid md:w-[22.6rem]">
                        <!-- <img src="<?php echo $themePath ?>/assets/images/date-icon.svg" alt=""> -->
                        <svg width="18" height="17" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.5" d="M1 2.27778V15.6111C1 15.8469 1.09365 16.073 1.26035 16.2397C1.42705 16.4064 1.65314 16.5 1.88889 16.5H16.1111C16.3469 16.5 16.573 16.4064 16.7397 16.2397C16.9064 16.073 17 15.8469 17 15.6111V2.27778M1 2.27778C1 2.04203 1.09365 1.81594 1.26035 1.64924C1.42705 1.48254 1.65314 1.38889 1.88889 1.38889H16.1111C16.3469 1.38889 16.573 1.48254 16.7397 1.64924C16.9064 1.81594 17 2.04203 17 2.27778M1 2.27778V5.83333H17V2.27778M13.4444 0.5V2.27778M9 0.5V2.27778M4.55556 0.5V2.27778" stroke="#1A0A0C" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p>On <?php echo $date; ?></p>
                    </div>
                    <!-- <hr class="bg-black/10 lg:w-[22.6rem] xmd:w-full w-full my-[2rem]"> -->
                    <div class="job-share md:mt-[2rem] mt-0 flex md:block justify-between items-center gap-[1rem]">
                        <p class="md:mb-[1rem] mb-0">Share:</p>



                        <a tabindex="0" role="link" aria-label="Share on Linkedin" class="flex justify-center items-center rounded-[0.5rem] h-[4.2rem] w-[4.2rem] bg-c-blue" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $pageURL; ?>" target="_blank">
                            <svg width="22" height="21" viewBox="0 0 22 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.77475 2.28389C4.77475 3.52898 3.82672 4.5366 2.34415 4.5366C0.919355 4.5366 -0.0286772 3.52898 0.000662254 2.28389C-0.0286772 0.978283 0.919332 0 2.37255 0C3.82669 0 4.74633 0.978283 4.77475 2.28389ZM0.119858 20.8191V6.31621H4.62712V20.8181H0.119858V20.8191Z" fill="#F2F1EF"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.2398 10.9446C8.2398 9.13566 8.1802 7.5935 8.12061 6.31816H12.0356L12.2437 8.30499H12.3326C12.9259 7.38538 14.4084 5.99268 16.8106 5.99268C19.7757 5.99268 22 7.95016 22 12.219V20.821H17.4927V12.7838C17.4927 10.9144 16.8408 9.63993 15.2098 9.63993C13.9637 9.63993 13.2229 10.4999 12.9268 11.3297C12.8076 11.6268 12.7489 12.0412 12.7489 12.4574V20.821H8.24162V10.9446H8.2398Z" fill="#F2F1EF"></path>
                            </svg>
                        </a>
                    </div>


                    <!-- print popup id here  -->

                </div>

            </div>
            <!-- Content -->
            <div class="content-wrapper lg:w-[50%] xmd:w-full w-full [&_p]:mb-[3rem] [&_p]:mt-0 ">

                <!-- Summary -->
                <?php if (!empty($job_summary)) { ?>
                    <div class="content lg:pb-[3rem] pb-0">
                        <div class="mb-[3rem] title ">
                            <?php $a = new Area('summary title');
                            $a->display($c); ?>
                        </div>
                        <div class="description prose-ul:ml-[1.5rem] prose-ul:mb-[3rem] prose-ul:list-disc text-[1.6rem] prose-u:leading-[1.9rem] prose-li:mb-[1rem]">
                            <?php echo $job_summary ?>
                        </div>
                    </div>
                <?php } ?>

                <!-- Description -->
                <?php if (!empty($job_description)) { ?>
                    <div class="content lg:pb-[3rem] pb-0">
                        <div class="mb-[3rem] title ">
                            <!-- <h4><? //php echo t('Job Description')
                                        ?></h4> -->
                            <?php $a = new Area('job description title');
                            $a->display($c); ?>
                        </div>
                        <div class="description prose-ul:ml-[1.5rem] prose-ul:mb-[3rem] prose-ul:list-disc text-[1.6rem] prose-u:leading-[1.9rem] prose-li:mb-[1rem]">
                            <?php echo $job_description ?>
                        </div>
                    </div>
                <?php } ?>

                <!-- Profile -->
                <?php if (!empty($job_profile)) {  ?>
                    <div class="content lg:pb-[3rem] pb-0">
                        <div class="mb-[3rem] title ">
                            <!-- <h4><? //php echo t('Profile')
                                        ?></h4> -->
                            <?php $a = new Area('job profile title');
                            $a->display($c); ?>
                        </div>
                        <div class="description prose-ul:ml-[1.5rem] prose-ul:mb-[3rem] prose-ul:list-disc text-[1.6rem] prose-u:leading-[1.9rem] prose-li:mb-[1rem]">
                            <?php echo $job_profile ?>
                        </div>
                    </div>
                <?php } ?>


                <!-- Apply Button -->
                <?php if ($apply_button_url) { ?>
                    <div class="bottom  mt-[3rem]">
                        <a class="applyBtn channeline-btn channeline-btn--arrow channeline-btn--border channeline-btn--red relative " href="javascript:void(0)" id="open-popUp_<?php echo $id ?>">
                            <span class="text z-2 relative"><?php echo t('Apply now') ?></span>
                            <div class="shape absolute">
                                <i class="icon-right_button_arrow absolute z-2"></i>
                                <span class="bg absolute inset-0 size-full"></span>
                            </div>
                        </a>

                    </div>
                <?php } ?>





            </div>
        </div>
    </section>
    <!-- print pageID here  -->
    <div class="vacancy-form-popUp z-index--1 group fixed h-full w-full z-[100] opacity-0 invisible right-0 top-0  [&.active]:overflow-x-hidden [&.active]:overflow-y-auto [&.active]:opacity-100 [&.active]:visible group-[active]" id="popUp-<?php echo $id ?>">

        <!-- Overlay -->
        <div class="overlay  fixed top-0 left-0 right-0  w-full h-full opacity-0 invisible transition-all duration-300 ease-out bg-rustic-red-50 group-[&.active]:opacity-100 group-[&.active]:visible"></div>

        <div class="outer-content">
            <div class="content bg-rustic-red z-index--2  sm:py-[4.6rem] py-[3rem] sm:px-[3rem] px-[1.5rem] h-auto relative rounded-[1rem] sm:w-auto w-fit sm:max-w-[60.7rem]  transition-transform duration-300 ease-out translate-x-0  -translate-y-[-25%] sm:mx-auto mx-[2rem]   my-[4rem]  [&.active]:opacity-100 group-[&.active]:translate-x-0 group-[&.active]:translate-y-0   ">
                <div class="mb-8 text-white">
                    <div class="mb-[4.6rem] flex items-center justify-between">
                        <h3 class=" "><?php echo t('Apply now') ?></h3>
                        <!-- Close Button -->
                        <div class="close w-[6rem] h-[6rem]  rounded-[3rem] flex justify-center items-center group transition-opacity duration-1000  pointer-events-auto bg-red-berry">
                            <svg class="hover:opacity-50" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 1L1 19M19 19L1 1" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                    <h5 class="mb-[1rem]  text-[2.4rem] font-normal leading-[2.8rem]"><?php echo $title ?></h5>
                    <div class="">
                        <?php if (!empty($job_short_description)) {  ?>
                            <?php echo $job_short_description ?>
                        <?php } ?>
                    </div>
                </div>

                <?php $s = Stack::getByName('Vacancy Form');
                if ($s) {
                    $s->display();
                } ?>




            </div>
        </div>
    </div>
</div>
<?php $a = new Area('Page Content');
$a->display($c); ?>

<?php $a = new Area('other blocks');
$a->display($c); ?>