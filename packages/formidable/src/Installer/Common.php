<?php
namespace Concrete\Package\Formidable\Src\Installer;

defined('C5_EXECUTE') or die('Access Denied.');

class Common
{
    protected $pkg = null;

    public function setPackage($pkg) {
        $this->pkg = $pkg;
    } 

    public function upgrade() {
        $this->install();
    }
}
