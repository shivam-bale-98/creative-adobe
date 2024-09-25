<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search\Result;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Search\Result\Result as SearchResult;

class Result extends SearchResult
{
    public function getItemDetails($item)
    {
        $node = new Item($this, $this->listColumns, $item);
        return $node;
    }

    public function getColumnDetails($column)
    {
        $node = new Column($this, $column);
        return $node;
    }
}
