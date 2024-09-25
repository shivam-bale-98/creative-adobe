<?php
namespace Concrete\Package\Formidable\Src\Formidable\Helpers;

// disable warnings, show only errors
error_reporting(E_ERROR);

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Package\PackageService;
use Concrete\Core\Utility\Service\Text;

use Concrete\Package\Formidable\Src\Formidable\Formidable;
use Concrete\Package\Formidable\Src\Formidable\Forms\Form;
use Concrete\Package\Formidable\Src\Formidable\Forms\Rows\Row;
use Concrete\Package\Formidable\Src\Formidable\Forms\Columns\Column;
use Concrete\Package\Formidable\Src\Formidable\Forms\Elements\Element;
use Concrete\Package\Formidable\Src\Formidable\Forms\Elements\ElementProperty;
use Concrete\Package\Formidable\Src\Formidable\Mails\Mail;
use Concrete\Package\Formidable\Src\Formidable\Templates\Template;
use Concrete\Package\Formidable\Src\Formidable\Helpers\Common as CommonHelper;
use Concrete\Package\Formidable\Src\Formidable\Results\Result;
use Concrete\Package\Formidable\Src\Formidable\Results\ResultElement;

class FormidableFull {

    protected $templates = [];
    protected $forms = [];
    protected $elements = [];
    protected $results = [];

    public static function exists()
    {
        $db = \Database::connection();
        return $db->getOne("SHOW TABLES LIKE 'FormidableForms'");
    }

    public function convert()
    {
        $this->convertTemplates();
        $this->convertForms();
    }

    public function remove()
    {
        $pkg = $this->getPackage();
        if ($this->isInstalled($pkg)) {

            $formidable = new Formidable();
            $pkgNew = $formidable->getPackageObject();

            /*
            $db = \Database::connection();

            // before unistalling, move pages to other package.
            $formidable = new Formidable();
            $pkgNew = $formidable->getPackageObject();

            // rename view.php
            $db->executeQuery("UPDATE Pages SET cFilename = ? WHERE cFilename = ?", ['/dashboard/formidable.php', '/dashboard/formidable/view.php']);

            $pages = [
                '/dashboard/formidable.php',
                '/dashboard/formidable/results.php',
                '/dashboard/formidable/forms.php',
                '/dashboard/formidable/templates.php'
            ];
            $and = [];
            foreach ($pages as $page) {
                $and[] = "cFilename = '".$page."'";
            }
            $db->executeQuery("UPDATE Pages SET pkgID = ? WHERE (?)", [$pkgNew->getPackageID(), implode(' OR ', $and)]);
            */

            // now uninstall
            $pkg->uninstall();

            // reinstall current formidable
            $pkgNew->upgrade();
        }

        // drop it!
        $db = \Database::connection();
        $db->executeQuery('DROP TABLE IF EXISTS FormidableForms');
        $db->executeQuery('DROP TABLE IF EXISTS FormidableFormElements');
        $db->executeQuery('DROP TABLE IF EXISTS FormidableFormMailings');
        $db->executeQuery('DROP TABLE IF EXISTS FormidableAnswerSets');
        $db->executeQuery('DROP TABLE IF EXISTS FormidableAnswers');
        $db->executeQuery('DROP TABLE IF EXISTS FormidableFormLayouts');
        $db->executeQuery('DROP TABLE IF EXISTS FormidableTemplates');
        $db->executeQuery('DROP TABLE IF EXISTS btFormidable');
        $db->executeQuery('DROP TABLE IF EXISTS FormidableAnswersSavedSearchQueries');
    }


    public function convertTemplates()
    {
        // get all templates
        $templates = $this->getTemplates();
        if (!count($templates)) {
            return true;
        }
        foreach ($templates as $old_template) {

            $new_template = new Template();
            $new_template->setName($old_template['label']);
            $new_template->setHandle(CommonHelper::generateHandle($old_template['label'], 'template'));
            $new_template->setContent(str_replace('{%formidable_mailing%}', '{%formidable_data%}', $old_template['content']));
            $new_template->setStyle('');
            $new_template->setDateAdded(new \DateTime());
            $new_template->setDateModified(new \DateTime());
            $new_template->setSite(app()->make('site')->getActiveSiteForEditing());
            $new_template->save();

            // now save to conversion table
            $this->templates[$old_template['templateID']] = $new_template;
        }
    }

