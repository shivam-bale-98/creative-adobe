<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;
use Concrete\Core\Captcha\Library as SystemCaptchaLibrary;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Http\ResponseAssetGroup;

class Captcha extends Element {

    public function getGroupHandle()
    {
        return 'handling';
    }

    public function getName()
    {
        return t('Captcha');
    }

    public function getDescription()
    {
        return t('Verification by captcha');
    }

    public function getEditableOptions()
    {
        $options = [
            // disable
            'view' => false,
            'option' => false,
            'default' => false,
            'range' => false,

            // enable
            'help' => true,

            // other
            'searchable' => false,
            'dependencies' => false,
        ];
        return array_merge(parent::getElementEditableOptions(), $options);
    }

    public function field()
    {
        $this->element->registerAsset('css', 'core/frontend/captcha');

        $captcha = $this->app->make('helper/validation/captcha');

        $input = $display = '';

        $config = $this->app->make('config');

        // because captcha just "echos" we need to capture this into a variable.
        // not the best way
        ob_start();
        $captcha->showInput();
        $input = ob_get_contents();
        ob_end_clean();
        if (isset($input) && !empty($input)) {
            $this->setField('input', $input);
        }

        //do the same for the picture..
        ob_start();
        $captcha->display();
        $display = ob_get_contents();
        ob_end_clean();
        if (isset($display) && !empty($display)) {
            $this->setField('display', $display);
        }

        /*
        // this needs to be below the "showInput" method of the recaptcha because of the assets
        $scl = SystemCaptchaLibrary::getActive();
        if ($scl->getSystemCaptchaLibraryHandle() == 'recaptchaV3') {

            // clear normal recaptcha
            $responseAssets = ResponseAssetGroup::get();
            $responseAssets->markAssetAsIncluded('recaptcha_v3');

            $config = $this->app->make('config');

            $assetUrl = $config->get('captcha.recaptcha_v3.url.javascript_asset');
            $assetUrl = str_replace('render=explicit', 'render='.$config->get('captcha.recaptcha_v3.site_key'), $assetUrl);

            $as = AssetList::getInstance();
            $as->register('javascript', 'recaptcha_api_form', $assetUrl, ['local' => false]);
            $this->element->registerAsset('javascript', 'recaptcha_api_form');


            $javascript = <<<JAVASCRIPT
            var refresh_recaptcha = function() {
                grecaptcha.ready(function() {
                    grecaptcha.execute('%s', {action: 'submit'}).then(function(token) {
                        $('#g-recaptcha-response').val(token);
                    });
                });
            }
            JAVASCRIPT;
            $javascript = str_replace(['%s'], [$config->get('captcha.recaptcha_v3.site_key')], $javascript);

            $handle = $this->element->getHandle();
            $as->register('javascript-inline', 'recaptcha-'.$handle, $javascript, ['minify' => true, 'combine' => true], $this->getPackageHandle());
            $this->element->registerAsset('javascript-inline', 'recaptcha-'.$handle);
        }
        */

        // this needs to be below the "showInput" method of the recaptcha because of the assets
        $scl = SystemCaptchaLibrary::getActive();
        if ($scl->getSystemCaptchaLibraryHandle() == 'recaptchaV3') {

            $javascript = <<<JAVASCRIPT
            var refresh_recaptcha = function() {
                grecaptcha.ready(function() {
                    grecaptcha.execute($(this).data("clientId"), {action: 'submit'}).then(function(token) {
                        $('#g-recaptcha-response').val(token);
                    });
                });
            }
            JAVASCRIPT;

            $handle = $this->element->getHandle();
            $as = AssetList::getInstance();
            $as->register('javascript-inline', 'recaptcha-'.$handle, $javascript, ['minify' => true, 'combine' => true], $this->getPackageHandle());
            $this->element->registerAsset('javascript-inline', 'recaptcha-'.$handle);
        }

        return '
            <div class="row">
                '.(!empty($display)?'<div class="col-auto captcha-display">'.$display.'</div>':'').'
                <div class="col captcha-input">'.$input.'</div>
            </div>
        ';
    }

    public function validate()
    {
        $e = $this->app->make(ErrorList::class);

        if ($this->element->isRequired()) {
            $captcha = $this->app->make('helper/validation/captcha');
            if (!$captcha->check()) {
                $e->add(t('Field "%s" is empty or invalid', $this->element->getName()));
            }
        }
        return $e;
    }
}