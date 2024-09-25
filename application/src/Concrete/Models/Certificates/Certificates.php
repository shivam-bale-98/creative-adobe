<?php

namespace Application\Concrete\Models\Certificates;

use Application\Concrete\Models\Common\Common;
use Concrete\Core\Localization\Service\Date;
use Application\Concrete\Page\Page;
use Core;

class Certificates extends Common
{
    protected $year;

    const PAGE_HANDLE = 'certificate_details';

    public function getYear()
    {
        if (!$this->year) {
            $this->year = $this->collectionObject->getAttribute('certificate_year');
        }
        return $this->year;
    }
}
