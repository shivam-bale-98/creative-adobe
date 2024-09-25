<?php

namespace Application\Concrete\Models\Products;

use Application\Concrete\Models\Common\Common;
use Concrete\Core\Localization\Service\Date;
use Application\Concrete\Page\Page;
use Core;
use Application\Concrete\Helpers\GeneralHelper;

class Product extends Common
{

    public $downloads;

    protected $collectionObject;
    const PAGE_HANDLE = 'product_detail';

    public function getDownloads()
    {
        if (!$this->downloads) {
            $downloads = $this->collectionObject->getAttribute('product_downloads');
            $finalArr = [];
            if($downloads)
            {
                foreach ($downloads->getFileObjects() as $download) {
                    $finalArr[$this->getcollectionObject()->getCollectionName()][] = GeneralHelper::getFileDetails($download);
                }
            }
           
            $this->downloads = $finalArr;
        }
        return $this->downloads;
    }
}
