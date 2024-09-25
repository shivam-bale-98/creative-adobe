<?php defined('C5_EXECUTE') or die("Access Denied.");

/** @var HtmlHelper $htmlHelper */
$htmlHelper = Loader::helper('html');

global $u;

$page = $c;

$pageType = (string) $page->getAttribute('page_type');
if (!$pageType) {
    $pageType = 'default';
}
$bodyClass = $bodyClass ?? '';
$bodyClass .= ' ' . $pageType . '-page';
if (User::isLoggedIn()) {
    $bodyClass .= ' logged-in';
}
if ($page->isEditMode()) {
    $bodyClass .= ' edit-mode';
}
$site = Config::get('concrete.site');
$version = Config::get('concrete.FILE_VERSION');
$is_arabic = 0;
$activeLanguage = Localization::activeLanguage();
$dir = "";
if (Localization::activeLanguage() === 'ar') {
    $activeLanguage = "ar";
    $dir = "rtl";
    $is_arabic = 1;
}
$themePath = $this->getThemePath();
?>
<!DOCTYPE html>
<!--[if lte IE 8]> <html lang="<?php echo $activeLanguage ?>" dir="<?php echo $dir; ?>" class="ie10 ie9 ie8"> <![endif]-->
<!--[if IE 9]> <html lang="<?php echo $activeLanguage ?>" dir="<?php echo $dir; ?>" class="ie10 ie9"> <![endif]-->
<!--[if gt IE 9]><!-->
<html lang="<?php echo $activeLanguage ?>" dir="<?php echo $dir; ?>"> <!--<![endif]-->

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Author -->
    <meta name="author" content="TenTwenty | Webdesign, Webshops & E-marketing | Dubai">

    <!-- Below tag will be used for android mobile browser colors, change it to main logo color of the project -->
    <meta name="theme-color" content="#393939">

    <?php
    $ih = new \Application\Concrete\Helpers\ImageHelper();
    $meta_description = ($c->getAttribute('meta_description')) ? $c->getAttribute('meta_description') : $page->getCollectionDescription();

    $meta_title = ($c->getAttribute('meta_title')) ? $c->getAttribute('meta_title') : $site . " | " . $page->getCollectionName();
    $seo_image = $c->getAttribute('seo_image');
    $banner_image = $c->getAttribute('banner_image');
    $thumbnail_image = $c->getAttribute('thumbnail_image');
    $listing_image = $c->getAttribute('listing_image');
    if ($seo_image) {
        $meta_image = $ih->getThumbnail($seo_image, 1000, 1000);
    } elseif (!$seo_image && $banner_image) {
        $meta_image = $ih->getThumbnail($banner_image, 1000, 1000);
    } else if (!$banner_image && $thumbnail_image) {
        $meta_image = $ih->getThumbnail($thumbnail_image, 1000, 1000);
    } else if (!$banner_image && !$thumbnail_image && $listing_image) {
        $meta_image = $ih->getThumbnail($listing_image, 1000, 1000);
    } else {
        $meta_image = BASE_URL . $this->getThemePath() . "/dist/images/logo.png";
    }
    ?>

    <!-- Meta Tags for Social Media -->
    <meta property="og:site_name" content="<?php echo $site; ?>">
    <meta property="og:image" content="<?php echo $meta_image; ?>">
    <meta property="og:title" content="<?= $meta_title; ?>">
    <meta property="og:description" content="<?php echo  $meta_description; ?>">
    <meta name="twitter:title" content="<?= $meta_title; ?>">
    <meta name="twitter:image" content="<?php echo $meta_image; ?>">
    <meta name="twitter:description" content="<?php echo  $meta_description; ?>">
    <meta name="twitter:card" content="summary_large_image" />

    <?php
    //print core cms files
    $metaTitle = $c->getAttribute('meta_title');
    $template = $c->getPageTemplateObject();
    $title = $metaTitle ? $metaTitle : ($pageType == 'home' || is_object($template) && $template->getPageTemplateHandle() === 'homepage' ?  $site :  $page->getCollectionName() . ' | ' . $site);
    View::element('header_required', [
        'pageTitle' => isset($title) ? $title : '',
        'pageDescription' => isset($pageDescription) ? $pageDescription : $page->getCollectionDescription(),
        'pageMetaKeywords' => isset($pageMetaKeywords) ? $pageMetaKeywords : ''
    ]);

    //custom css files
    $direction = "";
    if ($is_arabic == 1) {
        $direction = ".rtl";
        $bodyClass .= ' page-arabic';
    }

    //custom css files
    // $this->addHeaderItem($htmlHelper->css('css/all.css'));
    // $this->addHeaderItem($htmlHelper->css('css/style.css'));
    // $this->addHeaderItem($htmlHelper->css('css/print.css'));
    ?>
    <link rel="stylesheet" href="<?php echo $this->getThemePath() . '/dist/css/app' . $direction . '.min.css?v=' . $version; ?>">
    <!-- <link rel="stylesheet" href="<?php echo $this->getThemePath() . '/dist/css/vendors.min.css'; ?>">     -->

    <?php
    if ($is_arabic == 1) {
    ?>
        <link rel="stylesheet" href="<?php echo $this->getThemePath() . '/dist/css/arabic.min.css?v=' . $version; ?>">
    <?php
    }

    ?>
    <script>
        if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
            var msViewportStyle = document.createElement('style');
            msViewportStyle.appendChild(
                document.createTextNode(
                    '@-ms-viewport{width:auto!important}'
                )
            );
            document.querySelector('head').appendChild(msViewportStyle);
        }

        //set cookie for site
        function setCookie(cname, cvalue) {
            var d = new Date();
            d.setTime(d.getTime() + 2160000000);
            var expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
        }
    </script>

