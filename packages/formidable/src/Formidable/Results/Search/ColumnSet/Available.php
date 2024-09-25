<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet\Column\DateModifiedColumn;
use Concrete\Core\Search\Column\Column;

class Available extends DefaultSet
{
    public function __construct()
    {
        parent::__construct();

        $this->addColumn(new Column('result.resultIP', t('IP'), 'getIP'));
        $this->addColumn(new Column('result.resultBrowser', t('Browser'), 'getBrowser'));
        $this->addColumn(new Column('result.resultOperatingSystem', t('Operating System'), 'getOperatingSystem'));
        $this->addColumn(new Column('result.resultResolution', t('Resolution'), 'getResolution'));
        $this->addColumn(new Column('result.resultDevice', t('Device'), 'getDevice'));
        $this->addColumn(new Column('result.resultLocale', t('Locale'), ['Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet\DefaultSet', 'getLocale']));
        $this->addColumn(new Column('result.formID', t('FormID'), 'getFormID'));
        $this->addColumn(new Column('result.formName', t('Form Name'), ['Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet\DefaultSet', 'getFormName']));
        $this->addColumn(new Column('result.resultPage', t('Page'), ['Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet\DefaultSet', 'getPage'], true));
        $this->addColumn(new Column('result.resultUser', t('User'), ['Concrete\Package\Formidable\Src\Formidable\Results\Search\ColumnSet\DefaultSet', 'getUser'], true));        
        $this->addColumn(new DateModifiedColumn());
        $this->getColumnsForElements();        
    }
}

