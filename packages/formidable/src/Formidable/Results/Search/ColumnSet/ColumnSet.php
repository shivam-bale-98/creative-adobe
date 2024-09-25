<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Results\Search\SearchProvider;
use Concrete\Core\Search\Column\Set;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\Column\Column;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Package\Formidable\Src\Formidable\Forms\Form;

class ColumnSet extends Set
{
    public static function getCurrent()
    {
        $provider = (new self)->getSearchProvider();
        $query = $provider->getSessionCurrentQuery();
        if ($query) {
            $columns = $query->getColumns();
            return $columns;
        }
        return $provider->getDefaultColumnSet();
    }

    public function getColumnsForElements()
    {
        $columns = $this->getAllColumnsBasedOnElements();
        foreach ($columns as $i => $column) {
            if ($i <= 4) {
                continue;
            }
            $this->addColumn($column);
        }
    }

    public function getAllColumnsBasedOnElements()
    {
        $columns = [];

        $provider = (new self)->getSearchProvider();
        $formID = $provider->getFormID();
        if ((float)$formID != 0) {
            $form = Form::getByID($formID);
            if ($form) {
                $elements = $form->getElements();
                foreach ($elements as $element) {
                    if (!$element->getTypeObject()->isEditableOption('searchable', 'bool')) {
                        continue;
                    }
                    $columns[] = new Column('element_'.$element->getHandle(), $element->getName(), ['Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet\DefaultSet', 'getResultData', $element->getItemID()], true);
                }
            }
        }
        return $columns;
    }

    private function getSearchProvider()
    {
        $app = Facade::getFacadeApplication();
        return $app->make(SearchProvider::class);
    }

}
