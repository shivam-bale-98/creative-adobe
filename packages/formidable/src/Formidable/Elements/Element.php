<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Package\Formidable\Src\Formidable\Formidable;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Key\UserKey;
use Concrete\Core\Page\Page;

class Element extends Formidable {

    protected $element;
    protected $error;

    const EDITABLE_ELEMENT_OPTIONS = [
        // enabled
        'name' => true,
		'no_label' => true,
        'css' => true,
        'required' => true,

        // disabled
	    'default' => [],
		'placeholder' => false,
	    'range' => [],
	    'help' => false,

        'mask' => false,
        'format' => false,
        'advanced' => false,

        'view' => [],

        // field specials
        'option' => [],
        'confirm' => false,
        'country' => false,
        'code' => false,
        'wysiwyg' => false,

        // files
        'folder' => false,
        'fileset' => false,
        'extensions' => false,
        'filesize' => false,

        // address
        'labels_vs_placeholder' => false,

        // searchable
        'searchable' => true,

        // dependencies
        'dependencies' => [],

        /*
        'handling' => false,
		'errors' => [
			'empty' => true,
            'confirm' => false,
            'captcha' => false,
            'email' => false,
            'iban' => false,
            'numeric' => false,
            'date' => false,
            'time' => false,
        ]
        */
    ];

    public function setElement($element)
    {
        $this->element = $element;
    }

    public function getGroupHandle()
    {
        return 'basic';
    }

    public function getHandle()
    {
        $handle = get_class($this);
        if (strpos($handle, '\\') !== false) {
            $handle = array_pop(explode('\\', get_class($this)));
        }
        return strtolower($handle);
    }

    public function getTypeObject()
    {
        return $this->element;
    }

    public function getPostData($handle = '')
    {
        $request = $this->app['request'];

        if (empty($handle)) {
            $handle = $this->element->getHandle();
        }

        $value = $default = '';
        if ($this->getProperty('default', 'bool')) {
            switch ($this->getProperty('default_type', 'string')) {
                case 'value':
                    $default = (string)$this->getProperty('default_value', 'string');
                break;
                case 'request':
                    if ($request->request()) {
                        $default = $request->request((string)$this->getProperty('default_request', 'string'));
                    }
                break;
                case 'page':
                    $c = Page::getCurrentPage();
                    if ($c) {
                        $method = self::getMethodForValue((string)$this->getProperty('default_page', 'string'));
                        if (empty($method[0])) {
                            $method = ['getCollectionID', ''];
                        }
                        $default = $c->{$method[0]}($method[1]);
                    }
                break;
                case 'user':
                    $u = new User();
                    if ($u && $u->isRegistered()) {
                        $method = self::getMethodForValue((string)$this->getProperty('default_user', 'string'));
                        if (empty($method[0])) {
                            $method = ['getUserID', ''];
                        }
                        $default = $u->getUserInfoObject()->{$method[0]}($method[1]);
                    }
                break;
            }
            if ($this->getProperty('default_type', 'string') == 'value') {
                $default = $this->getProperty('default_value', 'string');
            }
        }

        // get data from session or default;
        $value = $this->data->session($handle, $default);

        if ($request->post()) {
            // on post remove session saved data
            $this->data->remove($handle);

            // get value from post
            $value = $this->data->get($handle, $default);
        }
        return $value;
    }

    public function getFileData($handle = '')
    {
        if (empty($handle)) {
            $handle = $this->element->getHandle();
        }
        return $this->data->files($handle);
    }

    /**
     * Process after submission
     * If there is any thing to do after the submission is done.
     * For example: upload files in filemanger
     */
    public function getProcessedData()
    {
        return $this->getPostData();
    }

    public function getDisplayData($data, $format = '')
    {
        if (is_array($data)) {
            return @implode(', ', $data);
        }
        return $data;
    }

    public function comparePostData($data, $compare, $comparison = 'equals')
    {
        $match = false;
        switch ($comparison) {
            case 'equals':
            case 'not_equals':
            case 'contains':
            case 'not_contains':
                if (empty($data)) {
                    return $match;
                }
                // no break!
            case 'equals':
                if (!is_array($compare)) {
                    $match = strtolower($data) == strtolower($compare);
                    break;
                }
                $match = in_array(strtolower($data), array_map(function($v) { return strtolower($v); }, $compare));
            break;
            case 'not_equals':
                if (!is_array($compare)) {
                    $match = strtolower($data) != strtolower($compare);
                    break;
                }
                $match != in_array(strtolower($data), array_map(function($v) { return strtolower($v); }, $compare));
            break;
            case 'empty':
            case 'non':
                $match = empty($compare);
            break;
            case 'not_empty':
            case 'any':
                $match = !empty($compare);
            break;
            case 'contains':
                $match = strstr(strtolower($compare), strtolower($data));
            break;
            case 'not_contains':
                $match = !strstr(strtolower($compare), strtolower($data));
            break;
        }
        return $match;
    }

