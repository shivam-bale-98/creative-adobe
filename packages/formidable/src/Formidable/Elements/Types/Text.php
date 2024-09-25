<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Elements\Element;

class Text extends Element {

    public function getName()
    {
        return t('Text');
    }

    public function getDescription()
    {
        return t('Basic single line textfield');
    }

    public function getEditableOptions()
    {
        $options = [
            // disable
            'view' => false,
            'option' => false,

            // enable
            'default' => true,
            'placeholder' => true,
            'mask' => [
                'help' => [
                    '0 - '.t('Represents a numeric character').'(0-9)',
                    '9 - '.t('Represents a optional numeric character').'(0-9)',
                    '# - '.t('Recursive item, only the previous pattern is allowed after the hashtag'),
                    'A - '.t('Represents an alphanumeric character').'(A-Z,a-z,0-9)',
                    'S - '.t('Represents an alpha character').'(A-Z,a-z)',
                    t('Examples:'),
                    t('Phone').': (999) 999-9999',
                    t('Product Code').': SA-999-a999',
                    t('More information about masking: <a href="https://igorescobar.github.io/jQuery-Mask-Plugin" target="_blank">click here</a>')
                ]
            ],
            'help' => true,
            'range' => [
                'types' => [
                    'words' => t('Words'),
                    'chars' => t('Characters')
                ],
            ],
        ];
        return array_merge(parent::getElementEditableOptions(), $options);
    }
}