<?php
namespace Application\Concrete\Page\Controller;

use Application\Concrete\User\User;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Http\Service\Ajax;
use Concrete\Core\Application\Application;
use Application\Concrete\Page\Page;
use Concrete\Core\Utility\Service\Text;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Http\Request;
use Mobile_Detect;

class PageTypeControllerBase extends PageController
{
    protected $form;
    protected $vh;
    protected $user;
    protected $errors;
    protected $errorCode;
    protected $errorHelper;
    protected $ajaxHelper;
    protected $th;
    protected $token_action;
    protected $current_page;
    protected $detectMobile;

    function on_start()
    {
        parent::on_start();

        $this->ajaxHelper  = new Ajax();
        $this->errorHelper = new ErrorList();
        $this->vh          = new Token();
        $this->th          = new Text();
        $this->form        = new Form($this->app);
        $this->current_page = Page::getCurrentPage();
        $this->user        = new User();
        $this->detectMobile    = new Mobile_Detect();

        $this->set('th', $this->th);
        $this->set('vh', $this->vh);
        $this->set('form', $this->form);
        $this->set('current_page', $this->current_page);
        $this->set('user', $this->user);
        $this->set('detectMobile', $this->detectMobile);

    }

    protected function getErrorCode()
    {
        return (int)$this->errorCode;
    }

    protected function setErrorCode($errorCode)
    {
        return $this->errorCode = $errorCode;
    }

    protected function getErrors()
    {
        $errorList = $this->getErrorHelper()->getList();
        $errorMsg = [];

        foreach ($errorList as $error){
            $errorMsg[] = $error->getMessage();
        }
        return $errorMsg;
    }

    protected function getErrorsMessage()
    {
        $errorMsg = array_map(function ($error) {
            return '<li>' . $error->getMessage() . '</li>';
        }, $this->getErrorHelper()->getList());

        return implode('', $errorMsg);
    }

    public function sendResult($result = [])
    {
        $ajax   = new Ajax();

        if ($this->hasErrors()) {
            echo \Core::make(\Concrete\Core\Http\ResponseFactoryInterface::class)->json(array_merge(['success' => false, 'error_code' => $this->getErrorCode(), 'message' => $this->getErrors(), 'display_error_message' => $this->getErrorHelper()->__toString()], $result));
        }

        $result['success'] = true;
        $result['server_time'] = time();
        echo  \Core::make(\Concrete\Core\Http\ResponseFactoryInterface::class)->json($result)->getContent();
    }

    /**
     * @return ErrorList
     */
    public function getErrorHelper()
    {
        return $this->errorHelper;
    }

    protected function addError($error, $fieldName = '')
    {
        $this->getErrorHelper()->add($error, $fieldName);
    }

    protected function hasErrors()
    {
        return $this->getErrorHelper()->has();
    }

    /**
     * @return Token
     */
    protected function getToken()
    {
        return $this->vh;
    }

    /**
     * @return Token
     */
    protected function getTokenAction()
    {
        return $this->token_action;
    }

    /**
     * @param $action mixed
     */
    protected function setTokenAction($action = '')
    {
        $this->token_action = $action;
    }

    /**
     * @param $token mixed
     * @return mixed
     */
    protected function validateToken($token = null)
    {
        if (!$this->getToken()->validate($this->getTokenAction(), $token)) {
            $this->addError('Token expired. Please refresh the page and try again.');
            return true;
        }
        return false;
    }

    /**
     * @param $action mixed
     * @return mixed
     */
    protected function getTokenOutput()
    {
        return $this->getToken()->output($this->getTokenAction(), true);
    }

    /**
     * @param $action mixed
     * @return mixed
     */
    protected function getTokenValue()
    {
        return $this->getToken()->generate($this->getTokenAction());
    }

    /**
     *@return Ajax
     */
    protected function getAjaxHelper()
    {
        return $this->ajaxHelper;
    }

    /**
     *@return bool
     */
    protected function isAjaxRequest()
    {
        return $this->getAjaxHelper()->isAjaxRequest(Request::getInstance());
    }


    /**
     *@return Text
     */
    protected function getText()
    {
        return $this->th;
    }


    /**
     *@return Page
     */
    protected function getCurrentPage()
    {
        return $this->current_page;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

}
