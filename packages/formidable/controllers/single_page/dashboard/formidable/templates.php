<?php
namespace Concrete\Package\Formidable\Controller\SinglePage\Dashboard\Formidable;

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Package\Formidable\Controller\SinglePage\Dashboard\Formidable;
use Concrete\Package\Formidable\Src\Formidable\Templates\Template;
use Concrete\Package\Formidable\Src\Formidable\Templates\TemplateList;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Support\Facade\Url;
use Concrete\Package\Formidable\Src\Formidable\Helpers\Common as CommonHelper;

class Templates extends Formidable
{
    protected $template;

    public function view()
    {
        $this->loadAssets();

        $list = new TemplateList();
        $list->filterBySite($this->app->make('site')->getActiveSiteForEditing());
        $templates = $list->getResults();
        $this->set('templates', $templates);

        $this->set('pageTitle', t('Formidable Templates'));
    }

    /* view and save properties of form */
    public function props($templateID = 0)
    {
        $this->loadAssets($templateID);

        $this->set('pageTitle', t('Add new template'));
        if ($templateID > 0) {
            $this->set('pageTitle', t('Edit properties for "%s"', $this->template->getName()));
        }

        // on submit
        if ($this->request->request->count()) {
            $this->templateSubmit();
        }
    }


    /* copy items */
    public function copy($type = 'template', $templateID = 0)
    {
        $this->loadAssets($templateID, true);

        switch ($type) {
            case 'template':
                return $this->templateCopy();
            break;
        }
        return $this->setResponse('error', t('Invalid request'));
    }

    /* delete items */
    public function delete($type = 'template', $templateID = 0)
    {
        $this->loadAssets($templateID, true);

        switch ($type) {
            case 'template':
                return $this->templateDelete();
            break;
        }
        return $this->setResponse('error', t('Invalid request'));
    }


    /* copy template */
    private function templateCopy()
    {
        $post = $this->request->request;

        if (!$this->token->validate('copy_template')) {
            $this->error->add(t($this->token->getErrorMessage()));
        }

        $str = $this->app->make('helper/validation/strings');

        $template = Template::getByID($post->get('templateID'));
        if (!is_object($template)) {
            return $this->setResponse('error',  t('Invalid template'));
        }

        if (!$str->notempty($post->get('templateName'))) {
            $this->error->add(t('Name is empty or invalid'));
        }

        if ($this->error->has()) {
            return $this->setResponse('error', $this->error);
        }
        else {

            $new = clone $template;

            $handle = $post->get('templateHandle');
            if (!$str->notempty($handle)) {
                $handle = $post->get('templateName');
            }

            $new->setName($post->get('templateName'));
            $new->setHandle(CommonHelper::generateHandle($handle, 'template'));

            // save new template
            $new->save();

            $this->notification('success', t('Template successfully copied!'));

            $redirect = Url::to('/dashboard/formidable/templates');
            return $this->setResponse('location', (string)$redirect);
        }
    }

    /* delete template */
    private function templateDelete()
    {
        $post = $this->request->request;

        if (!$this->token->validate('delete_template')) {
            $this->error->add(t($this->token->getErrorMessage()));
        }

        $template = Template::getByID($post->get('templateID'));
        if (!is_object($template)) {
            $this->error->add(t('Invalid template'));
        }

        if ($this->error->has()) {
            return $this->setResponse('error', $this->error);
        }
        else {
            // delete template
            $template->delete();

            $this->notification('success', t('Template successfully deleted!'));

            $redirect = Url::to('/dashboard/formidable/templates');
            return $this->setResponse('location', (string)$redirect);
        }
    }

    private function templateSubmit()
    {
        $token = 'add_template';
        if ((int)$this->template->getItemID() != 0) {
            $token = 'update_template';
        }
        if (!$this->token->validate($token)) {
            $this->error->add(t($this->token->getErrorMessage()));
        }

        $th = $this->app->make('helper/text');
        $str = $this->app->make('helper/validation/strings');

        $post = $this->request->request;
        if (!$str->notempty($post->get('templateName'))) {
            $this->error->add(t('Name is empty or invalid'));
        }

        if (!$str->notempty($post->get('templateContent'))) {
            $this->error->add(t('Content is empty or invalid'));
        }

        if (!$this->error->has()) {

            $template = $this->template;
            if (!is_object($template) || (int)$template->getItemID() == 0) {
                $template = new Template();
                $template->setDateAdded(new \DateTime());
            }

            // check if handle is set
            $handle = $post->get('templateHandle');
            if (!$str->notempty($handle)) {
                $handle = $post->get('templateName');
            }

            $template->setName($post->get('templateName'));
            $template->setHandle(CommonHelper::generateHandle($handle, 'template', $template));
            $template->setStyle(base64_encode($post->get('templateStyle')));
            $template->setContent($post->get('templateContent'));
            $template->setDateModified(new \DateTime());

            $template->setSite($this->app->make('site')->getActiveSiteForEditing());

            $template->save();

            $this->notification('success', $token=='add_template'?t('Template successfully added!'):t('Template successfully updated!'));

            $this->buildRedirect('/dashboard/formidable/templates')->send();
        }
    }

    /* load common assets for each action */
    private function loadAssets($templateID = 0, $force = false)
    {
        $this->requireAsset('javascript', 'formidable/dashboard/all');
        $this->requireAsset('javascript', 'formidable/dashboard/template');
        $this->requireAsset('css', 'formidable/dashboard/all');

        $this->requireAsset('ace');

        $template = new Template();
        if ($templateID > 0 || $force) {
            $template = Template::getByID($templateID);
            if (!is_object($template)) {
                $this->notification('error', t('Invalid template, not found'));
                $this->buildRedirect('/dashboard/formidable/templates')->send();
            }
            elseif ($template->getSite() != $this->app->make('site')->getActiveSiteForEditing()) {
                $this->notification('error', t('Invalid template, not found'));
                $this->buildRedirect('/dashboard/formidable/templates')->send();
            }
        }

        $this->template = $template;
        $this->set('template', $this->template);

        $header = $this->app->make(ElementManager::class);
        $this->set('headerMenu', $header->get('template/menu', ['action' => $this->getAction(), 'post' => $this->request->request->all(), 'template' => $this->template], 'formidable'));

        $this->showFlash();
    }
}