<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;

class Phone extends Element {

    public function getName()
    {
        return t('Phone');
    }

    public function getDescription()
    {
        return t('Phone Number');
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
            'format' => [
                'types' => [
                    '+00 000 00 00 00' => '+00 000 00 00 00',
                    '+00 (000) 00 00 00' => '+00 (000) 00 00 00',
                    '+00(0)000 000000' => '+00(0)000 000000',
                    '(0000) 00 00 00' => '(0000) 00 00 00',
                    '0000 00 00 00' => '0000 00 00 00',
                    '0000000000' => '0000000000',
                    'custom' => t('Custom format...'),
                    '' => t("None"),
                ],
                'placeholder' => t('+00(0)000 000000'),
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
            ]
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

                $mask = (string)$this->element->getProperty('format', 'string');
                if ($mask == 'custom') {
                    $mask = (string)$this->element->getProperty('format_other', 'string');
                }

                $regex = str_replace([0, 9, '#', 'A', 'S', '+', '(', ')'], ['\d', '*\d', '', '[A-z0-9]', '[A-z]', '', '\(', '\)'], $mask);
                if (!preg_match('`'.$regex.'`', $data)) {
                    $e->add(t('Field "%s" doesn\'t match format', $this->element->getName()));
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
        $as = AssetList::getInstance();
        $as->register('javascript', 'formidable/mask', 'js/plugins/mask.min.js', ['minify' => false, 'combine' => true], $this->getPackageHandle());
        $this->element->registerAsset('javascript', 'formidable/mask');

        $form = $this->app->make(Form::class);

        $handle = $this->element->getHandle();
        $value = $this->getPostData();

        $tags = $this->tags();

        $mask = (string)$this->element->getProperty('format', 'string');
        if($mask) {
            if ($mask == 'custom') {
                $mask = (string)$this->element->getProperty('format_other', 'string');
            }
            $tags['data-mask'] = $mask;
        }


        $input = $form->telephone($handle, $value, $tags);
        $input .= $form->hidden($handle . '-dial-code');
        $input .= $form->hidden($handle . '-number-original');
        return $input;
    }

    public function getDisplayData($data, $format = 'plain')
    {
        if ($format == 'plain') {
            return $data;
        }
        return t('<a href="tel:%s">%s</a>', preg_replace('/\D/', '', $data), $data);
    }
}