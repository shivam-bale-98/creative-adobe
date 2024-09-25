<?php

namespace Application\Controller\PageType;

use Application\Concrete\Helpers\GeneralHelper;
use Application\Concrete\Helpers\ConstantHelper;
use Application\Concrete\Helpers\SelectOptionsHelper;
use Application\Concrete\Models\Location\LocationList;
use Application\Concrete\Page\Controller\PageTypeControllerBase;
use Application\Concrete\View\View as CurrentView;
use Core;
use Config;

class ContactUs extends PageTypeControllerBase {

    const ITEMS_PER_PAGE     = 6;
    const VIEW = 'location/view';

    const TOKEN_TYPE = 'location_listing';
    public $page;
    public $keywords;
    public $location;

    function on_start()
    {
        parent::on_start();

        $this->setTokenAction(self::TOKEN_TYPE);
        $this->page     = $this->getText()->sanitize($this->get('page'));
        $this->page     = (int)$this->page ? $this->page : 1;
        $this->keywords = urldecode($this->getText()->sanitize($this->get('keywords')));
        $this->location     = urldecode($this->getText()->sanitize($this->get('location')));
    }

    public function view()
    {
        $this->isAjaxRequest() ? $this->search_api() : $this->search();
    }

    private function search(){

        $entries = $this->get_items();
        $this->set('pages', $entries);
        $this->set('viewType', self::VIEW);

        $this->set('noResultsFound', ConstantHelper::NOT_RESULTS_FOUND_TEMPLATE);
        $this->set('keywords', $this->keywords);
        $this->set('locationsOptions', GeneralHelper::setSortOptions(SelectOptionsHelper::getOptions('contact_locations'),'Location: All'));
        $this->set('tokenOutput', $this->getTokenOutput());
        $this->set('selectedLocation', '');
        $this->set('count', count($entries));

    }

    private function search_api(){

        $this->validateToken();
        $rParams = [];
        if(!$this->hasErrors()){
            $results    = $this->render_items();
            $rParams['data'] = $results['html'];
            $rParams['count'] = $results['count'];
        }

        $this->sendResult($rParams);
        exit();
    }

    private function render_items(){
        $results    = $this->get_items();
        $html       = '';
        if (GeneralHelper::pagesExist($results)) {
            $html .= CurrentView::elementRender('location/view', ['pages' => $results]);
        }
        $data['html'] = $html;
        $data['count'] = count($results);
        return $data;
    }

    private function get_items(){
        $resourceList = new LocationList();
        if($this->keywords){
           $resourceList->filterByKeywords($this->keywords);
        }

        if($this->location){
            $resourceList->filterByCustomAttribute('contact_locations', $this->location);
        }

        $resourceList->sortBy("ak_title");

        $entries = $resourceList->getResults();
        return $entries;
    }
}