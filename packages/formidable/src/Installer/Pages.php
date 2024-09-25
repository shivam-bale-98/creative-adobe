<?php
namespace Concrete\Package\Formidable\Src\Installer;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single as SinglePage;

class Pages extends Common
{
    // Define pages by there path
    protected $pages = [
        //['path' => '/my_single_page'],
    ];

    public function setPage($path) {
        $this->pages[] = ['path' => $path];
    }

    public function install()
    {
        if (count($this->pages)) {
            foreach ($this->pages as $page) {

                $path = $page['path'];
                if (strpos($path, '/') != 0) {
                    $path = '/'.$path;
                }

                // Check if controller exists
                if (!file_exists($this->pkg->getPackagePath().'/single_pages'.$path.'.php')) {
                    continue;
                }

                // Then install
                $p = Page::getByPath($path);
                if (!$p || (int)$p->getCollectionID() == 0 ) {
                    $p = SinglePage::add($path, $this->pkg);
                }
            }
        }
    }
}
