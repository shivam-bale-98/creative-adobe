<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search\Field\Field;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Results\ResultList;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;

class DateModifiedField extends AbstractField
{
    protected $requestVariables = [
        'date_modified_from_dt',
        'date_modified_from_h',
        'date_modified_from_m',
        'date_modified_from_a',
        'date_modified_to_dt',
        'date_modified_to_h',
        'date_modified_to_m',
        'date_modified_to_a',
    ];

    public function getKey()
    {
        return 'date_modified';
    }

    public function getDisplayName()
    {
        return t('Last Modified');
    }

    public function renderSearchField()
    {
        $wdt = Application::getFacadeApplication()->make('helper/form/date_time');
        return $wdt->datetime('date_modified_from', $wdt->translate('date_modified_from', $this->data)) . t('to') . $wdt->datetime('date_modified_to', $wdt->translate('date_modified_to', $this->data));

    }

    /**
     * @param ResultList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $wdt = Application::getFacadeApplication()->make('helper/form/date_time');
        /* @var $wdt \Concrete\Core\Form\Service\Widget\DateTime */
        $dateFrom = $wdt->translate('date_modified_from', $this->data);
        if ($dateFrom) {
            $list->filterByDateModified($dateFrom, '>=');
        }
        $dateTo = $wdt->translate('date_modified_to', $this->data);
        if ($dateTo) {
            if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                $dateTo = $m[1] . ':59';
            }
            $list->filterByDateModified($dateTo, '<=');
        }
    }



}
