<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\ItemList\Database\ItemList;
use Concrete\Package\Formidable\Src\Formidable\Forms\Form;
use Pagerfanta\Doctrine\DBAL\QueryAdapter;
use Concrete\Package\Formidable\Src\Formidable\Results\Result;

class ResultList extends ItemList
{
    protected $autoSortColumns = [
        'result.resultID',
        'result.resultDateAdded',
    ];

    protected $sortColumnParameter = 'fSearchDefaultSort';
    protected $sortDirectionParameter = 'fSearchDefaultSortDirection';

    public function createQuery()
    {
        $this->query->select('result.resultID')->from('FormidableResult', 'result')->groupBy('result.resultID');
    }

    public function filterByForm($form)
    {
        if (!is_object($form)) {
            $form = Form::getByID($form);
        }
        if (!is_object($form)) {
            return;
        }
        $this->query->andWhere('result.formID = :resultFormID');
        $this->query->setParameter('resultFormID', $form->getItemID());
    }

    public function filterByIP($ip)
    {
        $this->query->andWhere('result.resultIP = :resultIP');
        $this->query->setParameter('resultIP', $ip);
    }

    public function filterByUser($user)
    {
        $this->query->andWhere('result.resultUser = :resultUser');
        $this->query->setParameter('resultUser', $user);
    }

    public function filterByPage($page)
    {
        $this->query->andWhere('result.resultPage = :resultPage');
        $this->query->setParameter('resultPage', $page);
    }

    public function filterByDateAdded($from = null, $to = null)
    {
        if (!empty($from)) {
            if (!is_object($from)) {
                $from = new \DateTime($from);
            }
            $this->query->andWhere('result.resultDateAdded >= :resultDateAddedFrom');
            $this->query->setParameter('resultDateAddedFrom', $from->format("Y-m-d"));
        }
        if (!empty($to)) {
            if (!is_object($to)) {
                $to = new \DateTime($to);
            }
            $this->query->andWhere('result.resultDateAdded <= :resultDateAddedTo');
            $this->query->setParameter('resultDateAddedTo', $to->format("Y-m-d"));
        }
    }

    public function filterByDateModified($from = null, $to = null)
    {
        if (!empty($from)) {
            if (!is_object($from)) {
                $from = new \DateTime($from);
            }
            $this->query->andWhere('result.resultDateModified >= :resultDateModifiedFrom');
            $this->query->setParameter('resultDateModifiedFrom', $from->format("Y-m-d"));
        }
        if (!empty($to)) {
            if (!is_object($to)) {
                $to = new \DateTime($to);
            }
            $this->query->andWhere('result.resultDateModified <= :resultDateModifiedTo');
            $this->query->setParameter('resultDateModifiedTo', $to->format("Y-m-d"));
        }
    }

    public function filterByKeywords($keywords)
    {
        $this->query->leftJoin('result', 'FormidableResultElement', 'resultElement', 'result.resultID = resultElement.resultID');

        $expressions = [
            $this->query->expr()->like('result.resultBrowser', ':keywords'),
            $this->query->expr()->like('result.resultIP', ':keywords'),
            $this->query->expr()->like('resultElement.resultElementValueDisplay', ':keywords'),
        ];
        $expr = $this->query->expr();
        $this->query->andWhere(call_user_func_array([$expr, 'orX'], $expressions));
        $this->query->setParameter('keywords', '%' . $keywords . '%');
    }

    public function filterByElement($element, $value)
    {
        $element->filterResult($this, $value);
    }

    /*
    public function getColumns()
    {
        return Result::getColumns();
    }
    public function getAdvancedColumns()
    {
        return Result::getAdvancedColumns();
    }
    */

    public function getResult($queryRow)
    {
        return Result::getByID($queryRow['resultID']);
    }

    protected function createPaginationObject()
    {
        $adapter = new QueryAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct result.resultID)')->setMaxResults(1);
        });
        $pagination = new Pagination($this, $adapter);
        return $pagination;
    }

    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();
        return $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct result.resultID)')->setMaxResults(1)->execute()->fetchColumn();
    }
}