<?php
namespace Concrete\Package\Formidable\Controller\Element\Results\Search\Preset;

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Dialog\Search\Preset\Delete as PresetDelete;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker as Permissions;

class Delete extends PresetDelete
{
    protected $viewPath = '/dialogs/search/preset/delete';

    protected function canAccess()
    {
        $c = Page::getByPath('/dashboard/formidable/results');
        if ($c && !$c->isError()) {
            $cp = new Permissions($c);
            return $cp->canViewPage();
        }
        return false;
    }

    public function getSavedSearchEntity()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository('\Concrete\Package\Formidable\Src\Formidable\Results\Search\SavedSearch');
        }

        return null;
    }
}
