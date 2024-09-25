<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Elements\Element;

class Submit extends Element {

    public function getGroupHandle()
    {
        return 'handling';
    }

    public function getName()
    {
        return t('Submit');
    }

    public function getDescription()
    {
        return t('Submit-button');
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

            // enable
            'css' => true,

            // searchable
            'searchable' => false,

            // dependencies
            'dependencies' => [
                // available actions for itself
                'action' => [
                    'show',
                    'disable',
                    'class',
                ],
                // available conditions for other elements
                'condition' => [
                    // if empty the element can't be used for other elements
                ]
            ]
        ];
        return array_merge(parent::getElementEditableOptions(), $options);
    }

    public function field()
    {
        $tags = $this->tags();
        if (!isset($tags['type'])) {
            $tags['type'] = 'submit';
        }
        if (!isset($tags['class'])) {
            $tags['class'] = 'btn btn-primary '.$this->element->getHandle();
        }

        $attr = '';
        foreach ($tags as $k => $v) {
            $attr .= $k.'="'.$v.'" ';
        }
        return '<button '.$attr.'>'.$this->element->getName().' <i class="fas fa-spinner fa-spin"></i></button>';
    }
}