<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search\Field\Field;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;

class ElementField extends AbstractField
{
    protected $element;
    protected $requestVariables = [];

    public function setElement($element)
    {
        $this->element = $element;
        $this->requestVariables = [$this->getKey()];
    }

    public function getElement()
    {               
        return $this->element;
    }

    public function getData($key)
    {
        return isset($this->data[$key])?$this->data[$key]:'';
    }

    public function getKey()
    {
        return 'el-'.$this->getElement()->getHandle();
    }

    public function getDisplayName()
    {
        return $this->getElement()->getName();
    }

    public function filterList(ItemList $list)
    {   
        if (isset($this->data)) {
            $list->filterByElement($this->getElement(), $this->getData($this->getKey()));
        }
    }

    public function renderSearchField()
    {
        $form = Application::getFacadeApplication()->make(Form::class);
        return $form->text($this->getKey(), $this->getData($this->getKey()));
    }
}
