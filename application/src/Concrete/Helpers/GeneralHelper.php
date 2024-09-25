<?php

namespace Application\Concrete\Helpers;

use Application\Concrete\Models\Application\Application;
use Application\Concrete\Models\Application\ApplicationList;
use Application\Concrete\Models\Common\CommonList;
use Application\Concrete\Models\Products\ProductList;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Page\Page;
use Concrete\Core\Localization\Localization;
use Concrete\Core\View\View;
use Application\Concrete\Models\FileLogos\FileLogosList;
use Concrete\Theme\Concrete\PageTheme;


class GeneralHelper
{
    public static function setSortOptions($options, $default = false) {
        $sortArray = [];
        if($default) $sortArray = array_map('t', ['' =>$default]);

        $sortArray = array_merge($sortArray,array_map('t', $options));
        return $sortArray;
    }

    public static function pagesExist($pages)
    {
        if($pages && is_array($pages) && count($pages)){
            return true;
        }
        return false;
    }

    public static function getProductOptions(array $returnArr = []){
        $productList = new ProductList();
        $productList->sortByDisplayOrder();
        $productsArr   = $productList->getResults();
        /** @var Product $product */
        foreach ($productsArr as $product) $returnArr[$product->getID()] = $product->getTitle();
        return $returnArr;
    }

    public static function getApplicationsOptions(array $returnArr = []){
        $list = new ApplicationList();
        $list->sortByDisplayOrder();
        $applicationArr   = $list->getPage()->getPages();
        /** @var Application $application */
        foreach ($applicationArr as $application) $returnArr[$application->getID()] = $application->getTitle();
        return $returnArr;
    }

    /**
     * @param PageResponse $results
     * @return array
     */
    public static function responseFormat($results)
    {
        return [
            'data' => $results->getHtmlData(),
            'total' => $results->getTotal(),
            'loadMoreValue' => $results->getLoadMore()
        ];
    }

    public static function getAutoNavLink($page)
    {
        $tree = array();

        while ($page->getCollectionID()) {
            if($page->getCollectionParentID()){
                $page = Page::getByID($page->getCollectionParentID());
                array_push($tree, $page);
            }else{
                break;
            }
        }

        $tree = array_reverse($tree);
        $treeCount = count($tree) - 1;
        $linkNav = '';

        if (isset($tree)) {
            foreach ($tree as $index => $t) {
                $linkNav .= $index != $treeCount ? "<li><a class=\" l-sp-2\" href=\"{$t->getCollectionLink()}\" target=\"_self\">{$t->getCollectionName()}</a></li>
                            <li class=\"sep\"> / </li>" : "<li class=\"active  l-sp-2\"><a href=\"{$t->getCollectionLink()}\" target=\"_self\">{$t->getCollectionName()}</a></li>";
            }
        }

        return $linkNav;
    }

    public static function getSubPages($parentPageId)
    {
        $pl = new CommonList();
        $pl->filterByExcludedFromNav();
        $pl->sortByDisplayOrder();
        $pl->filterByParentID($parentPageId);
        $pages = $pl->getResults();
        return $pages;
    }

    public static function formURL($url){
        if(Localization::activeLocale() =='ar_AE' || Localization::activeLanguage()=='ar') $url = '/ar/'.$url;
        return View::url($url);
    }

    public static function getTwoDigitCount($count)
    {
        return str_pad($count, 2, '0', STR_PAD_LEFT);
    }

    public static function productDownloads()
    {
        $products = new ProductList();
        $products->sortByDisplayOrder();
        $productsArr   = $products->getResults();
        $downloads = [];
        foreach($productsArr as $product)
        {
            $downloads[] = $product->getDownloads();
        }
        return collect($downloads)->flatten(2)->unique()->toArray();
    }

    public static function fileSetDownloads($setName = '')
    {
        $files = [];
        if(empty($setName))
        {
            $mySets = collect(\Concrete\Core\File\Set\Set::getMySets())->pluck('fsName')->toArray();
            foreach($mySets as $setName)
            {
                $files[] = Set::getFilesBySetName($setName);
            }
            $files = collect($files)->flatten(1)->toArray();
        }else{
            $files = Set::getFilesBySetName($setName);
        }

        $filesData = [];
        foreach($files as $file)
        {
            $filesData[] = self::getFileDetails($file);
        }
        return $filesData;
    }

    public static function getFileDetails($file)
    {
        $icon = strtolower($file->getType()) == "pdf" ? "pdf-icon" : "magic_icon";
        return [
            'name' => $file->getTitle(),
            'url' => $file->getUrl(),
            'type' => $file->getType(),
            'year' => (string) $file->getAttributeValue('year'),
            'fileImg' => PageTheme::getSiteTheme()->getThemeURL() . "/assets/images/{$icon}.svg"
        ];
    }
    public static function getFileImg($type)
    {
        $pl = new FileLogosList();
        $pl->filterByKeywords($type);
        $result = $pl->getResults();
        if($result)
        {
            return $result[0]->getImage(regenerate:true);
        }
        return '';
    }
    public static function getProductPages($ids)
    {
        $ids = explode(',',$ids);
        $pl = new ProductList();
        $pl->filterByIDs($ids);
        return $pl->getResults();
    }

    public static function sanitizeText($value){
        $th = \Core::make('helper/text');
        return $th->entities($th->sanitize(urldecode($value)));
    }
}