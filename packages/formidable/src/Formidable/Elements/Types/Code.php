<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Elements\Element;

class Code extends Element {

    public function getGroupHandle()
    {
        return 'layout';
    }

    public function getName()
    {
        return t('Code');
    }

    public function getDescription()
    {
        return t('HTML / Javascript / CSS for your form');
    }

    public function getEditableOptions()
    {
        $options = [
            // disable
            'option' => false,
            'view' => false,
            'default' => false,
            'range' => false,
            'required' => false,
            'css' => false,

            // enable
            'code' => true,

            // other
            'searchable' => false,
            'dependencies' => false,
        ];
        return array_merge(parent::getElementEditableOptions(), $options);
    }

    public function field()
    {
        return $this->element->getProperty('code');
    }

}