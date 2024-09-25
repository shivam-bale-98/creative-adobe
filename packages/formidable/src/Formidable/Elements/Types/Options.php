<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Utility\Service\Text;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Page\PageList;
use Concrete\Core\User\Group\GroupRepository;
use Concrete\Core\User\UserList;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Key\UserKey;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;

class Options extends Element {

    public function getName()
    {
        return t('Option List');
    }

    public function getDescription()
    {
        return t('List of available options');
    }

    public function getEditableOptions()
    {
        $options = [
            // disable
            'default' => true,

            // enable
            'placeholder' => [
                'help' => t('Shown as first option (e.g. "Please select!")')
            ],
            'help' => true,
            'range' => [
                'types' => [
                    'options' => t('Options'),
                ],
            ],
            'view' => [
                'types' => [
                    'dropdown' => t('Dropdown (select)'),
                    'checkbox' => t('Checkbox'),
                    'radio' => t('Radio (buttons)')
                ]
            ]
        ];
        return array_merge(parent::getElementEditableOptions(), $options);
    }

    public function tags()
    {
        $tags = parent::tags();

        switch ($this->element->getProperty('view', 'string')) {
            case 'dropdown':
            case 'select':
                if (!isset($tags['class'])) {
                    $tags['class'] = 'form-control form-select';
                }
                if (!strpos($tags['class'], 'form-control')) {
                    $tags['class'] .= ' form-control';
                }
                if (!strpos($tags['class'], 'form-select')) {
                    $tags['class'] .= ' form-select';
                }
                if ($this->element->getProperty('placeholder', 'bool')) {
                    $tags['data-placeholder'] = 'true';
                }
            break;
        }
        return $tags;
    }

    public function field()
    {
        $form = $this->app->make(Form::class);
        $text = $this->app->make(Text::class);

        $field = null;

        $options = $this->getOptions();
        if (!count($options)) {
            return '<div class="help-block mt-0">'.t('No options set or found for "%s"', $this->element->getName()).'</div>';
        }

        $data = $this->getPostData();

        if ($this->element->getProperty('option_other', 'bool')) {

            $editable = $this->getEditableOptions();

            $label = $this->element->getProperty('option_other_value', 'string');

            $other_type = $this->element->getProperty('option_other_type', 'string');
            if (!in_array($other_type, array_keys($editable['option']['other']['types']))) {
                $other_type = 'text';
            }

            $other = $form->{$other_type}($this->element->getHandle().'[other_value_text]', is_array($data)&&isset($data['other_value_text'])?$data['other_value_text']:'', ['data-option-other-value' => $this->element->getHandle(), 'placeholder' => $label, 'disabled' => true]);
            $this->setField('other', $other);

            $options['other_value'] = $label;
        }

        $view = (string)$this->element->getProperty('view', 'string');
        switch ($view) {
            case 'select':
            case 'dropdown':
                $field = $form->select($this->element->getHandle().'[]', $options, '', $this->tags());
            break;
            case 'checkbox':
            case 'radio':
                $tags = $this->tags();
                $field .= '<div data-'.$view.'-list class="'.(isset($tags['class'])?$tags['class']:'').'">';
                foreach ($options as $value => $name) {
                    $tags['id'] = $text->handle($this->element->getHandle().'_'.(!empty($value)?$value:'empty'));
                    $field .= '<div class="form-check custom-control custom-'.$view.'">';
                    $field .= $form->{$view}($this->element->getHandle().'[]', $value, in_array($value, (array)$data), ['class' => 'form-check-input custom-control-input', 'id' => $tags['id'], 'data-element-handle' => $this->element->getHandle()]);
                    $field .= $form->label($tags['id'], $name, ['class' => 'form-check-label custom-control-label']);
                    $field .= '</div>';
                }
                $field .= '</div>';
            break;
        }

        $this->setField('input', $field);

        if ($this->element->getProperty('option_other', 'bool')) {
            $field = '<div data-option-other>'.$field.'<div class="form-group option-other mt-2">'.$other.'</div></div>';
        }

        return $field;
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

        if (is_array($data)) {
            $data = array_filter($data);
        }

        if ($this->element->isRequired()) {
            if (empty($data) || !count($data)) {
                $e->add(t('Field "%s" is empty or invalid', $this->element->getName()));
            }
        }

        if ($this->element->getProperty('range', 'bool')) {
            $min = $this->element->getProperty('range_min', 'int');
            $max = $this->element->getProperty('range_max', 'int');
            $options = !empty($data)?count($data):0;
            if ($options < $min || $options > $max) {
                $e->add(t('Field "%s" should have between %s and %s options selected/checked', $this->element->getName(), $min, $max));
            }
        }

        if ($this->element->getProperty('option_other', 'bool')) {
            if (in_array('other_value', (array)$data)) {
                $data = $this->getPostData();
                if (!$str->notempty($data['other_value_text'])) {
                    $e->add(t('Field "%s" ("%s") is empty or invalid', $this->element->getName(), $this->element->getProperty('option_other_value', 'string')));
                }
            }
        }

        return $e;
    }

