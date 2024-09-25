<?php
namespace Concrete\Package\Formidable\Controller\SinglePage\Dashboard\Formidable;

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Package\Formidable\Controller\SinglePage\Dashboard\Formidable;
use Concrete\Package\Formidable\Src\Formidable\Formidable as FormidableSrc;
use Concrete\Package\Formidable\Src\Formidable\Forms\Form as FormidableForm;
use Concrete\Package\Formidable\Src\Formidable\Forms\FormList;
use Concrete\Package\Formidable\Src\Formidable\Forms\Rows\Row;
use Concrete\Package\Formidable\Src\Formidable\Forms\Columns\Column;
use Concrete\Package\Formidable\Src\Formidable\Forms\Elements\Element;
use Concrete\Package\Formidable\Src\Formidable\Mails\Mail;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Support\Facade\Url;
use Concrete\Package\Formidable\Src\Formidable\Forms\Elements\ElementProperty;
use Concrete\Package\Formidable\Src\Formidable\Helpers\Common as CommonHelper;
use Concrete\Core\File\Service\Application as FileServiceHelper;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Http\Service\Json;
use Concrete\Package\Formidable\Src\Formidable\Helpers\FormidableFull;
use Symfony\Component\HttpFoundation\Session\Session;

class Forms extends Formidable
{
    protected $ff;

    public function view()
    {
        $this->loadAssets();

        $list = new FormList();
        $list->filterBySite($this->app->make('site')->getActiveSiteForEditing());
        $forms = $list->getResults();
        $this->set('forms', $forms);

        $this->set('pageTitle', t('Formidable Forms'));

        // old version active?
        if (FormidableFull::exists()) {
            $this->set('formidable_full', true);
        }
    }

    /* view and save properties of form */
    public function props($formID = 0)
    {
        $this->loadAssets($formID);

        $this->set('pageTitle', t('Add new form'));
        if ($formID > 0) {
            $this->set('pageTitle', t('Edit properties for "%s"', $this->ff->getName()));
        }

        // on submit
        if ($this->request->request->count()) {
            $this->propsSubmit();
        }
    }

    /* view layout of form */
    public function layout($formID = 0)
    {
        $this->loadAssets($formID, true);
        $this->set('pageTitle', t('Edit layout and elements for "%s"', $this->ff->getName()));
    }

    /* view mails of form */
    public function mails($formID = 0)
    {
        $this->loadAssets($formID, true);
        $this->set('mails', $this->ff->getMails());
        $this->set('pageTitle', t('Edit mails for "%s"', $this->ff->getName()));
    }


    /* view row of form */
    public function row($formID, $rowID = 0)
    {
        $this->loadAssets($formID, true);

        $row = new Row();

        // check row
        if ((int)$rowID != 0) {
            $row = Row::getByID($rowID);
            if (!is_object($row) || $row->getForm()->getItemID() != $this->ff->getItemID()) {
                $this->notification('error', t('Invalid row, not found'));
                $this->buildRedirect('/dashboard/formidable/forms/layout/'.$formID)->send();
            }
        }

        $this->set('pageTitle', t('Add new row'));
        if (is_object($row) && $row->getItemID() != 0) {
            $this->set('pageTitle', t('Edit row "%s"', $row->getName()));
        }

        $this->set('row', $row);

        // on submit
        if ($this->request->request->count()) {
            $this->rowSubmit();
        }
    }

    /* view column of form */
    public function column($formID, $rowID, $columnID = 0)
    {
        $this->loadAssets($formID, true);

        // check column
        $row = Row::getByID($rowID);
        if (!is_object($row) || $row->getForm()->getItemID() != $this->ff->getItemID()) {
            $this->notification('error', t('Invalid row, not found'));
            $this->buildRedirect('/dashboard/formidable/forms/layout/'.$formID)->send();
        }

        $column = new Column();

        // check column
        if ((int)$columnID != 0) {
            $column = Column::getByID($columnID);
            if (!is_object($column) || $column->getRow()->getForm()->getItemID() != $this->ff->getItemID()) {
                $this->notification('error', t('Invalid column, not found'));
                $this->buildRedirect('/dashboard/formidable/forms/layout/'.$formID)->send();
            }
        }

        $this->set('pageTitle', t('Add new column'));
        if (is_object($column) && $column->getItemID() != 0) {
            $this->set('pageTitle', t('Edit column "%s"', $column->getName()));
        }

        $this->set('row', $row);
        $this->set('column', $column);

        // on submit
        if ($this->request->request->count()) {
            return $this->columnSubmit();
        }
    }

    /* view element of form */
    public function element($formID, $columnID, $elementID = 0)
    {
        $this->loadAssets($formID, true);

        // check column
        $column = Column::getByID($columnID);
        if (!is_object($column) || $column->getRow()->getForm()->getItemID() != $this->ff->getItemID()) {
            $this->notification('error', t('Invalid column, not found'));
            $this->buildRedirect('/dashboard/formidable/forms/layout/'.$formID)->send();
        }

        $element = new Element();

        // check element
        if ((int)$elementID != 0) {
            $element = Element::getByID($elementID);
            if (!is_object($element) || $element->getColumn()->getRow()->getForm()->getItemID() != $this->ff->getItemID()) {
                $this->notification('error', t('Invalid column, not found'));
                $this->buildRedirect('/dashboard/formidable/forms/layout/'.$formID)->send();
            }
        }

        $this->set('pageTitle', t('Add new element'));
        if (is_object($element) && $element->getItemID() != 0) {
            $this->set('pageTitle', t('Edit element "%s"', $element->getName()));
        }

        $this->set('column', $column);
        $this->set('element', $element);

        // on submit
        if ($this->request->request->count()) {
            $this->elementSubmit();
        }
    }

    /* view mail of form */
    public function mail($formID, $mailID = 0)
    {
        $this->loadAssets($formID, true);

        $mail = new Mail();

        // check mail
        if ((int)$mailID != 0) {
            $mail = Mail::getByID($mailID);
            if (!is_object($mail) || $mail->getForm()->getItemID() != $this->ff->getItemID()) {
                $this->notification('error', t('Invalid mail, not found'));
                $this->buildRedirect('/dashboard/formidable/forms/mails/'.$formID)->send();
            }
        }

        $this->set('pageTitle', t('Add new mail'));
        if (is_object($mail) && $mail->getItemID() != 0) {
            $this->set('pageTitle', t('Edit mail "%s"', $mail->getName()));
        }

        $this->set('mail', $mail);

        // on submit
        if ($this->request->request->count()) {
            $this->mailSubmit();
        }
    }

    /* copy items */
    public function copy($type = 'form', $formID = 0)
    {
        $this->loadAssets($formID, $type!=='form'?true:false);

        switch ($type) {
            case 'form':
                return $this->formCopy();
            break;
            case 'row':
                return $this->rowCopy();
            break;
            case 'column':
                return $this->columnCopy();
            break;
            case 'element':
                return $this->elementCopy();
            break;
            case 'mail':
                return $this->mailCopy();
            break;
        }
        return $this->setResponse('error', t('Invalid request'));
    }

    /* delete items */
    public function delete($type = 'form', $formID = 0)
    {
        $this->loadAssets($formID, $type!=='form'?true:false);

        switch ($type) {
            case 'form':
                return $this->formDelete();
            break;
            case 'row':
                return $this->rowDelete();
            break;
            case 'column':
                return $this->columnDelete();
            break;
            case 'element':
                return $this->elementDelete();
            break;
            case 'mail':
                return $this->mailDelete();
            break;
        }
        return $this->setResponse('error', t('Invalid request'));
    }

