<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Geolocator\GeolocationResult;
use Concrete\Core\Localization\Service\CountryList;
use Concrete\Core\Localization\Service\StatesProvincesList;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;

class Address extends Element {

    protected $fields = [];

    public function __construct()
    {
        $this->fields = [
            'address1' => [
                'name' => t('Address 1'),
                'validate' => true,
                'format' => '<div class="col address1">%s</div>'
            ],
            'address2' => [
                'name' =>t('Address 2'),
                'validate' => false,
                'format' => '<div class="col address2">%s</div>'
            ],
            'street' => [
                'name' =>t('Street'),
                'validate' => true,
                'format' => '<div class="col street">%s</div>'
            ],
            'number' => [
                'name' =>t('Number'),
                'validate' => true,
                'format' => '<div class="col number">%s</div>'
            ],
            'number_suffix' => [
                'name' =>t('Suffix'),
                'validate' => false,
                'format' => '<div class="col number_suffix">%s</div>'
            ],
            'zipcode' => [
                'name' =>t('Zipcode'),
                'validate' => true,
                'format' => '<div class="col zipcode">%s</div>'
            ],
            'postal_code' => [
                'name' =>t('Postal Code'),
                'validate' => false,
                'format' => '<div class="col postal_code">%s</div>'
            ],
            'state' => [
                'name' =>t('State'),
                'validate' => false,
                'format' => '<div class="col state">%s</div>'
            ],
            'city' => [
                'name' =>t('City'),
                'validate' => true,
                'format' => '<div class="col city">%s</div>'
            ],
            'country' => [
                'name' =>t('Country'),
                'validate' => true,
                'format' => '<div class="col country">%s</div>'
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
        return t('Address');
    }

    public function getDescription()
    {
        return t('Address field with Address 1 and 2, Postal Code etc. ');
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
                    '{address1}{n}{address2}{n}{city}{n}{state}{n}{country}{n}{postal_code}' => t('Address 1').' '.t('(new line)').' '.t('Address 2').' '.t('(new line)').' '.t('City').' '.t('(new line)').' '.t('State').' '.t('(new line)').' '.t('Country').' '.t('(new line)').' '.t('Postal Code'),
                    '{street} {number} {number_suffix}{n}{zipcode} {city}{n}{country}' => t('Street').' '.t('Number').' '.t('Suffix').' '.t('(new line)').' '.t('Zipcode').' '.t('City').' '.t('(new line)').' '.t('Country'),
                    'custom' => t('Custom format...')
                ],
                'placeholder' => t('{address1}{n}{address2}{n}...'),
                'help' => [
                    '{address1} - '.t('Address 1'),
                    '{address2} - '.t('Address 2'),
                    '{street} - '.t('Street'),
                    '{number} - '.t('Number'),
                    '{number_suffix} - '.t('Suffix'),
                    '{zipcode} - '.t('Zipcode'),
                    '{postal_code} - '.t('Postal Code'),
                    '{city} - '.t('City'),
                    '{state} - '.t('State'),
                    '{province} - '.t('Province'),
                    '{county} - '.t('County'),
                    '{country} - '.t('Country'),
                    '{n} - '.t('New line'),
                    t('You can also use specialchars like ,.!;: etc...')
                ]
            ],
            'country' => true,
            'labels_vs_placeholder' => true,

            // other
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

        if ($this->element->isRequired()) {
            foreach ($this->fields as $key => $field) {
                if ($field['validate'] && preg_match('/{'.$key.'}/', $format)) {
                    if (!$str->notempty($data[$key])) {
                        $e->add(t('Field "%s (%s)" is empty or invalid', $this->element->getName(), strtolower($field['name'])));
                    }
                    if ($key == 'number' && preg_match('/{number_suffix}/', $this->format())) {
                        if ((float)$data[$key] == 0) {
                            $e->add(t('Field "%s (%s)" can\'t be zero', $this->element->getName(), strtolower($field['name'])));
                        }
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
        $format = $this->format();
        $value = $this->getPostData();

        if ($this->element->getProperty('country_ip', 'bool')) {
            if (!is_array($value) || !count($value)) {
                $geolocated = $this->app->make(GeolocationResult::class);
                if (is_object($geolocated)) {
                    $value = [
                        'zipcode' => $geolocated->getPostalCode(),
                        'postal_code' => $geolocated->getPostalCode(),
                        'state' => $geolocated->getStateProvinceCode(),
                        'province' => $geolocated->getStateProvinceCode(),
                        'county' => $geolocated->getStateProvinceCode(),
                        'city' => $geolocated->getCityName(),
                        'country' => $geolocated->getCountryCode(),
                    ];
                }
            }
        }

        $find = ['/{n}/'];
        $replace = ['<div class="break"></div>'];

        foreach ($this->fields as $key => $field) {

            $element = '';

            $tags = parent::tags();

            $tags['id'] = $handle.'_'.$key;

            $class = $key;
            if (isset($tags['class'])) {
                $class .= ' '.$tags['class'];
            }
            if (!strpos($class, 'form-control')) {
                $class .= ' form-control';
            }
            $tags['class'] = $class;

            $placeholder = $field['name'];
            if (isset($tags['placeholder'])) {
                $placeholder .= ' '.$tags['placeholder'];
            }

            $element = '';
            $tags['placeholder'] = $placeholder;
            if ($this->element->getProperty('labels_vs_placeholder', 'bool')) {
                $element = $form->label($key, ucfirst($placeholder));
                $tags['placeholder'] = '';
            }

            $find[] = '/{'.$key.'}/';
            switch ($key) {
                case 'address1':
                case 'address2':
                case 'street':
                case 'number_suffix':
                case 'zipcode':
                case 'postal_code':
                case 'city':
                    $element .= $form->text($handle.'['.$key.']', isset($value[$key])?$value[$key]:'', $tags);
                break;
                case 'number':
                    if (preg_match('/{number_suffix}/', $format)) {
                        $tags += ['step' => 1, 'min' => 1];
                        $element .= $form->number($handle.'['.$key.']', isset($value[$key])?$value[$key]:'', $tags);
                    }
                    else {
                        $element .= $form->text($handle.'['.$key.']', isset($value[$key])?$value[$key]:'', $tags);
                    }
                break;
                case 'state':
                case 'province':
                case 'county':
                    if (preg_match('/{country}/', $format)) {
                        $tags['class'] .= ' ccm-attribute-address-state-province';
                        $tags['data-countryfield'] = $handle.'_country';
                    }
                    $element .= $form->text($handle.'['.$key.']', isset($value[$key])?$value[$key]:'', $tags);
                break;
                case 'country':
                    if (!is_array($value) || !isset($value[$key])) {
                        if (!is_array($value)) {
                            $value = [];
                        }
                        $value[$key] = $this->element->getProperty('country_default', 'string');
                    }
                    $stateProvinceAvailable = false;
                    if (preg_match('/{state}/', $format) || preg_match('/{province}/', $format) || preg_match('/{county}/', $format)) {
                        $stateProvinceAvailable = true;
                    }

                    $allowedCountries = null;
                    if ($this->element->getProperty('country_custom', 'bool')) {
                        $allowedCountries = $this->element->getProperty('country_available', 'array');
                    }

                    $config = [
                        'noCountryText' => '',
                        'required' => $this->element->isRequired(),
                        'allowedCountries' => $allowedCountries,
                        'linkStateProvinceField' => $stateProvinceAvailable,
                        'hideUnusedStateProvinceField' => $this->element->getProperty('country_hideunused', 'bool'),
                        'clearStateProvinceOnChange' => $this->element->getProperty('country_clearonchange', 'bool'),
                    ];
                    $element .= $form->selectCountry($handle.'['.$key.']', isset($value[$key])?$value[$key]:'', $config, $tags);
                break;
            }

            // set element as object
            $this->setField($key, $element);

            // setup replace
            $replace[] = vsprintf($field['format'], [$element]);
        }

        $field  = '<div class="row">';
        $field .= preg_replace($find, $replace, $format);
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
        // because of json array
        $data = $data[0];

        foreach (array_keys($this->fields) as $key)
        {
            $find[] = '/{'.$key.'}/';

            // get country name
            if ($key == 'country' && isset($data[$key])) {
                $countryList = new CountryList();
                $data[$key] = $countryList->getCountryName($data[$key]);
            }

            // get state/province/county name
            if (in_array($key, ['state', 'province', 'county']) && isset($data[$key]) && isset($data['country'])) {
                $statesList = new StatesProvincesList();
                $state = $statesList->getStateProvinceName($data[$key], $data['country']);
                if (!empty($state)) {
                    $data[$key] = $state;
                }
            }

            $replace[] = isset($data[$key])?$data[$key]:'';
        }

        if ($format == 'object') {
            return $replace;
        }
        if ($format == 'plain') {
            return @implode(', ', array_filter($replace));
        }

        // add new line
        $find[] = '/{n}/';
        $replace[] = PHP_EOL;

        return nl2br(h(preg_replace($find, $replace, $this->format())));
    }
}