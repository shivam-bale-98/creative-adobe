<?php
namespace Application\Authentication\Concrete;


use Concrete\Authentication\Concrete\Controller as ParentController;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\User\Exception\UserException;
use Concrete\Core\User\Exception\UserPasswordExpiredException;
use Concrete\Core\User\Exception\UserPasswordResetException;
use Concrete\Core\User\Login\LoginService;
use Concrete\Core\User\Login\PasswordUpgrade;

class Controller extends ParentController
{
    public function authenticate() {
        $config = $this->app->make(Repository::class);

        $uName = $this->request->request->get('uName');
        $uName = is_string($uName) ? trim($uName) : '';
        $uPassword = $this->request->request->get('uPassword');
        if (!is_string($uPassword)) {
            $uPassword = '';
        }

        if ($uName === '' || $uPassword === '') {
            if ($config->get('concrete.user.registration.email_registration')) {
                throw new UserMessageException(t('Please provide both email address and password.'));
            } else {
                throw new UserMessageException(t('Please provide both username and password.'));
            }
        }

        $failedLogins = $this->app->make('failed_login');
        if ($failedLogins->isDenylisted()) {
            throw new UserMessageException($failedLogins->getErrorMessage());
        }

          $captcha = $this->app->make('helper/validation/captcha');
          if ($captcha->check()) {
              throw new UserMessageException(t('Incorrect image validation code. Please check the image and re-enter the letters or numbers as necessary.'));
          }

        $loginService = $this->app->make(LoginService::class);

        try {
            $user = $loginService->login($uName, $uPassword);
        } catch (UserPasswordResetException $e) {
            $this->app->make('session')->set(static::REQUIRED_PASSWORD_UPGRADE_SESSIONKEY, ['type' => PasswordUpgrade::PASSWORD_RESET_KEY, 'uName' => $uName]);
            $this->redirect('/login/', $this->getAuthenticationType()->getAuthenticationTypeHandle(), 'required_password_upgrade');
        } catch (UserPasswordExpiredException $e) {
            $this->app->make('session')->set(static::REQUIRED_PASSWORD_UPGRADE_SESSIONKEY, ['type' => PasswordUpgrade::PASSWORD_EXPIRED_KEY, 'uName' => $uName]);
            $this->redirect('/login/', $this->getAuthenticationType()->getAuthenticationTypeHandle(), 'required_password_upgrade');
        } catch (UserException $e) {
            $this->handleFailedLogin($loginService, $uName, $uPassword, $e);
        }

        if ($user->isError()) {
            throw new UserMessageException(t('Unknown login error occurred. Please try again.'));
        }

        if ($this->request->request->get('uMaintainLogin')) {
            $user->setAuthTypeCookie('concrete');
        }

        $loginService->logLoginAttempt($uName);

        return $user;
    }
}