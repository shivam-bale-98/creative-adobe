<?php


namespace Application\Concrete\Models\Products;

use Application\Concrete\Models\Application\ApplicationList;
use Concrete\Core\Page\Page;
use Application\Concrete\Models\CaseStudies\CaseStudiesList;

class ProductList extends \Application\Concrete\Models\Common\CommonList
{
    public function __construct()
    {
        parent::__construct();
        $this->filterByPageTypeHandle(Product::PAGE_HANDLE);
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
            $c = Product::getByID($c->getCollectionID());
            return $c;
        }
    }

    public static function getRelatedCaseStudies($productId, $itemsPerPage=5)
    {
        $pl = new CaseStudiesList();
        $pl->filterByCategory('related_products', $productId);
        $pl->setItemsPerPage($itemsPerPage);
        $pl->sortByDisplayOrder();
        return $pl->getResults();
    }

    public static function getRelatedApplications($productId, $itemsPerPage=5)
    {
        $pl = new ApplicationList();
        $pl->filterByCategory('related_products', $productId);
        $pl->setItemsPerPage($itemsPerPage);
        $pl->sortByDisplayOrder();
        return $pl->getResults();
    }

}