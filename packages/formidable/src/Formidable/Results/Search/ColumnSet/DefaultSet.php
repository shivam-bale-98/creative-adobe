<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet\Column\ResultIDColumn;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet\Column\DateAddedColumn;
use Concrete\Core\Support\Facade\Application;

class DefaultSet extends ColumnSet
{
    public function __construct()
    {   
        $this->addColumn(new ResultIDColumn()); 
        $columns = $this->getAllColumnsBasedOnElements(); 
        foreach ($columns as $i => $column) {
            $this->addColumn($column);
            if ($i > 4) {
                break;
            }            
        }
        $this->addColumn(new DateAddedColumn());

        $default = $this->getColumnByKey('result.resultDateAdded');
        $this->setDefaultSortColumn($default, 'desc');
    }

    public static function getResultData($elementID, $result) 
    {      
        return $result->getElementDataByElement($elementID);
    }

    public static function getDateAdded($result)
    {
        return Application::getFacadeApplication()->make('helper/date')->formatDateTime($result->getDateAdded());
    }

    public static function getDateModified($result)
    {
        return Application::getFacadeApplication()->make('helper/date')->formatDateTime($result->getDateModified());
    }

    public static function getLocale($result)
    {
        return $result->getLocale(true);
    }    

    public static function getFormName($result)
    {
        $form = $result->getForm();        
        if (is_object($form)) {
            return $form->getName();
        }
    }

    public static function getUser($result)
    {
        $ui = $result->getUserObject();        
        if (is_object($ui)) {
            return $ui->getUserName();
        }
    }

    public static function getPage($result)
    {
        $p = $result->getPageObject();        
        if (is_object($p)) {
            return $p->getCollectionName();
        }
    }

}
