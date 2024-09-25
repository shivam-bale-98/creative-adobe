<?php
namespace Concrete\Package\Formidable\Src\Formidable\Forms;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\ItemList\Database\ItemList;
use \Pagerfanta\Adapter\DoctrineDbalAdapter;
use Concrete\Package\Formidable\Src\Formidable\Forms\Form;

class FormList extends ItemList
{
    protected $autoSortColumns = [
        'form.formID',
        'form.formDateAdded',
    ];

    public function createQuery()
    {
        $this->query->select('form.formID')->from('FormidableForm', 'form')->groupBy('form.formID');
    }

    public function filterBySite()
    {
        $site = app()->make('site')->getActiveSiteForEditing();
        $this->query->andWhere('form.siteID = :formSiteID');
        $this->query->setParameter('formSiteID', $site->getSiteID());
    }

    public function filterByDateAdded($from = null, $to = null)
    {
        if (!empty($from)) {
            if (!is_object($from)) {
                $from = (new \DateTime($from))->format("Y-m-d");
            }
            $this->query->andWhere('form.formDateAdded >= :formDateAddedFrom');
            $this->query->setParameter('formDateAddedFrom', $from);
        }
        if (!empty($to)) {
            if (!is_object($to)) {
                $to = (new \DateTime($to))->format("Y-m-d");
            }
            $this->query->andWhere('form.formDateAdded <= :formDateAddedTo');
            $this->query->setParameter('formDateAddedTo', $to);
        }
    }

    public function filterByDateModified($from = null, $to = null)
    {
        if (!empty($from)) {
            if (!is_object($from)) {
                $from = (new \DateTime($from))->format("Y-m-d");
            }
            $this->query->andWhere('form.formDateModified >= :formDateModifiedFrom');
            $this->query->setParameter('formDateModifiedFrom', $from);
        }
        if (!empty($to)) {
            if (!is_object($to)) {
                $to = (new \DateTime($to))->format("Y-m-d");
            }
            $this->query->andWhere('form.formDateModified <= :formDateModifiedTo');
            $this->query->setParameter('formDateModifiedTo', $to);
        }
    }

    public function filterByKeywords($keywords)
    {
        $expressions = [
            $this->query->expr()->like('form.formHandle', ':keywords'),
            $this->query->expr()->like('form.formName', ':keywords'),
        ];
        $expr = $this->query->expr();
        $this->query->andWhere(call_user_func_array([$expr, 'orX'], $expressions));
        $this->query->setParameter('keywords', '%' . $keywords . '%');
    }

    public function filterByPrivacyEnabled($privacy = 1, $comparison = '=')
    {
        $this->query->andWhere('form.formPrivacy '.$comparison.' :formPrivacy');
        $this->query->setParameter('formPrivacy', $privacy);
    }

    public function filterByPrivacyRemove($remove = 1, $comparison = '=')
    {
        $this->query->andWhere('form.formPrivacyRemove '.$comparison.' :formPrivacyRemove');
        $this->query->setParameter('formPrivacyRemove', $remove);
    }

    public function filterByEnabled($eneabled = 1, $comparison = '=')
    {
        $this->query->andWhere('form.formEnabled '.$comparison.' :formEnabled');
        $this->query->setParameter('formEnabled', $eneabled);
    }

    public function getColumns()
    {
        return Form::getColumns();
    }
    public function getAdvancedColumns()
    {
        return Form::getAdvancedColumns();
    }

    public function getResult($queryRow)
    {
        return Form::getByID($queryRow['formID']);
    }

    protected function createPaginationObject()
    {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct form.formID)')->setMaxResults(1);
        });
        $pagination = new Pagination($this, $adapter);
        return $pagination;
    }

    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();
        return $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct form.formID)')->setMaxResults(1)->execute()->fetchColumn();
    }

    public static function getOptionList()
    {
        $forms = [];
        $list = new FormList();
        $list->filterBySite(app()->make('site')->getActiveSiteForEditing());
        $list->filterByEnabled();
        $list->sortBy('formHandle', 'asc');
        $items = $list->getResults();
        foreach($items as $item) {
            $forms[$item->getItemID()] = $item->getName();
        }
        return $forms;
    }
}