    /* save element dependency in session */
    public function dependency($type = 'element', $formID, $action = 'save')
    {
        $this->loadAssets($formID, true);

        $post = $this->request->request;

        if (!$this->token->validate('dependency')) {
            return $this->setResponse('error', t('Invalid token. Please reload the page and retry.'));
        }

        $dependencies = [];
        if ($action != 'save') {
            $session = $this->app->make(Session::class);
            switch ($type) {
                case 'form':
                    // nothing?
                break;
                case 'row':
                    // nothing?
                break;
                case 'column':
                    // nothing?
                break;
                case 'element':
                    $dependencies = $session->get('savedElementDependency['.$this->ff->getItemID().']');
                break;
                case 'mail':
                    $dependencies = $session->get('savedMailDependency['.$this->ff->getItemID().']');
                break;
            }
            $dependencies = (new Json)->decode($dependencies);
            return $this->setResponse('success', ['message' => t('Dependency successfully loaded'), 'dependencies' => $dependencies]);
        }

        if (!empty($post->get('dependency'))) {
            $dependencies = $post->get('dependency');
        }
        $dependencies = (new Json)->encode($dependencies);

        $session = $this->app->make(Session::class);
        switch ($type) {
            case 'form':
                // nothing?
            break;
            case 'row':
                // nothing?
            break;
            case 'column':
                // nothing?
            break;
            case 'element':
                $session->set('savedElementDependency['.$this->ff->getItemID().']', $dependencies);
            break;
            case 'mail':
                $session->set('savedMailDependency['.$this->ff->getItemID().']', $dependencies);
            break;
        }
        return $this->setResponse('success', t('Dependency successfully saved'));
    }

    /* get element options */
    public function options()
    {
        $post = $this->request;

        if (!$this->token->validate('generate_options')) {
            return $this->setResponse('error', t('Invalid token. Please reload the page and retry.'));
        }

        $elementType = FormidableSrc::getElementTypeByHandle($post->get('type'));
        if (!$elementType) {
            return $this->setResponse('error', t('Invalid request'));
        }

        $actions = [];
        if (method_exists($elementType, 'getDependencyActions')) {
            $_actions = $elementType->getDependencyActions();
            foreach($_actions as $key => $value) {
                $actions[] = [
                    'value' => $key,
                    'name' => $value
                ];
            }
        }

        $options = [];
        if (method_exists($elementType, 'getOptions')) {
            $options = [
                [
                    'value' => '',
                    'name' => t('Select value')
                ]
            ];

            $_options = $elementType->getOptions($this->post());
            foreach($_options as $key => $value) {
                $options[] = [
                    'value' => $key,
                    'name' => $value
                ];
            }

            $other = (int)$post->get('option_other');
            if ($other == 1) {
                $options[] = [
                    'value' => 'option_other',
                    'name' => t('"Other"-option').' ('.$post->get('option_other_value').')'
                ];
            }
        }

        return $this->setResponse('success', ['actions' => $actions, 'options' => $options]);
    }


    /* import from formidable full */
    public function import()
    {
        if (!FormidableFull::exists()) {
            $this->buildRedirect('/dashboard/formidable/forms')->send();
        }

        // on submit
        if ($this->request->request->count()) {
            $this->importSubmit();
        }

    }

    /* save sorting of form */
    public function sort($formID = 0)
    {
        $this->loadAssets($formID, true);

        // on submit
        if ($this->request->request->count()) {
            return $this->sortSubmit();
        }
        return $this->setResponse('error', t('Invalid request'));
    }


    /* load common assets for each action */
    private function loadAssets($formID = 0, $force = false)
    {
        $this->requireAsset('javascript', 'formidable/dashboard/all');
        $this->requireAsset('javascript', 'formidable/dashboard/form');
        $this->requireAsset('css', 'formidable/dashboard/all');

        $this->requireAsset('ace');

        $ff = new FormidableForm();
        if ($formID > 0 || $force) {
            $ff = FormidableForm::getByID($formID);
            if (!is_object($ff)) {
                $this->notification('error', t('Invalid form, not found'));
                $this->buildRedirect('/dashboard/formidable/forms')->send();
            }
            elseif ($ff->getSite() != $this->app->make('site')->getActiveSiteForEditing()) {
                $this->notification('error', t('Invalid form, not found'));
                $this->buildRedirect('/dashboard/formidable/forms')->send();
            }
        }

        $this->ff = $ff;
        $this->set('ff', $this->ff);

        $header = $this->app->make(ElementManager::class);
        $this->set('headerMenu', $header->get('form/menu', ['action' => $this->getAction(), 'post' => $this->request->request->all(), 'ff' => $ff], 'formidable'));

        $this->showFlash();
    }


    /* copy form */
    private function formCopy()
    {
        $post = $this->request->request;

        if (!$this->token->validate('copy_form')) {
            return $this->setResponse('error', t('Invalid token. Please reload the page and retry.'));
        }

        $str = $this->app->make('helper/validation/strings');

        $form = FormidableForm::getByID($post->get('formID'));
        if (!is_object($form)) {
            return $this->setResponse('error',  t('Invalid form'));
        }

        if (!$str->notempty($post->get('formName'))) {
            $this->error->add(t('Name is empty or invalid'));
        }

        if ($this->error->has()) {
            return $this->setResponse('error', $this->error);
        }
        else {

            $new = clone $form;

            $handle = $post->get('formHandle');
            if (!$str->notempty($handle)) {
                $handle = $post->get('formName');
            }

            $new->setName($post->get('formName'));
            $new->setHandle(CommonHelper::generateHandle($handle, 'form'));

            if ((int)$post->get('copyResults') == 0) {
                $new->clearResults();
            }

            // save new form
            $new->save();

            // assign element in result to new element
            if ((int)$post->get('copyResults') != 0) {

                // get old results with new elements
                $results = $new->getResults();
                foreach ($results as $result) {
                    $data = $result->getElementData();
                    foreach ($data as $d) {
                        $element = Element::getByHandle($d->getElement()->getHandle(), $new);
                        if (!$element) {
                            $d->delete();
                            continue;
                        }
                        $d->setElement($element);
                        $d->save();
                    }
                }
            }

            $this->notification('success', t('Form successfully copied!'));

            $redirect = Url::to('/dashboard/formidable/forms');
            return $this->setResponse('location', (string)$redirect);
        }
    }

    /* delete form */
    private function formDelete()
    {
        $post = $this->request->request;

        if (!$this->token->validate('delete_form')) {
            return $this->setResponse('error', t('Invalid token. Please reload the page and retry.'));
        }

        $form = FormidableForm::getByID($post->get('formID'));
        if (!is_object($form)) {
            return $this->setResponse('error',  t('Invalid form'));
        }

        if ($this->error->has()) {
            return $this->setResponse('error', $this->error);
        }
        else {

            // delete form
            $form->delete();

            $this->notification('success', t('Form successfully deleted!'));

            $redirect = Url::to('/dashboard/formidable/forms');
            return $this->setResponse('location', (string)$redirect);
        }
    }


