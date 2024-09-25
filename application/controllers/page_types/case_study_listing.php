<?php

namespace Application\Controller\PageType;

use Application\Concrete\Helpers\GeneralHelper;
use Application\Concrete\Helpers\ConstantHelper;
use Application\Concrete\Helpers\AttributeHelper;
use Application\Concrete\Helpers\SelectOptionsHelper;
use Application\Concrete\Models\Location\OurPeopleList;
use Application\Concrete\Models\Products\ProductList;
use Application\Concrete\Page\Controller\PageTypeControllerBase;
use Application\Concrete\User\User;
use Application\Concrete\View\View as CurrentView;
use Application\Concrete\Models\CaseStudies\CaseStudiesList;
use Core;
use Config;

class CaseStudyListing extends PageTypeControllerBase {

    const ITEMS_PER_PAGE     = 5;
    const VIEW = 'case_studies/view';
    const TOKEN_TYPE = 'case_study_listing';
    public $page;
    public $keywords;
    public $range;
    public $product;
    public $application;
    public $specification;
    public $year;
    public $location;
    public $itemsCounter;

    function on_start()
    {
        parent::on_start();
        $this->setTokenAction(self::TOKEN_TYPE);
        $this->page     = $this->getText()->sanitize($this->get('page'));
        $this->page     = (int)$this->page ? $this->page : 1;

        $this->itemsCounter       = GeneralHelper::sanitizeText($this->get('itemsCounter'));
        $this->itemsCounter       = (int)$this->itemsCounter ? $this->itemsCounter : 0;

        $this->keywords = urldecode($this->getText()->sanitize($this->get('keywords')));
        $this->range     = urldecode($this->getText()->sanitize($this->get('range')));
        $this->product     = urldecode($this->getText()->sanitize($this->get('product')));
        $this->application    = urldecode($this->getText()->sanitize($this->get('application')));
        $this->specification     = urldecode($this->getText()->sanitize($this->get('specification')));
        $this->year     = urldecode($this->getText()->sanitize($this->get('year')));
        $this->location     = urldecode($this->getText()->sanitize($this->get('location')));
    }

    public function view()
    {
        $this->isAjaxRequest() ? $this->search_api() : $this->search();
    }

    private function search(){
        $pages    = $this->get_items();
        $this->set('pages', $pages->getPages());
        $this->set('viewType', self::VIEW);
        $this->set('noResultsFound', ConstantHelper::NOT_RESULTS_FOUND_TEMPLATE);

        $this->set('keywords', $this->keywords);
        $this->set('selectedRange', $this->range);
        $this->set('selectedProduct', $this->product);
        $this->set('selectedApplication', $this->application);
        $this->set('selectedSpecification', $this->specification);
        $this->set('selectedYear', $this->year);
        $this->set('selectedLocation', $this->location);
        $this->set('hasFiltersSelected', $this->range || $this->product || $this->application || $this->specification || $this->year || $this->location);

        $this->set('rangeOptions', GeneralHelper::setSortOptions(SelectOptionsHelper::getOptions('range'),'Diameter: All'));
        $this->set('productsOptions', GeneralHelper::getProductOptions(array('' => t('Products: All'))));
        $this->set('applicationsOptions', GeneralHelper::getApplicationsOptions(array('' => t('Application: All'))));
        $this->set('specificationOptions', GeneralHelper::setSortOptions(SelectOptionsHelper::getOptions('specifications'),'Specification: All'));

        $this->set('yearOptions', SelectOptionsHelper::getOptions('year', ['' => 'Year: All']));
        $this->set('locationsOptions', GeneralHelper::setSortOptions(SelectOptionsHelper::getOptions('cs_location'),'Location: All'));

        $this->set('tokenOutput', $this->getTokenOutput());
        $this->set('loadMore', $pages->getLoadMore());
        $this->set('count', $pages->getTotal());

        $this->set('itemsCounter', $pages->getCurrentPageResultsCount());
    }

    private function search_api(){

        $this->validateToken();
        $rParams = [];
        if(!$this->hasErrors()){
            $results    = $this->render_items();

            $rParams = GeneralHelper::responseFormat($results);
            $rParams['countMessage'] = $results->getTotal() > 0 ? $results->getTotal().' '. $results->getResultsText('Result found'): '';
            $rParams['itemsCounter']  = $this->itemsCounter + $results->getCurrentPageResultsCount();
 
        }
        $this->sendResult($rParams);
        exit();
    }

    private function render_items(){
        $results    = $this->get_items();

        $html       = '';
        $pages = $results->getPages();
        if (GeneralHelper::pagesExist($pages)) {
            $html .= CurrentView::elementRender('case_studies/view', ['pages' => $pages,'loadMore' => $results->getLoadMore(), 'counter' => $this->itemsCounter]);
        }
        $results->setHtmlData($html);
        return $results;
    }

    private function get_items(){
        $pl = new CaseStudiesList();
        $pl->setItemsPerPage(static::ITEMS_PER_PAGE);
        if($this->keywords){
            $pl->filterByKeywords($this->keywords);
        }
        if($this->range){
            $pl->filterByCustomAttribute(AttributeHelper::CS_RANGE, $this->range);
        }
        if($this->product){
            $pl->filterByCustomAttribute(AttributeHelper::CS_RELATED_PRODUCTS, $this->product);
        }
        if($this->application){
            $pl->filterByCustomAttribute(AttributeHelper::CS_RELATED_APPLICATIONS, $this->application);
        }
        if($this->specification){
            $pl->filterByCustomAttribute(AttributeHelper::CS_SPECIFICATIONS, $this->specification);
        }
        if($this->year){
            $pl->filterByOptionAttribute(AttributeHelper::CS_YEAR, $this->year);
        }
        if($this->location){
            $pl->filterByOptionAttribute(AttributeHelper::CS_LOCATION, $this->location);
        }

        $pl->sortByDisplayOrder();

        $result = $pl->getPage($this->page);

        return $result;
    }
}