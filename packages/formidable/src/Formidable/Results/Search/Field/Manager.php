<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search\Field;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Results\Search\Field\Field\DateAddedField;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\Field\Field\DateModifiedField;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\Field\Field\ElementField;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Field\Manager as FieldManager;
use Concrete\Package\Formidable\Src\Formidable\Forms\Form;

class Manager extends FieldManager
{
    public function __construct($form)
    {
        if (!is_object($form)){
            $form = Form::getByID($form);
        }
        if (!is_object($form)) {
            return;
        }

        $properties = [
            new KeywordsField(),
            new DateAddedField(),
            new DateModifiedField(),
        ];
        $this->addGroup(t('Core Properties'), $properties);

        $elements = [];
        foreach($form->getElements() as $element) {
            // TODO
            // shouldn't be an editable variable
            if (!$element->getTypeObject()->isEditableOption('searchable', 'bool')) {
                continue;
            }
            $ef = new ElementField();
            $ef->setElement($element);
            $elements[] = $ef;
        }
        $this->addGroup(t('Elements'), $elements);
    }
}
