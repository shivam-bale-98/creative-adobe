<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;
use Concrete\Core\Asset\AssetList;
use Concrete\Package\Formidable\Src\Formidable\Helpers\Common as CommonHelper;
use Punic\Calendar;

class Date extends Element {

    public function getGroupHandle()
    {
        return 'advanced';
    }

    public function getName()
    {
        return t('Date');
    }

    public function getDescription()
    {
        return t('Date picker/selector/input');
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
            'view' => [
                'types' => [
                    // TODO
                    //'datepicker' => t('Datepicker (Vue.js)'),
                    'date' => t('Datepicker (HTML5)'),
                    'select' => t('Selectboxes'),
                    'input' => t('Masked input (limited formating)')
                ]
            ],
            'format' => [
                'types' => [
                    'YYYY/MM/DD' => t('YYYY/MM/DD (e.g. 1985/01/05)'),
                    'MMMM D YYYY' => t('MMMM D YYYY (e.g. January 5 1985)'),
                    'DD-MM-YYYY' => t('DD-MM-YYYY (e.g. 05-01-1985)'),
                    'D MMMM YYYY' => t('D MMMM YYYY (e.g. 5 January 1985)'),
                    'dddd D MMMM YYYY' => t('dddd D MMMM YYYY (e.g. Sunday 5 January 1985)'),
                    'custom' => t('Custom format...')
                ],
                'help' => [
                    t('D - Day of Month (1 2 .. 30 31)'),
                    t('DD - Day of Month (01 02 ... 30 31)'),
                    t('ddd - Sun Mon ... Fri Sat'),
                    t('dddd - Sunday Monday ... Friday Saturday'),
                    t('M - 1 2 ... 11 12'),
                    t('MM - 01 02 ... 11 12'),
                    t('MMM - Jan Feb ... Nov Dec'),
                    t('MMMM - January February ... November December'),
                    t('YY - 70 71 ... 29 30'),
                    t('YYYY - 1970 1971 ... 2029 2030'),
                ]
            ],

            // others
            'dependencies' => [
                // available actions for itself
                'action' => [
                    'show',
                    'disable',
                    'class',
                ],
                // available conditions for other elements
                'condition' => [
                    // if empty the element can't be used for other elements
                ]
            ]

        ];
        return array_merge(parent::getElementEditableOptions(), $options);
    }

    public function validate()
    {
        $e = $this->app->make(ErrorList::class);
        $str = $this->app->make(Strings::class);

        $format = $this->format();
        $data = $this->getPostData();

        // it true, there is an active dependecy rule.
        // if active, skip validation
        if ($this->skipByDependency($data)) {
            return $e;
        }

        $view = $this->element->getProperty('view', 'string');
        switch ($view) {

            case 'datepicker':
                // TODO
            break;

            case 'date':
                if ($this->element->isRequired()) {
                    if (!$str->notempty($data)) {
                        $e->add(t('Field "%s" is empty or invalid', $this->element->getName()));
                    }
                }
            break;

            case 'select':

                if ($this->element->isRequired()) {
                    if (preg_match('/Y/', $format)) {
                        if ((float)$data['year'] <= 0) {
                            $e->add(t('Field "%s" (year) is empty or invalid', $this->element->getName()));
                        }
                    }
                    if (preg_match('/M/', $format)) {
                        if ((float)$data['month'] <= 0) {
                            $e->add(t('Field "%s" (month) is empty or invalid', $this->element->getName()));
                        }
                    }
                    if (preg_match('/D/', $format) || preg_match('/d/', $format)) {
                        if ((float)$data['day'] <= 0) {
                            $e->add(t('Field "%s" (day) is empty or invalid', $this->element->getName()));
                        }
                    }
                }

                // check valid date!
                $now = new \DateTime();
                $year = $now->format('Y');
                if (preg_match('/Y/', $format)) {
                    $year = (float)$data['year'];
                }
                $month = $now->format('m');
                if (preg_match('/M/', $format)) {
                    $month = (float)$data['month'];
                }
                $day = $now->format('d');
                if (preg_match('/D/', $format) || preg_match('/d/', $format)) {
                    $day = (float)$data['day'];
                }
                if (!checkdate($month, $day, $year)) {
                    $e->add(t('Field "%s" is invalid', $this->element->getName()));
                }


            break;

            case 'input':

                if ($this->element->isRequired()) {
                    if (!$str->notempty($data)) {
                        $e->add(t('Field "%s" is empty or invalid', $this->element->getName()));
                    }
                }

            break;

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
        $format = $this->format();
        $data = $this->getPostData();

        $view = $this->element->getProperty('view', 'string');
        switch ($view) {

            case 'datepicker':
                // TODO
            break;

            case 'date':
                $tags = $this->tags();
                $tags['data-date'] = $data;
                $tags['data-date-format'] = $format;
                $field = str_replace('type="text"', 'type="date"', $form->text($handle, $data, $tags));
            break;

            case 'select':

                $tags = parent::tags();

                // update classes
                $class = '';
                if (isset($tags['class'])) {
                    $class .= ' '.$tags['class'];
                }
                if (!strpos($class, 'form-control')) {
                    $class .= ' form-control';
                }
                if (!strpos($class, 'form-select')) {
                    $class .= ' form-select';
                }
                $tags['class'] = $class;

                // year selector
                if (preg_match('/Y/', $format)) {
                    $attr = $tags;
                    $attr['class'] .= ' date-year';
                    $years = [];
                    foreach (range(date('Y', strtotime('-80 year')), date('Y', strtotime('+80 year'))) as $i) {
                        $years[$i] = $i;
                        if (!preg_match('/YYYY/', $format)) {
                            $years[$i] = substr($i, 2);
                        }
                    }
                    $year = $form->select($handle.'[year]', $years, isset($value['year'])?$value['year']:'', $attr);
                    $this->setField('year', $year);
                }

                // month selector
                if (preg_match('/M/', $format)) {
                    $attr = $tags;
                    $attr['class'] .= ' date-month';
                    $months = [];
                    foreach (range(1, 12) as $i) {
                        $months[$i] = $i;
                        if (preg_match('/MMMM/', $format)) {
                            $months[$i] = Calendar::getMonthName($i, 'wide');

                        }
                        elseif (preg_match('/MMM/', $format)) {
                            $months[$i] = Calendar::getMonthName($i, 'abbreviated');
                        }
                        elseif (preg_match('/MM/', $format)) {
                            $months[sprintf('%0d', $i)] = sprintf('%0d', $i);
                        }
                    }
                    $month = $form->select($handle.'[month]', $months, isset($value['month'])?$value['month']:'', $attr);
                    $this->setField('month', $month);
                }

                // day selector
                if (preg_match('/D/', $format) || preg_match('/d/', $format)) {
                    $attr = $tags;
                    $attr['class'] .= ' date-day';
                    $days = [];
                    foreach (range(1, 31) as $i) {
                        $days[$i] = $i;
                        if (preg_match('/DD/', $format)) {
                            $days[sprintf('%0d', $i)] = sprintf('%0d', $i);
                        }
                        elseif (preg_match('/dddd/', $format)) {
                            $days[$i] = Calendar::getWeekdayName($i, 'wide');
                        }
                        elseif (preg_match('/ddd/', $format)) {
                            $days[$i] = Calendar::getWeekdayName($i, 'abbreviated');
                        }
                        elseif (preg_match('/dd/', $format)) {
                            $days[$i] = Calendar::getWeekdayName($i, 'short');
                        }
                    }
                    $day = $form->select($handle.'[day]', $days, isset($value['day'])?$value['day']:'', $attr);
                    $this->setField('day', $day);
                }

                // TODO
                // Add format to data-tag
                $replace = [
                    '/dddd/' => '<div class="col day-lg" data-day-format="">'.$day.'</div>',
                    '/ddd/' => '<div class="col day-sm" data-day-format="">'.$day.'</div>',
                    '/dd/' => '<div class="col day-sm" data-day-format="">'.$day.'</div>',
                    '/DD/' => '<div class="col day-sm" data-day-format="">'.$day.'</div>',
                    '/D/' => '<div class="col day-sm" data-day-format="">'.$day.'</div>',
                    '/MMMM/' => '<div class="col month-lg" data-month-format="">'.$month.'</div>',
                    '/MMM/' => '<div class="col month-md" data-month-format="">'.$month.'</div>',
                    '/MM/' => '<div class="col month-sm" data-month-format="">'.$month.'</div>',
                    '/YYYY/' => '<div class="col year-md" data-year-format="">'.$year.'</div>',
                    '/YY/' => '<div class="col year-sm" data-year-format="">'.$year.'</div>',
                ];
                $field = '<div class="row">'.preg_replace(array_keys($replace), array_values($replace), $format).'</div>';

            break;

            case 'input':
                $tags = $this->tags();
                $field = $form->text($handle, $data, $tags);
            break;
        }

        $this->loadAsset();

        return $field;
    }

    public function getDisplayData($data, $format = 'plain')
    {
        $date = Calendar::toDateTime($data);
        if ($format == 'object') {
            return $date;
        }

        $replace = [
            '/dddd/' => 'l',
            '/ddd/' => 'D',
            '/dd/' => 'D',
            '/DD/' => 'd',
            '/D/' => 'j',
            '/MMMM/' => 'F',
            '/MMM/' => 'M',
            '/MM/' => 'm',
            '/M/' => 'n',
            '/YYYY/' => 'Y',
            '/YY/' => 'y',
        ];

        return $date->format(preg_replace(array_keys($replace), array_values($replace), $this->format()));
    }

    private function loadAsset()
    {
        $as = AssetList::getInstance();

        $handle = $this->element->getHandle();

        $view = $this->element->getProperty('view', 'string');

        switch ($view) {

            case 'datepicker':
                // TODO
            break;

            case 'date':
                $css = <<<CSS
                    div[data-formidable-type="date"] input[id="%s"] {
                        position: relative;
                        min-height: 38px;
                    }
                    div[data-formidable-type="date"] input[id="%s"]:before {
                        position: absolute;
                        content: attr(data-date);
                        display: inline-block;
                        width: 100%;
                    }
                    div[data-formidable-type="date"] input[id="%s"]::-webkit-datetime-edit, div[data-formidable-type="date"] input[id="%s"]::-webkit-inner-spin-button, div[data-formidable-type="date"] input[id="%s"]::-webkit-clear-button {
                        display: none;
                    }
                    div[data-formidable-type="date"] input[id="%s"]::-webkit-calendar-picker-indicator {
                        position: absolute;
                        opacity: 1;
                        right: 10px;
                    }
                CSS;
                $css = str_replace('%s', $handle, $css);

                $javascript = <<<JAVASCRIPT
                    $(function() {
                        $('[id="formidable_%z"] input[id="%s"]').on('change', function() {
                            if ($(this).val().length > 0) {
                                $(this).attr('data-date', moment($(this).val(), "YYYY-MM-DD").format( $(this).attr("data-date-format")));
                            }
                        }).trigger('change');
                    });
                JAVASCRIPT;
                $javascript = str_replace(['%s', '%l', '%z'], [$handle, substr(CommonHelper::getLocale(), 0, 2), $this->element->getForm()->getHandle()], $javascript);

                $as->register('css-inline', 'date-'.$handle.'-'.$view, $css, ['minify' => true, 'combine' => true], $this->getPackageHandle());
                $as->register('javascript', 'formidable/moment', 'js/plugins/moment.min.js', ['minify' => false, 'combine' => true], $this->getPackageHandle());
                $as->register('javascript-inline', 'date-'.$handle.'-'.$view, $javascript, ['minify' => true, 'combine' => true], $this->getPackageHandle());

                $this->element->registerAsset('javascript', 'formidable/moment');
                $this->element->registerAsset('javascript-inline', 'date-'.$handle.'-'.$view);
                $this->element->registerAsset('css-inline', 'date-'.$handle.'-'.$view);
            break;

            case 'input':

                $replace = [
                    '/dddd/' => '00',
                    '/ddd/' => '00',
                    '/dd/' => '00',
                    '/DD/' => '00',
                    '/D/' => '00',
                    '/MMMM/' => '00',
                    '/MMM/' => '00',
                    '/MM/' => '00',
                    '/YYYY/' => '0000',
                    '/YY/' => '00',
                ];
                $format = preg_replace(array_keys($replace), array_values($replace), $this->format());

                if ($this->element->getProperty('placeholder', 'bool')) {
                    $placeholder = $this->element->getProperty('placholder_value', 'string');
                    if ($placeholder) {
                        $replace = [
                            '/dddd/' => t('dd'),
                            '/ddd/' => t('dd'),
                            '/dd/' => t('dd'),
                            '/DD/' => t('dd'),
                            '/D/' => t('dd'),
                            '/MMMM/' => t('mm'),
                            '/MMM/' => t('mm'),
                            '/MM/' => t('mm'),
                            '/YYYY/' => t('yyyy'),
                            '/YY/' => t('yy'),
                        ];
                        $placeholder = preg_replace(array_keys($replace), array_values($replace), $this->format());
                    }
                }

                $javascript = <<<JAVASCRIPT
                    $(function() {
                        $('[id="formidable_%z"] input[id="%s"]').mask('%f', {placeholder: '%p'});
                    });
                JAVASCRIPT;
                $javascript = str_replace(['%s', '%f', '%p', '%z'], [$handle, $format, $placeholder, $this->element->getForm()->getHandle()], $javascript);

                $as->register('javascript', 'formidable/mask', 'js/plugins/mask.min.js', ['minify' => false, 'combine' => true], $this->getPackageHandle());
                $as->register('javascript-inline', 'date-'.$handle.'-'.$view, $javascript, ['minify' => true, 'combine' => true], $this->getPackageHandle());

                $this->element->registerAsset('javascript', 'formidable/mask');
                $this->element->registerAsset('javascript-inline', 'date-'.$handle.'-'.$view);

            break;
        }
    }
}