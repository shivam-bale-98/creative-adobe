<?php

namespace Concrete\Package\GoogleAuthentication;

use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Package\Package;
use Concrete\Core\Attribute\Key\UserKey as UserAttributeKey;
/**
 * This is the main controller for the package which controls the  functionality like Install/Uninstall etc.
 *
 * @author AN 05/16/2016
 */
class Controller extends Package
{

    /**
     * Protected data members for controlling the instance of the package
     */
    protected $pkgHandle = 'google_authentication';
    protected $appVersionRequired = '5.7.6';
    protected $pkgVersion = '1.0';

    /**
     * This function returns the functionality description ofthe package.
     *
     * @param void
     * @return string $description
     * @author AN 05/16/2016
     */
    public function getPackageDescription()
    {
        return t("Enable Google Two Factor Authentication");
    }

    /**
     * This function returns the name of the package.
     *
     * @param void
     * @return string $name
     * @author AN 05/16/2016
     */
    public function getPackageName()
    {
        return t("Google Authentication");
    }

    /**
     * This function is executed during initial installation of the package.
     *
     * @param void
     * @return void
     * @author AN 05/16/2016
     */
    public function install()
    {
        $pkg = parent::install();

        // Install Single Pages
        $this->install_single_pages($pkg);
    }

    /**
     * This function is executed during uninstallation of the package.
     *
     * @param void
     * @return void
     * @author AN 05/16/2016
     */
    public function uninstall()
    {
        $pkg = parent::uninstall();
    }

    /**
     * This function is used to install single pages.
     *
     * @param type $pkg
     * @return void
     * @author AN 05/16/2016
     */
    function install_single_pages($pkg)
    {
        $directoryDefault = SinglePage::add('/dashboard/system/registration/google_authentication', $pkg);
        $directoryDefault->update(array('cName' => t('Google Authentication'), 'cDescription' => t('Google Authentication')));

        $pkg = Package::getByHandle('google_authentication');
        $app = Facade::getFacadeApplication();
        $typeFactory = $app->make(TypeFactory::class);
        $type = $typeFactory->getByHandle('text');

        //create user attributes required for the authentication
        $ak = UserAttributeKey::getByHandle('google_authenticator');
        if(!is_object($ak)) {
            UserAttributeKey::add(
                $type,
                array('akHandle' => 'google_authenticator', 'akName' => t('Google Authenticator'), 'akIsSearchable' => true), $pkg
            );
        }

        $akk = UserAttributeKey::getByHandle('google_authenticator_secret');

        if(!is_object($akk)) {
            UserAttributeKey::add(
                $type,
                array('akHandle' => 'google_authenticator_secret', 'akName' => t('Google Authenticator Secret'), 'akIsSearchable' => true), $pkg
            );
        }
    }

}