    // display value formater
    public function getDisplayData($data, $format = 'plain')
    {
        $options = [];

        // type of options maybe changed over the course of time
        // so do this based on the value...
        foreach ((array)$data as $d) {

            // check if it is a page
            if (isset($d['cID'])) {
                $p = Page::getByID($d['cID']);
                if ($format == 'object') {
                    if ($p) {
                        $options[] = $p;
                    }
                    continue;
                }
                if ($format == 'plain') {
                    $options[] = !empty($d['value'])?$d['value']:$d['cName'];
                    continue;
                }
                if (!$p) {
                    $options[] = t('<div>%s (%s) (removed page)<div>', !empty($d['value'])?$d['value']:$d['cName'], $d['cID']);
                    continue;
                }
                $options[] = t('<div><a href="%s" target="_blank">%s</a> (PageID: %s)<div>', $p->getCollectionLink(), !empty($d['value'])?$d['value']:$p->getCollectionName(), $p->getCollectionID());
            }

            // check if it is a member
            elseif (isset($d['uID'])) {
                $u = User::getByUserID($d['uID']);
                if ($format == 'object') {
                    if ($u) {
                        $options[] = $u;
                    }
                    continue;
                }
                if ($format == 'plain') {
                    $options[] = !empty($d['value'])?$d['value']:$d['uName'];
                    continue;
                }
                if (!$u) {
                    $options[] = t('<div>%s (%s) (removed member)<div>', !empty($d['value'])?$d['value']:$d['uName'], $d['uID']);
                    continue;
                }
                $options[] = t('<div><a href="%s" target="_blank">%s / %s</a> (UserID: %s)<div>', Url::to('dashboard/users/search/edit/', $u->getUserID()), !empty($d['value'])?$d['value']:$u->getUserName(), $u->getUserInfoObject()->getUserEmail(), $u->getUserID());
            }

            // no? then it's a manual option
            else {
                if (!isset($d['name'])) {
                    continue;
                }
                if ($format == 'plain') {
                    $options[] = $d['name'];
                    continue;
                }
                $options[] = '<div>'.$d['name'].' <span class="small text-muted">'.(isset($d['value'])?$d['value']:'').'</span></div>';
            }
        }

        if ($format == 'object') {
            return $options;
        }
        if ($format == 'plain') {
            return @implode(', ', $options);
        }
        return @implode(PHP_EOL, $options);
    }

    public function comparePostData($data, $compare, $comparison = 'equals')
    {
        $match = false;
        switch ($comparison) {
            case 'equals':
            case 'not_equals':
            case 'contains':
            case 'not_contains':
                // convert data
                if (empty($data)) {
                    return $match;
                }
                elseif (!is_array($data)) {
                    $data = [strtolower($data)];
                }
                else {
                    $data = array_map(function($v) { return strtolower($v); }, (array)$data);
                }
                // convert compare
                if (!is_array($compare)) {
                    $compare = [strtolower($compare)];
                }
                else {
                    $compare = array_map(function($v) { return is_array($v)?strtolower($v['value']):strtolower($v); }, (array)$compare);
                }
                // no break!
            case 'equals':
                $match = !count(array_diff($data, $compare));
            break;
            case 'not_equals':
                $match = count(array_diff($data, $compare));
            break;
            case 'empty':
            case 'non':
                $match = !count($compare);
            break;
            case 'not_empty':
            case 'any':
                $match = count($compare);
            break;
            case 'contains':
                $match = count(array_diff($data, $compare)) != count($compare);
            break;
            case 'not_contains':
                $match = count(array_diff($data, $compare)) == count($compare);
            break;
        }
        return $match;
    }

