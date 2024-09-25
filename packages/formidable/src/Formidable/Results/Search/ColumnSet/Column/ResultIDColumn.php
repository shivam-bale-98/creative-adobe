<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet\Column;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class ResultIDColumn extends Column implements PagerColumnInterface
{
    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'result.resultID';
    }

    public function getColumnName()
    {
        return t('ID');
    }

    public function getColumnCallback()
    {
        return 'getItemID';
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('result.resultID %s :sortID', $sort);
        $query->setParameter('sortID', $mixed->getItemID());
        $this->andWhereNotExists($query, $where);
    }

}