    private function propsSubmit()
    {
        $token = 'add_form';
        if ((int)$this->ff->getItemID() != 0) {
            $token = 'update_form';
        }
        if (!$this->token->validate($token)) {
            $this->error->add(t('Invalid token. Please reload the page and retry.'));
        }

        $th = $this->app->make('helper/text');
        $str = $this->app->make('helper/validation/strings');

        $post = $this->request->request;
        if (!$str->notempty($post->get('formName'))) {
            $this->error->add(t('Name is empty or invalid'));
        }

        if ((int)$post->get('formLimit') == FormidableForm::LIMIT_ENABLE) {

            if ((int)$post->get('formLimitValue') <= 0) {
                $this->error->add(t('Limits (value) can\'t be zero or lower'));
            }
            if (!$str->notempty($post->get('formLimitType'))) {
                $this->error->add(t('Limits (type) is empty or invalid'));
            }
            if (!$str->notempty($post->get('formLimitMessage'))) {
                $this->error->add(t('Limits (on limit reached) is empty or invalid'));
            }
            else {
                if ($post->get('formLimitMessage') == FormidableForm::LIMIT_MESSAGE_CONTENT) {
                    if (!$str->notempty($post->get('formLimitMessageContent'))) {
                        $this->error->add(t('Limits (message) is empty or invalid'));
                    }
                }
                if ($post->get('formLimitMessage') == FormidableForm::LIMIT_MESSAGE_REDIRECT) {
                    if ((int)$post->get('formLimitMessagePage') <= 0) {
                        $this->error->add(t('Limits (page) is empty or invalid'));
                    }
                }
            }
        }

        if ((int)$post->get('formSchedule') == FormidableForm::SCHEDULE_ENABLE) {

            $datetime = $this->app->make('helper/form/date_time');

            $dateFrom = $datetime->translate('formScheduleDateFrom');
            $dateTo = $datetime->translate('formScheduleDateTo');

            if (!$str->notempty($dateFrom) && !$str->notempty($dateTo)) {
                $this->error->add(t('Schedule (from) or Schedule (to) should be selected'));
            }
            else {
                if ($str->notempty($dateFrom) && $str->notempty($dateTo) && $dateFrom > $dateTo) {
                    $this->error->add(t('Schedule (from) can\'t be later than Schedule (to)'));
                }
            }

            if (!$str->notempty($post->get('formScheduleMessage'))) {
                $this->error->add(t('Schedule (when outside schedule) is empty or invalid'));
            }
            else {
                if ($post->get('formScheduleMessage') == FormidableForm::SCHEDULE_MESSAGE_CONTENT) {
                    if (!$str->notempty($post->get('formScheduleMessageContent'))) {
                        $this->error->add(t('Schedule (message) is empty or invalid'));
                    }
                }
                if ($post->get('formScheduleMessage') == FormidableForm::SCHEDULE_MESSAGE_REDIRECT) {
                    if ((int)$post->get('formScheduleMessagePage') <= 0) {
                        $this->error->add(t('Schedule (page) is empty or invalid'));
                    }
                }
            }
        }

        if ((int)$post->get('formPrivacy') == FormidableForm::PRIVACY_ENABLE) {
            if ((int)$post->get('formPrivacyRemove') == FormidableForm::PRIVACY_REMOVE_ENABLE) {
                if ((int)$post->get('formPrivacyRemoveValue') <= 0) {
                    $this->error->add(t('Privacy (value) can\'t be zero or lower'));
                }
                if (!$str->notempty($post->get('formPrivacyRemoveType'))) {
                    $this->error->add(t('Privacy (type) is empty or invalid'));
                }
            }
        }


        if (!$this->error->has()) {

            $ff = $this->ff;
            if (!is_object($ff) || (int)$ff->getItemID() == 0) {
                $ff = new FormidableForm();
                $ff->setDateAdded(new \DateTime());
            }

            // check if handle is set
            $handle = $post->get('formHandle');
            if (!$str->notempty($handle)) {
                $handle = $post->get('formName');
            }

            $ff->setName($post->get('formName'));
            $ff->setHandle(CommonHelper::generateHandle($handle, 'form', $ff));

            $ff->setLimit((int)$post->get('formLimit'));
            $ff->setLimitValue((int)$post->get('formLimitValue'));
            $ff->setLimitType($post->get('formLimitType'));
            $ff->setLimitMessage($post->get('formLimitMessage'));
            $ff->setLimitMessageContent($post->get('formLimitMessage')==FormidableForm::LIMIT_MESSAGE_CONTENT?$post->get('formLimitMessageContent'):'');
            $ff->setLimitMessagePage($post->get('formLimitMessage')==FormidableForm::LIMIT_MESSAGE_REDIRECT?(int)$post->get('formLimitMessagePage'):0);

            $dateFrom = $dateTo = null;
            if ((int)$post->get('formSchedule') == FormidableForm::SCHEDULE_ENABLE) {
                $dateFrom = $datetime->translate('formScheduleDateFrom', null, true);
                $dateTo = $datetime->translate('formScheduleDateTo', null, true);
            }

            $ff->setSchedule((int)$post->get('formSchedule'));
            $ff->setScheduleDateFrom($dateFrom);
            $ff->setScheduleDateTo($dateTo);
            $ff->setScheduleMessage($post->get('formScheduleMessage'));
            $ff->setScheduleMessageContent($post->get('formScheduleMessage')==FormidableForm::SCHEDULE_MESSAGE_CONTENT?$post->get('formScheduleMessageContent'):'');
            $ff->setScheduleMessagePage($post->get('formScheduleMessage')==FormidableForm::SCHEDULE_MESSAGE_REDIRECT?(int)$post->get('formScheduleMessagePage'):0);

            $ff->setPrivacy((int)$post->get('formPrivacy'));
            $ff->setPrivacyIP((int)$post->get('formPrivacyIP'));
            $ff->setPrivacyBrowser((int)$post->get('formPrivacyBrowser'));
            $ff->setPrivacyLog((int)$post->get('formPrivacyLog'));
            $ff->setPrivacyRemove((int)$post->get('formPrivacyRemove'));
            $ff->setPrivacyRemoveValue((int)$post->get('formPrivacyRemoveValue'));
            $ff->setPrivacyRemoveType($post->get('formPrivacyRemoveType'));
            $ff->setPrivacyRemoveFiles((int)$post->get('formPrivacyRemoveFiles'));

            $ff->setEnabled((int)$post->get('formEnabled'));

            $ff->setDateModified(new \DateTime());

            $ff->setSite($this->app->make('site')->getActiveSiteForEditing());

            $ff->save();

            $this->notification('success', $token=='add_form'?t('Form successfully added!'):t('Form successfully updated!'));

            if ((int)$this->ff->getItemID() != 0) {
                $this->buildRedirect('/dashboard/formidable/forms/')->send();
                exit();
            }

            // add first row
            $row = new Row();
            $row->setDateAdded(new \DateTime());
            $row->setForm($ff);
            $row->setOrder($ff->getNextRowOrder());
            $row->setName(t('Row #%s', 1));
            $row->setHandle('row_1');
            $row->setType( Row::ROW_TYPE_ROW);
            $row->setDateModified(new \DateTime());
            $row->save();

            // add first column
            $column = new Column();
            $column->setDateAdded(new \DateTime());
            $column->setRow($row);
            $column->setOrder($row->getNextColumnOrder());
            $column->setName(t('Column #%s', 1));
            $column->setHandle('column_1');
            $column->setWidth(12);
            $column->setDateModified(new \DateTime());
            $column->save();

            $this->buildRedirect('/dashboard/formidable/forms/layout/'.$this->ff->getItemID())->send();
        }
    }



    /********* ROWS ************/

