<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Elements\Element;

class Line extends Element {

    public function getGroupHandle()
    {
        return 'layout';
    }

    public function getName()
    {
        return t('Line');
    }

    public function getDescription()
    {
        return t('Horizontal rule');
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

            // others
            'searchable' => false,
            'dependencies' => [
                // available actions for itself
                'action' => [
                    'show',
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
        $handle = $this->element->getHandle();

        $tags = $this->tags();

        // update classes
        $class = $handle;
        if (isset($tags['class'])) {
            $class .= ' '.$tags['class'];
        }
        $tags['class'] = $class;

        return '<hr class="'.$tags['class'].'" data-element-handle="'.$handle.'" />';
    }
}