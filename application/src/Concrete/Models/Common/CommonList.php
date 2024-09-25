<?php


namespace Application\Concrete\Models\Common;

use Application\Concrete\Page\Controller\PageResponse;
use Application\Concrete\Page\Page;
use Concrete\Core\Entity\File\File;
use Concrete\Theme\Concrete\PageTheme;

class CommonList extends \Concrete\Core\Page\PageList
{
    protected $baseClassName;

    public function __construct($baseClassName = "", $excludePageList = true)
    {
        parent::__construct();
        if($excludePageList) {
            $this->includeExcludePageList();
        }
        $baseClassName = $baseClassName ? $baseClassName : Common::class;
        
        $this->setBaseClassName($baseClassName);
    }

    public function includeExcludePageList()
    {
        $this->filter(false, '(ak_exclude_page_list = 0 or ak_exclude_page_list is null)');
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

            if($this->getBaseClassName()){
                $class = $this->getBaseClassName();
                $baseObject = new $class($c);
            }
            else{
                $baseObject = $c;
            }

            return $baseObject;
        }

        return null;
    }

    /**
     * @param int $page
     * @return PageResponse
     */
    public function getPage($page = 1)
    {
        $pages    = [];
        $loadMore = false;

        $pagination = $this->getPagination();

        if ($pagination->getTotalPages() >= $page) {
            $pagination->setCurrentPage($page);
            $pages = $pagination->getCurrentPageResults();
        }

        if($pagination->hasNextPage()) {
            $loadMore = true;
        }

        return new PageResponse($pages, $loadMore, $pagination->getTotalResults(), $pagination->getTotalPages());
    }


    /** Returns a full array of results. */
    public function getResults()
    {
        $results = array();

        $this->debugStart();

        $executeResults = $this->executeGetResults();

        $this->debugStop();

        foreach ($executeResults as $result) {
            $r = $this->getResult($result);
            if ($r != null) {
                $results[] = $r;
            }
        }

        return $results;
    }

    public function excludeByID($ID)
    {
        $params = [];
        if (!is_array($ID)) {
            $items = [$ID];
        } else {
            $items = $ID;
        }
        foreach ($items as $key => $value) {
            $params[] = ':CID' . $key;
            $this->getQueryObject()->setParameter('CID' . $key, $value);
        }

        $this->getQueryObject()->andWhere($this->getQueryObject()->expr()->notIn('p.cID', $params));
    }

    protected function setBaseClassName($cm)
    {
        $this->baseClassName = $cm;
    }

    protected function getBaseClassName()
    {
        return $this->baseClassName;
    }

    public function filterByCategory($attrHandle,$value){
        if (!is_array($value)) {
            $value = explode(',', $value);
        }

        foreach ($value as $key => $stat) {
            $expressions[] = $this->getQueryObject()->expr()->like('ak_'.$attrHandle, ':subs_'.$key);
            $this->getQueryObject()->setParameter('subs_'.$key, '%' .trim($stat). '%');
        }

        $expr = $this->query->expr();
        if($expressions){
            $this->query->andWhere(call_user_func_array([$expr, 'orX'], $expressions));
        }
    }
    
    public function sortByApplication($value)
    {
        switch ($value){
            case 'date_asc':
                $this->sortByPublicDate();
                break;
            case 'date_desc':
                $this->sortByPublicDateDescending();
                break;
            case 'name_asc':
                $this->sortByName();
                break;
            case 'name_desc':
                $this->sortByNameDescending();
                break;
            case 'display_asc':
                $this->sortByDisplayOrder();
                break;
            case 'display_desc':
                $this->sortByDisplayOrderDescending();
                break;
        }
    }

    public function filterByExcludedFromNav(){
        $this->filter(false, '(ak_exclude_nav = 0 or ak_exclude_nav is null)');
    }

    public function filterByIDs($ids) {
        $params = [];
        if (!is_array($ids)) {
            $items = [$ids];
        } else {
            $items = $ids;
        }
        foreach ($items as $key => $value) {
            $params[] = ':CID' . $key;
            $this->getQueryObject()->setParameter('CID' . $key, $value);
        }

        $this->getQueryObject()->andWhere($this->getQueryObject()->expr()->In('p.cID', $params));

    }

    public function getImageByAttributeHandle($aHandle, $width = 400, $height = 400, $crop = false)
    {
        /** @var File $file */
        /** @var \Concrete\Core\File\Image\BasicThumbnailer $ih */
        $ih = Core::make('helper/image');

        $imageFile = $this->collectionObject->getAttribute($aHandle);
        if ($imageFile && $imageFile instanceof File) {
            $imageFile = $ih->getThumbnail($imageFile, $width, $height, $crop);
            if ($imageFile) {
                $image = $imageFile->src;
            }
        }

        if (!isset($image)) {
            $image = BASE_URL . PageTheme::getSiteTheme()->getThemeURL() . '/assets/images/placeholder.jpg';
        }
        return $image;
    }
}