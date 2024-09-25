<?php    
/**
 * Clear Clipboard
 * For concrete5
 * @author 	    Shahroq <shahroq \at\ yahoo.com>
 * @copyright  	Copyright 2018 Shahroq
 * @link        https://www.concrete5.org/marketplace/addons/clear-clipboard-job
 */

namespace Concrete\Package\WhaleClearClipboard;

defined('C5_EXECUTE') or die('Access denied.');

use Concrete\Core\Job\Job;
use Concrete\Core\Package\Package;

class Controller extends Package 
{

    protected $pkgHandle = 'whale_clear_clipboard';
    protected $appVersionRequired = '5.7.3';
    protected $pkgVersion = '1.0.1'; 
    
    public function getPackageName() 
    {
        return t("Clear Clipboard"); 
    }	
    
    public function getPackageDescription() 
    {
        return t("A job that clears the entire clipboard");
    }
    
    public function install() 
    {
        $pkg = parent::install();
        
        Job::installByPackage('whale_clear_clipboard', $pkg);
    }
}