</head>

<body class="<?php echo $bodyClass; ?> <?php echo $c->getPageWrapperClass() ?>">
    <!-- Site Loader -->
    <div class="site-loader fixed inset-0  size-full">

        <div class="post-loader absolute inset-0 size-full bg-rustic-red z-0"></div>
        <div class="logo-middle z-2 absolute">
            <img src="<?php echo $this->getThemePath(); ?>/assets/images/channeline-romance.svg" alt="<?php echo $site; ?>">
        </div>

        <!-- <div class="progress flex absolute  mx-[2rem] md:mx-[6rem]">
            <div class="progress-bar justify-end bg-red-berry md:p-[2rem] p-[1.6rem]  flex justify-end">
                <div class="progress-counter flex"><span class="number" id="percentage">0</span>%</div>
            </div>
        </div> -->
    </div>

    <svg class="btn_mask absolute">
        <defs>
            <clipPath id="btn_shape" clipPathUnits="objectBoundingBox">
                <path d="M0.630343 1C0.753688 1 0.852333 0.934408 0.923136 0.804991C1.02792 0.613516 1.02537 0.354682 0.9167 0.175353C0.846196 0.0589665 0.749796 0 0.630343 0C0.306565 0 0.000449057 0.242712 -1.17922e-08 0.499779V0.500221C0.000299368 0.761705 0.300577 1 0.630343 1Z" fill="#80151A" />
            </clipPath>
        </defs>
    </svg>


    <div class="cookies-popUp |  fixed bottom-10 left-1/2 bg-romance text-rustic-red xl:pl-[4rem] md:pl-[3rem] pl-[2rem] xl:pr-[6.6rem] md:pr-[3rem] pr-[2rem] xl:py-[3.3rem] md:py-[3rem] py-[2rem] flex rounded-[2rem] overflow-hidden">
        <div class="flex justify-between xl:items-center items-start w-full xl:flex-row flex-col">
            <div class="left w-full xl:max-w-[71rem] ">
                <h4 class="xl:mb-[3.5rem] mb-[2rem]">
                This website uses cookies
                </h4>
                <p>This website uses cookies to improve user experience. By using our website you consent to all cookies in accordance with our privacy policy.</p>
            </div>

            <div class="right flex gap-[2rem] xl:mt-[0rem] mt-[3rem] md:flex-row flex-col">
                <a href="" class="channeline-btn text-center channeline-btn--rounded-red  relative z-2 overflow-hidden">
                    <span class="z-2 relative">Allow all</span>
                    <!-- <span class="circle absolute z-1"></span> -->
                </a>

                <a href="" class="channeline-btn text-center channeline-btn--white  relative z-2 overflow-hidden">
                    <span class="z-2 relative">Reject all</span>
                    <!-- <span class="circle absolute z-1"></span> -->
                </a>
            </div>
        </div>

    </div>



    <div class="blur-overlay absolute size-full inset-0 hidden"></div>
    <script>
        if (document.cookie.indexOf("visited=") == -1) {
            setCookie("visited", "1");
        } else {
            document.body.classList.add('visited');
        }
    </script>
    <!-- Site Loader End -->

    <div class="wrapper <?php echo $c->getPageWrapperClass() ?>"><!-- opening of wrapper div ends in footer_bottom.php -->