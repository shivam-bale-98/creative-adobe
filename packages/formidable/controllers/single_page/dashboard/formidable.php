<?php
namespace Concrete\Package\Formidable\Controller\SinglePage\Dashboard;

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Routing\Redirect;
use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\Application\Service\UserInterface;
use Concrete\Package\Formidable\Src\Formidable\Forms\Form as FormidableForm;
use Concrete\Package\Formidable\Src\Formidable\Forms\Rows\Row;
use Concrete\Package\Formidable\Src\Formidable\Forms\Columns\Column;
use Concrete\Package\Formidable\Src\Formidable\Forms\Elements\Element;
use Concrete\Package\Formidable\Src\Formidable\Mails\Mail;
use Concrete\Package\Formidable\Src\Formidable\Templates\Template;
use Concrete\Package\Formidable\Src\Formidable\Helpers\Common as CommonHelper;

use Concrete\Package\Formidable\Src\Formidable\Helpers\FormidableFull;

class Formidable extends DashboardSitePageController
{
    public function view()
    {
        $this->buildRedirect('/dashboard/formidable/forms')->send();
    }

    /**
     * Handling response
     */
    public function setResponse($type, $params, $additional = [])
    {
        $data = [];
        $http = JsonResponse::HTTP_OK;

        if ($type == 'error') {
            $http = JsonResponse::HTTP_BAD_REQUEST;
            $data = ['error' => []];
            if (is_string($params)) {
                $data['error'][] = $params;
            }
            if (is_array($params)) {
                foreach ($params as $err) {
                    $data['error'][] = $err;
                }
            }
            if (is_object($params)) {
                foreach ($params->getList() as $err) {
                    $data['error'][] = $err->getMessage();
                }
            }
        }

        if ($type == 'success') {
            $data['success'] = $params;
        }

        if ($type == 'location') {
            $data['redirect'] = $params;
        }

        if ($additional) {
            $data = array_merge($data, $additional);
        }

        return new JsonResponse($data, $http);
    }

    /**
     * Set notification
     */
    public function notification($type, $message)
    {
        $session = $this->app->make('session');
        $session->getFlashBag('formidable')->add($type, $message);
    }

    /**
     * Set notification
     */
    public function showFlash()
    {
        $messages = [];

        $session = $this->app->make('session');
        $flash = $session->getFlashBag('formidable')->all();
        if (count($flash)) {
            foreach ($flash as $type => $message) {
                //$this->flash($type, @implode(PHP_EOL, (array)$message));
                switch($type) {
                    case 'success':
                    case 'info':
                        $args = [
                            'type' => $type,
                            'title' => t('Success'),
                            'hide' => true,
                            'icon' => 'fas fa-check-circle',
                            'text' => @implode(PHP_EOL, (array)$message)
                        ];
                        $messages[] = $this->app->make(UserInterface::class)->notify($args);
                    break;
                    case 'warning':
                        $args = [
                            'type' => $type,
                            'title' => t('Warning'),
                            'hide' => true,
                            'icon' => 'fas fa-times',
                            'text' => @implode(PHP_EOL, (array)$message)
                        ];
                        $messages[] = $this->app->make(UserInterface::class)->notify($args);
                    break;
                    case 'error':
                        $args = [
                            'type' => $type,
                            'title' => t('Error'),
                            'hide' => true,
                            'icon' => 'fas fa-times',
                            'text' => @implode(PHP_EOL, (array)$message)
                        ];
                        $messages[] = $this->app->make(UserInterface::class)->notify($args);
                    break;
                }
            }
            if (count($messages)) {
                $this->set('flash', @implode(PHP_EOL, $messages));
            }
        }
        $session->getFlashBag('formidable')->clear();
    }

    /* generate handle */
    public function handle()
    {
        $post = $this->request;

        if (!$this->token->validate('generate_handle')) {
            return $this->setResponse('error', t($this->token->getErrorMessage()));
        }

        if (empty($post->post('name')) && empty($post->post('type'))) {
            return $this->setResponse('error', t('Invalid request'));
        }

        $form = null;
        if (!empty($post->post('form'))) {
            $form = FormidableForm::getByID((int)$post->post('form'));
        }

        $current = null;
        if (!empty($post->post('current'))) {
            switch ($post->post('type')) {
                case 'form':
                    $current = FormidableForm::getByID((int)$post->post('current'));
                break;
                case 'row':
                    $current = Row::getByID((int)$post->post('current'));
                break;
                case 'column':
                    $current = Column::getByID((int)$post->post('current'));
                break;
                case 'element':
                    $current = Element::getByID((int)$post->post('current'));
                break;
                case 'mail':
                    $current = Mail::getByID((int)$post->post('current'));
                break;
                case 'template':
                    $current = Template::getByID((int)$post->post('current'));
                break;
            }
        }

        $handle = CommonHelper::generateHandle($post->post('name'), $post->post('type'), $form, $current);
        if (!$handle) {
            return $this->setResponse('error', t('Invalid request'));
        }
        return $this->setResponse('success', ['handle' => $handle]);
    }
}