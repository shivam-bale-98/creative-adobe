<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet\Column;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class DateAddedColumn extends Column implements PagerColumnInterface
{
    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'result.resultDateAdded';
    }

    public function getColumnName()
    {
        return t('Date Added');
    }

    public function getColumnCallback()
    {
        return ['Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet\DefaultSet', 'getDateAdded'];
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(result.resultDateAdded, result.resultID) %s (:sortDate, :sortID)', $sort);
        $query->setParameter('sortDate', $mixed->getDateAdded());
        $query->setParameter('sortID', $mixed->getItemID());
        $this->andWhereNotExists($query, $where);
    }

}