    public function convertForms()
    {
        // get all forms
        $forms = $this->getForms();
        if (!count($forms)) {
            return true;
        }

        // load all variables for conversion
        $variables = $this->getVariables();

        foreach ($forms as $old_form) {

            $new_form = new Form();
            $new_form->setName($old_form['label']);
            $new_form->setHandle(CommonHelper::generateHandle($old_form['handle'], 'form'));

            $new_form->setLimit((int)$old_form['limits']);
            $new_form->setLimitValue((int)$old_form['limits_value']);
            $new_form->setLimitType($old_form['limits_redirect']);
            $new_form->setLimitMessage(Form::LIMIT_MESSAGE_CONTENT);
            $new_form->setLimitMessageContent(!empty($old_form['limits_redirect_content'])?$old_form['limits_redirect_content']:'');
            $new_form->setLimitMessagePage(0);

            $new_form->setSchedule((int)$old_form['schedule']);
            $new_form->setScheduleDateFrom(new \DateTime($old_form['schedule_start']));
            $new_form->setScheduleDateTo(new \DateTime($old_form['schedule_end']));
            $new_form->setScheduleMessage(Form::SCHEDULE_MESSAGE_CONTENT);
            $new_form->setScheduleMessageContent(!empty($old_form['schedule_redirect_content'])?$old_form['schedule_redirect_content']:'');
            $new_form->setScheduleMessagePage(0);

            $new_form->setPrivacy((int)$old_form['gdpr']);
            $new_form->setPrivacyIP((int)$old_form['gdpr_ip']);
            $new_form->setPrivacyBrowser((int)$old_form['gdpr_browser']);
            $new_form->setPrivacyLog(0);
            $new_form->setPrivacyRemove((int)$old_form['gdpr']);
            $new_form->setPrivacyRemoveValue((int)$old_form['gdpr_value']);
            $new_form->setPrivacyRemoveType($old_form['gdpr_type']);
            $new_form->setPrivacyRemoveFiles((int)$old_form['gdpr']);

            $new_form->setEnabled(1);

            $new_form->setDateAdded(new \DateTime());
            $new_form->setDateModified(new \DateTime());

            $new_form->setSite(app()->make('site')->getActiveSiteForEditing());

            $new_form->save();

            // now save to conversion table
            $this->forms[$old_form['formID']] = $new_form;

            $layouts = $this->getLayouts($old_form['formID']);
            if (!count($layouts)) {
                return true;
            }

            $row_order_nr = 1;

            foreach ($layouts as $old_row) {

                $new_row = new Row();
                $new_row->setForm($new_form);
                $new_row->setName(t('Row #%s', $row_order_nr));
                $new_row->setHandle(CommonHelper::generateHandle(t('Row #%s', $row_order_nr), 'row', $new_form));
                $new_row->setCss(0);
                $new_row->setCssValue('');
                $new_row->setOrder($row_order_nr-1);
                $new_row->setDateAdded(new \DateTime());
                $new_row->setDateModified(new \DateTime());

                $new_row->save();

                $row_order_nr++;
                $column_order_nr = 1;

                $width = round(12 / count($old_row));
                foreach($old_row as $old_column) {

                    $new_column = new Column();
                    $new_column->setRow($new_row);
                    $new_column->setName(t('Column #%s', $column_order_nr));
                    $new_column->setHandle(CommonHelper::generateHandle(t('Column #%s', $column_order_nr), 'column', $new_form));
                    $new_column->setType($old_column['appearance']=='fieldset'?Column::COLUMN_TYPE_FIELDSET:Column::COLUMN_TYPE_COLUMN);
                    $new_column->setWidth($width);
                    $new_column->setCss((int)$old_column['css']);
                    $new_column->setCssValue((int)$old_column['css']==1?$old_column['css_value']:'');
                    $new_column->setOrder($column_order_nr-1);
                    $new_column->setDateAdded(new \DateTime());
                    $new_column->setDateModified(new \DateTime());

                    $new_column->save();

                    $column_order_nr++;
                    $element_order_nr = 1;

                    $elements = $this->getElements($old_form['formID'], $old_column['layoutID']);
                    if (is_array($elements) && count($elements)) {
                        foreach($elements as $old_element) {

                            $details = $this->getElementDetails($old_element);

                            // these have no label by default!
                            if (in_array($details['type']->getHandle(), ['line', 'captcha', 'code', 'hidden', 'submit'])) {
                                $details['no_label'] = true;
                            }

                            $new_element = new Element();
                            $new_element->setColumn($new_column);
                            $new_element->setType($details['type']);
                            $new_element->setName($old_element['label']);
                            $new_element->setHandle(CommonHelper::generateHandle($old_element['label'], 'element', $new_form));
                            $new_element->setRequired($details['required']);
                            $new_element->setOrder($element_order_nr-1);
                            $new_element->setDateAdded(new \DateTime());
                            $new_element->setDateModified(new \DateTime());

                            foreach ($details['properties'] as $prop) {

                                $property = new ElementProperty();
                                $property->setHandle($prop['field']);
                                $property->setElement($new_element);
                                $property->setValue($prop['value']);

                                $new_element->addProperty($property);
                            }

                            // for options add the view
                            if ($details['type']->getHandle() == 'options') {
                                switch ($old_element['element_type']) {
                                    case 'checkbox':
                                        $view = 'checkbox';
                                    break;
                                    case 'radio':
                                        $view = 'radio';
                                    break;
                                    case 'select':
                                    case 'dropdown':
                                    default:
                                        $view = 'dropdown';
                                    break;
                                }
                                $property = new ElementProperty();
                                $property->setHandle('view');
                                $property->setElement($new_element);
                                $property->setValue($view);

                                $new_element->addProperty($property);
                            }

                            // for different layout types add the wysiwyg
                            if ($details['type']->getHandle() == 'content' && !in_array($old_element['element_type'], ['wysiwyg'])) {

                                $class = '';
                                if (isset($old_element['css']) && (int)$old_element['css_value'] && isset($old_element['css_value'])) {
                                    $class = $old_element['css_value'];
                                }

                                $label = $old_element['label_import'];
                                $name = $old_element['label'];

                                switch ($old_element['element_type']) {
                                    case 'heading':
                                        $selector = 'h1';
                                        if (isset($old_element['appearance'])) {
                                            $selector = $old_element['appearance'];
                                        }
                                        $value = '<'.$selector.' class="'.$class.'" name="'.$label.'" id="'.$label.'">'.$name.'</'.$selector.'>';
                                    break;
                                    case 'line':
                                        $value = '<br />';
                                        // set no label!
                                        $details['no_label'] = true;
                                    break;
                                    case 'paragraph':
                                        $selector = 'p';
                                        if (isset($old_element['appearance'])) {
                                            $selector = $old_element['appearance'];
                                        }
                                        $content = (string)implode(array_filter(array_map(function($p) { if ($p['field'] == 'wysiwyg') return $p['value']; }, $details['properties'])));
                                        $value = '<'.$selector.' class="'.$class.'" name="'.$label.'" id="'.$label.'">'.$content.'</'.$selector.'>';
                                    break;
                                }

                                $property = new ElementProperty();
                                $property->setHandle('wysiwyg');
                                $property->setElement($new_element);
                                $property->setValue($value);

                                $new_element->addProperty($property);
                            }

                            if ($details['type']->getHandle() == 'file') {
                                $property = new ElementProperty();
                                $property->setHandle('view');
                                $property->setElement($new_element);
                                $property->setValue('dropzone');

                                $new_element->addProperty($property);
                            }

                            if ($details['type']->getHandle() == 'date') {
                                $property = new ElementProperty();
                                $property->setHandle('view');
                                $property->setElement($new_element);
                                $property->setValue('date');

                                $new_element->addProperty($property);
                            }

                            if ($details['no_label']) {
                                $property = new ElementProperty();
                                $property->setHandle('no_label');
                                $property->setElement($new_element);
                                $property->setValue(1);
                                $new_element->addProperty($property);
                            }

                            // save dependencies, if there are any
                            if (count($details['dependencies'])) {
                                $property = new ElementProperty();
                                $property->setHandle('dependency');
                                $property->setElement($new_element);
                                $property->setValue($details['dependencies']);
                                $new_element->addProperty($property);
                            }

                            $new_element->save();

                            $element_order_nr++;

                            // now save to conversion table
                            $this->elements[$old_element['elementID']] = $new_element;

                            // add element to variables
                            $variables[] = ['old' => $old_element['label_import'], 'new' => $new_element->getHandle()];
                        }
                    }
                }
            }

            // now update dependecie rules if there are any...
            foreach ($this->elements as $element) {
                $properties = $element->getProperties();
                if (!count($properties)) {
                    continue;
                }
                $deps = false;
                foreach ($properties as $property) {
                    if ($property->getHandle() != 'dependency') {
                        continue;
                    }
                    $deps = $property;
                }
                if (!$deps){
                    continue;
                }

                $dependencies = $deps->getValue('array');
                if (!count($dependencies)) {
                    continue;
                }
                foreach ($dependencies as $rid => $r) {
                    foreach ($r['selector'] as $sid => $s) {
                        $dependencies[$rid]['selector'][$sid]['element'] = isset($this->elements[(int)$s['element']])?$this->elements[(int)$s['element']]->getItemID():0;
                    }
                }
                $deps->setValue($dependencies);
                $deps->save();
            }

            $mailings = $this->getMails($old_form['formID']);
            if (count($mailings)) {
                foreach ($mailings as $old_mail) {

                    $new_mail = new Mail();

                    $new_mail->setForm($new_form);

                    $new_mail->setName($old_mail['label']);
                    $new_mail->setHandle(CommonHelper::generateHandle($old_mail['label'], 'mail', $new_form));

                    $from_type = Mail::MAIL_FROM_CUSTOM;
                    if (is_numeric($old_mail['from_type'])) {
                        $from_type = Mail::MAIL_REPLY_ELEMENT;
                    }
                    $new_mail->setFrom($from_type);
                    $new_mail->setFromEmail(($from_type==Mail::MAIL_FROM_CUSTOM)?$old_mail['from_email']:'');
                    $new_mail->setFromName(($from_type==Mail::MAIL_FROM_CUSTOM)?$old_mail['from_name']:'');
                    $form_element = '';
                    if ($from_type == Mail::MAIL_FROM_ELEMENT) {
                        $form_element = isset($this->elements[(int)$old_mail['from_type']])?$this->elements[(int)$old_mail['from_type']]->getItemID():'';
                    }
                    $new_mail->setFromElement($form_element);

                    $reply_type = Mail::MAIL_REPLY_FROM;
                    if (is_numeric($old_mail['reply_type'])) {
                        $reply_type = Mail::MAIL_REPLY_ELEMENT;
                    }
                    else if ($old_mail['reply_type'] == 'other') {
                        $reply_type = Mail::MAIL_FROM_CUSTOM;
                    }
                    $new_mail->setReplyTo($reply_type);
                    $new_mail->setReplyToEmail(($reply_type==Mail::MAIL_REPLY_CUSTOM)?$old_mail['reply_email']:'');
                    $new_mail->setReplyToName(($reply_type==Mail::MAIL_REPLY_CUSTOM)?$old_mail['reply_name']:'');
                    $reply_element = '';
                    if ($from_type == Mail::MAIL_REPLY_ELEMENT) {
                        $reply_element = isset($this->elements[(int)$old_mail['from_type']])?$this->elements[(int)$old_mail['from_type']]->getItemID():'';
                    }
                    $new_mail->setReplyToElement($reply_element);

                    $send = [];
                    if (!empty($old_mail)) {
                        $send = array_filter(array_map(function($e) { return isset($this->elements[(int)$e])?$this->elements[(int)$e]->getItemID():''; }, explode(',', $old_mail['send'])));
                    }
                    $new_mail->setTo($send);
                    $new_mail->setToEmail((int)$old_mail['send_custom']==1?explode(',', $old_mail['send_custom_value']):[]);

                    $new_mail->setUseCC((int)$old_mail['send_cc']);

                    $new_mail->setSubject($this->convertTags($old_mail['subject'], $variables));
                    $new_mail->setTemplate(isset($this->templates[(int)$old_mail['templateID']])?$this->templates[(int)$old_mail['templateID']]->getItemID():0);
                    $new_mail->setMessage($this->convertTags($old_mail['message'], $variables));
                    $new_mail->setSkipEmpty((int)$old_mail['discard_empty']);
                    $new_mail->setSkipLayout((int)$old_mail['discard_layout']);

                    $attachments = [];
                    if (!empty($old_mail)) {
                        $attachments = array_filter(array_map(function($e) { return isset($this->elements[(int)$e])?$this->elements[(int)$e]->getItemID():''; }, explode(',', $old_mail['attachment_elements'])));
                    }
                    $new_mail->setAttachments($attachments);
                    $new_mail->setAttachmentFiles(array_filter(explode(',', $old_mail['attachment_files'])));

                    // now update dependecie rules if there are any...
                    $dependencies = [];
                    $rules = unserialize($old_mail['dependencies']);
                    if (is_array($rules)) {
                        foreach ($rules as $rule) {

                            // set actions
                            $actions = [array_map(function($a) { return $a['action']; }, $rule['actions'])];

                            // set selectors
                            $selector = [];
                            foreach ($rule['elements'] as $element) {

                                $condition = ['condition' => $element['condition']];
                                if (!empty($element['condition_value'])) {
                                    $condition['value'] = $element['condition_value'];
                                }
                                $selector[] = [
                                    'element' => (string)isset($this->elements[(int)$element['element']])?$this->elements[(int)$element['element']]->getItemID():'',
                                    'condition' => [$condition]
                                ];
                            }

                            $dependencies[] = [
                                'action' => $actions,
                                'selector' => $selector
                            ];
                        }
                    }

                    $new_mail->setDependencies($dependencies);

                    $new_mail->setDateAdded(new \DateTime());
                    $new_mail->setDateModified(new \DateTime());

                    $new_mail->save();
                }
            }


            $results = $this->getResults($old_form['formID']);
            if (count($results)) {
                foreach ($results as $old_result) {

                    $result = new Result();
                    $result->setForm($new_form);
                    $result->setUser((int)$old_result['userID']);
                    $result->setPage((int)$old_result['collectionID']!=0?(int)$old_result['collectionID']:1);
                    //$result->setBlock($b);
                    $result->setIP($old_result['ip']);
                    $result->setBrowser($old_result['browser']);
                    //$result->setDevice('');
                    $result->setOperatingSystem($old_result['platform']);
                    $result->setResolution($old_result['resolution']);
                    $result->setLocale($old_result['locale']);
                    $result->setDateAdded((new \DateTime())->setTimestamp(strtotime($old_result['submitted'])));
                    $result->setDateModified((new \DateTime())->setTimestamp(strtotime($old_result['submitted'])));

                    // get data for each element
                    foreach ($old_result['answers'] as $answer) {

                        $element = $this->elements[$answer['elementID']];
                        if (!is_object($element)) {
                            continue;
                        }

                        $postData = unserialize($answer['answer_unformated']);

                        // skip empty data
                        if (empty($postData)) {
                            continue;
                        }

                        // this is how data is saved in old Formidable.
                        // so convert to new Formidable data.
                        $postData = $postData['value'];
                        switch ($element->getTypeObject()->getHandle()) {
                            case 'options':
                                $values = [];
                                foreach ((array)$postData as $value) {
                                    $values[] = ['name' => $value, 'value' => $value];
                                }
                                $postData = $values;
                            break;
                            case 'name':
                            case 'address':
                                $postData = [$postData];
                            break;
                        }

                        // transform data for display methods
                        $displayData = $element->getDisplayData($postData, 'plain'); // just plain text for now

                        $re = new ResultElement();
                        $re->setResult($result);
                        $re->setElement($element);

                        // set data
                        $re->setPostValue($postData);
                        $re->setDisplayValue($displayData);

                        // add to result
                        $result->addElementData($element, $re);
                    }

                    // save the result
                    $result->save();
                }
            }
        }
    }

