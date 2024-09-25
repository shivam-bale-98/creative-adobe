<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;

class Name extends Element {

    public function __construct()
    {
        $this->fields = [
            'firstname' => [
                'name' => t('First Name'),
                'validate' => true,
                'format' => '<div class="col">%s</div>'
            ],
            'prefix' => [
                'name' =>t('Prefix'),
                'validate' => false,
                'format' => '<div class="col-3">%s</div>'
            ],
            'lastname' => [
                'name' =>t('Last Name'),
                'validate' => true,
                'format' => '<div class="col">%s</div>'
            ],
        ];

        parent::__construct();
    }

    public function getGroupHandle()
    {
        return 'advanced';
    }

    public function getName()
    {
        return t('Name');
    }

    public function getDescription()
    {
        return t('Name field with First Name / Last Name');
    }

    public function getEditableOptions()
    {
        $options = [
            // disable
            'default' => false,
            'option' => false,
            'view' => false,
            'range' => false,

            // enable
            'help' => true,
            'css' => true,
            'format' => [
                'types' => [
                    '{firstname} {prefix} {lastname}' => t('First Name').' '.t('Prefix').' '.t('Last Name'),
                    '{firstname} {lastname}' => t('First Name').' '.t('Last Name'),
                    '{prefix} {lastname}' => t('Prefix').' '.t('Last Name'),
                    'custom' => t('Custom format...')
                ],
                'placeholder' => t('{firstname} {lastname}'),
                'help' => [
                    '{firstname} - '.t('First Name'),
                    '{prefix} - '.t('Prefix'),
                    '{lastname} - '.t('Last Name'),
                    '{n} - '.t('Break / New line'),
                    t('You can also use specialchars like ,.!;: etc...')
                ]
            ],
            'labels_vs_placeholder' => true,

            // others
            'dependencies' => [
                // available actions for itself
                'action' => [
                    'show',
                    'disable',
                    'class',
                ]
            ]
        ];
        return array_merge(parent::getElementEditableOptions(), $options);
    }

    public function validate()
    {
        $e = $this->app->make(ErrorList::class);
        $str = $this->app->make(Strings::class);

        $data = $this->getPostData();

        // it true, there is an active dependecy rule.
        // if active, skip validation
        if ($this->skipByDependency($data)) {
            return $e;
        }

        if ($this->element->isRequired()) {
            foreach ($this->fields as $key => $field) {
                if ($field['validate'] && preg_match('/{'.$key.'}/', $this->format())) {
                    if (!$str->notempty($data[$key])) {
                        $e->add(t('Field "%s (%s)" is empty or invalid', $this->element->getName(), strtolower($field['name'])));
                    }
                }
            }
        }
        return $e;
    }

    public function field()
    {
        $form = $this->app->make(Form::class);

        $handle = $this->element->getHandle();
        $value = $this->getPostData();

        $find = ['/{n}/'];
        $replace = ['<div class="break"></div>'];

        foreach ($this->fields as $key => $field) {

            $tags = parent::tags();

            $class = $key;
            if (isset($tags['class'])) {
                $class .= ' '.$tags['class'];
            }
            if (!strpos($class, 'form-control')) {
                $class .= ' form-control';
            }
            $tags['class'] = $class;

            $element = '';
            $tags['placeholder'] = $field['name'];
            if ($this->element->getProperty('labels_vs_placeholder', 'bool')) {
                $element = $form->label($key, $field['name']);
                $tags['placeholder'] = '';
            }

            $find[] = '/{'.$key.'}/';
            $element .= $form->text($handle.'['.$key.']', isset($value[$key])?$value[$key]:'', $tags);

            // set element as object
            $this->setField($key, $element);

            // setup replace
            $replace[] = vsprintf($field['format'], [$element]);
        }

        $field  = '<div class="row">';
        $field .= preg_replace($find, $replace, $this->format());
        $field .= '</div>';

        return $field;
    }

    // process data after successful submitted
    public function getProcessedData()
    {
        $address = [];
        $values = $this->getPostData();
        foreach ($this->fields as $key => $field) {
            $address[$key] = isset($values[$key])?$values[$key]:'';
        }

        return [$address];
    }

    public function getDisplayData($data, $format = 'plain')
    {
        $data = $data[0];

        foreach (array_keys($this->fields) as $key)
        {
            $find[] = '/{'.$key.'}/';
            $replace[] = isset($data[$key])?$data[$key]:'';
        }

        if ($format == 'object') {
            return $replace;
        }
        if ($format == 'plain') {
            return @implode(' ', array_filter($replace));
        }

        // add new line
        $find[] = '/{n}/';
        $replace[] = PHP_EOL;

        return h(preg_replace($find, $replace, $this->format()));
    }
}