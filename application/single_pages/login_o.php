<?php

use Concrete\Core\Attribute\Key\Key;
use Concrete\Core\Http\ResponseAssetGroup;

defined('C5_EXECUTE') or die('Access denied.');

$r = ResponseAssetGroup::get();
$r->requireAsset('javascript', 'underscore');
$r->requireAsset('javascript', 'core/events');

$form = Loader::helper('form');

$captchaConfig = \Concrete\Core\Package\Package::getByHandle('ec_recaptcha')->getConfig();

if (isset($authType) && $authType) {
    $active = $authType;
    $activeAuths = array($authType);
} else {
    $active = null;
    $activeAuths = AuthenticationType::getList(true, true);
}
if (!isset($authTypeElement)) {
    $authTypeElement = null;
}
if (!isset($authTypeParams)) {
    $authTypeParams = null;
}
$image = date('Ymd') . '.jpg';

/* @var Key[] $required_attributes */

$attribute_mode = (isset($required_attributes) && count($required_attributes));

// See if we have a user object and if that user is registered
$loggedIn = !$attribute_mode && isset($user) && $user->isRegistered();

// Determine title
$title = t('Sign in.');

if ($attribute_mode) {
    $title = t('Required Attributes');
}

if ($loggedIn) {
    $title = 'Log Out.';
}

