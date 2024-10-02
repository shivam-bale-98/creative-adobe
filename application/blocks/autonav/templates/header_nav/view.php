<?php defined('C5_EXECUTE') or die('Access Denied.');

use Application\Concrete\Helpers\ConstantHelper;
use Application\Concrete\Helpers\GeneralHelper;
use Application\Concrete\Helpers\ImageHelper;
use Application\Concrete\Models\Products\Product;
use Application\Concrete\Models\Application\Application;

/** @var \Concrete\Core\Block\View\BlockView $view */
$view->requireAsset('javascript', 'jquery');
/** @var \Concrete\Block\Autonav\Controller $controller */
$navItems = $controller->getNavItems();
$themePath = $this->getThemePath();
$site = Config::get('concrete.site');


// STEP 1 of 2: Determine all CSS classes (only 2 are enabled by default, but you can un-comment other ones or add your own)
foreach ($navItems as $ni) {
    $classes = [];

    if ($ni->isCurrent) {
        //class for the page currently being viewed
        $classes[] = 'nav-selected';
    }

    if ($ni->inPath) {
        //class for parent items of the page currently being viewed
        $classes[] = 'nav-path-selected';
    }

    //Put all classes together into one space-separated string
    $ni->classes = implode(' ', $classes);
}

//*** Step 2 of 2: Output menu HTML ***/
?>

<header class="header fixed w-full">
    <div class="header-wrapper p-[2rem] md:p-[4rem] xl:px-[6rem]  xxl:px-[10rem]">
        <div class="flex justify-between items-center relative">
            <div class="logo relative z-3">
                <a class="w-full h-full block absolute inset-0" role="link" araia-label="Home" tabindex="0" href="<?php echo View::url('/'); ?>">
                    <img class="white absolute z-2" src="<?php echo $themePath; ?>/assets/images/site-logo.svg" alt="<?php echo $site; ?>" />
                    <img class="red absolute z-2" src="<?php echo $themePath; ?>/assets/images/site-logo-dark.svg" alt="<?php echo $site; ?>" />
                    <div class="logo-bg | absolute h-full top-4 left-1/2 -translate-x-1/2 w-[9rem] bg-red-berry z-1 opacity-0"></div>
                </a>
            </div>
            <nav class=" flex items-center">
                <ul class="links xl:flex hidden ">
                    <?php foreach ($navItems as $ni) {
                        $megaMenu = $ni->cObj->getAttribute(ConstantHelper::ATTR_MEGA_MENU);

                        echo '<li class="' . $ni->classes . ($megaMenu ? " has-mega-menu " : "") . '">';
                        $name = (isset($translate) && $translate == true) ? t($ni->name) : $ni->name;
                        echo '<a tabindex="0" role="link"  aria-label="'. t($ni->name) .'" href="' . $ni->url . '" target="' . $ni->target . '" class="relative ' . $ni->classes . '">' . $name . '</a>';

                        if ($megaMenu) {
                            $subPages = GeneralHelper::getSubPages($ni->cID);

                            $megaMenuType = $ni->cObj->getAttribute(ConstantHelper::ATTR_MEGA_MENU_TYPE);

                            switch ($megaMenuType) {
                                case ConstantHelper::MEGA_MENU_TYPE_ONE:
                                    $megaMenuTypeClasses = "type-1";
                                    $subPages = array_map(function ($subPage) {
                                        $subPage->getProductCategory();
                                        return $subPage;
                                    }, $subPages);
                                    break;
                                case ConstantHelper::MEGA_MENU_TYPE_TWO:
                                default:
                                    $megaMenuTypeClasses = "type-2";
                                    break;
                            }

                            $subPagesCategories = collect($subPages)->groupBy('product_category')->toArray();
                            $currentIndex = 1;
                    ?>
                            <div class="mega-menu flex <?php echo $megaMenuTypeClasses; ?> absolute  justify-between w-full h-auto rounded-[2rem] overflow-hidden bg-romance text-rustic-red p-[4rem]">
                                <?php foreach ($subPagesCategories as $key => $pages) {
                                    if ($currentIndex > 3) break;

                                    if ($currentIndex % 2 != 0) { ?>
                                        <div class="left">
                                            <h5><?php echo t($key); ?></h5>
                                            <div class="flex flex-wrap mt-[3rem] gap-[2rem]">
                                                <?php View::element(ConstantHelper::NAV_LEFT_ITEM, ["pages" => $pages]); ?>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="right">
                                            <h5><?php echo t($key); ?></h5>

                                            <?php View::element(ConstantHelper::NAV_RIGHT_ITEM, ["pages" => $pages]); ?>
                                        </div>
                                <?php }
                                    $currentIndex++;
                                } ?>
                            </div>
                    <?php }

                        echo '</li>';
                    } ?>
                </ul>
                <ul class="accesibility flex items-center ">
                    <!-- <li class="search">
                        <a role="button" tabindex="0" aria-label="search products" id="search" class=" flex justify-center items-center z-2 overflow-hidden relative" href="<?php echo View::url("/search"); ?>">
                            <i class="icon-search z-2"></i>
                            <span class="absolute inset-0 size-full z-1"></span>
                        </a>
                    </li> -->

                    <li role="button" aria-label="open mega menu" class="mobile-menu relative xl:hidden z-2">
                        <div class="hamburger flex flex-col items-center justify-center gap-[5px] bg-white h-[6rem] w-[6rem] rounded-full overflow-hidden">
                           <span class="h-[1px] w-[30px] bg-[#171717] relative block"></span>
                           <span class="h-[1px] w-[30px] bg-[#171717] relative block"></span>
                           <span class="h-[1px] w-[30px] bg-[#171717] relative block top-[1px]"></span>
                        </div>
                        <div class="menu-close absolute h-[6rem] w-[6rem] "></div>
                    </li>
                </ul>
                <!-- <div class="mobile-menu">
                    <span class="nav-icon"></span>
                </div> -->
            </nav>
        </div>
    </div>

    <div class="search-popUp bg-red-berry w-full p-[2rem] md:p-[4rem] rounded-[2rem] overflow-hidden  absolute">
        <div class="flex items-center ">
            <form class="backend--form flex md:gap-[4rem] gap-[1rem] w-full" id="search-form" action="<?php echo \Application\Concrete\Helpers\GeneralHelper::formURL('/search'); ?>">
                <div class="icon" onclick="javascript:$('#search-form').submit();">
                    <i class="icon-search "></i>
                </div>
                <div class="input-box w-full xl:max-w-[80rem] md:max-w-[45rem] max-w-[20rem] z-1">
                    <?php echo $form->text('keywords', null, ["placeholder" => t('What are you looking for?'), 'class' => "keywords_search"]) ?>
                </div>
            </form>

            <div class="close-search absolute md:h-[6rem] h-[4rem] md:w-[6rem] w-[4rem] rounded-full overflow-hidden bg-white z-2">
                <div class="bg-scale bg-rustic-red size-full absolute top-0 left-0 rounded-full z-1"></div>
                <span class="z-2"></span>
                <span class="z-2"></span>
            </div>
        </div>
    </div>
</header>