    public function tags()
    {
        $tags = [
            'data-element-handle' => $this->element->getHandle(),
        ];
        if ($this->element->getProperty('css', 'bool')) {
            $tags['class'] = (string)$this->element->getProperty('css_value', 'string');
        }
        if ($this->element->getProperty('placeholder', 'bool')) {
            $tags['placeholder'] = (string)$this->element->getProperty('placeholder_value', 'string');
        }
        if ($this->element->getProperty('mask', 'bool')) {
            $tags['data-mask'] = (string)$this->element->getProperty('mask_value', 'string');
        }
        if ($this->element->getProperty('range', 'bool')) {
            $tags['data-range-min'] = $this->element->getProperty('range_min', 'float');
            $tags['data-range-max'] = $this->element->getProperty('range_max', 'float');
            $tags['data-range-type'] = $this->element->getProperty('range_type', 'string');
        }
        if ($this->element->getProperty('option_multiple', 'bool')) {
            $tags['multiple'] = 'true';
        }

        /* TODO: Other tags? */

        return $tags;
    }

    public function label($format = '<label for="%s">%s</label>', $id = '')
    {
        $label = $this->element->getName();
        if (empty($id)) {
            $id = $this->element->getHandle();
        }
        return t($format, $id, $label);
    }

    public function field()
    {
        return $this->app->make(Form::class)->text($this->element->getHandle(), $this->getPostData(), $this->tags());
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

        // check required
        if ($this->element->isRequired()) {
            if (!$str->notempty($data)) {
                $e->add(t('Field "%s" is empty or invalid', $this->element->getName()));
            }
        }

        // check masked data
        if ($this->element->getProperty('mask', 'bool')) {
            if ($str->notempty($data)) {
                $mask = (string)$this->element->getProperty('mask_value', 'string');
                if (!empty($mask)) {
                    $regex = str_replace([0, 9, '#', 'A', 'S', '+', '(', ')'], ['\d', '*\d', '', '[A-z0-9]', '[A-z]', '', '\(', '\)'], $mask);
                    if (!preg_match('`'.$regex.'`', $data)) {
                        $e->add(t('Field "%s" doesn\'t match format', $this->element->getName()));
                    }
                }
            }
        }

        // check confirm
        if ($this->element->getProperty('confirm', 'bool')) {
            $element = $this->element->getForm()->getElementByID($this->element->getProperty('confirm_value', 'int'));
            if ($element) {
                $confirm = $this->getPostData($element->getHandle());
                if ($data != $confirm) {
                    $e->add(t('Field "%s" and "%s" should be the same', $this->element->getName(), $element->getName()));
                }
            }
        }

        // check range
        if ($this->element->getProperty('range', 'bool')) {

            $min = $this->element->getProperty('range_min', 'float');
            $max = $this->element->getProperty('range_max', 'float');

            switch($this->element->getProperty('range_type', 'string')) {
                case 'words':
                    $words = str_word_count($data);
                    if ($words < $min || $words > $max) {
                        $e->add(t('Field "%s" should be between %s and %s words', $this->element->getName(), $min, $max));
                    }
                break;
                case 'chars':
                    $chars = strlen($data);
                    if ($chars < $min || $chars > $max) {
                        $e->add(t('Field "%s" should be between %s and %s characters', $this->element->getName(), $min, $max));
                    }
                break;
                case 'options':
                    $options = count((array)$data);
                    if ($options < $min || $options > $max) {
                        $e->add(t('Field "%s" should have between %s and %s options selected/checked', $this->element->getName(), $min, $max));
                    }
                break;
            }
        }

        return $e;
    }

    public function isEditableOption($option)
    {
        $options = $this->getEditableOptions();
        if (in_array($option, (array)$options) && $options[$option] === false) {
            return false;
        }
        return $options[$option];
    }

    private function getProperty($handle)
    {
        return $this->element->getProperty($handle);
    }

    public function format()
    {
        $format = $this->element->getProperty('format', 'string');
        if ($format == 'custom') {
            $format = (string)$this->element->getProperty('format_other', 'string');
        }
        return $format;
    }