$task = $task ?? '';
$requiresOTP = $requiresOTP ?? '';
$errorsList = $errorsList ?? [];
$link = $link ?? '';
?>
 <?php   switch ($task) {
    case 'otp_screen':
    if ($requiresOTP) {
        $errorList = [];
        if ($errorsList && is_array($errorsList)) {
            foreach ($errorsList as $error) {
                /** @var \Concrete\Core\Error\ErrorList\Field\Field $field */
                /** @var \Concrete\Core\Error\ErrorList\Error\Error $error */
                $field = $error->getField();
                if ($field) {
                    $errorList[$field->getFieldElementName()] = $error->getMessage();
                } else {
                    $errorList['token'] = $error->getMessage();
                }
            }
        }
        $otpError = isset($errorList['otp']) ? $errorList['otp'] : '';
    ?>
    <div class="inner-block offset-top pb100">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <div class="login-page">

                        <div class="page-header">
                            <h1>Sign in.</h1>

                            <?php if($link) { ?>
                                <p class="small_text"><?= t('Scan the QR code and enter the code below.'); ?></p>
                            <?php } else { ?>
                                <p class="small_text"><?= t('Enter the code from Google Authenticator'); ?></p>
                            <?php } ?>

                            <div class="small_text">
                                <img src="<?php echo $link; ?>">
                            </div>
                        </div>

                        <div class="login-form">
                            <div class="row">
                                <div class="visible-xs ccm-authentication-type-select form-group text-center">
                                    <label>&nbsp;</label>
                                </div>
                            </div>
                            <div class="row login-row">
                                <div class="controls col-sm-12 col-xs-12">
                                    <div data-handle="concrete" class="authentication-type authentication-type-concrete active" style="display: block;">

                                        <form class="login-form form_section" method="post" action="<?php echo $this->url('/login', 'login_via_google_authenticator'); ?>">
                                            <input type="hidden" name="otp_token" value="<?php echo $token; ?>">
                                            <div class="form-group element element--placeholder <?php echo ($otpError) ? 'error' : ''; ?>">
                                                <label class="control-label" for="uName">Enter your OTP</label>
                                                <input autocomplete="off" type="text" name="otp" class="ccm-input-text form-control">
                                                <?php echo $otpError ? '<p class="text-danger error">' .$otpError . '</p>' : '';
                                                ?>
                                            </div>

                                            <div class="form-group">
                                                <button class="btn btn-primary pull-right"><span>Log in</span></button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php }
break;
default: ?>
    <div class="login-page">

    <?php
    $disclaimer = new Area('Disclaimer');
    if ($disclaimer->getTotalBlocksInArea($c) || $c->isEditMode()) { ?>
        <div class="ccm-login-disclaimer">
            <?=$disclaimer->display($c);?>
        </div>
    <?php } ?>
    <div class="col-sm-6 col-sm-offset-3">
        <h1><?= $title ?></h1>
    </div>
    <div class="col-sm-6 col-sm-offset-3 login-form">
        <div class="row">
            <div class="visible-xs ccm-authentication-type-select form-group text-center">
                <?php
                if ($attribute_mode) {
                    ?>
                    <i class="fa fa-question"></i>
                    <span><?= t('Attributes') ?></span>
                <?php

                } elseif (count($activeAuths) > 1) {
                    ?>
                    <select class="form-control col-xs-12">
                        <?php
                        foreach ($activeAuths as $auth) {
                            ?>
                            <option value="<?= $auth->getAuthenticationTypeHandle() ?>">
                                <?= $auth->getAuthenticationTypeDisplayName() ?>
                            </option>
                        <?php

                        }
                    ?>
                    </select>

                    <?php

                }
                ?>
                <label>&nbsp;</label>
            </div>
        </div>
        <div class="row login-row">
            <div <?php if ($loggedIn || count($activeAuths) < 2) {
    ?>style="display: none" <?php 
} ?> class="types col-sm-4 hidden-xs">
                <ul class="auth-types">
                    <?php
                    if ($attribute_mode) {
                        ?>
                        <li data-handle="required_attributes">
                            <i class="fa fa-question"></i>
                            <span><?= t('Attributes') ?></span>
                        </li>
                        <?php

                    } else {
                        /** @var AuthenticationType[] $activeAuths */
                        foreach ($activeAuths as $auth) {
                            ?>
                            <li data-handle="<?= $auth->getAuthenticationTypeHandle() ?>">
                                <?= $auth->getAuthenticationTypeIconHTML() ?>
                                <span><?= $auth->getAuthenticationTypeDisplayName() ?></span>
                            </li>
                        <?php

                        }
                    }
                    ?>
                </ul>
            </div>
            <div class="controls <?php if (count($activeAuths) < 2 || $loggedIn) {
    ?>col-sm-12<?php 
} else {
    ?>col-sm-8<?php 
} ?> col-xs-12">
                <?php
                if ($loggedIn) {
                    ?>
                    <div class="text-center">
                        <h3><?= t('You are already logged in.') ?></h3>
                        <?= Core::make('helper/navigation')->getLogInOutLink() ?>
                    </div>
                    <?php
                } elseif ($attribute_mode) {
                    $attribute_helper = new Concrete\Core\Form\Service\Widget\Attribute();
                    ?>
                    <form action="<?= View::action('fill_attributes') ?>" method="POST">
                        <div data-handle="required_attributes"
                             class="authentication-type authentication-type-required-attributes">
                            <div class="ccm-required-attribute-form"
                                 style="height:340px;overflow:auto;margin-bottom:20px;">
                                <?php
                                foreach ($required_attributes as $key) {
                                    echo $attribute_helper->display($key, true);
                                }
                    ?>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary pull-right"><?= t('Submit') ?></button>
                            </div>

                        </div>
                    </form>
                    <?php

                } else {
                    /** @var AuthenticationType[] $activeAuths */
                    foreach ($activeAuths as $auth) {
                        ?>
                        <div data-handle="<?= $auth->getAuthenticationTypeHandle() ?>"
                             class="authentication-type authentication-type-<?= $auth->getAuthenticationTypeHandle() ?>">
                            <?php $auth->renderForm($authTypeElement ?: 'form', $authTypeParams ?: array()) ?>
                        </div>
                    <?php

                    }
                }
                ?>
            </div>
        </div>
    </div>
     <div style="clear:both"></div>

    <style type="text/css">

        div.login-form hr {
            margin-top: 10px !important;
            margin-bottom: 5px !important;
        }

        ul.auth-types {
            margin: 20px 0px 0px 0px;
            padding: 0;
        }

        ul.auth-types > li > .fa,
        ul.auth-types > li svg,
        ul.auth-types > li .ccm-auth-type-icon {
            position: absolute;
            top: 2px;
            left: 0px;
        }

        ul.auth-types > li {
            list-style-type: none;
            cursor: pointer;
            padding-left: 25px;
            margin-bottom: 15px;
            transition: color .25s;
            position: relative;
        }

        ul.auth-types > li:hover {
            color: #cfcfcf;
        }

        ul.auth-types > li.active {
            font-weight: bold;
            cursor: auto;
        }
    </style>

    <script type="text/javascript">
        (function ($) {
            "use strict";

            var forms = $('div.controls').find('div.authentication-type').hide(),
                select = $('div.ccm-authentication-type-select > select');
            var types = $('ul.auth-types > li').each(function () {
                var me = $(this),
                    form = forms.filter('[data-handle="' + me.data('handle') + '"]');
                me.click(function () {
                    select.val(me.data('handle'));
                    if (typeof Concrete !== 'undefined') {
                        Concrete.event.fire('AuthenticationTypeSelected', me.data('handle'));
                    }

                    if (form.hasClass('active')) return;
                    types.removeClass('active');
                    me.addClass('active');
                    if (forms.filter('.active').length) {
                        forms.stop().filter('.active').removeClass('active').fadeOut(250, function () {
                            form.addClass('active').fadeIn(250);
                        });
                    } else {
                        form.addClass('active').show();
                    }
                });
            });

            select.change(function() {
                types.filter('[data-handle="' + $(this).val() + '"]').click();
            });
            types.first().click();

            $('ul.nav.nav-tabs > li > a').on('click', function () {
                var me = $(this);
                if (me.parent().hasClass('active')) return false;
                $('ul.nav.nav-tabs > li.active').removeClass('active');
                var at = me.attr('data-authType');
                me.parent().addClass('active');
                $('div.authTypes > div').hide().filter('[data-authType="' + at + '"]').show();
                return false;
            });

            <?php
            if (isset($lastAuthType)) {
                ?>
                $("ul.auth-types > li[data-handle='<?= $lastAuthType->getAuthenticationTypeHandle() ?>']")
                    .trigger("click");
                <?php

            }
            ?>
        })(jQuery);


        </script>
        <?php  if($captchaConfig && $captchaConfig->get('captcha.enable_login_captcha')) { ?>
            <script>
                var form = document.getElementById('login_form');
                var onSubmit = function (token) {
                form.submit();
                };

                var onloadCallback = function () {
                grecaptcha.render('login_button', {
                'sitekey': '<?php echo $captchaConfig->get('captcha.site_key');?>',
                'callback': onSubmit
                });
                };
            </script>
            <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
        <?php  } ?>

    </div>
<?php  break; } ?>