    private function getElementType($type)
    {
        $new_type = $type;

        switch ($type) {

            case 'buttons':
                $new_type = 'submit';
            break;

            case 'checkbox':
            case 'radio':
            case 'select':
                $new_type = 'options';
            break;

            case 'emailaddress':
                $new_type = 'email';
            break;

            case 'fullname':
                $new_type = 'name';
            break;

            case 'gdpr':
                $new_type = 'accept';
            break;

            case 'line':
            case 'heading':
            case 'paragraph':
            case 'wysiwyg':
                $new_type = 'content';
            break;

            case 'integer':
                $new_type = 'number';
            break;

            case 'range':
            case 'slider':
                $new_type = 'range';
            break;

            case 'rating':
            case 'signature':
            case 'time':
                $new_type = 'number';
            break;

            case 'upload':
                $new_type = 'file';
            break;

            case 'website':
                $new_type = 'url';
            break;

            case 'hr':
                $new_type = 'line';
            break;
        }

        $available = Formidable::getElementTypes();
        if (isset($available[$new_type])) {
            return $available[$new_type];
        }
    }


    private function getElementDetails($element)
    {
        $return = [
            'type' => '',
            'properties' => [],
            'confirm' => false,
            'appearance' => '',
            'required' => false,
            'no_label' => false,
            'dependencies' => []
        ];

        $return['type'] = $this->getElementType($element['element_type']);
        $return['no_label'] = (int)$element['label_hide']==1?true:false;

        $properties = [];
        $props = unserialize($element['params']);
        foreach ($props as $key => $value) {

            if (empty($value)) {
                continue;
            }

            switch ($key) {

                case 'required':
                    $return['required'] = true;
                break;

                case 'confirmation':
                    $return['confirm'] = true;
                break;

                case 'appearance':
                    $return['appearance'] = $value;
                break;

                case 'default_value':

                    // check if types are valid
                    $type = $props['default_value_type'];
                    if (empty($type) || !in_array($type, ['value', 'request', 'user_attribute', 'collection_attribute'])) {
                        break;
                    }

                    // set new type
                    $new_type = $type;
                    if ($type == 'user_attribute') {
                        $new_type = 'member';
                    }
                    if ($type == 'collection_attribute') {
                        $new_type = 'page';
                    }

                    $properties[] = [
                        'field' => 'default',
                        'value' => 1,
                    ];
                    $properties[] = [
                        'field' => 'default_type',
                        'value' => $new_type,
                    ];
                    $properties[] = [
                        'field' => 'default_'.$new_type,
                        'value' => !empty($props['default_value_'.$type])?$props['default_value_'.$type]:'value',
                    ];
                break;

                case 'placeholder':
                case 'tooltip':
                case 'advanced':
                case 'folder':
                case 'fileset':
                case 'allowed_extensions':
                case 'chars_allowed':
                case 'css':
                case 'help':
                    $properties[] = [
                        'field' => $key,
                        'value' => 1,
                    ];
                    $properties[] = [
                        'field' => $key.'_value',
                        'value' => !empty($props[$key.'_value'])?$props[$key.'_value']:'',
                    ];
                break;

                case 'range':
                    $properties[] = [
                        'field' => $key,
                        'value' => 1,
                    ];
                    $properties[] = [
                        'field' => $key.'_min',
                        'value' => (int)$props[$key.'_min'],
                    ];
                    $properties[] = [
                        'field' => $key.'_max',
                        'value' => (int)$props[$key.'_max'],
                    ];
                    $properties[] = [
                        'field' => $key.'_type',
                        'value' => !empty($props[$key.'_type'])?$props[$key.'_type']:'',
                    ];
                    $properties[] = [
                        'field' => $key.'_step',
                        'value' => $props[$key.'_step'],
                    ];
                break;

                case 'mask':
                    $properties[] = [
                        'field' => $key,
                        'value' => 1,
                    ];
                    $properties[] = [
                        'field' => $key.'_format',
                        'value' => !empty($props[$key.'_format'])?$props[$key.'_format']:'',
                    ];
                break;

                case 'format':
                    $properties[] = [
                        'field' => $key,
                        'value' => $value,
                    ];
                    if (!empty($props['format_other'])) {
                        $properties[] = [
                            'field' => 'format_other',
                            'value' => $props['format_other'],
                        ];
                    }
                break;

                case 'content':
                case 'html_value':
                    $properties[] = [
                        'field' => 'wysiwyg',
                        'value' => html_entity_decode($value),
                    ];
                break;

                case 'code_value':
                    $properties[] = [
                        'field' => 'code',
                        'value' => $value,
                    ];
                break;

                case 'options':
                case 'options_dynamic':

                    $properties[] = [
                        'field' => 'option_type',
                        'value' => $props['options_dynamic'],
                    ];

                    $options = [];

                    switch ($props['options_dynamic']) {

                        case 'manual':

                            foreach ($props['options'] as $opt) {
                                $options[] = [
                                    'name' => $opt['name'],
								    'value' => $opt['value']
                                ];
                            }
                            $properties[] = [
                                'field' => 'option_value',
                                'value' => $options,
                            ];

                        break;

                        case 'pages':

                            $opts = $props['options_dynamic_value']['pages'];

                            $properties[] = [
                                'field' => 'option_page_type',
                                'value' => $opts['page_type'],
                            ];
                            $properties[] = [
                                'field' => 'option_page_location',
                                'value' => $opts['location'],
                            ];
                            if ($opts['location'] == 'beneath') {
                                $properties[] = [
                                    'field' => 'option_page_location_value',
                                    'value' => $opts['location_page'],
                                ];
                            }
                            if ((int)$opts['alias'] == 1) {
                                $properties[] = [
                                    'field' => 'option_page_aliasses',
                                    'value' => 1,
                                ];
                            }
                            if ((int)$opts['featured'] == 1) {
                                $properties[] = [
                                    'field' => 'option_page_featured',
                                    'value' => 1,
                                ];
                            }
                            $properties[] = [
                                'field' => 'option_page_name',
                                'value' => 'cName',
                            ];
                            $properties[] = [
                                'field' => 'option_page_order',
                                'value' => $opts['order'],
                            ];

                        break;

                        case 'members':

                            $opts = $props['options_dynamic_value']['members'];

                            $properties[] = [
                                'field' => 'option_page_type',
                                'value' => $opts['group'],
                            ];
                            $properties[] = [
                                'field' => 'option_member_name',
                                'value' => $opts['name'],
                            ];
                            if ((int)$opts['skip_empty'] == 1) {
                                $properties[] = [
                                    'field' => 'option_member_empty',
                                    'value' => 1,
                                ];
                            }
                            if ((int)$opts['validated'] == 1) {
                                $properties[] = [
                                    'field' => 'option_member_valid',
                                    'value' => 1,
                                ];
                            }
                            if ((int)$opts['validated'] == 1) {
                                $properties[] = [
                                    'field' => 'option_member_valid',
                                    'value' => 1,
                                ];
                            }

                            $properties[] = [
                                'field' => 'option_sort_by',
                                'value' => $opts['order_by'],
                            ];
                            $properties[] = [
                                'field' => 'option_sort_order',
                                'value' => $opts['order_dir'],
                            ];

                        break;

                    }

                    if ((int)$props['option_other'] == 1) {
                        $properties[] = [
                            'field' => 'option_multiple',
                            'value' => 1,
                        ];
                        $properties[] = [
                            'field' => 'option_other_value',
                            'value' => $props['option_other_value'],
                        ];
                        $properties[] = [
                            'field' => 'option_other_type',
                            'value' => $props['option_other_type'],
                        ];
                    }

                    if ((int)$props['multiple'] == 1) {
                        $properties[] = [
                            'field' => 'option_multiple',
                            'value' => 1,
                        ];
                    }



                    // check if types are valid
                    $type = $props['default_value_type'];
                    if (empty($type) || !in_array('value', 'request', 'user_attribute', 'collection_attribute')) {
                        break;
                    }

                    // set new type
                    $new_type = $type;
                    if ($type == 'user_attribute') {
                        $new_type = 'member';
                    }
                    if ($type == 'collection_attribute') {
                        $new_type = 'page';
                    }

                    $properties[] = [
                        'field' => 'default',
                        'value' => 1,
                    ];
                    $properties[] = [
                        'field' => 'default_type',
                        'value' => $new_type,
                    ];
                    $properties[] = [
                        'field' => 'default_'.$new_type,
                        'value' => !empty($props['default_value_'.$type])?$props['default_value_'.$type]:'value',
                    ];
                break;

                case 'enable_custom_countries':
                case 'default_country':
                case 'use_geocode':
                    $properties[] = [
                        'field' => 'country_custom',
                        'value' => (int)$props['enable_custom_countries'],
                    ];
                    $properties[] = [
                        'field' => 'country_available',
                        'value' => $props['custom_countries'],
                    ];
                    $properties[] = [
                        'field' => 'country_default',
                        'value' => $props['default_country'],
                    ];
                    if ((int)$props['use_geocode'] == 1) {
                        $properties[] = [
                            'field' => 'country_ip',
                            'value' => 1,
                        ];
                    }
                    $properties[] = [
                        'field' => 'country_clearonchange',
                        'value' => 1,
                    ];
                    $properties[] = [
                        'field' => 'country_hideunused',
                        'value' => 1,
                    ];
                break;

                default:
                    // we have no default
                    // just skip this property
                break;

            }
        }
        $return['properties'] = $properties;

        $dependencies = [];
        $rules = unserialize($element['dependencies']);
        foreach ((array)$rules as $rule) {

            // set actions
            $actions = [array_map(function($a) { return $a['action']; }, $rule['actions'])];

            // set selectors
            $selector = [];
            foreach ($rule['elements'] as $element) {

                $condition = ['condition' => $element['condition']];
                if (!empty($element['condition_value'])) {
                    $condition['value'] = $element['condition_value'];
                }
                $selector[] = [
                    'element' => $element['element'],
                    'condition' => [$condition]
                ];
            }

            $dependencies[] = [
                'action' => $actions,
                'selector' => $selector
            ];
        }
        $return['dependencies'] = $dependencies;

        return $return;
    }