    /* submit row */
    private function rowSubmit()
    {
        $row = new Row();
        $row->setDateAdded(new \DateTime());
        $row->setForm($this->ff);
        $row->setOrder($this->ff->getNextRowOrder());

        $post = $this->request->request;
        if ((int)$post->get('rowID') != 0) {
            $row = Row::getByID($post->get('rowID'));
            if (!is_object($row) || $row->getForm()->getItemID() != $this->ff->getItemID()) {
                $this->notification('error', t('Invalid row, not found'));
                $this->buildRedirect('/dashboard/formidable/forms/layout/'.$this->ff->getItemID())->send();
            }
        }

        $token = 'add_row';
        if ((int)$row->getItemID() != 0) {
            $token = 'update_row';
        }
        if (!$this->token->validate($token)) {
            $this->error->add(t('Invalid token. Please reload the page and retry.'));
        }

        $th = $this->app->make('helper/text');
        $str = $this->app->make('helper/validation/strings');

        if (!$str->notempty($post->get('rowName'))) {
            $this->error->add(t('Name is empty or invalid'));
        }

        /*
        // disabled for now
        if (!$str->notempty($post->get('rowType'))) {
            $this->error->add(t('Type is empty or invalid'));
        }
        else {
            if ($post->get('rowType') == Row::ROW_TYPE_STEP) {
                if ($post->get('rowType') == 1) {

                    if (!$str->notempty($post->get('rowButtonPreviousName'))) {
                        $this->error->add(t('Button name (previous) is empty or invalid'));
                    }
                    if ((int)$post->get('rowButtonPreviousCss') == 1) {
                        if (!$str->notempty($post->get('rowButtonPreviousCssValue'))) {
                            $this->error->add(t('CSS value (previous) is empty or invalid'));
                        }
                    }

                    if (!$str->notempty($post->get('rowButtonNextName'))) {
                        $this->error->add(t('Button name (next) is empty or invalid'));
                    }
                    if ((int)$post->get('rowButtonNextCss') == 1) {
                        if (!$str->notempty($post->get('rowButtonNextCssValue'))) {
                            $this->error->add(t('CSS value (next) is empty or invalid'));
                        }
                    }
                }
            }
        }
        */

        if ((int)$post->get('rowCss') == 1) {
            if (!$str->notempty($post->get('rowCssValue'))) {
                $this->error->add(t('Additional CSS (value) is empty or invalid'));
            }
        }

        if (!$this->error->has()) {

            // check if handle is set
            $handle = $post->get('rowHandle');
            if (!$str->notempty($handle)) {
                $handle = $post->get('rowName');
            }

            $row->setName($post->get('rowName'));
            $row->setHandle(CommonHelper::generateHandle($handle, 'row', $this->ff, $row));

            $row->setType($post->get('rowType'));

            if ($post->get('rowType') == Row::ROW_TYPE_STEP && $post->get('rowButton') == 1) {

                // check if handle is set
                $handle = $post->get('rowButtonPreviousHandle');
                if (!$str->notempty($handle)) {
                    $handle = $post->get('rowButtonPreviousName');
                }
                $row->setButtonPreviousName($post->get('rowButtonPreviousName'));
                $row->setButtonPreviousHandle($th->handle($handle));
                $row->setButtonPreviousCss((int)$post->get('rowButtonPreviousCss'));
                $row->setButtonPreviousCssValue((int)$post->get('rowButtonPreviousCss')?$post->get('rowButtonPreviousCssValue'):'');

                // check if handle is set
                $handle = $post->get('rowButtonNextHandle');
                if (!$str->notempty($handle)) {
                    $handle = $post->get('rowButtonNextName');
                }
                $row->setButtonNextName($post->get('rowButtonNextName'));
                $row->setButtonNextHandle($th->handle($handle));
                $row->setButtonNextCss((int)$post->get('rowButtonNextCss'));
                $row->setButtonNextCssValue((int)$post->get('rowButtonNextCss')?$post->get('rowButtonNextCssValue'):'');
            }

            $row->setCss((int)$post->get('rowCss'));
            $row->setCssValue((int)$post->get('rowCss')?$post->get('rowCssValue'):'');

            $row->setDateModified(new \DateTime());

            $row->save();

            $this->notification('success', t('Row successfully saved!'));
            $this->buildRedirect('/dashboard/formidable/forms/layout/'.$this->ff->getItemID())->send();
        }
    }

    /* copy row */
    private function rowCopy()
    {
        $post = $this->request->request;

        if (!$this->token->validate('copy_row')) {
            return $this->setResponse('error', t('Invalid token. Please reload the page and retry.'));
        }

        $th = $this->app->make('helper/text');
        $str = $this->app->make('helper/validation/strings');

        $row = Row::getByID($post->get('rowID'));
        if (!is_object($row) || $row->getForm()->getItemID() != $this->ff->getItemID()) {
            return $this->setResponse('error',  t('Invalid row'));
        }

        if (!$str->notempty($post->get('rowName'))) {
            $this->error->add(t('Name is empty or invalid'));
        }

        if ($this->error->has()) {
            return $this->setResponse('error', $this->error);
        }
        else {

            $new = clone $row;

            $handle = $post->get('rowHandle');
            if (!$str->notempty($handle)) {
                $handle = $post->get('rowName');
            }

            $new->setName($post->get('rowName'));
            $new->setHandle(CommonHelper::generateHandle($handle, 'row', $this->ff));
            $new->setOrder($this->ff->getNextRowOrder());

            if ((int)$post->get('copyColumns') == 0) {
                $new->clearColumns();
            }
            else {

                $th = Application::getFacadeApplication()->make('helper/text');

                if ((int)$post->get('copyElements') == 0) {
                    foreach ($new->getColumns() as $col) {
                        $col->clearElements();
                    }
                }
                else {
                    // update name / handle for these
                    foreach ($new->getColumns() as $col) {
                        $col->setName(t('%s (copy)', $col->getName()));
                        $col->setHandle($th->handle($col->getName()));
                        // do for elements also!
                        $elements = $col->getElements();
                        foreach ($elements as $element) {
                            $element->setName(t('%s (copy)', $element->getName()));
                            $element->setHandle($th->handle($element->getName()));
                        }
                    }
                }
            }

            // save new row
            $new->save();

            $this->notification('success', t('Row successfully copied!'));

            $redirect = Url::to('/dashboard/formidable/forms/layout', $this->ff->getItemID());
            return $this->setResponse('location', (string)$redirect);
        }
    }

    /* delete row */
    private function rowDelete()
    {
        $post = $this->request->request;

        if (!$this->token->validate('delete_row')) {
            return $this->setResponse('error', t('Invalid token. Please reload the page and retry.'));
        }

        $row = Row::getByID($post->get('rowID'));
        if (!is_object($row) || $row->getForm()->getItemID() != $this->ff->getItemID()) {
            return $this->setResponse('error',  t('Invalid row'));
        }
        else {
            if ($row->getTotalColumns() && (int)$post->get('forceDelete') == 0) {
                $this->error->add(t('The row isn\'t empty. Check "remove all" to force removal'));
            }
        }

        if ($this->error->has()) {
            return $this->setResponse('error', $this->error);
        }
        else {
            // delete row
            $row->delete();

            $this->notification('success', t('Row successfully deleted!'));

            $redirect = Url::to('/dashboard/formidable/forms/layout', $this->ff->getItemID());
            return $this->setResponse('location', (string)$redirect);
        }
    }


    /********* COLUMNS ************/

    /* submit column */
    private function columnSubmit()
    {
        $post = $this->request->request;

        // check row
        if ((int)$post->get('rowID') != 0) {
            $row = Row::getByID($post->get('rowID'));
            if (!is_object($row) || $row->getForm()->getItemID() != $this->ff->getItemID()) {
                $this->notification('error', t('Invalid row, not found'));
                $this->buildRedirect('/dashboard/formidable/forms/layout/'.$this->ff->getItemID())->send();
            }
        }

        $column = new Column();
        $column->setDateAdded(new \DateTime());
        $column->setRow($row);
        $column->setOrder($row->getNextColumnOrder());

        // check column
        if ((int)$post->get('columnID') != 0) {
            $column = Column::getByID($post->get('columnID'));
            if (!is_object($column) || $column->getRow()->getForm()->getItemID() != $this->ff->getItemID()) {
                $this->notification('error', t('Invalid column, not found'));
                $this->buildRedirect('/dashboard/formidable/forms/layout/'.$this->ff->getItemID())->send();
            }
        }

        $token = 'add_column';
        if ((int)$column->getItemID() != 0) {
            $token = 'update_column';
        }
        if (!$this->token->validate($token)) {
            $this->error->add(t('Invalid token. Please reload the page and retry.'));
        }

        $th = $this->app->make('helper/text');
        $str = $this->app->make('helper/validation/strings');

        if (!$str->notempty($post->get('columnName'))) {
            $this->error->add(t('Name is empty or invalid'));
        }

        if (!$str->notempty($post->get('columnType'))) {
            $this->error->add(t('Type is empty or invalid'));
        }

        if (!$str->notempty($post->get('columnWidth'))) {
            $this->error->add(t('Width is empty or invalid'));
        }

        if ((int)$post->get('columnCss') == 1) {
            if (!$str->notempty($post->get('columnCssValue'))) {
                $this->error->add(t('Additional  CSS (value) is empty or invalid'));
            }
        }

        if (!$this->error->has()) {

            // check if handle is set
            $handle = $post->get('columnHandle');
            if (!$str->notempty($handle)) {
                $handle = $post->get('columnName');
            }

            $column->setName($post->get('columnName'));
            $column->setHandle(CommonHelper::generateHandle($handle, 'column', $this->ff, $column));

            $column->setType($post->get('columnType'));
            $column->setWidth($post->get('columnWidth'));

            $column->setCss((int)$post->get('columnCss'));
            $column->setCssValue((int)$post->get('columnCss')?$post->get('columnCssValue'):'');

            $column->setDateModified(new \DateTime());

            $column->save();

            $this->notification('success', t('Row successfully saved!'));
            $this->buildRedirect('/dashboard/formidable/forms/layout/'.$this->ff->getItemID())->send();
        }
    }

