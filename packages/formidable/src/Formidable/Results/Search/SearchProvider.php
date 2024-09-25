<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Results\ResultList;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\Result\Result;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\Field\Manager;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet\DefaultSet;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet\Available;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet\ColumnSet;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\SavedSearch as SavedResultSearch;
use Concrete\Package\Formidable\Src\Formidable\Forms\FormList;
use Concrete\Package\Formidable\Src\Formidable\Forms\Form;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\Query;
use Symfony\Component\HttpFoundation\Session\Session;

class SearchProvider extends AbstractSearchProvider
{
    protected $formID = 0;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function setSessionCurrentQuery($query)
    {
        $this->session->set('search/' . $this->getSessionNamespace() . '/query', $query);
    }

    public function getFieldManager()
    {
        return new Manager($this->getForm());
    }

    public function getSessionNamespace()
    {
        return 'formidable';
    }

    public function getCustomAttributeKeys()
    {
        return [];
    }

    public function getBaseColumnSet()
    {
        return new ColumnSet();
    }

    public function getAvailableColumnSet()
    {
        return new Available();
    }

    public function getCurrentColumnSet()
    {
        return ColumnSet::getCurrent();
    }

    public function createSearchResultObject($columns, $list)
    {
        return new Result($columns, $list);
    }

    public function getItemList()
    {
        $list = new ResultList();
        return $list;
    }

    public function getDefaultColumnSet()
    {
        return new DefaultSet();
    }

    public function getSavedSearch()
    {
        return new SavedResultSearch();
    }

    public function setFormID($formID)
    {
        $this->formID = $formID;
    }

    /**
     * Gets items per page from the current preset or from the session.
     *
     * @return int
     */
    public function getFormID()
    {
        $sessionQuery = $this->getSessionCurrentQuery();
        if ($sessionQuery instanceof Query) {
            if (in_array($sessionQuery->getFormID(), array_keys((array)$this->getFormOptions()))) {
                $this->formID = $sessionQuery->getFormID();
                return $this->formID;
            }
        }

        // if not set, then set
        foreach (array_keys((array)$this->getFormOptions()) as $id) {
            $this->formID = $id;
            break;
        }
        return $this->formID;
    }

    /**
     * Gets the form object from the current preset or from the session.
     *
     * @return object
     */
    public function getForm()
    {
        return Form::getByID($this->getFormID());
    }

    /**
     * @return array
     */
    public function getFormOptions()
    {
        return FormList::getOptionList();
    }

}