    public function requireAssets()
    {
        $as = AssetList::getInstance();

        if ($this->element->getProperty('mask', 'bool')) {
            $as->register('javascript', 'formidable/mask', 'js/plugins/mask.min.js', ['minify' => false, 'combine' => true], $this->getPackageHandle());
            $this->element->registerAsset('javascript', 'formidable/mask');
        }

        if ($this->element->getProperty('range', 'bool') && in_array($this->element->getProperty('range_type', 'string'), ['words', 'chars'])) {
            $as->register('javascript', 'formidable/simplycountable', 'js/plugins/simplycountable.min.js', ['minify' => false, 'combine' => true], $this->getPackageHandle());
            $this->element->registerAsset('javascript', 'formidable/simplycountable');
        }

        $dependencies = $this->element->getProperty('dependency', 'array');
        if (count($dependencies)) {

            $rules = [];
            foreach ($dependencies as $rv) {

                $actions = [];
                foreach ((array)$rv['action'] as $a) {
                    $actions[] = $a;
                }

                $selectors = [];

                foreach ((array)$rv['selector'] as $s) {
                    $element = $this->element->getForm()->getElementByID((int)$s['element']);
                    if (!$element) {
                        continue;
                    }

                    if (!isset($rules[$this->element->getItemID()])) {
                        $rules[$this->element->getItemID()] = [
                            'selector' => '[data-element-handle="'.$this->element->getHandle().'"]',
                            'rules' => []
                        ];
                    }

                    $conditions = [];
                    foreach ((array)$s['condition'] as $c) {
                        $conditions[] = [
                            'value' => isset($c['value'])?$c['value']:'',
                            'comparison' => isset($c['condition'])?$c['condition']:'equals',
                        ];
                    }

                    $selectors[] = [
                        'handle' => '[data-element-handle="'.$element->getHandle().'"]',
                        'conditions' => $conditions
                    ];
                }

                $rules[$this->element->getItemID()]['rules'][] = [
                    'actions' => $actions,
                    'selectors' => $selectors
                ];
            }

            if (count($rules)) {
                $this->element->registerDependency($rules);
            }
        }
    }

    public static function getElementEditableOptions()
    {
        $options = self::EDITABLE_ELEMENT_OPTIONS;

        $pageAttrs = [];
        $attrs = CollectionKey::getList();
        foreach ((array)$attrs as $ak) {
            if (in_array($ak->getAttributeTypeHandle(), ['tags', 'topics', 'select', 'file', 'image', 'textarea'])) {
                continue;
            }
            $pageAttrs['akID_'.$ak->getAttributeKeyHandle()] = tc('AttributeKeyDisplayName', $ak->getAttributeKeyDisplayName());
        }

        $pages = [
            t('Properties') => [
                'cID' => t('CollectionID'),
                'cName' => t('Page Name'),
                'cHandle' => t('Page Handle'),
                'cDateAdded' => t('Date Added')
            ],
            t('Attributes') => $pageAttrs
        ];

        $userAttrs = [];
        $attrs = UserKey::getList();
        foreach ((array)$attrs as $ak) {
            if (in_array($ak->getAttributeTypeHandle(), ['tags', 'topics', 'select', 'file', 'image', 'textarea'])) {
                continue;
            }
            $userAttrs['akID_'.$ak->getAttributeKeyHandle()] = tc('AttributeKeyDisplayName', $ak->getAttributeKeyDisplayName());
        }

        $users = [
            t('Properties') => [
                'uID' => t('UserID'),
                'uName' => t('Username'),
                'uEmail' => t('Email Address'),
                'uDateAdded' => t('Date Added')
            ],
            t('Attributes') => $userAttrs
        ];

        // Default are the same for all!
        $options['default'] = [
            'types' => [
                'value' => t('Value'),
                'request' => t('POST/GET'),
                'page' => t('Page'),
                'member' => t('Member')
            ],
            'pages' => $pages,
            'members' => $users,
            'help' => [
                t('Value - Insert predefinded value'),
                t('POST/GET - Insert value based the POST/GET (?query=...)'),
                t('Page - Insert property / attribute based on the current Page'),
                t('Member - Insert property / attribute based on the loggedin Member')
            ]
        ];

        // get all pagetypes
        $types = [];
        $pts = PageType::getList(false);
        if (count($pts)) {
            foreach ($pts as $pt) {
                $types[$pt->getPageTypeID()] = $pt->getPageTypeDisplayName();
            }
        }

        // get all groups
        $groups = [];
        $gl = new GroupList();
        $gl->sortBy('gID', 'asc');
        //$gl->includeAllGroups();
        $gps = $gl->getResults();
        foreach ((array)$gps as $g) {
            $groups[$g->getGroupID()] = tc('GroupName', $g->getGroupName());
        }

        $options['option'] = [
            'types' => [
                'manual' => t('Manual options'),
                'pages' => t('Pages'),
                'members' => t('Members')
            ],
            'pages' => [
                'types' => $types,
                'properties' => $pages,
                'help' => [
                    t('Options will be dynamically loaded, based on the parameters set.')
                ]
            ],
            'members' => [
                'groups' => $groups,
                'properties' => $users,
                'help' => [
                    t('Options will be dynamically loaded, based on the parameters set.')
                ]
            ],
            'other' => [
                'types' => [
                    'text' => t('Text'),
                    'textarea' => t('Textarea')
                ],
                'help' => [
                    t('If "none of the above" is appliable, the submitter can manually add his own.')
                ]
            ],
            'multiple' => true,
        ];


        $options['view'] = [
            'types' => [
                'default' => t('Default')
            ],
        ];

        return $options;
    }