    /* copy column */
    private function columnCopy()
    {
        $post = $this->request->request;

        if (!$this->token->validate('copy_column')) {
            return $this->setResponse('error', t('Invalid token. Please reload the page and retry.'));
        }

        $th = $this->app->make('helper/text');
        $str = $this->app->make('helper/validation/strings');

        $column = Column::getByID($post->get('columnID'));
        if (!is_object($column) || $column->getRow()->getForm()->getItemID() != $this->ff->getItemID()) {
            return $this->setResponse('error',  t('Invalid column'));
        }

        if (!$str->notempty($post->get('columnName'))) {
            $this->error->add(t('Name is empty or invalid'));
        }

        if ($this->error->has()) {
            return $this->setResponse('error', $this->error);
        }
        else {

            $new = clone $column;

            $handle = $post->get('columnHandle');
            if (!$str->notempty($handle)) {
                $handle = $post->get('columnName');
            }

            $new->setName($post->get('columnName'));
            $new->setHandle(CommonHelper::generateHandle($handle, 'column', $this->ff));
            $new->setOrder($new->getRow()->getNextColumnOrder());

            if ((int)$post->get('copyElements') == 0) {
                $new->clearElements();
            }
            else {
                $th = Application::getFacadeApplication()->make('helper/text');

                // update name / handle for these
                $elements = $new->getElements();
                foreach ($elements as $element) {
                    $element->setName(t('%s (copy)', $element->getName()));
                    $element->setHandle($th->handle($element->getName()));
                }
            }

            // save new column
            $new->save();

            $this->notification('success', t('Column successfully copied!'));

            $redirect = Url::to('/dashboard/formidable/forms/layout', $this->ff->getItemID());
            return $this->setResponse('location', (string)$redirect);
        }
    }

    /* delete column */
    private function columnDelete()
    {
        $post = $this->request->request;

        if (!$this->token->validate('delete_column')) {
            return $this->setResponse('error', t('Invalid token. Please reload the page and retry.'));
        }

        $column = Column::getByID($post->get('columnID'));
        if ($column->getRow()->getForm()->getItemID() != $this->ff->getItemID()) {
            $this->error->add(t('Invalid column'));
        }
        else {
            if ($column->getTotalElements() && (int)$post->get('forceDelete') == 0) {
                $this->error->add(t('The column isn\'t empty. Check "remove all" to force removal'));
            }
        }

        if ($this->error->has()) {
            return $this->setResponse('error', $this->error);
        }
        else {
            // delete column
            $column->delete();

            $this->notification('success', t('Column successfully deleted!'));

            $redirect = Url::to('/dashboard/formidable/forms/layout', $this->ff->getItemID());
            return $this->setResponse('location', (string)$redirect);
        }
    }


    /********* ELEMENTS ************/

