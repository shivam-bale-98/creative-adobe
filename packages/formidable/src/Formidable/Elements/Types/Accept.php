<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Elements\Element;
use Concrete\Core\Form\Service\Form;

class Accept extends Element {

    public function getGroupHandle()
    {
        return 'advanced';
    }

    public function getName()
    {
        return t('Accept');
    }

    public function getDescription()
    {
        return t('Acceptance for Terms of conditions and/or GDPR');
    }

    public function getEditableOptions()
    {
        $options = [
            // disable
            'option' => false,
            'view' => false,
            'default' => false,
            'range' => false,

            // enable
            'wysiwyg' => true,
            'css' => true,
            'required' => true,

            // other
            'dependencies' => [
                // available actions for itself
                'action' => [
                    'show',
                    'class',
                    'disable',
                ],
                // available conditions for other elements
                'condition' => [
                    'empty',
                    'not_empty',
                ]
            ]
        ];
        return array_merge(parent::getElementEditableOptions(), $options);
    }

    public function field()
    {
        $form = $this->app->make(Form::class);

        $handle = $this->element->getHandle();
        $data = $this->getPostData();

        $field = '';
        $field .= '<div class="form-check custom-control custom-checkbox">';
        $field .= $form->checkbox($handle, 'accepted', in_array('accepted', (array)$data), ['data-element-handle' => $handle, 'class' => 'form-check-input custom-control-input', 'id' => $handle]);
        $field .= $form->label($handle, $this->element->getProperty('wysiwyg'), ['class' => 'form-check-label custom-control-label']);
        $field .= '</div>';

        $tags = $this->tags();

        // update classes
        $class = $handle;
        if (isset($tags['class'])) {
            $class .= ' '.$tags['class'];
        }
        $tags['class'] = $class;

        return '<div class="'.$tags['class'].'" data-element-handle="'.$handle.'">'.$field.'</div>';

    }
}