<?php

namespace Application\Concrete\Models\FileLogos;

use Concrete\Core\Express\EntryList;
use Concrete\Core\Support\Facade\Express;

class FileLogosList extends EntryList
{
    public function __construct()
    {
        $entity = Express::getObjectByHandle(FileLogos::HANDLE);
        parent::__construct($entity);
    }

    public function getResult($queryRow)
    {
        return new FileLogos(parent::getResult($queryRow));
    }

    /**
     * @return LocarionResource[]
     */
    public function getResults()
    {
        return parent::getResults();
    }

}