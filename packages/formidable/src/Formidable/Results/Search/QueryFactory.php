<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Results\Search\Query;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\SavedSearch;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\SearchProvider;
use Symfony\Component\HttpFoundation\Request;

class QueryFactory
{
    private function getRequestData($request, $method)
    {
        $vars = $method == Request::METHOD_POST ? $request->request->all() : $request->query->all();
        return (array)$vars;
    }
    
    public function createQuery(SearchProvider $searchProvider, $fields = [])
    {
        $query = new Query();

        $formID = $searchProvider->getFormID();        
        $query->setFormID($formID);

        $query->setFields($fields);
               
        $query->setColumns($searchProvider->getDefaultColumnSet());

        $itemsPerPage = $searchProvider->getItemsPerPage();
        $query->setItemsPerPage($itemsPerPage);

        return $query;
    }

    public function createFromAdvancedSearchRequest(SearchProvider $searchProvider, Request $request, $method = Request::METHOD_POST)
    {
        $query = new Query();
        
        $vars = $this->getRequestData($request, $method);
        
        $formID = $vars['fSearchFormID'];
        $query->setFormID($formID);
        
        $available = $searchProvider->getAvailableColumnSet();

        $set = $searchProvider->getBaseColumnSet();
        if (isset($vars['column']) && is_array($vars['column'])) {
            foreach ($vars['column'] as $key) {
                $set->addColumn($available->getColumnByKey($key));
            }
        } 

        $fields = $searchProvider->getFieldManager()->getFieldsFromRequest($vars);
        $query->setFields($fields);

        $sort = $available->getColumnByKey($vars['fSearchDefaultSort']); 
        $set->setDefaultSortColumn($sort, $vars['fSearchDefaultSortDirection']);
        $query->setColumns($set);

        $itemsPerPage = $vars['fSearchItemsPerPage'];
        $query->setItemsPerPage((int)$itemsPerPage);        

        return $query;
    }

    public function createFromSavedSearch(SavedSearch $preset)
    {
        return $preset->getQuery();
    }
}