    // process data after successful submitted
    public function getProcessedData()
    {
        $options = [];

        $values = $this->getPostData();

        $type = (string)$this->element->getProperty('option_type', 'string');
        switch ($type) {

            // manual options
            case 'manual':
                $opts = $this->element->getProperty('option_value', 'array');
                foreach ((array)$opts as $opt) {
                    if (in_array($opt['value'], (array)$values)) {
                        $options[] = [
                            'name' => $opt['name'],
                            'value' => $opt['value']
                        ];
                    }
                }
            break;

            // page list options
            case 'pages':
                // SHOULDN'T WE SAVE THE SELECTED VALUE, INSTEAD OF THE NAME?
                $method = self::getMethodForValue((string)$this->element->getProperty('option_page_name', 'string'));
                if (empty($method[0])) {
                    $method = ['getCollectionID', ''];
                }

                foreach ((array)$values as $cID) {
                    $p = Page::getByID($cID);
                    if ($p) {
                        $options[] = [
                            'cID' => $p->getCollectionID(),
                            'cName' => $p->getCollectionName(),
                            'value' => $p->{$method[0]}($method[1])
                        ];
                    }
                }
            break;

            // mamber list options
            case 'members':
                // SHOULDN'T WE SAVE THE SELECTED VALUE, INSTEAD OF THE NAME?
                $method = self::getMethodForValue((string)$this->element->getProperty('option_member_name', 'string'));
                if (empty($method[0])) {
                    $method = ['getUserID', ''];
                }

                foreach ((array)$values as $uID) {
                    $u = User::getByUserID($uID);
                    if ($u) {
                        $options[] = [
                            'uID' => $u->getUserID(),
                            'uName' => $u->getUserName(),
                            'uEmail' => $u->getUserInfoObject()->getUserEmail(),
                            'value' => $u->getUserInfoObject()->{$method[0]}($method[1])
                        ];
                    }
                }
            break;
        }

        // other?!
        if (in_array('other_value', (array)$values)) {
            $options[] = [
                'name' => $this->element->getProperty('option_other_value', 'string').' '.$values['other_value_text'],
            ];
        }

        return $options;
    }


