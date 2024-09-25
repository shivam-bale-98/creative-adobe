<?php

namespace Application\Concrete\Models\Location;

use Concrete\Core\Express\EntryList;
use Concrete\Core\Support\Facade\Express;

class LocationList extends EntryList
{
    public function __construct()
    {
        $entity = Express::getObjectByHandle(Location::HANDLE);
        parent::__construct($entity);
    }

    public function getResult($queryRow)
    {
        return new Location(parent::getResult($queryRow));
    }

    /**
     * @return LocarionResource[]
     */
    public function getResults()
    {
        return parent::getResults();
    }

    public function filterByCustomAttribute($handle, $value){
        if (!is_array($value)) {
            $value = explode(',', $value);
        }
        foreach ($value as $key => $stat) {
            $expressions[] = $this->getQueryObject()->expr()->like('ak_'.$handle, ':subs_'.$key);
            $this->getQueryObject()->setParameter('subs_'.$key, '%' .trim($stat). '%');
        }

        $expr = $this->query->expr();
        if($expressions){
            $this->query->andWhere(call_user_func_array([$expr, 'orX'], $expressions));
        }

    }
}