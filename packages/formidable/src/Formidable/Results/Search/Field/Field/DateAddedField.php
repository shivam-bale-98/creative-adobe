<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search\Field\Field;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Results\ResultList;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;

class DateAddedField extends AbstractField
{
    protected $requestVariables = [
        'date_added_from_dt',
        'date_added_from_h',
        'date_added_from_m',
        'date_added_from_a',
        'date_added_to_dt',
        'date_added_to_h',
        'date_added_to_m',
        'date_added_to_a',
    ];

    public function getKey()
    {
        return 'date_added';
    }

    public function getDisplayName()
    {
        return t('Date Added');
    }

    public function renderSearchField()
    {
        $wdt = Application::getFacadeApplication()->make('helper/form/date_time');
        return $wdt->datetime('date_added_from', $wdt->translate('date_added_from', $this->data)) . t('to') . $wdt->datetime('date_added_to', $wdt->translate('date_added_to', $this->data));
    }

    /**
     * @param ResultList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $wdt = Application::getFacadeApplication()->make('helper/form/date_time');
        /* @var $wdt \Concrete\Core\Form\Service\Widget\DateTime */
        $dateFrom = $wdt->translate('date_added_from', $this->data);
        if ($dateFrom) {
            $list->filterByDateAdded($dateFrom, '>=');
        }
        $dateTo = $wdt->translate('date_added_to', $this->data);
        if ($dateTo) {
            if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                $dateTo = $m[1] . ':59';
            }
            $list->filterByDateAdded($dateTo, '<=');
        }
    }
}
