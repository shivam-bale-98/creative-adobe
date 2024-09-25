<?php

namespace Application\Concrete\Models\CaseStudies;

use Application\Concrete\Models\Common\Common;
use Concrete\Core\Localization\Service\Date;
use Application\Concrete\Page\Page;
use Core;

class CaseStudies extends Common
{
    protected $location, $category;

    protected $collectionObject;
    const PAGE_HANDLE = 'case_study_detail';

    public function getLocation()
    {
        if (!$this->location) {
            $this->location = $this->collectionObject->getAttribute('cs_location');
        }
        return $this->location;
    }

    public function getCategory()
    {
        if (!$this->category) {
            $this->category = $this->collectionObject->getAttribute('cs_categories');
        }
        return $this->category;
    }

}
