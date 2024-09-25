<?php

namespace Concrete\Package\GoogleAuthentication\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Package\Package;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Core;

class GoogleAuthentication extends DashboardPageController
{
    public function view()
    {
        if($this->isPost()) {
            $google_twofa = $this->post('google_twofa');
            $config = Package::getByHandle('google_authentication')->getConfig();
            $config->save('google_authentication.ENABLE_GOOGLE_2FA', $google_twofa);
        }
    }
}
?>
