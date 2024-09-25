<?php


namespace Application\Concrete\Models\Vacancy;

use Concrete\Core\Page\Page;
use Application\Concrete\Models\CaseStudies\CaseStudiesList;

class VacancyList extends \Application\Concrete\Models\Common\CommonList
{
    public function __construct()
    {
        parent::__construct();
        $this->filterByPageTypeHandle(Vacancy::PAGE_HANDLE);
    }

    protected function getBaseClassName()
    {
        return Vacancy::class;
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
            $c = Vacancy::getByID($c->getCollectionID());
            return $c;
        }
    }

}