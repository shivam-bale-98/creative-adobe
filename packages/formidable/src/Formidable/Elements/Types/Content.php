<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Elements\Element;

class Content extends Element {

    public function getGroupHandle()
    {
        return 'layout';
    }

    public function getName()
    {
        return t('Content');
    }

    public function getDescription()
    {
        return t('Text and images');
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
            'wysiwyg' => true,
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
                'condition' => []
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

        return '<div class="'.$tags['class'].'" data-element-handle="'.$handle.'">'.$this->element->getProperty('wysiwyg').'</div>';

    }
}