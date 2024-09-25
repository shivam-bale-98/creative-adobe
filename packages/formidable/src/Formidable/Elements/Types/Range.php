<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;

class Range extends Element {

    public function getGroupHandle()
    {
        return 'advanced';
    }

    public function getName()
    {
        return t('Range');
    }

    public function getDescription()
    {
        return t('Range slider');
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

        // update tags=
        $class = 'form-range';
        if (isset($tags['class'])) {
            $class = $tags['class'];
        }
        if (!strpos($class, 'form-range')) {
            $class = 'form-range';
        }
        $tags['class'] = $class;
        $tags['step'] = 'any';
        $tags['min'] = 0;
        $tags['max'] = 10;
        if ($this->element->getProperty('range', 'bool')) {
            $min = $this->element->getProperty('range_min', 'float');
            if ($min !== false) {
                $tags['min'] = $min;
            }
            $max = $this->element->getProperty('range_max', 'int');
            if ($max !== false) {
                $tags['max'] = $max;
            }
            $step = $this->element->getProperty('range_step', 'float');
            if ($step !== false) {
                $tags['step'] = $step;
            }
        }

        // just to be sure..
        if (isset($tags['data-range-type'])) {
            unset($tags['data-range-type']);
        }

        $handle = $this->element->getHandle();
        $value = $this->getPostData();

        $input = str_replace('type="text"', 'type="range"', $form->text($handle, $value, $tags));
        $input = str_replace('form-control', '', $input);
        $this->setField('input', $input);

        $value = '<span data-range-value="'.$this->getHandle().'" class="badge bg-dark"></span>';
        $this->setField('value', $value);

        return '<div data-type-range class="range range-'.$this->getHandle().'">' . $input . $value . '</div>';
    }
}