<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet\Column;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class DateModifiedColumn extends Column implements PagerColumnInterface
{
    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'result.resultDateModified';
    }

    public function getColumnName()
    {
        return t('Date Modified');
    }

    public function getColumnCallback()
    {
        return ['Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet\DefaultSet', 'getDateModified'];
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(result.resultDateModified, result.resultID) %s (:sortDate, :sortID)', $sort);
        $query->setParameter('sortDate', $mixed->getDateModified());
        $query->setParameter('sortID', $mixed->getItemID());
        $this->andWhereNotExists($query, $where);
    }


}
