<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search\Column;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Search\Column\Column as ColumnParent;

class Column extends ColumnParent
{
    public function getColumnValue($obj)
    {
        $callback = $this->getColumnCallback();
        if (is_array($callback)) {
            // PHP 8.0 only allows static functions with call_user_func
            // So institate the callback if its not callable
            // (php 8 will return false on is_callable, < less than php 8 will return true)
            if (is_string($callback[0]) && !is_callable($callback)) {
                return call_user_func([new $callback[0],$callback[1]], $callback[2], $obj);
            }
            return call_user_func($callback, $obj);
        }

        return call_user_func([$obj, $callback]);
    }
}
