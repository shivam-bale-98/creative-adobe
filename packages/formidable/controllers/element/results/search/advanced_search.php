<?php
namespace Concrete\Package\Formidable\Controller\Element\Results\Search;

defined('C5_EXECUTE') or die("Access Denied.");

use Doctrine\ORM\EntityManager;
use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\SavedSearch as SavedResultSearch;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\Field\Manager;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\QueryFactory;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\Query;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Controller\Element\Search\SearchFieldSelector;
use Concrete\Controller\Element\Search\CustomizeResults;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker as Permissions;
use \Exception;

class AdvancedSearch extends AdvancedSearchController
{
    protected $supportsSavedSearch = true;

    protected function canAccess()
    {
        $c = Page::getByPath('/dashboard/formidable/results');
        if ($c && !$c->isError()) {
            $cp = new Permissions($c);
            return $cp->canViewPage();
        }
        return false;
    }

    protected function getSearchFieldSelectorElement()
    {
        $query = $this->app->make(Query::class);
        $bag = $this->getParameterBag();
        if ($bag->has('query')) {
            $query = $this->deserializeQuery($bag->get('query'));
        }
        $element = new SearchFieldSelector($this->getFieldManager(), $this->getAddFieldAction(), $query);
        return $element;
    }

    public function deserializeQuery($jsonQuery)
    {
        return $this->app->make(JsonSerializer::class)->deserialize($jsonQuery, Query::class, 'json', ['searchProvider' => $this->getSearchProvider()]);
    }

    public function getBasicSearchBaseURL()
    {
        return $this->app->make('url')->to('/dashboard/formidable/results', 'advanced_search');
    }

    public function getCurrentSearchBaseURL()
    {
        return $this->app->make('url')->to('/dashboard/formidable/results', 'advanced_search');
    }

    public function getSavedSearchEntity()
    {
        return $this->app->make('Concrete\Package\Formidable\Src\Formidable\Results\Search\SavedSearch');
    }

    public function getSearchProvider()
    {
        return $this->app->make('Concrete\Package\Formidable\Src\Formidable\Results\Search\SearchProvider');
    }

    public function getSearchPresets()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository(SavedResultSearch::class)->findAll();
        }
    }

    public function getSubmitMethod()
    {
        return 'get';
    }

    public function getSubmitAction()
    {
        return $this->app->make('url')->to('/dashboard/formidable/results', 'advanced_search');
    }

    protected function getCustomizeResultsElement()
    {
        $query = null;
        $bag = $this->request->getMethod() === Request::METHOD_POST ? $this->request->request : $this->request->query;
        if ($bag->has('query')) {
            $query = $this->deserializeQuery($bag->get('query'));
        }

        $provider = $this->getSearchProvider();
        $element = new CustomizeResults($provider, $query);
        return $element;
    }

    public function savePreset()
    {
        if ($this->validateAction() && $this->supportsSavedSearch) {
            $provider = $this->getSearchProvider();
            $query = $this->app->make(QueryFactory::class)->createFromAdvancedSearchRequest($provider, $this->request, Request::METHOD_POST);
            if (is_object($query)) {
                $em = $this->app->make(EntityManager::class);
                $search = $provider->getSavedSearch();
                if (is_object($search)) {

                    $search->setQuery($query);
                    $search->setPresetName($this->request->request->get('presetName'));
                    $em->persist($search);
                    $em->flush();

                    $this->onAfterSavePreset($search);

                    $result = $provider->getSearchResultFromQuery($query);
                    $result->setBaseURL($this->getSavedSearchBaseURL($search));

                    return new JsonResponse($result->getJSONObject());
                }
            }
        }
        throw new Exception(t('An error occurred while saving the search preset.'));
    }


    public function view()
    {
        $app = Application::getFacadeApplication();
        $this->set('customizeElement', $this->getCustomizeResultsElement());
        $this->set('searchFieldSelectorElement', $this->getSearchFieldSelectorElement());
        if ($this->supportsSavedSearch) {
            $this->set('searchPresets', $this->getSearchPresets());
        }
        $this->set('supportsSavedSearch', $this->supportsSavedSearch);
        $this->set('form', $app->make('helper/form'));
    }

    protected function getQueryFromRequest()
    {
        $query = new Query();
        $manager = $this->getFieldManager();
        $bag = $this->getParameterBag();
        $fields = $manager->getFieldsFromRequest($bag->all());
        $provider = $this->getSearchProvider();

        $set = $provider->getBaseColumnSet();
        $available = $provider->getAvailableColumnSet();

        foreach ($bag->get('column') as $key) {
            $set->addColumn($available->getColumnByKey($key));
        }

        $sort = $available->getColumnByKey($this->getRequestDefaultSort());
        $set->setDefaultSortColumn($sort, $this->getRequestDefaultSortDirection());

        $query->setFields($fields);
        $query->setColumns($set);

        $itemsPerPage = $this->getItemsPerPage();
        $query->setItemsPerPage((int)$itemsPerPage);

        $formID = $this->getFormID();
        $query->setFormID($formID);

        return $query;
    }

    public function getFieldManager()
    {
        //return ManagerFactory::get('formidable');
        return new Manager($this->getSearchProvider()->getFormID());
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return (string)Url::to('/dashboard/formidable/results', 'preset', $search->getID());
    }

    public function getSavedSearchDeleteURL(SavedResultSearch $search)
    {
        return (string)Url::to('/formidable/dialog/dashboard/results/advanced_search/preset/delete?presetID=' . $search->getID());
    }

    public function getSavedSearchEditURL(SavedResultSearch $search)
    {
        return (string)Url::to('/formidable/dialog/dashboard/results/advanced_search/preset/edit?presetID=' . $search->getID());
    }


    private function getItemsPerPage()
    {
        return $this->getParameterBag()->get('fSearchItemsPerPage');
    }

    protected function getRequestDefaultSort()
    {
        return $this->getParameterBag()->get('fSearchDefaultSort');
    }

    protected function getRequestDefaultSortDirection()
    {
        return $this->getParameterBag()->get('fSearchDefaultSortDirection');
    }

    private function getFormID()
    {
        return $this->getParameterBag()->get('fSearchFormID');
    }

    protected function getParameterBag()
    {
        $bag = $this->request->getMethod() === Request::METHOD_POST ? $this->request->request : $this->request->query;
        return $bag;
    }
}
