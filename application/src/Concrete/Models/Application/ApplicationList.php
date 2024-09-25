<?php


namespace Application\Concrete\Models\Application;

use Concrete\Core\Page\Page;
use Application\Concrete\Models\CaseStudies\CaseStudiesList;

class ApplicationList extends \Application\Concrete\Models\Common\CommonList
{

    public function __construct()
    {
        parent::__construct();
        $this->filterByPageTypeHandle(Application::PAGE_HANDLE);
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
            $c = Application::getByID($c->getCollectionID());
            return $c;
        }
    }

    public static function getRelatedCaseStudies($applicationId, $itemsPerPage=5)
    {
        $pl = new CaseStudiesList();
        $pl->filterByCategory('related_applications', $applicationId);
        $pl->setItemsPerPage($itemsPerPage);
        $pl->sortByDisplayOrder();
        return $pl->getResults();
    }
}