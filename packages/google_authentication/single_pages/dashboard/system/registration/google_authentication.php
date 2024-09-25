<?php

use Concrete\Core\Config\ConfigStore;
use Concrete\Core\Package\Package;

$vth = new \Concrete\Core\Validation\CSRF\Token();

/* @var Concrete\Core\Config\Repository\Liaison $config */
$config = Package::getByHandle('google_authentication')->getConfig();
$google_twofa = $config->get('google_authentication.ENABLE_GOOGLE_2FA');


?>
<div id="ccm-dashboard-content-inner">
    <form method="post" id="google_auth" name="google_auth" action="<?php echo \Concrete\Core\View\View::url('/dashboard/system/registration/google_authentication')?>">
        <?php echo $vth->output(); ?>
        <div class="form-group">
            <label id="optionsCheckboxes" for="public_profiles" class="control-label">Google Two Factor Authentication</label>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="google_twofa" value="1" <?= ($google_twofa) ? 'checked' : ''; ?> />

                    <span>Enable Google Two Factor Authentication.</span>
                </label>
            </div>
        </div>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button class="pull-right btn btn-primary" type="submit">Save</button>
            </div>
        </div>
    </form>
</div>