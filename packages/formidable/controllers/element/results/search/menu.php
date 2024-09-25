<?php
namespace Concrete\Package\Formidable\Controller\Element\Results\Search;

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Controller\ElementController;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\Query;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\SearchProvider;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Utility\Service\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Package\Formidable\Src\Formidable\Forms\Form as FormidableForm;

class Menu extends ElementController
{
    protected $query;
    protected $searchProvider;

    public function __construct(SearchProvider $searchProvider)
    {
        parent::__construct();
        $this->searchProvider = $searchProvider;
    }

    public function getElement()
    {
        return 'results/search/menu';
    }

    public function setQuery(Query $query): void
    {
        $this->query = $query;
    }

    public function view()
    {
        $formOptions = $this->searchProvider->getFormOptions();
        $this->set('formOptions', $formOptions);
        
        $formID = (isset($this->query)) ? $this->query->getFormID() : $this->searchProvider->getFormID();
        $form = FormidableForm::getByID($formID);
        if ($form) {
            $this->set('formID', $formID);
            $this->set('formName', $form->getName());
        }        

        $itemsPerPage = (isset($this->query)) ? $this->query->getItemsPerPage() : $this->searchProvider->getItemsPerPage();
        $this->set('itemsPerPage', (int)$itemsPerPage);

        $this->set('itemsPerPageOptions', $this->searchProvider->getItemsPerPageOptions());

        $this->set('form', $this->app->make(Form::class));
        $this->set('token', $this->app->make(Token::class));
        $this->set('urlHelper', $this->app->make(Url::class));
    }

}