    public function getOptions($post = [])
    {
        $options = [];

        // add placeholder
        if (!count($post) && is_object($this->element) && $this->element->getProperty('placeholder', 'bool')) {
            $options[''] = $this->element->getProperty('placeholder_value', 'string');
        }

        $type = is_object($this->element)?(string)$this->element->getProperty('option_type', 'string'):'';
        if (isset($post['option_type'])) {
            $type = $post['option_type'];
        }

        switch ($type) {

            // manual options
            case 'manual':
                $opts = is_object($this->element)?$this->element->getProperty('option_value', 'array'):'';
                if (isset($post['option_value'])) {
                    $opts = $post['option_value'];
                }
                foreach ((array)$opts as $opt) {
                    $options[$opt['value']] = $opt['name'];
                }
            break;

            // page list options
            case 'pages':

                $list = new PageList();
                $list->disableAutomaticSorting();

                $types = is_object($this->element)?$this->element->getProperty('option_page_type', 'array'):'';
                if (isset($post['option_page_type'])) {
                    $types = (array)$post['option_page_type'];
                }
                if (is_array($types) && count($types)) {
                    $list->filterByPageTypeID($types);
                }

                $location = is_object($this->element)?$this->element->getProperty('option_page_location', 'string'):'';
                if (isset($post['option_page_location'])) {
                    $location = $post['option_page_location'];
                }
                $locationID = is_object($this->element)?$this->element->getProperty('option_page_location_value', 'int'):'';
                if (isset($post['option_page_location_value'])) {
                    $locationID = (int)$post['option_page_location_value'];
                }
                if ($location == 'beneath' && $locationID) {
                    //$list->filterByParentID($locationID);
                }

                $featured = is_object($this->element)?$this->element->getProperty('option_page_featured', 'bool'):'';
                if (isset($post['option_page_featured'])) {
                    $featured = (int)$post['option_page_featured']==1?true:false;
                }
                if ($featured) {
                    $cak = CollectionKey::getByHandle('is_featured');
                    if (is_object($cak)) {
                        $list->filterByIsFeatured(1);
                    }
                }

                $alias = is_object($this->element)?$this->element->getProperty('option_page_aliasses', 'bool'):'';
                if (isset($post['option_page_aliasses'])) {
                    $alias = (int)$post['option_page_aliasses']==1?true:false;
                }
                if ($alias) {
                    $list->includeAliases();
                }

                $cak = CollectionKey::getByHandle('exclude_from_formidable');
                if (is_object($cak)) {
                    $list->filterByAttribute('exclude_from_formidable', 1, '!=');
                }

                $order = is_object($this->element)?$this->element->getProperty('option_page_order', 'string'):'';
                if (isset($post['option_page_order'])) {
                    $order = $post['option_page_order'];
                }
                switch ($order) {
                    case 'display_asc':
                        $list->sortByDisplayOrder();
                    break;
                    case 'display_desc':
                        $list->sortByDisplayOrderDescending();
                    break;
                    case 'chrono_asc':
                        $list->sortByPublicDate();
                    break;
                    case 'modified_desc':
                        $list->sortByDateModifiedDescending();
                    break;
                    case 'random':
                        $list->sortBy('RAND()');
                    break;
                    case 'alpha_asc':
                        $list->sortByName();
                    break;
                    case 'alpha_desc':
                        $list->sortByNameDescending();
                    break;
                    default:
                        $list->sortByPublicDateDescending();
                    break;
                }

                $pages = $list->getResults();
                if (count($pages)) {

                    $name = is_object($this->element)?$this->element->getProperty('option_page_name', 'string'):'';
                    if (isset($post['option_page_name'])) {
                        $name = $post['option_page_name'];
                    }

                    $empty = is_object($this->element)?$this->element->getProperty('option_page_empty', 'bool'):'';
                    if (isset($post['option_page_empty'])) {
                        $empty = (int)$post['option_page_empty']==1?true:false;
                    }

                    $method = self::getMethodForValue((string)$name);
                    if (empty($method[0])) {
                        $method = ['getCollectionID', ''];
                    }
                    foreach ($pages as $page) {
                        $value = $page->{$method[0]}($method[1]);
                        if ($empty && empty($value)) {
                            continue;
                        }
                        $options[$page->getCollectionID()] = !empty($value)?$value:t('(empty)');
                    }
                }
            break;

            // mamber list options
            case 'members':

                $list = new UserList();

                /*
                // DISABLED FOR NOW. WE USE THE CORE SELECTOR FOR GROUPS
                if (is_object($member_group)) {
                    $groups = $member_group->getValue();
                    if (is_array($groups) && count($groups)) {
                        $list->filterByInAnyGroup($groups);
                    }
                }
                */
                $member_group = is_object($this->element)?$this->element->getProperty('option_member_group', 'int'):'';
                if (isset($post['option_member_group'])) {
                    $member_group = (int)$post['option_member_group'];
                }
                if ($member_group > 0) {
                    $g = $this->app->make(GroupRepository::class)->getGroupByID($member_group);
                    if (is_object($g)) {
                        $list->filterByGroup($g);
                    }
                }

                $active = is_object($this->element)?$this->element->getProperty('option_member_active', 'bool'):'';
                if (isset($post['option_member_active'])) {
                    $active = (int)$post['option_member_active']==1?true:false;
                }
                if ($active) {
                    $list->filterByIsActive(1);
                }

                $valid = is_object($this->element)?$this->element->getProperty('option_member_valid', 'bool'):'';
                if (isset($post['option_member_valid'])) {
                    $valid = (int)$post['option_member_valid']==1?true:false;
                }
                if ($valid) {
                    $list->filterByIsValidated(1);
                }

                $cak = UserKey::getByHandle('exclude_from_formidable');
                if (is_object($cak)) {
                    $list->filterByAttribute('exclude_from_formidable', 1, '!=');
                }

                $sort = is_object($this->element)?$this->element->getProperty('option_member_sort_order', 'string'):'';
                if (isset($post['option_member_sort_order'])) {
                    $sort = $post['option_member_sort_order'];
                }
                $order = is_object($this->element)?$this->element->getProperty('option_member_sort_by', 'string'):'';
                if (isset($post['option_member_sort_by'])) {
                    $order = $post['option_member_sort_by'];
                }
                $list->sortBy($sort, $order);

                $users = $list->getResults();
                if (count($users)) {

                    $name = is_object($this->element)?$this->element->getProperty('option_member_name', 'string'):'';
                    if (isset($post['option_member_name'])) {
                        $name = $post['option_member_name'];
                    }

                    $empty = is_object($this->element)?$this->element->getProperty('option_member_empty', 'bool'):'';
                    if (isset($post['option_member_empty'])) {
                        $empty = (int)$post['option_member_empty']==1?true:false;
                    }

                    $method = self::getMethodForValue((string)$name);
                    if (empty($method[0])) {
                        $method = ['getUserID', ''];
                    }
                    foreach($users as $user) {
                        $value = $user->{$method[0]}($method[1]);
                        if ($empty && empty($value)) {
                            continue;
                        }
                        $options[$user->getUserID()] = !empty($value)?$value:t('(empty)');
                    }
                }

            break;
        }

        return $options;
    }
}