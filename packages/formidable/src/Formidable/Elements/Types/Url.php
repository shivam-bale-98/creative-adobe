<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;
use Concrete\Package\Formidable\Src\Formidable\Helpers\Common as CommonHelper;

class Url extends Element {

    public function getName()
    {
        return t('URL');
    }

    public function getDescription()
    {
        return t('Website (url)');
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
                if (!preg_match('/^((http|https):\\/\\/|)[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$data)) {
                    $e->add(t('Field "%s" is invalid URL', $this->element->getName()));
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

        $input = $form->url($handle, $value, $this->tags());
        return $input;
    }

    public function getDisplayData($data, $format = 'plain')
    {
        if ($format == 'plain') {
            return $data;
        }
        return t('<a href="%s" target="_blank">%s</a>', CommonHelper::createURL($data), $data);
    }
}