    /* submit element */
    private function elementSubmit()
    {
        $post = $this->request->request;

        // check row
        $column = Column::getByID((int)$post->get('columnID'));
        if (!is_object($column) || $column->getRow()->getForm()->getItemID() != $this->ff->getItemID()) {
            $this->notification('error', t('Invalid column, not found'));
            $this->buildRedirect('/dashboard/formidable/forms/layout/'.$this->ff->getItemID())->send();
        }

        $element = new Element();
        $element->setDateAdded(new \DateTime());
        $element->setColumn($column);
        $element->setOrder($column->getNextElementOrder());

        // check column
        if ((int)$post->get('elementID') != 0) {
            $element = Element::getByID($post->get('elementID'));
            if (!is_object($element) || $element->getColumn()->getRow()->getForm()->getItemID() != $this->ff->getItemID()) {
                $this->notification('error', t('Invalid element, not found'));
                $this->buildRedirect('/dashboard/formidable/forms/layout/'.$this->ff->getItemID())->send();
            }
        }

        $token = 'add_element';
        if ((int)$element->getItemID() != 0) {
            $token = 'update_element';
        }
        if (!$this->token->validate($token)) {
            $this->error->add(t('Invalid token. Please reload the page and retry.'));
        }

        $th = $this->app->make('helper/text');
        $str = $this->app->make('helper/validation/strings');

        if (!$str->notempty($post->get('elementType'))) {
            $this->error->add(t('Type is empty or invalid'));
        }
        else {
            $type = FormidableSrc::getElementTypeByHandle($post->get('elementType'));
            if (!is_object($type)) {
                $this->error->add(t('Type is empty or invalid'));
            }
        }

        if (!$str->notempty($post->get('elementName'))) {
            $this->error->add(t('Name is empty or invalid'));
        }

        $options = $type->getEditableOptions();
        foreach ($options as $handle => $option) {
            if ($option === false) {
                continue;
            }
            switch ($handle) {

                case 'default':
                    if ((int)$post->get('default') == 1) {
                        if (!$str->notempty($post->get('default_type'))) {
                            $this->error->add(t('Default (type) is empty or invalid'));
                        }
                        else {
                            if ($post->get('default_type') == 'value') {
                                if (!$str->notempty($post->get('default_value'))) {
                                    $this->error->add(t('Default (value) is empty or invalid'));
                                }
                            }
                            if ($post->get('default_type') == 'request') {
                                if (!$str->notempty($post->get('default_request'))) {
                                    $this->error->add(t('Default (request) is empty or invalid'));
                                }
                            }
                            if ($post->get('default_type') == 'page') {
                                if (!$str->notempty($post->get('default_page'))) {
                                    $this->error->add(t('Default (page) is empty or invalid'));
                                }
                            }
                            if ($post->get('default_type') == 'member') {
                                if (!$str->notempty($post->get('default_member'))) {
                                    $this->error->add(t('Default (member) is empty or invalid'));
                                }
                            }
                        }
                    }
                break;

                case 'range':
                    if ((int)$post->get('range') == 1) {
                        if ((int)$post->get('range_min') == 0 && (int)$post->get('range_max') == 0) {
                            $this->error->add(t('Range / Limit (minimal) and (maximal) are empty or invalid'));
                        }
                        elseif ((int)$post->get('range_min') >= (int)$post->get('range_max')) {
                            $this->error->add(t('Range / Limit (minimal) can\'t be larger then Range / Limit (maximal)'));
                        }
                        if (isset($option['range']['types'])) {
                            if (!$str->notempty($post->get('range_type'))) {
                                $this->error->add(t('Range / Limit (type) is empty or invalid'));
                            }
                        }
                        if (isset($option['range']['step'])) {
                            if (!$str->notempty($post->get('range_step'))) {
                                $this->error->add(t('Range / Limit (step) is empty or invalid'));
                            }
                        }
                    }
                break;

                case 'mask':
                    if ((int)$post->get('mask') == 1) {
                        if (!$str->notempty($post->get('mask_value'))) {
                            $this->error->add(t('Masking (format) is empty or invalid'));
                        }
                    }
                break;

                case 'advanced':
                    if ((int)$post->get('advanced') == 1) {
                        if (!$str->notempty($post->get('advanced_value'))) {
                            $this->error->add(t('Advanced (format) is empty or invalid'));
                        }
                    }
                break;

                case 'confirm':
                    if ((int)$post->get('confirm') == 1) {
                        if (!$str->notempty($post->get('confirm_value'))) {
                            $this->error->add(t('Confirm (field) is empty or invalid'));
                        }
                    }
                break;

                case 'option':
                    if (isset($option['types']) && $option['types'] !== false) {
                        if (!$str->notempty($post->get('option_type'))) {
                            $this->error->add(t('Option (types) is empty or invalid'));
                        }
                        else {
                            if ($post->get('option_type') == 'manual') {
                                if (!count($post->get('option_value'))) {
                                    $this->error->add(t('Option (values) are empty'));
                                }
                                else {
                                    $i = 1;
                                    foreach ($post->get('option_value') as $k => $option) {
                                        if (!isset($option['value']) || empty($option['value']) || !isset($option['name']) || empty($option['name'])) {
                                            $this->error->add(t('Option (row #%s) is empty or invalid', $i));
                                        }
                                        $i++;
                                    }
                                }
                            }
                            if ($post->get('option_type') == 'pages') {
                                if (!$str->notempty($post->get('option_page_location'))) {
                                    $this->error->add(t('Pages (location) is empty or invalid'));
                                }
                                else {
                                    if ($post->get('option_page_location') == 'beneath') {
                                        if ((int)$post->get('option_page_location_value') == 0) {
                                            $this->error->add(t('Pages (page) is empty or invalid'));
                                        }
                                    }
                                }
                                if (!$str->notempty($post->get('option_page_name'))) {
                                    $this->error->add(t('Pages (option name) is empty or invalid'));
                                }
                                if (!$str->notempty($post->get('option_page_order'))) {
                                    $this->error->add(t('Pages (order) is empty or invalid'));
                                }
                            }
                            if ($post->get('option_type') == 'members') {
                                /*
                                // DISABLED FOR NOW. WE USE THE CORE SELECTOR FOR GROUPS
                                if (!count($post->get('option_member_group'))) {
                                    $this->error->add(t('Members (groups) are empty'));
                                }
                                */
                                if (!$str->notempty($post->get('option_member_group'))) {
                                    $this->error->add(t('Members (groups) are empty'));
                                }
                                if (!$str->notempty($post->get('option_member_name'))) {
                                    $this->error->add(t('Members (option name) is empty or invalid'));
                                }
                                if (!$str->notempty($post->get('option_member_sort_by'))) {
                                    $this->error->add(t('Members (sorting) is empty or invalid'));
                                }
                                if (!$str->notempty($post->get('option_member_sort_order'))) {
                                    $this->error->add(t('Members (order) is empty or invalid'));
                                }
                            }
                        }
                    }

                    if (isset($option['other']) && $option['other'] !== false) {
                        if ((int)$post->get('option_other') == 1) {
                            if (!$str->notempty($post->get('option_other_type'))) {
                                $this->error->add(t('"Other"-option (type) is empty or invalid'));
                            }
                            if (!$str->notempty($post->get('option_other_value'))) {
                                $this->error->add(t('"Other"-option (value) is empty or invalid'));
                            }
                        }
                    }
                break;

                case 'placeholder':
                    if ((int)$post->get('placeholder') == 1) {
                        if (!$str->notempty($post->get('placeholder_value'))) {
                            $this->error->add(t('Placeholder (value) is empty or invalid'));
                        }
                    }
                break;

                case 'help':
                    if ((int)$post->get('help') == 1) {
                        if (!$str->notempty($post->get('help_value'))) {
                            $this->error->add(t('Help (text) is empty or invalid'));
                        }
                    }
                break;

                case 'view':
                    if (!$str->notempty($post->get('view'))) {
                        $this->error->add(t('Template (view) is empty or invalid'));
                    }
                break;

                case 'css':
                    if ((int)$post->get('css') == 1) {
                        if (!$str->notempty($post->get('css_value'))) {
                            $this->error->add(t('Additional  CSS (value) is empty or invalid'));
                        }
                    }
                break;

                case 'extensions':
                    if ((int)$post->get('extensions') == 1) {
                        if (!$str->notempty($post->get('extensions_value')) || !$str->min($post->get('extensions_value'), 2)) {
                            $this->error->add(t('Force extenstion(s) is empty or invalid'));
                        }
                        else {
                            $fsh = new FileServiceHelper();
                            $extensions = explode(",", strtolower(str_replace(array("*","."," "), "", $post->get('extensions_value'))));
                            $difference = array_diff($extensions, $fsh->getAllowedFileExtensions());
                            if (!empty($difference)) {
                                $this->error->add(t('Extensions "%s" in "%s" aren\'t allowed globally (check Allowed File Types)', @implode(', ', $difference), t('Force extenstion(s)')));
                            }
                        }
                    }
                break;

                case 'filesize':
                    if ((int)$post->get('filesize') == 1) {
                        $max_filesize = (int)ini_get('upload_max_filesize');
                        if ((float)$post->get('filesize_value') < 0) {
                            $this->error->add(t('Filesize (value) is empty or invalid'));
                        }
                        elseif ($max_filesize > 0 && $post->get('filesize_value') > $max_filesize) {
                            $this->error->add(t('Filesize (value) is larger then the server allows (%s MB)', $max_filesize));
                        }
                    }
                break;

                case 'folder':
                    if ((int)$post->get('folder') == 1) {
                        if (!$str->notempty($post->get('folder_value'))) {
                            $this->error->add(t('Folder (value) is empty or invalid'));
                        }
                    }
                break;

                case 'fileset':
                    if ((int)$post->get('fileset') == 1) {
                        if (!count((array)$post->get('fileset_value'))) {
                            $this->error->add(t('Filesets (value) is empty or invalid'));
                        }
                    }
                break;

                case 'dependencies':
                    if (count((array)$post->get('dependency'))) {
                        foreach ($post->get('dependency') as $v) {
                            $actions = [];
                            foreach ((array)$v['action'] as $a) {
                                $actions[] = array_shift($a);
                            }
                            if (in_array('show', $actions) && in_array('hide', $actions)) {
                                $this->error->add(t('Independency is invalid (show and hide)'));
                                break;
                            }
                            if (in_array('disable', $actions) && in_array('enable', $actions)) {
                                $this->error->add(t('Independency is invalid (enable and disable)'));
                                break;
                            }
                            if (in_array('show', $actions) && in_array('disable', $actions)) {
                                $this->error->add(t('Independency is invalid (show and disable)'));
                                break;
                            }
                            if (in_array('hide', $actions) && in_array('enable', $actions)) {
                                $this->error->add(t('Independency is invalid (hide and enable)'));
                                break;
                            }
                            if (in_array('value', $actions) && in_array('clear', $actions)) {
                                $this->error->add(t('Independency is invalid (value and clear)'));
                                break;
                            }
                        }
                        // TODO
                        // validate
                    }
                break;
            }
        }

        if ($this->error->has()) {
            return $this->setResponse('error', $this->error);
        }
        else {

            $element->setType($post->get('elementType'));

            // check if handle is set
            $handle = $post->get('elementHandle');
            if (!$str->notempty($handle)) {
                $handle = $post->get('elementName');
            }

            $element->setName($post->get('elementName'));
            $element->setHandle(CommonHelper::generateHandle($handle, 'element', $this->ff, $element));
            $element->setRequired((int)$post->get('elementRequired'));

            foreach ($options as $handle => $option) {

                $property = $element->getProperty($handle);
                if ($option === false) {
                    if (is_object($property)) {
                        $element->removeProperty($property);
                    }
                    continue;
                }

                $fields = [];

                switch ($handle) {

                    case 'default':
                        $fields = [
                            $handle,
                            $handle.'_type',
                            $handle.'_value',
                            $handle.'_request',
                            $handle.'_page',
                            $handle.'_member'
                        ];
                    break;

                    case 'range':
                        $fields = [
                            $handle,
                            $handle.'_min',
                            $handle.'_max',
                            $handle.'_type',
                            $handle.'_step'
                        ];
                    break;

                    case 'mask':
                    case 'view':
                    case 'placeholder':
                    case 'help':
                    case 'advanced':
                    case 'confirm':
                    case 'css':
                    case 'folder':
                    case 'fileset':
                    case 'filesize':
                        $fields = [
                            $handle,
                            $handle.'_value'
                        ];
                    break;

                    case 'format':
                        $fields = [
                            $handle,
                            $handle.'_other',
                        ];
                    break;

                    case 'country':
                        $fields = [
                            $handle.'_placeholder',
                            $handle.'_custom',
                            $handle.'_available',
                            $handle.'_default',
                            $handle.'_ip',
                            $handle.'_clearonchange',
                            $handle.'_hideunused'
                        ];
                    break;

                    case 'no_label':
                    case 'wysiwyg':
                    case 'code':
                    case 'labels_vs_placeholder':
                        $fields = [$handle];
                    break;

                    case 'option':
                        $fields = [
                            $handle.'_type',
                            $handle.'_value',
                            $handle.'_page_type',
                            $handle.'_page_location',
                            $handle.'_page_location_value',
                            $handle.'_page_name',
                            $handle.'_page_aliasses',
                            $handle.'_page_featured',
                            $handle.'_page_empty',
                            $handle.'_page_order',
                            $handle.'_member_group',
                            $handle.'_member_empty',
                            $handle.'_member_active',
                            $handle.'_member_valid',
                            $handle.'_member_name',
                            $handle.'_member_sort_by',
                            $handle.'_member_sort_order',
                            $handle.'_other',
                            $handle.'_other_type',
                            $handle.'_other_value',
                            $handle.'_multiple'
                        ];
                    break;

                    case 'dependencies':
                        $fields = ['dependency'];
                    break;
                }

                foreach ($fields as $field) {
                    $value = $post->get($field);
                    if (in_array($field, ['default', 'range', 'range_min', 'range_max', 'no_label', 'confirm', 'option_other'])) {
                        $value = (int)$value;
                    }
                    if (in_array($field, ['filesize_value'])) {
                        $value = (float)$value;
                    }
                    if (in_array($field, ['option_value'])) {
                        $opts = [];
                        foreach ((array)$value as $v) {
                            if (!$str->notempty($v['value']) && !$str->notempty($v['name'])) {
                                continue;
                            }
                            $opts[] = $v;
                        }
                        $value = $opts;
                    }
                    if (in_array($field, ['dependency'])) {
                        unset($value['_tmp']);
                        $key = 0;
                        $dependencies = [];
                        foreach ((array)$value as $k => $v) {
                            // set actions
                            foreach ((array)$v['action'] as $ak => $a) {
                                $dependencies[$key]['action'][] = $a;
                            }
                            // set selector
                            foreach ((array)$v['selector'] as $sk => $s) {
                                $selector = [
                                    'element' => $s['element'],
                                    'condition' => []
                                ];
                                foreach ((array)$s['condition'] as $ck => $c) {
                                    $selector['condition'][] = $c;
                                }
                                $dependencies[$key]['selector'][] = $selector;
                            }
                            $key++;
                        }
                        $value = $dependencies;
                    }
                    $property = $element->getProperty($field);
                    if (empty($value)) {
                        if (is_object($property)) {
                            $element->removeProperty($property);
                        }
                        continue;
                    }
                    if (!is_object($property)) {
                        $property = new ElementProperty();
                        $property->setHandle($field);
                        $property->setElement($element);
                        $element->addProperty($property);
                    }
                    $property->setValue($value);
                }
            }

            $element->setDateModified(new \DateTime());

            $element->save();
        }

        $this->notification('success', t('Element successfully saved!'));
        $this->buildRedirect('/dashboard/formidable/forms/layout/'.$this->ff->getItemID())->send();
    }

