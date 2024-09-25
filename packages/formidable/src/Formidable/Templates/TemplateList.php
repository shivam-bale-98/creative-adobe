<?php
namespace Concrete\Package\Formidable\Src\Formidable\Templates;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\ItemList\Database\ItemList;
use \Pagerfanta\Adapter\DoctrineDbalAdapter;
use Concrete\Package\Formidable\Src\Formidable\Templates\Template;

class TemplateList extends ItemList
{
    protected $autoSortColumns = [
        'template.templateID',
        'template.templateDateAdded',
    ];

    public function createQuery()
    {
        $this->query->select('template.templateID')->from('FormidableTemplate', 'template')->groupBy('template.templateID');
    }

    public function filterBySite()
    {
        $site = app()->make('site')->getActiveSiteForEditing();
        $this->query->andWhere('template.siteID = :templateSiteID');
        $this->query->setParameter('templateSiteID', $site->getSiteID());
    }

    public function filterByDateAdded($from = null, $to = null)
    {
        if (!empty($from)) {
            if (!is_object($from)) {
                $from = (new \DateTime($from))->format("Y-m-d");
            }
            $this->query->andWhere('template.templateDateAdded >= :templateDateAddedFrom');
            $this->query->setParameter('templateDateAddedFrom', $from);
        }
        if (!empty($to)) {
            if (!is_object($to)) {
                $to = (new \DateTime($to))->format("Y-m-d");
            }
            $this->query->andWhere('template.templateDateAdded <= :templateDateAddedTo');
            $this->query->setParameter('templateDateAddedTo', $to);
        }
    }

    public function filterByDateModified($from = null, $to = null)
    {
        if (!empty($from)) {
            if (!is_object($from)) {
                $from = (new \DateTime($from))->format("Y-m-d");
            }
            $this->query->andWhere('template.templateDateModified >= :templateDateModifiedFrom');
            $this->query->setParameter('templateDateModifiedFrom', $from);
        }
        if (!empty($to)) {
            if (!is_object($to)) {
                $to = (new \DateTime($to))->format("Y-m-d");
            }
            $this->query->andWhere('template.templateDateModified <= :templateDateModifiedTo');
            $this->query->setParameter('templateDateModifiedTo', $to);
        }
    }

    public function filterByKeywords($keywords)
    {
        $expressions = [
            $this->query->expr()->like('template.templateHandle', ':keywords'),
            $this->query->expr()->like('template.templateName', ':keywords'),
        ];
        $expr = $this->query->expr();
        $this->query->andWhere(call_user_func_array([$expr, 'orX'], $expressions));
        $this->query->setParameter('keywords', '%' . $keywords . '%');
    }

    public function getColumns()
    {
        return Template::getColumns();
    }
    public function getAdvancedColumns()
    {
        return Template::getAdvancedColumns();
    }

    public function getResult($queryRow)
    {
        return Template::getByID($queryRow['templateID']);
    }

    protected function createPaginationObject()
    {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct template.templateID)')->setMaxResults(1);
        });
        $pagination = new Pagination($this, $adapter);
        return $pagination;
    }

    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();
        return $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct template.templateID)')->setMaxResults(1)->execute()->fetchColumn();
    }

    public static function getOptionList()
    {
        $templates = [];
        $list = new TemplateList();
        $list->filterBySite(app()->make('site')->getActiveSiteForEditing());
        $list->sortBy('templateHandle', 'asc');
        $items = $list->getResults();
        foreach($items as $item) {
            $templates[$item->getItemID()] = $item->getName();
        }
        return $templates;
    }
}