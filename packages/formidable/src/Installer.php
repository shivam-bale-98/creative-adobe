<?php
namespace Concrete\Package\Formidable\Src;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Support\Facade\DatabaseORM;
use Concrete\Package\Formidable\Src\Formidable\Formidable;
use Concrete\Package\Formidable\Src\Installer\Pages;
use Concrete\Package\Formidable\Src\Installer\Blocks;
use Concrete\Core\Http\Request;
use Concrete\Package\Formidable\Src\Formidable\Forms\FormList;
use Concrete\Package\Formidable\Src\Formidable\Templates\TemplateList;

class Installer
{
    public function install($pkg = null)
    {
        if (!$pkg) {
            $formidable = new Formidable();
            $pkg = $formidable->getPackageObject();
        }

        // Install pages
        $p = new Pages();
        $p->setPackage($pkg);
        $p->setPage('/dashboard/formidable');
        $p->setPage('/dashboard/formidable/results');
        $p->setPage('/dashboard/formidable/forms');
        $p->setPage('/dashboard/formidable/templates');
        $p->install();

        // Install Blocks
        $bt = new Blocks();
        $bt->setPackage($pkg);
        $bt->setBlock('formidable_form');
        $bt->install();
    }

    public function upgrade($pkg = null)
    {
        if (!$pkg) {
            $formidable = new Formidable();
            $pkg = $formidable->getPackageObject();
        }
        $this->install($pkg);

        $site = Application::getFacadeApplication()->make('site')->getActiveSiteForEditing();

        // add site to forms
        $list = new FormList();
        $list->sortBy('formHandle', 'asc');
        $items = $list->getResults();
        if (count($items)) {
            foreach($items as $item) {
                if ($item->getSite()) {
                    continue;
                }
                $item->setSite($site);
                $item->save();
            }
        }

        // add site to templates
        $list = new TemplateList();
        $list->sortBy('templateHandle', 'asc');
        $items = $list->getResults();
        if (count($items)) {
            foreach($items as $item) {
                if ($item->getSite()) {
                    continue;
                }
                $item->setSite($site);
                $item->save();
            }
        }
    }


    public function uninstall($pkg = null)
    {
        $app = Application::getFacadeApplication();
        $request = $app->make(Request::class);
        if ((float)$request->request->get('removeContent') == 1) {
            $db = $app->make('database')->connection();

            $db->query("SET foreign_key_checks = 0");

            $db->query('DROP TABLE IF EXISTS FormidableForm');
            $db->query('DROP TABLE IF EXISTS FormidableFormColumn');
            $db->query('DROP TABLE IF EXISTS FormidableFormElement');
            $db->query('DROP TABLE IF EXISTS FormidableFormElementProperty');
            $db->query('DROP TABLE IF EXISTS FormidableFormRow');
            $db->query('DROP TABLE IF EXISTS FormidableMail');
            $db->query('DROP TABLE IF EXISTS FormidableResult');
            $db->query('DROP TABLE IF EXISTS FormidableResultElement');
            $db->query('DROP TABLE IF EXISTS FormidableResultLog');
            $db->query('DROP TABLE IF EXISTS FormidableTemplate');

            $db->query('DROP TABLE IF EXISTS SavedFormidableSearchQueries');

            $db->query("SET foreign_key_checks = 1");
        }
    }

    public function clearCache()
    {
        Localization::clearCache();
    }

    public function refreshEntities()
    {
        $em = DatabaseORM::entityManager();
        $manager = new DatabaseStructureManager($em);
        $manager->refreshEntities();
    }
}