    /* copy element */
    private function elementCopy()
    {
        $post = $this->request->request;

        if (!$this->token->validate('copy_element')) {
            return $this->setResponse('error', t('Invalid token. Please reload the page and retry.'));
        }

        $th = $this->app->make('helper/text');
        $str = $this->app->make('helper/validation/strings');

        $element = Element::getByID($post->get('elementID'));
        if (!is_object($element) || $element->getColumn()->getRow()->getForm()->getItemID() != $this->ff->getItemID()) {
            return $this->setResponse('error',  t('Invalid element'));
        }

        if (!$str->notempty($post->get('elementName'))) {
            $this->error->add(t('Name is empty or invalid'));
        }

        if ($this->error->has()) {
            return $this->setResponse('error', $this->error);
        }
        else {

            $new = clone $element;

            $handle = $post->get('elementHandle');
            if (!$str->notempty($handle)) {
                $handle = $post->get('elementName');
            }

            $new->setName($post->get('elementName'));
            $new->setHandle(CommonHelper::generateHandle($handle, 'element', $this->ff));
            $new->setOrder($new->getColumn()->getNextElementOrder());

            // save new element
            $new->save();

            $this->notification('success', t('Element successfully copied!'));

            $redirect = Url::to('/dashboard/formidable/forms/layout', $this->ff->getItemID());
            return $this->setResponse('location', (string)$redirect);
        }
    }

    /* delete element */
    private function elementDelete()
    {
        $post = $this->request->request;

        if (!$this->token->validate('delete_element')) {
            return $this->setResponse('error', t('Invalid token. Please reload the page and retry.'));
        }

        $element = Element::getByID($post->get('elementID'));
        if (!is_object($element) || $element->getColumn()->getRow()->getForm()->getItemID() != $this->ff->getItemID()) {
            $this->error->add(t('Invalid element'));
        }

        if ($this->error->has()) {
            return $this->setResponse('error', $this->error);
        }
        else {
            // delete element
            $element->delete();

            $this->notification('success', t('Element successfully deleted!'));

            $redirect = Url::to('/dashboard/formidable/forms/layout', $this->ff->getItemID());
            return $this->setResponse('location', (string)$redirect);
        }
    }


    /********* MAILS ************/

