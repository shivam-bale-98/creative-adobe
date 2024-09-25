<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;
use Exception;
use Laminas\Validator\Iban as IbanValidator;

class Iban extends Element {

    public function getGroupHandle()
    {
        return 'advanced';
    }

    public function getName()
    {
        return t('IBAN');
    }

    public function getDescription()
    {
        return t('International Bank Account Number');
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
                try {
                    $iban = new IbanValidator(['country_code' => strtoupper(substr($data, 0, 2))]);
                    if (!$iban->isValid($data)) {
                        throw new Exception(t('Field "%s" is invalid IBAN', $this->element->getName()));
                    }
                }
                catch (Exception $er) {
                    $e->add($er->getMessage());
                }
            }
        }

        if ($e->has()) {
            return $e;
        }
        return true;
    }
}