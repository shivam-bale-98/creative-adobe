<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search\Result;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Search\Result\Item as SearchResultItem;
use Concrete\Core\Search\Result\Result as SearchResult;
use Concrete\Core\Search\Column\Set;

class Item extends SearchResultItem
{
    public $resultID;

    public function __construct(SearchResult $result, Set $columns, $item)
    {
        parent::__construct($result, $columns, $item);
        $this->populateDetails($item);
    }

    protected function populateDetails($item)
    {
        $this->resultID = $item->getItemID();
    }
}