    /* submit mail */
    private function mailSubmit()
    {
        $mail = new Mail();
        $mail->setDateAdded(new \DateTime());
        $mail->setForm($this->ff);

        $post = $this->request->request;
        if ((int)$post->get('mailID') != 0) {
            $mail = Mail::getByID($post->get('mailID'));
            if (!is_object($mail) || $mail->getForm()->getItemID() != $this->ff->getItemID()) {
                $this->notification('error', t('Invalid mail, not found'));
                $this->buildRedirect('/dashboard/formidable/forms/mails/'.$this->ff->getItemID())->send();
            }
        }

        $token = 'add_mail';
        if ((int)$mail->getItemID() != 0) {
            $token = 'update_mail';
        }
        if (!$this->token->validate($token)) {
            $this->error->add(t('Invalid token. Please reload the page and retry.'));
        }

        $th = $this->app->make('helper/text');
        $str = $this->app->make('helper/validation/strings');

        if (!$str->notempty($post->get('mailName'))) {
            $this->error->add(t('Name is empty or invalid'));
        }

        if (!$str->notempty($post->get('mailFrom'))) {
            $this->error->add(t('From (type) is empty or invalid'));
        }
        else {
            if ($post->get('mailFrom') == Mail::MAIL_FROM_CUSTOM) {
                if (!$str->email($post->get('mailFromEmail'))) {
                    $this->error->add(t('From (email address) is empty or invalid'));
                }
                if (!$str->notempty($post->get('mailFromName'))) {
                    $this->error->add(t('From (name) is empty or invalid'));
                }
            }
            if ($post->get('mailFrom') == Mail::MAIL_FROM_ELEMENT) {
                if ((int)$post->get('mailFromElement') <= 0) {
                    $this->error->add(t('From (element) is empty or invalid'));
                }
            }
        }

        if (!$str->notempty($post->get('mailReplyTo'))) {
            $this->error->add(t('ReplyTo (type) is empty or invalid'));
        }
        else {
            if ($post->get('mailReplyTo') == Mail::MAIL_REPLY_CUSTOM) {
                if (!$str->email($post->get('mailReplyToEmail'))) {
                    $this->error->add(t('ReplyTo (email address) is empty or invalid'));
                }
                if (!$str->notempty($post->get('mailReplyToName'))) {
                    $this->error->add(t('ReplyTo (name) is empty or invalid'));
                }
            }
            if ($post->get('mailReplyTo') == Mail::MAIL_REPLY_ELEMENT) {
                if ((int)$post->get('mailReplyToElement') <= 0) {
                    $this->error->add(t('ReplyTo (element) is empty or invalid'));
                }
            }
        }

        $to = false;
        $toEmail = $post->get('mailToEmailAddresses');
        foreach ((array)$toEmail as $t) {
            if ($str->notempty($t)) {
                if (!$str->email($t)) {
                    $this->error->add(t('To (email address) is empty or invalid'));
                    continue;
                }
                $to = true;
            }
        }
        if (!$to && !count((array)$post->get('mailTo'))) {
            $this->error->add(t('To (Email Address or Element) is empty or invalid. Please add or select at least one.'));
        }

        if (!$str->notempty($post->get('mailSubject'))) {
            $this->error->add(t('Subject is empty or invalid'));
        }
        if (!$str->notempty($post->get('mailMessage'))) {
            $this->error->add(t('Message is empty or invalid'));
        }


        if (!$this->error->has()) {

            // check if handle is set
            $handle = $post->get('mailHandle');
            if (!$str->notempty($handle)) {
                $handle = $post->get('mailName');
            }

            $mail->setName($post->get('mailName'));
            $mail->setHandle(CommonHelper::generateHandle($handle, 'mail', $this->ff, $mail));

            $mail->setFrom($post->get('mailFrom'));
            $mail->setFromEmail(($post->get('mailFrom')==Mail::MAIL_FROM_CUSTOM)?$post->get('mailFromEmail'):'');
            $mail->setFromName(($post->get('mailFrom')==Mail::MAIL_FROM_CUSTOM)?$post->get('mailFromName'):'');
            $mail->setFromElement(($post->get('mailFrom')==Mail::MAIL_FROM_ELEMENT)?(int)$post->get('mailFromElement'):'');

            $mail->setReplyTo($post->get('mailReplyTo'));
            $mail->setReplyToEmail(($post->get('mailReplyTo')==Mail::MAIL_REPLY_CUSTOM)?$post->get('mailReplyToEmail'):'');
            $mail->setReplyToName(($post->get('mailReplyTo')==Mail::MAIL_REPLY_CUSTOM)?$post->get('mailReplyToName'):'');
            $mail->setReplyToElement(($post->get('mailReplyTo')==Mail::MAIL_REPLY_ELEMENT)?(int)$post->get('mailReplyToElement'):'');

            $mail->setTo(array_filter((array)$post->get('mailTo')));
            $mail->setToEmail(array_filter(array_map('strtolower', (array)$post->get('mailToEmailAddresses'))));

            $mail->setUseCC((int)$post->get('mailToUseCC'));

            $mail->setSubject($post->get('mailSubject'));
            $mail->setTemplate((int)$post->get('mailTemplate'));
            $mail->setMessage($post->get('mailMessage'));
            $mail->setSkipEmpty((int)$post->get('mailSkipEmpty'));
            $mail->setSkipLayout((int)$post->get('mailSkipLayout'));

            $mail->setAttachmentFiles(array_filter((array)$post->get('mailAttachmentFiles')));
            $mail->setAttachments(array_filter((array)$post->get('mailAttachments')));

            $dependencies = [];
            $value = $post->get('dependency');
            if ($value && count($value)) {
                unset($value['_tmp']);
                $key = 0;
                foreach ((array)$value as $k => $v) {
                    // set actions
                    foreach ((array)$v['action'] as $ak => $a) {
                        $dependencies[$key]['action'][] = $a;
                    }
                    // set selector
                    foreach ((array)$v['selector'] as $sk => $s) {
                        $selector = [
                            'element' => $s['element'],
                            'condition' => []
                        ];
                        foreach ((array)$s['condition'] as $ck => $c) {
                            $selector['condition'][] = $c;
                        }
                        $dependencies[$key]['selector'][] = $selector;
                    }
                    $key++;
                }
            }
            $mail->setDependencies($dependencies);
            $mail->setDateModified(new \DateTime());

            $mail->save();

            $this->notification('success', t('Mail successfully saved!'));
            $this->buildRedirect('/dashboard/formidable/forms/mails/'.$this->ff->getItemID())->send();
        }
    }

    /* copy mail */
    private function mailCopy()
    {
        $post = $this->request->request;

        if (!$this->token->validate('copy_mail')) {
            return $this->setResponse('error', t('Invalid token. Please reload the page and retry.'));
        }

        $th = $this->app->make('helper/text');
        $str = $this->app->make('helper/validation/strings');

        $mail = Mail::getByID($post->get('mailID'));
        if (!is_object($mail) || $mail->getForm()->getItemID() != $this->ff->getItemID()) {
            return $this->setResponse('error',  t('Invalid mail'));
        }

        if (!$str->notempty($post->get('mailName'))) {
            $this->error->add(t('Name is empty or invalid'));
        }

        if ($this->error->has()) {
            return $this->setResponse('error', $this->error);
        }
        else {

            $new = clone $mail;

            $handle = $post->get('mailHandle');
            if (!$str->notempty($handle)) {
                $handle = $post->get('mailName');
            }

            $new->setName($post->get('mailName'));
            $new->setHandle(CommonHelper::generateHandle($handle, 'mail', $this->ff));

            // save new mail
            $new->save();

            $this->notification('success', t('Mail successfully copied!'));

            $redirect = Url::to('/dashboard/formidable/forms/mails', $this->ff->getItemID());
            return $this->setResponse('location', (string)$redirect);
        }
    }

    /* delete mail */
    private function mailDelete()
    {
        $post = $this->request->request;

        if (!$this->token->validate('delete_mail')) {
            return $this->setResponse('error', t('Invalid token. Please reload the page and retry.'));
        }

        $mail = Mail::getByID($post->get('mailID'));
        if (!is_object($mail) || $mail->getForm()->getItemID() != $this->ff->getItemID()) {
            return $this->setResponse('error',  t('Invalid mail'));
        }

        if ($this->error->has()) {
            return $this->setResponse('error', $this->error);
        }
        else {
            // delete mail
            $mail->delete();

            $this->notification('success', t('Mail successfully deleted!'));

            $redirect = Url::to('/dashboard/formidable/forms/mails', $this->ff->getItemID());
            return $this->setResponse('location', (string)$redirect);
        }
    }


    /********* OTHERS ************/

    /**
     * Sorting layout
     */
    private function sortSubmit()
    {
        $post = $this->request->request;

        if (!$this->token->validate('sort_form')) {
            $this->error->add(t('Invalid token. Please reload the page and retry.'));
        }
        if ($this->error->has()) {
            return $this->setResponse('error', $this->error);
        }
        else {

            foreach ((array)$post->get('rows') as $kr => $r) {
                $row = Row::getByID($r['rowID']);
                if (!is_object($row)) {
                    continue;
                }
                $row->setOrder($kr);
                $row->setDateModified(new \DateTime());
                $row->save();

                foreach ((array)$r['columns'] as $kc => $c) {
                    $column = Column::getByID($c['columnID']);
                    if (!is_object($column)) {
                        continue;
                    }
                    $column->setRow($row);
                    $column->setOrder($kc);
                    $column->setDateModified(new \DateTime());
                    $column->save();

                    foreach ((array)$c['elements'] as $ke => $e) {
                        $element = Element::getByID($e);
                        if (!is_object($element)) {
                            continue;
                        }
                        $element->setColumn($column);
                        $element->setOrder($ke);
                        $element->setDateModified(new \DateTime());
                        $element->save();
                    }
                }
            }

            return $this->setResponse('success', t('Sorting is successfully saved!'));
        }
    }


    /**
     * Submit import
     */
    private function importSubmit()
    {
        if (!FormidableFull::exists()) {
            $this->buildRedirect('/dashboard/formidable/forms')->send();
        }

        $post = $this->request->request;

        if (!$this->token->validate('import_formidable')) {
            $this->error->add(t('Invalid token. Please reload the page and retry.'));
        }

        $formidable_full = new FormidableFull();
        if ($post->get('convertFromFormidableFull')) {
            $formidable_full->convert();
            if ($post->get('removeFormidableFull')) {
                $formidable_full->remove();
            }
        }

        $this->notification('success', t('Import successfull!'));
        $this->buildRedirect('/dashboard/formidable/forms')->send();
    }
}