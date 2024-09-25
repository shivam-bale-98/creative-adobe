<?php

namespace Application\Controller\PageType;

use Application\Concrete\Helpers\GeneralHelper;
use Application\Concrete\Helpers\ConstantHelper;
use Application\Concrete\Helpers\SelectOptionsHelper;
use Application\Concrete\Models\Common\CommonList;
use Application\Concrete\Models\News\News;
use Application\Concrete\Page\Controller\PageTypeControllerBase;
use Application\Concrete\View\View;
use Application\Concrete\View\View as CurrentView;
use Core;
use Config;
use Symfony\Component\HttpFoundation\JsonResponse;

class SearchListing extends PageTypeControllerBase {

    const ITEMS_PER_PAGE = 6;
    const VIEW_TYPE = 'search/view';
    const TOKEN_TYPE = 'search_listing';
    public $page;
    public $sortBy;
    public $searchType;
    public $keywords;
    const SEARCH_VIEW = 'common/search';
    const SEARCH_CATEGORIES = ['portfolio', 'brands', 'locations'];
    public $label;
    function on_start()
    {
        parent::on_start();

        $this->setTokenAction(self::TOKEN_TYPE);
        $this->page     = $this->getText()->sanitize($this->get('page'));
        $this->page     = (int)$this->page ? $this->page : 1;
        $this->keywords = urldecode($this->getText()->sanitize($this->get('keywords')));
        $this->searchType = urldecode($this->getText()->sanitize($this->get('search')));

        if(!empty($this->keywords))
        {
            $this->label = 'for “'.ucfirst($this->keywords).'”';
        }
    }

    public function view()
    {
        return $this->isAjaxRequest() ? $this->search_api() : $this->search();
    }

    private function search(){
        $pages    = $this->get_items();
        $this->set('pages', $pages->getPages());
        $this->set('viewType', static::VIEW_TYPE);
        $this->set('searchView', static::SEARCH_VIEW);
        $this->set('noResultsFound', ConstantHelper::NOT_RESULTS_FOUND_TEMPLATE);
        $this->set('keywords', $this->keywords);
        $this->set('loadMore', $pages->getLoadMore());
        $this->set('searchTypeOptions', ConstantHelper::SEARCH_TYPE_OPTIONS);
        $this->set('selectedCategory', $this->searchType);
        
        $this->set('countMessage', ($pages->getTotal() > 0 ? $pages->getTotal(). ' ' . $pages->getResultsText('Result') . ' ' . t(ConstantHelper::FOUND_TEXT).' '.$this->label : ''));
        $this->set('count', $pages->getTotal());
        $this->set('tokenOutput', $this->getTokenOutput());
    }

    private function search_api(){
        $this->validateToken();

        if ($this->hasErrors()) {
            return $this->getErrorHelper()->createResponse();
        }

        $pageResponse = $this->get_items();

        $data = [
            "data" => View::elementRender(static::VIEW_TYPE, ["pages" => $pageResponse->getPages(), "showNoResultFound" => $this->page == 1]),
            "hasNextPage" => $pageResponse->getLoadMore(),
            "success" => $pageResponse->getTotal() > 0 ? true:false,
            "countMessage" => ($pageResponse->getTotal() > 0 ? $pageResponse->getTotal().' '. $pageResponse->getResultsText('Result') . ' ' . t(ConstantHelper::FOUND_TEXT).' '.$this->label : '')
        ];

        return new JsonResponse($data);

    }

    private function get_items(){

        $pl = new CommonList();
        $pl->setItemsPerPage(self::ITEMS_PER_PAGE);

        if($this->keywords) $pl->filterByKeywords($this->keywords);

        if(isset(ConstantHelper::SEARCH_TYPE_FILTER[$this->searchType]))
        {
            $pl->filterByPageTypeHandle(ConstantHelper::SEARCH_TYPE_FILTER[$this->searchType]);
        }

        $result = $pl->getPage($this->page);

        return $result;

    }
}