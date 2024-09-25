<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;

class Password extends Element {

    public function getName()
    {
        return t('Password');
    }

    public function getDescription()
    {
        return t('Password field');
    }

    public function getEditableOptions()
    {
        $options = [
            // disable
            'option' => false,
            'view' => false,

            // enable
            'placeholder' => true,
            'confirm' => true,
            'help' => true,
            'css' => true,
            'range' => [
                'types' => [
                    'chars' => t('Characters')
                ],
            ],
            'format' => [
                'types' => [
                    '{number}{lowercase}{uppercase}{special}' => '{number} {lowercase} {uppercase} {special} - '.t('At least one number and one lowercase, uppercase and special character'),
                    '{number}{lowercase}{uppercase}' => '{number} {lowercase} {uppercase} - '.t('At least one number and at least one lowercase and one uppercase character'),
                    '{lowercase}{uppercase}{special}' => '{lowercase} {uppercase} {special} - '.t('At least one lowercase, one uppercase and one special character'),
                    '{number}{lowercase}' => '{number} {lowercase} - '.t('At least one number and one lowercase character'),
                    '{number}' => '{number} - '.t('At least one number'),
                    '{special}' => '{special} - '.t('At least one special character'),
                    '{lowercase}{uppercase}' => '{lowercase} {uppercase} - '.t('At least one lowercase and one uppercase character'),
                    'custom' => t('Custom format...')
                ],
                'placeholder' => t('^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[*.!@$%^&(){}[:;<>,.?/~_+-=|\]]).{8,32}$'),
                'help' => [
                    '{number}  - '.t('At least one number').' - ([0-9])',
                    '{lowercase} - '.t('At least one lowercase character').' - ([a-z])',
                    '{uppercase} - '.t('At least one uppercase character').' - ([A-Z])',
                    '{special} - '.t('At least one special character').' - ([*.!@$%^&(){}[:;<>,.?/~_+-=|\]])',
                    t('or use your own custom regex')
                ]
            ],
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
                $replace = [
                    '{number}' => '([0-9]){1,}',
                    '{lowercase}' => '([a-z]){1,}',
                    '{uppercase}' => '([A-Z]){1,}',
                    '{special}' => '([*.!@$%^&(){}[:;<>,.?\/~_+-=|\]]){1,}',
                ];
                $format = $this->format();

                if ($this->element->getProperty('format', 'string') != 'custom') {

                    if (preg_match('`{number}`', $format) && !preg_match("`".$replace['{number}']."`", $data)) {
                        $e->add(t('Field "%s" should have at least one number (0-9)', $this->element->getName()));
                    }
                    if (preg_match('`{lowercase}`', $format) && !preg_match("`".$replace['{lowercase}']."`", $data)) {
                        $e->add(t('Field "%s" should have at least one lowercase character (a-z)', $this->element->getName()));
                    }
                    if (preg_match('`{uppercase}`', $format) && !preg_match("`".$replace['{uppercase}']."`", $data)) {
                        $e->add(t('Field "%s" should have at least one uppercase character (A-Z)', $this->element->getName()));
                    }
                    if (preg_match('`{special}`', $format) && !preg_match("`".$replace['{special}']."`", $data)) {
                        $e->add(t('Field "%s" should have at least one special character (*.!@$%%^&(){}[:;<>,.?\/~_+-=|\])', $this->element->getName()));
                    }
                }
                else {
                    $format = preg_replace(array_keys($replace), array_values($replace), $format);
                    if (!preg_match('`'.$format.'`', $data)) {
                        $e->add(t('Field "%s" doesn\'t match "%s"', $this->element->getName(), $format));
                    }
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

        $input = $form->password($handle, $value, $this->tags());
        return $input;
    }
}