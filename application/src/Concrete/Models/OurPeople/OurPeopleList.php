<?php

namespace Application\Concrete\Models\OurPeople;

use Concrete\Core\Express\EntryList;
use Concrete\Core\Support\Facade\Express;

class OurPeopleList extends EntryList
{
    public function __construct()
    {
        $entity = Express::getObjectByHandle(OurPeople::HANDLE);
        parent::__construct($entity);
    }

    public function getResult($queryRow)
    {
        return new OurPeople(parent::getResult($queryRow));
    }

    /**
     * @return LocarionResource[]
     */
    public function getResults()
    {
        return parent::getResults();
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

        return ['pages' => $pages, 'loadMore' => $loadMore, 'total' => $pagination->getTotalResults(), 'totalPages' => $pagination->getTotalPages()];
    }
}