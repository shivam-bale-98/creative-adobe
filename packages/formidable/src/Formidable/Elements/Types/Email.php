<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;

class Email extends Element {

    public function getName()
    {
        return t('Email Address');
    }

    public function getDescription()
    {
        return t('Email Address field');
    }

    public function getEditableOptions()
    {
        $options = [
            // disable
            'option' => false,
            'view' => false,
            'range' => false,

            // enable
            'placeholder' => true,
            'confirm' => true,
            'help' => true,
            'css' => true,
        ];
        return array_merge(parent::getElementEditableOptions(), $options);
    }

    public function validate()
    {
        $e = parent::validate();

        // do custom validation if basic is done
        if (!$e->has()) {

            $str = $this->app->make(Strings::class);

            $data = $this->getPostData();

            if ($str->notempty($data)) {
                if (!$str->email($data)) {
                    $e->add(t('Field "%s" is an invalid Email Address', $this->element->getName()));
                }
            }
        }

        if ($e->has()) {
            return $e;
        }
        return true;
    }

    public function field()
    {
        $form = $this->app->make(Form::class);

        $handle = $this->element->getHandle();
        $value = $this->getPostData();

        $input = $form->email($handle, $value, $this->tags());
        return $input;
    }

    public function getDisplayData($data, $format = 'plain')
    {
        if ($format == 'plain') {
            return $data;
        }
        return t('<a href="mailto:%s">%s</a>', $data, $data);
    }
}