<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;

class Number extends Element {

    public function getName()
    {
        return t('Number');
    }

    public function getDescription()
    {
        return t('Only numbers allowed');
    }

    public function getEditableOptions()
    {
        $options = [
            // disable
            'option' => false,
            'view' => false,

            // enable
            'range' => [
                'step' => true
            ],
            'placeholder' => true,
            'help' => true,
            'css' => true,

        ];
        return array_merge(parent::getElementEditableOptions(), $options);
    }

    public function validate()
    {
        $e = $this->app->make(ErrorList::class);
        $nbr = $this->app->make(Numbers::class);

        $data = $this->getPostData();

        // it true, there is an active dependecy rule.
        // if active, skip validation
        if ($this->skipByDependency($data)) {
            return $e;
        }

        if ($this->element->isRequired()) {

            $data = str_replace(',', '.', $data);

            if (trim($data) == '' || !is_numeric($data)) {
                $e->add(t('Field "%s" is an invalid number', $this->element->getName()));
            }
            else {
                if ($this->element->getProperty('range', 'bool')) {
                    $min = $this->element->getProperty('range_min', 'float');
                    $max = $this->element->getProperty('range_max', 'float');
                    if (!$nbr->number((float)$data, $min, $max)) {
                        $e->add(t('Field "%s" should be between %s and %s', $this->element->getName(), $min, $max));
                    }
                }
                elseif (!$nbr->number((float)$data)) {
                    $e->add(t('Field "%s" is an invalid number', $this->element->getName()));
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

        $tags = parent::tags();

        // update tags
        $tags['step'] = 'any';
        if ($this->element->getProperty('range', 'bool')) {
            $min = $this->element->getProperty('range_min', 'float');
            if ($min !== false) {
                $tags['min'] = $min;
            }
            $max = $this->element->getProperty('range_max', 'float');
            if ($max !== false) {
                $tags['max'] = $max;
            }
            $step = $this->element->getProperty('range_step', 'float');
            if ($step !== false) {
                $tags['step'] = $step;
            }
        }

        $handle = $this->element->getHandle();
        $value = $this->getPostData();

        $input = $form->number($handle, $value, $tags);
        return $input;
    }
}