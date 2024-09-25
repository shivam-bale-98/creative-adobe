<?php

namespace Application\Concrete\Models\Application;

use Application\Concrete\Models\Common\Common;
use Concrete\Core\Localization\Service\Date;
use Application\Concrete\Page\Page;
use Core;

class Application extends Common
{
    protected $video;
    protected $url;

    protected $collectionObject;
    const PAGE_HANDLE = 'application_detail';

    public function getVideoUrl()
    {
        if (!$this->video) {
            $this->video = $this->collectionObject->getAttribute('video_url');
        }
        return $this->video;
    }
}