    private function getTemplates()
    {
        $templates = [];
        $db = \Database::connection();
        $results = $db->fetchAll("SELECT * FROM FormidableTemplates ORDER BY templateID ASC");
        if (is_array($results) && count($results) > 0) {
            foreach ($results as $result) {
                $templates[$result['templateID']] = $result;
            }
        }
        return $templates;
    }

    private function getForms()
    {
        $forms = [];
        $db = \Database::connection();
        $results = $db->fetchAll("SELECT * FROM FormidableForms ORDER BY formID ASC");
        if (is_array($results) && count($results) > 0) {
            foreach ($results as $result) {
                $forms[$result['formID']] = $result;
            }
        }
        return $forms;
    }

    private function getLayouts($formID)
    {
        $rows = [];
        $db = \Database::connection();
		$results = $db->fetchAll("SELECT * FROM FormidableFormLayouts WHERE formID = ? ORDER BY rowID ASC, sort ASC", [$formID]);
		if (is_array($results) && count($results)) {
            foreach ($results as $result) {
                // this is a step in Formidable Full
                // we do not have this in Formidable (yet), so skip
                if ($result['appearance'] == 'step') {
                    continue;
                }
                $rows[$result['rowID']][$result['layoutID']] = $result;
            }
        }
        return $rows;
    }

