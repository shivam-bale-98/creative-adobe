<?php


namespace Application\Concrete\Models\CaseStudies;

use Application\Concrete\Helpers\ConstantHelper;
use Concrete\Core\Page\Page;


class CaseStudiesList extends \Application\Concrete\Models\Common\CommonList
{

    public function __construct()
    {
        parent::__construct();
        $this->filterByPageTypeHandle(CaseStudies::PAGE_HANDLE);
    }

    public function getResult($queryRow)
    {
        $c = Page::getByID($queryRow['cID'], 'ACTIVE');

        if (is_object($c) && $this->checkPermissions($c)) {

            if ($this->pageVersionToRetrieve == self::PAGE_VERSION_RECENT) {
                $cp = new \Permissions($c);
                if ($cp->canViewPageVersions() || $this->permissionsChecker === -1) {
                    $c->loadVersionObject('RECENT');
                }
            } elseif ($this->pageVersionToRetrieve == self::PAGE_VERSION_SCHEDULED) {
                $cp = new \Permissions($c);
                if ($cp->canViewPageVersions() || $this->permissionsChecker === -1) {
                    $c->loadVersionObject('SCHEDULED');
                }
            } elseif ($this->pageVersionToRetrieve == self::PAGE_VERSION_RECENT_UNAPPROVED) {
                $cp = new \Permissions($c);
                if ($cp->canViewPageVersions() || $this->permissionsChecker === -1) {
                    $c->loadVersionObject('RECENT_UNAPPROVED');
                }
            }
            if (isset($queryRow['cIndexScore'])) {
                $c->setPageIndexScore($queryRow['cIndexScore']);
            }
            $c = CaseStudies::getByID($c->getCollectionID());
            return $c;
        }
    }

    public function filterByCustomAttribute($handle, $value){
        $expressions = [];
        if (!is_array($value)) {
            $value = explode(',', $value);
        }
        foreach ($value as $key => $stat) {
            $expressions[] = $this->getQueryObject()->expr()->like('ak_'.$handle, ':subs_'.$handle);
            $this->getQueryObject()->setParameter('subs_'.$handle, '%' .trim($stat). '%');
        }

        $expr = $this->query->expr();
        if($expressions){
            $this->query->andWhere(call_user_func_array([$expr, 'orX'], $expressions));
        }

    }

    public function filterByOptionAttribute($handle, $value)
    {
        $this->filterByAttribute($handle, $value);
    }

    public static function getRelatedProducts($csId, $itemsPerPage=5)
    {
        $pl = new CaseStudiesList();
        $pl->filterByCategory('related_products', $csId);
        $pl->setItemsPerPage($itemsPerPage);
        $pl->sortByDisplayOrder();
        return $pl->getResults();
    }

    public function filterByLocation($value){
        $this->filterByCategory('related_products',$value);
    }

    public static function getRelatedCaseStudiesBasedOnLocation($currentPageId, $relatedProductsData)
    {
        $pl = new self();
        $pl->filterByLocation(explode(',',$relatedProductsData));
        $pl->excludeByID($currentPageId);
        $pl->sortByPublicDateDescending();
        $pages = $pl->getPage();
        return $pages->getPages();
    }

    public function filterByRange($value) {
        if(isset(ConstantHelper::DIAMETER_QUERIES[$value])) $this->getQueryObject()->andWhere(ConstantHelper::DIAMETER_QUERIES[$value]);
    }

}