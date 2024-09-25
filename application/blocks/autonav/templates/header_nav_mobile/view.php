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

<div class="mobile-nav   fixed  w-screen text-rustic-red md:mt-[20rem] mt-[10rem]  pb-[4rem]">
    <div class="nav-items md:px-[6rem] px-[2rem] relative">
        <?php foreach ($navItems as $ni) {
            $megaMenu = $ni->cObj->getAttribute(ConstantHelper::ATTR_MEGA_MENU);

            echo '<h4 class="md:mb-[4rem] mb-[3rem] ' . $ni->classes . ($megaMenu ? " has-mega-menu " : "") . '">';
            $name = (isset($translate) && $translate == true) ? t($ni->name) : $ni->name;
            echo '<a role="link" aria-label="'.t($ni->name).'"  href="' . $ni->url . '" target="' . $ni->target . '" class="link ' . $ni->classes . '">' . $name . '</a>';

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
                <div class="<?php echo $megaMenuTypeClasses; ?> mega-menu fixed hidden h-screen w-screen  flex-col overflow-hidden bg-romance text-rustic-red md:px-[4rem] md:mt-[20rem] mt-[10rem] px-[2rem] md:pb-[3rem] pb-[2rem]">
                    <?php foreach ($subPagesCategories as $key => $pages) {
                        if ($currentIndex > 3) break;

                        if ($currentIndex % 2 != 0) { ?>
                            <div class="left">
                                <div class="title flex gap-[1rem] md:mb-[3rem] mb-[2rem]">
                                    <div class="t-shape relative close">
                                        <i class="icon-right_button_arrow absolute z-2"></i>
                                        <span class="bg bg-rustic-red absolute inset-0 size-full"></span>
                                    </div>

                                    <h4>
                                        <a href="<?php echo $ni->url; ?>"><?php echo $name; ?></a>
                                    </h4>
                                </div>
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
            echo '</h4>';
        } ?>
    </div>
    <div class="shape absolute">
        <img src="<?php echo $themePath; ?>/assets/images/mobile-shape.svg" alt="channeline-shape">
    </div>

    <div class="anim"></div>
</div>


