<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;

class Hidden extends Element {

    public function getName()
    {
        return t('Hidden');
    }

    public function getDescription()
    {
        return t('Hidden field');
    }

    public function getEditableOptions()
    {
        $options = [
            // disable
            'option' => false,
            'view' => false,
            'css' => false,

            'dependencies' => [
                // available actions for itself
                'action' => [
                    'value',
                ],
            ]

        ];
        return array_merge(parent::getElementEditableOptions(), $options);
    }

    public function field()
    {
        $form = $this->app->make(Form::class);

        $handle = $this->element->getHandle();
        $value = $this->getPostData();

        $input = $form->hidden($handle, $value, $this->tags());
        return $input;
    }
}