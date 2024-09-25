<?php
namespace Concrete\Package\Formidable\Src\Formidable;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Utility\Service\Text;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Package\PackageService;
use Concrete\Package\Formidable\Src\Formidable\Data;
use \Exception;

class Formidable {

    protected $pkgHandle = 'formidable';

    public $app;
    public $data;

    public function __construct()
    {
        $this->app = Application::getFacadeApplication();
        $this->data = new Data();
    }

    public function getPackageHandle()
    {
        return $this->pkgHandle;
    }

    public function getPackageObject()
    {
        return $this->app->make(PackageService::class)->getByHandle($this->getPackageHandle());
    }

    public static function getPackageVersion()
    {
        $pkg = (new self)->getPackageObject();
        if ($pkg) {
            return $pkg->getPackageVersion();
        }
        return false;
    }

    public static function getElementGroupName($handle = '')
    {
        $available = [
            'basic' => t('Basic elements'),
            'advanced' => t('Advanced elements'),
            'layout' => t('Layout elements'),
            'handling' => t('Handling elements'),
            'custom' => t('Custom elements'),
        ];
        if (empty($handle)) {
            return $available;
        }
        if (isset($available[$handle])) {
            return $available[$handle];
        }
        return $available['custom'];
    }

    public static function getElementTypes()
    {
        $formidable = new self();

        $available = [];

        $pkg = $formidable->getPackageObject();
        if ($pkg) {
            if (file_exists($pkg->getPackagePath().'/src/Formidable/Elements/Types')) {
                $files = scandir($pkg->getPackagePath().'/src/Formidable/Elements/Types');
                if (count($files)) {
                    foreach ($files as $file) {
                        if (in_array($file, ['.', '..'])) {
                            continue;
                        }
                        $class = '\Concrete\Package\Formidable\Src\Formidable\Elements\Types\\'.str_replace('.php', '', $file);
                        $el = new $class();
                        if (is_object($el)) {
                            $available[$el->getHandle()] = $el;
                        }
                    }
                }
            }
        }

        if (file_exists(DIR_APPLICATION.'/src/Formidable/Elements/Types')) {
            $files = scandir(DIR_APPLICATION.'/src/Formidable/Elements/Types');
            if (count($files)) {
                foreach ($files as $file) {
                    if (in_array($file, ['.', '..'])) {
                        continue;
                    }
                    require_once(DIR_APPLICATION.'/src/Formidable/Elements/Types/'.$file);
                    $class = '\Application\Src\Formidable\Elements\Types\\'.str_replace('.php', '', $file);
                    $el = new $class();
                    if (is_object($el)) {
                        $available[$el->getHandle()] = $el;
                    }
                }
            }
        }

        if (!count($available)) {
            throw new Exception(t('No element-types found!'));
        }

        return $available;
    }

    public static function getElementTypeByHandle($handle)
    {
        $formidable = new self();

        $class = '';
        $className = $formidable->app->make(Text::class)->camelcase($handle);

        if (file_exists(DIR_APPLICATION.'/src/Formidable/Elements/Types/'.$className.'.php')) {
            require_once(DIR_APPLICATION.'/src/Formidable/Elements/Types/'.$className.'.php');
            $class = ucfirst(DIRNAME_APPLICATION).'\Src\Formidable\Elements\Types\\'.$className;
        }
        else {
            $pkg = $formidable->getPackageObject();
            if ($pkg) {
                if (file_exists($pkg->getPackagePath().'/src/Formidable/Elements/Types/'.$className.'.php')) {
                    $class = '\Concrete\Package\Formidable\Src\Formidable\Elements\Types\\'.$className;
                }
            }
        }
        if (empty($class)) {
            throw new Exception(t('Element with handle "%s" not found!', $handle));
        }

        return new $class();
    }

    public static function getMethodForValue($column)
    {
        $method = $handle = '';
        if (!empty($column)) {
            if (strpos($column, 'akID_') !== false) {
                $method = 'getAttribute';
                $handle = substr($column, strlen('akID_'));
            }
            else {
                switch($column) {

                    // Page Methods
                    case 'cID':
                        $method = 'getCollectionID';
                    break;
                    case 'cName':
                        $method = 'getCollectionName';
                    break;
                    case 'cHandle':
                        $method = 'getCollectionHandle';
                    break;
                    case 'cHandle':
                        $method = 'getCollectionHandle';
                    break;
                    case 'cDateAdded':
                        $method = 'getCollectionDateAdded';
                    break;

                    // User Methods
                    case 'uID':
                        $method = 'getUserID';
                    break;
                    case 'uName':
                        $method = 'getUserName';
                    break;
                    case 'uEmail':
                        $method = 'getUserEmail';
                    break;
                    case 'uDateAdded':
                        $method = 'getUserDateAdded';
                    break;

                }
            }
        }
        return [$method, $handle];
    }
}