    private function getElements($formID, $layoutID)
    {
        $elements = [];

        $db = \Database::connection();
        $results = $db->fetchAll("SELECT * FROM FormidableFormElements WHERE formID = ? AND layoutID = ? ORDER BY sort ASC", [$formID, $layoutID]);
        if (is_array($results) && count($results) > 0) {
            foreach ($results as $result) {
                $elements[$result['elementID']] = $result;
            }
        }
        return $elements;
    }

    private function getMails($formID)
    {
        $mailings = [];
        $db = \Database::connection();
		$results = $db->fetchAll("SELECT * FROM FormidableFormMailings WHERE formID = ? ORDER BY mailingID ASC", [$formID]);
		if (is_array($results) && count($results)) {
            foreach ($results as $result) {
                $mailings[$result['mailingID']] = $result;
            }
        }
        return $mailings;
    }

    private function getResults($formID)
    {
        $answersets = [];
        $db = \Database::connection();
		$results = $db->fetchAll("SELECT * FROM FormidableAnswerSets WHERE formID = ? AND temp = 0 ORDER BY answerSetID ASC", [$formID]);
		if (is_array($results) && count($results)) {
            foreach ($results as $result) {
                $result['answers'] = [];
                $answers = $db->fetchAll("SELECT * FROM FormidableAnswers WHERE answerSetID = ? ORDER BY answerSetID ASC", [$result['answerSetID']]);
                if (is_array($answers) && count($answers)) {
                    $result['answers'] = $answers;
                }
                $answersets[$result['answerSetID']] = $result;
            }
        }
        return $answersets;
    }