    public function getDependencyActions()
    {
        $actions = [
            'show' => t('Show (and enable)'),
            'hide' => t('Hide (and disable)'),
            'enable' => t('Enable'),
            'disable' => t('Disable'),
            'class' => t('Add class'),
            'value' => t('Set value'),
            //'clear' => t('Clear value')
        ];

        $dependencies = (array)$this->isEditableOption('dependencies', 'array');
        if (!isset($dependencies['action']) || !count($dependencies['action'])) {
            return $actions;
        }
        $_actions = [];
        foreach(array_keys($actions) as $key) {
            if (!in_array($key, $dependencies['action'])) {
                continue;
            }
            $_actions[$key] = $actions[$key];
        }
        return $_actions;
    }

    public function getDependencyConditions()
    {
        $conditions = [
            //'enabled' => t('is enabled'),
            //'not_enabled' => t('is not enabled (disabled)'),
            'empty' => t('is empty / not checked'),
            'not_empty' => t('is not empty / is checked'),
            'equals' => t('equals'),
            'not_equals' => t('does not equal'),
            'contains' => t('contains'),
            'not_contains' => t('does not contain'),
        ];

        $dependencies = (array)$this->isEditableOption('dependencies', 'array');
        if (!isset($dependencies['condition']) || !count($dependencies['condition'])) {
            return $conditions;
        }
        $_conditions = [];
        foreach(array_keys($conditions) as $key) {
            if (!in_array($key, $dependencies['condition'])) {
                continue;
            }
            $_conditions[$key] = $conditions[$key];
        }
        return $_conditions;
    }

    public function skipByDependency($data)
    {
        $dependencies = $this->element->getProperty('dependency', 'array');
        if (!count($dependencies)) {
            return false;
        }

        // by default we accept that this element has an active dependency rule.
        // if a rule matches, then validate
        $skip = true;

        foreach ($dependencies as $i => $rv) {

            $actions = [];
            foreach ((array)$rv['action'] as $a) {
                // always validate with these
                if (in_array($a[0], ['class', 'value', 'clear'])) {
                    continue;
                }
                $actions[] = $a[0];
            }

            // no actions, just continue
            if (!count($actions)) {
                continue;
            }

            $inverse = false;
            // if (array_intersect(['hide', 'enable'], $actions)) {
            //     $inverse = true;
            // }

            // by default we ensure there is a match
            // if this condition matches, then validate
            $rule_match = true;

            // if a single selector don't match, break
            // it's an "and"-method
            foreach ((array)$rv['selector'] as $s) {

                $element = $this->element->getForm()->getElementByID((int)$s['element']);
                if (!$element) {
                    continue;
                }
                // get compare data
                $compare = $this->getPostData($element->getHandle());
                if (is_object($data)) {
                    $elementData = $data->get($element->getItemID());
                    if (is_object($elementData)) {
                        $compare = $elementData->getPostValue();
                    }
                }

                foreach ((array)$s['condition'] as $c) {

                    // match conditions
                    $match = $element->getTypeObject()->comparePostData(isset($c['value'])?$c['value']:'', $compare, isset($c['condition'])?$c['condition']:'equals');

                    // if a single condition don't match, break
                    // it's an "and"-method...
                    // check next rule
                    if (!$match) {
                        $rule_match = false;
                        break(2);
                    }
                }
            }

            // reverse match
            if ($inverse) {
                $rule_match = !$rule_match;
            }

            if ($rule_match) {
                $skip = false;
                break;
            }
        }
        return $skip;
    }

    /**
     * Magic Method to get or set fields as elements
     * This enables $element->getField('address1') to receive the "address"-field separately
     */
    public function __call($nm, $a)
    {
        if (substr($nm, 0, 8) == 'getField') {
            // make sure fields are generated!
            if (!isset($this->{'tmp_element_'.$a[0]})) {
                $this->field();
            }

            if (isset($this->{'tmp_element_'.$a[0]})) {
                return $this->{'tmp_element_'.$a[0]};
            }
        }
        if (substr($nm, 0, 8) == 'setField') {
            $this->{'tmp_element_'.$a[0]} = $a[1];
        }
        return null;
    }

    public function __toString()
    {
        return $this->getName().' - '.$this->getDescription();
    }
}