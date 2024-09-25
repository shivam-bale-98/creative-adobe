<?php

namespace Application\Concrete\Models\Vacancy;

use Application\Concrete\Models\Common\Common;
use Concrete\Core\Localization\Service\Date;
use Application\Concrete\Page\Page;
use Core;

class Vacancy extends Common
{
    protected $vacancy_type;
    protected $department;
    protected $close_date;

    const PAGE_HANDLE = 'vacancy_detail';

    public function getVacancyType()
    {
        if (!$this->vacancy_type) {
            $this->vacancy_type = $this->collectionObject->getAttribute('type');
        }
        return $this->vacancy_type;
    }

    public function department()
    {
        if (!$this->department) {
            $this->department = $this->collectionObject->getAttribute('job_posting_department');
        }
        return $this->department;
    }

    public function getClosingDate($format = "d / m / Y")
    {
        if (!$this->close_date) {
            $dh = new Date();
            $this->close_date = $dh->formatCustom($format, $this->collectionObject->getAttribute('closing_date'));
        }
        return $this->close_date;
    }
}