    private function convertTags($content, $variables)
    {
        $content = app()->make(Text::class)->decodeEntities($content);

        $old = [
            '/{%all_advanced_data%}/',
            '/{%all_elements%}/'
        ];
        $new = [
            '{%form_data%}',
            '{%element_data%}'
        ];
        foreach ($variables as $variable) {
            $old[] = '/{%'.$variable['old'].'.label%}/';
            $old[] = '/{%'.$variable['old'].'.value%}/';
            $new[] = '{%element_label|'.$variable['new'].'%}';
            $new[] = '{%element_value|'.$variable['new'].'%}';
        }
        return preg_replace($old, $new, $content);
    }

    private function getVariables()
    {
		$variables = [
			['old' => 'form_name', 'new' => 'form_name'],
			['old' => 'answerset_id', 'new' => 'result_id'],
			['old' => 'ip', 'new' => 'result_ip'],
			['old' => 'browser', 'new' => 'result_browser'],
			['old' => 'platform', 'new' => 'result_platform'],
			['old' => 'resolution', 'new' => 'result_resolution'],
			['old' => 'locale', 'new' => 'result_locale'],
			['old' => 'submitted', 'new' => 'result_date_added'],

			['old' => 'collection_id', 'new' => 'page_id'],
			['old' => 'collection_url', 'new' => 'page_url'],
			['old' => 'collection_name', 'new' => 'page_name'],
			['old' => 'collection_added', 'new' => 'page_added'],
			['old' => 'collection_modified', 'new' => 'page_modified'],

            ['old' => 'user_id', 'new' => 'user_id'],
			['old' => 'user_url', 'new' => 'user_profile'],
			['old' => 'user_name', 'new' => 'user_name'],
		];

        // add collection attributes
		$attribs = \CollectionAttributeKey::getList();
		if (is_array($attribs) && count($attribs)) {
			foreach ($attribs as $at) {
				$variables[] = ['old' => 'collection_ak_'.$at->getAttributeKeyHandle(), 'new' => 'ak_page_'.$at->getAttributeKeyHandle()];
			}
		}

        // add user attributes
		$attribs = \UserAttributeKey::getList();
		if (is_array($attribs) && count($attribs)) {
			foreach ($attribs as $at) {
				$variables[] = ['old' => 'user_ak_'.$at->getAttributeKeyHandle(), 'new' => 'ak_user_'.$at->getAttributeKeyHandle()];
			}
		}

		return $variables;
	}

    private function getPackage()
    {
        $pkg = app()->make(PackageService::class)->getByHandle('formidable_full');
        return $pkg?$pkg:false;
    }

    private function isInstalled()
    {
        $db = \Database::connection();
        return $db->getOne("SELECT pkgID FROM Packages WHERE pkgHandle = ? AND pkgIsInstalled = ?", ['formidable_full', 1]);
    }
}