<?php

namespace Application\Controller\SinglePage;

use Application\Concrete\Helpers\GoogleAuthenticator;
use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Authentication\AuthenticationTypeFailureException;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Package\Package;
use Concrete\Core\Routing\RedirectResponse;
use Concrete\Core\Support\Facade\Config;
use Concrete\Core\User\PostLoginLocation;
use Concrete\Core\Utility\Service\Text;
use Concrete\Core\Validation\CSRF\Token;
use Exception;
use PageController;
use Concrete\Core\User\User;
use UserAttributeKey;
use UserInfo;
use Loader;

class Login extends \Concrete\Controller\SinglePage\Login
{

    use LoggerAwareTrait;

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_SECURITY;
    }

    public $helpers = ['form'];
    protected $locales = [];

    public function on_before_render()
    {
        if ($this->error->has()) {
            $this->set('error', $this->error);
        }
    }

    /* automagically run by the controller once we're done with the current method */
    /* method is passed to this method, the method that we were just finished running */

    public function account_deactivated()
    {
        $config = $this->app->make('config');
        $this->error->add(t($config->get('concrete.user.deactivation.message')));
    }

    public function session_invalidated()
    {
        $this->error->add(t('Your session has expired. Please sign in again.'));
    }

    /**
     * Concrete5_Controller_Login::callback
     * Call an AuthenticationTypeController method throw a uri.
     * Use: /login/TYPE/METHOD/PARAM1/.../PARAM10.
     *
     * @param string $type
     * @param string $method
     * @param null $a
     * @param null $b
     * @param null $c
     * @param null $d
     * @param null $e
     * @param null $f
     * @param null $g
     * @param null $h
     * @param null $i
     * @param null $j
     *
     * @throws \Concrete\Core\Authentication\AuthenticationTypeFailureException
     * @throws \Exception
     */
    public function callback($type = null, $method = 'callback', $a = null, $b = null, $c = null, $d = null, $e = null, $f = null, $g = null, $h = null, $i = null, $j = null)
    {
        if (!$type) {
            return $this->view();
        }
        $at = AuthenticationType::getByHandle($type);
        if (!$at || !$at->isEnabled()) {
            throw new AuthenticationTypeFailureException(t('Invalid authentication type.'));
        }

        $this->set('authType', $at);
        if (!method_exists($at->controller, $method)) {
            return $this->view();
        }
        if ($method != 'callback') {
            if (!is_array($at->controller->apiMethods) || !in_array($method, $at->controller->apiMethods)) {
                return $this->view();
            }
        }
        try {
            $params = func_get_args();
            array_shift($params);
            array_shift($params);

            $this->view();
            $this->set('authTypeParams', $params);
            $this->set('authTypeElement', $method);
        } catch (Exception $e) {
            if ($e instanceof AuthenticationTypeFailureException) {
                // Throw again if this is a big`n
                throw $e;
            }
            $this->error->add($e->getMessage());
        }
    }

    /**
     * Concrete5_Controller_Login::authenticate
     * Authenticate the user using a specific authentication type.
     *
     * @param $type    AuthenticationType handle
     */
    public function authenticate($type = '')
    {
        $valt = $this->app->make('token');

        $pkg_config         = Package::getByHandle('google_authentication')->getConfig();
        $enableGoogleAuth   = $pkg_config->get('google_authentication.ENABLE_GOOGLE_2FA');

        if (!$valt->validate('login_' . $type)) {
            $this->error->add($valt->getErrorMessage());
        } else {
            try {
                $at = AuthenticationType::getByHandle($type);
                if (!$at->isEnabled()) {
                    throw new AuthenticationTypeFailureException(t('Invalid authentication type.'));
                }
                $user = $at->controller->authenticate();

                if ($user && $user->isRegistered()) {
                    //return $this->finishAuthentication($at, $user);
                    if($user && $this->hasDashboardAccess($user) && $enableGoogleAuth) {
                        $this->generateGoogleAuthToken($user);
                        $user->logout(true);
                    } else {
                        return $this->finishAuthentication($at, $user);
                    }

                }


            } catch (Exception $e) {
                $this->error->add($e->getMessage());
            }
        }

        if (isset($at)) {
            $this->set('lastAuthType', $at);
        }

        $this->view();
    }

    /**
     * @param AuthenticationType $type Required
     *
     * @throws Exception
     */
    public function finishAuthentication(
        AuthenticationType $type,
        User $u
    )
    {
        $this->app->instance(User::class, $u);
        if (!$type || !($type instanceof AuthenticationType)) {
            return $this->view();
        }


        $captchaConfig = Package::getByHandle('ec_recaptcha')->getConfig();
        if($captchaConfig && $captchaConfig->get('captcha.enable_login_captcha')){
            $captcha = $this->app->make('helper/validation/captcha');
            if (!$captcha->check()) {
                $this->error->add(t('Incorrect image validation code. Please check the image and re-enter the letters or numbers as necessary.'));
            }
        }

        $config = $this->app->make('config');
        if ($config->get('concrete.i18n.choose_language_login')) {
            $userLocale = $this->post('USER_LOCALE');
            if (is_string($userLocale) && ($userLocale !== '')) {
                if ($userLocale !== Localization::BASE_LOCALE) {
                    $availableLocales = Localization::getAvailableInterfaceLanguages();
                    if (!in_array($userLocale, $availableLocales)) {
                        $userLocale = '';
                    }
                }
                if ($userLocale !== '') {
                    if (Localization::activeLocale() !== $userLocale) {
                        Localization::changeLocale($userLocale);
                    }
                    $u->setUserDefaultLanguage($userLocale);
                }
            }
        }



        $ui = UserInfo::getByID($u->getUserID());
        $aks = UserAttributeKey::getRegistrationList();

        $unfilled = array_values(
            array_filter(
                $aks,
                function ($ak) use ($ui) {
                    return $ak->isAttributeKeyRequiredOnRegister() && !is_object($ui->getAttributeValueObject($ak));
                }));

        if (count($unfilled)) {
            $u->logout(false);

            if (!$this->error) {
                $this->on_start();
            }

            $this->set('required_attributes', $unfilled);
            $this->set('u', $u);

            $session = $this->app->make('session');
            $session->set('uRequiredAttributeUser', $u->getUserID());
            $session->set('uRequiredAttributeUserAuthenticationType', $type->getAuthenticationTypeHandle());

            $this->view();

            return $this->getViewObject()->render();
        }

        $u->setLastAuthType($type);

        $ue = new \Concrete\Core\User\Event\User($u);
        $this->app->make('director')->dispatch('on_user_login', $ue);

        return new RedirectResponse(
            $this->app->make('url/manager')->resolve(['/login', 'login_complete'])
        );
    }

    public function login_complete()
    {
        // Move this functionality to a redirected endpoint rather than from within the previous method because
        // session isn't set until we redirect and reload.
        $u = $this->app->make(User::class);
        if (!$this->error) {
            $this->error = $this->app->make('helper/validation/error');
        }

        if ($u->isRegistered()) {
            $pll = $this->app->make(PostLoginLocation::class);
            $response = $pll->getPostLoginRedirectResponse(true);

            return $response;
        } else {
            $session = $this->app->make('session');
            $this->logger->notice(
                t('Session made it to login_complete but was not attached to an authenticated session.'),
                ['session' => $session->getId(), 'ip_address' => $_SERVER['REMOTE_ADDR']]
            );
            $this->error->add(t('User is not registered. Check your authentication controller.'));
            $u->logout();
        }
    }

    public function on_start()
    {
        $config = $this->app->make('config');
        $this->error = $this->app->make('helper/validation/error');
        $this->set('valt', $this->app->make('helper/validation/token'));

        $txt = $this->app->make('helper/text');
        if (isset($_GET['uName']) && strlen($_GET['uName'])
        ) { // pre-populate the username if supplied, if its an email address with special characters the email needs to be urlencoded first,
            $this->set('uName', trim($txt->email($_GET['uName'])));
        }

        $loc = Localization::getInstance();
        $loc->pushActiveContext(Localization::CONTEXT_SITE);
        if ($config->get('concrete.user.registration.email_registration')) {
            $this->set('uNameLabel', t('Email Address'));
        } else {
            $this->set('uNameLabel', t('Username'));
        }
        $languages = [];
        $locales = [];

        if ($config->get('concrete.i18n.choose_language_login')) {
            $languages = Localization::getAvailableInterfaceLanguages();
            if (count($languages) > 0) {
                array_unshift($languages, Localization::BASE_LOCALE);
            }
            $locales = [];
            foreach ($languages as $lang) {
                $locales[$lang] = \Punic\Language::getName($lang, $lang);
            }
            asort($locales);
            $locales = array_merge(['' => tc('Default locale', '** Default')], $locales);
        }
        $loc->popActiveContext();
        $this->locales = $locales;
        $this->set('locales', $locales);
    }

    /**
     * @deprecated Use the getPostLoginUrl method of \Concrete\Core\User\PostLoginLocation
     *
     * @see \Concrete\Core\User\PostLoginLocation::getPostLoginUrl()
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        $pll = $this->app->make(PostLoginLocation::class);
        $url = $pll->getPostLoginUrl(true);

        return $url;
    }

    /**
     * @deprecated Use the getSessionPostLoginUrl method of \Concrete\Core\User\PostLoginLocation
     *
     * @see \Concrete\Core\User\PostLoginLocation::getSessionPostLoginUrl()
     *
     * @return string|false
     */
    public function getRedirectUrlFromSession()
    {
        $pll = $this->app->make(PostLoginLocation::class);
        $url = $pll->getSessionPostLoginUrl(true);

        return $url === '' ? false : $url;
    }

    public function view($type = null, $element = 'form')
    {
        $this->requireAsset('javascript', 'backstretch');
        $this->set('authTypeParams', $this->getSets());

        $user = $this->app->make(User::class);
        $this->set('user', $user);

        if (strlen($type)) {
            try {
                $at = AuthenticationType::getByHandle($type);
                if ($at->isEnabled()) {
                    $this->set('authType', $at);
                    $this->set('authTypeElement', $element);
                }
            } catch (\Exception $e) {
                // Don't fail loudly
            }
        }
    }

    public function fill_attributes()
    {
        try {
            $session = $this->app->make('session');
            if (!$session->has('uRequiredAttributeUser') ||
                intval($session->get('uRequiredAttributeUser')) < 1 ||
                !$session->has('uRequiredAttributeUserAuthenticationType') ||
                !$session->get('uRequiredAttributeUserAuthenticationType')
            ) {
                $session->remove('uRequiredAttributeUser');
                $session->remove('uRequiredAttributeUserAuthenticationType');
                throw new Exception(t('Invalid Request, please attempt login again.'));
            }
            User::loginByUserID($session->get('uRequiredAttributeUser'));
            $session->remove('uRequiredAttributeUser');
            $u = $this->app->make(User::class);
            $at = AuthenticationType::getByHandle($session->get('uRequiredAttributeUserAuthenticationType'));
            $session->remove('uRequiredAttributeUserAuthenticationType');
            if (!$at || !$at->isEnabled()) {
                throw new Exception(t('Invalid Authentication Type'));
            }

            $ui = UserInfo::getByID($u->getUserID());
            $aks = UserAttributeKey::getRegistrationList();

            $unfilled = array_values(
                array_filter(
                    $aks,
                    function ($ak) use ($ui) {
                        return $ak->isAttributeKeyRequiredOnRegister() && !is_object($ui->getAttributeValueObject($ak));
                    }));

            $saveAttributes = [];
            foreach ($unfilled as $attribute) {
                $controller = $attribute->getController();
                $validator = $controller->getValidator();
                $response = $validator->validateSaveValueRequest($controller, $this->request);
                /* @var \Concrete\Core\Validation\ResponseInterface $response */
                if ($response->isValid()) {
                    $saveAttributes[] = $attribute;
                } else {
                    $error = $response->getErrorObject();
                    $this->error->add($error);
                }
            }

            if (count($saveAttributes) > 0) {
                $ui->saveUserAttributesForm($saveAttributes);
            }

            return $this->finishAuthentication($at, $u);
        } catch (Exception $e) {
            $this->error->add($e->getMessage());
        }
    }

    /**
     * @deprecated
     */
    public function logout($token = false)
    {
        if ($this->app->make('token')->validate('logout', $token)) {
            $u = $this->app->make(User::class);
            $u->logout();
            $this->redirect('/');
        }
    }

    /**
     * @param $token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function do_logout($token = false)
    {
        $factory = $this->app->make(ResponseFactoryInterface::class);
        /* @var ResponseFactoryInterface $factory */
        $valt = $this->app->make('token');
        /* @var \Concrete\Core\Validation\CSRF\Token $valt */

        if ($valt->validate('do_logout', $token)) {
            // Resolve the current logged in user and log them out
            $this->app->make(User::class)->logout();

            // Determine the destination URL
            $url = $this->app->make('url/manager')->resolve(['/']);

            // Return a new redirect to the homepage.
            return $factory->redirect((string) $url, 302);
        }

        return $factory->error($valt->getErrorMessage());
    }

    public function forward($cID = 0)
    {
        $nh = $this->app->make('helper/validation/numbers');
        if ($nh->integer($cID, 1)) {
            $rcID = (int) $cID;
            $this->set('rcID', $rcID);
            $pll = $this->app->make(PostLoginLocation::class);
            $pll->setSessionPostLoginUrl($rcID);
        }
    }

    private function generateGoogleAuthToken($u)
    {
        $e          = new ErrorList();
        $vth        = new Token();

        $userInfo   = $u->getUserInfoObject();

        if (!$userInfo) {
            $e->add(t('Invalid User.'), 'otp');
        }

        $email      = $userInfo->getUserEmail();
        $link       = $userInfo->getAttribute('google_authenticator');

        if (!$email) {
            $e->add(t('Email Address not set to your profile. Kindly request the admin to update your profile & login again.'), 'otp');
        }

        if (!$e->has() && !$link) {
            $g            = new GoogleAuthenticator();
            $secret       = $g->createSecret();

            $link         = $g->getQRCodeGoogleUrl(Config::get('concrete.site'), $secret, $email);
            $userInfo->setAttribute('google_authenticator', $link);
            $userInfo->setAttribute('google_authenticator_secret', $secret);
            $this->set('link', $link);
        }
        $this->setOTPUsername($u->getUserName());
        $u->logout(true);

        if ($e->has()) {
            $this->set('errors', $e->getList());
        }

        $this->set('task', 'otp_screen');
        $this->set('token', $vth->generate('otp'));
        $this->set('requiresOTP', true);
    }

    private function setOTPUsername($username)
    {
        setcookie('otp', MD5($username), time() + (60 * 5) , "/");
    }
    private function hasDashboardAccess($user)
    {
        foreach ($user->getUserGroups() as $group) {
            if(in_array($group, [ADMIN_GROUP_ID])) {
                return true;
            }
        }

        if($user->isSuperUser()) {
            return true;
        }

        return false;
    }
    private function getOTPUsername()
    {
        return isset($_COOKIE['otp']) ? $_COOKIE['otp'] : null;
    }

    private function clearOTPUsername()
    {
        setcookie('otp', '', time() - 3600 , "/");
    }
    public function login_via_google_authenticator()
    {
        $vs         = Loader::helper('validation/strings');
        $th         = new Text();
        $token      = new Token();
        $e          = new ErrorList();
        $otp        = $th->sanitize($this->post('otp'));
        $otpToken   = $th->sanitize($this->post('otp_token'));
        $username   = $this->getOTPUsername();

        try {
            if (!$username) {
                $e->add(t('Could not set user data.'), 'otp');
            }

            if(!$token->validate('otp', $otpToken)) {
                $e->add(t('Invalid Form Token.'), 'otp');
            }

            if ((!$vs->notempty($this->post('otp')))) {
                $e->add(t('Please enter your OTP.'), 'otp');
            }

            if(!$e->has()) {
                /** @var UserInfo $user */
                $userList = new \Concrete\Core\User\UserList();
                $userList->filter('MD5(u.uName)', $username, '=');
                $user = reset($userList->get(1));

                if (!$user) {
                    $e->add(t('Invalid code entered.'), 'otp');
                }

                if($user) {
                    $secret = $user->getAttribute('google_authenticator_secret');

                    $ga = new GoogleAuthenticator();
                    if (!$ga->verifyCode($secret, $otp, 2)) {
                        $e->add(t('Invalid code entered.'), 'otp');
                    }
                }

                if (!$e->has()) {
                    $singleUser = User::getByUserID($user->getUserID());
                    if (!($this->hasDashboardAccess($singleUser))) {
                        $this->redirect('/login');
                    }

                    $this->clearOTPUsername();

                    $u = User::getByUserID($user->getUserID(), true);

                    $loginData['success'] = 1;
                    $loginData['msg']     = t('Login Successful');
                    $loginData['uID']     = intval($user->getUserID());

                    $at = AuthenticationType::getByHandle('concrete');
                    $this->finishAuthentication($at, $u);
                    $this->redirect('/');
                }
            }

        } catch (Exception $ex) {
            $e->add($ex);
        }

        $this->set('errorsList', $e->getList());
        $this->set('task', 'otp_screen');
        $this->set('requiresOTP', true);
        $this->set('token', $token->generate('otp'));
    }
}
