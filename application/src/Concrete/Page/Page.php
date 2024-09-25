<?php

namespace Application\Concrete\Page;

use Application\Concrete\Models\Locale\App;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Site\Tree\TreeInterface;
use Concrete\Theme\Concrete\PageTheme;
use Config;
use Core;

class Page extends \Concrete\Core\Page\Page
{
    protected $thumbnail;
    protected $banner;
    protected $favoritesID;
    protected $downloadsID;
    protected $likes;

    public function getThumbnailImage($width = 400, $height = 400, $crop = false)
    {
        /** @var File $file */
        /** @var \Concrete\Core\File\Image\BasicThumbnailer $ih */
        $ih = Core::make('helper/image');

        if (!$this->thumbnail) {
            $image = $this->getAttribute('thumbnail');
            if ($image && $image instanceof File) {
                $image = $ih->getThumbnail($image, $width, $height, $crop);
                if ($image) {
                    $this->thumbnail = $image->src;
                }
            }
        }

        if (!$this->thumbnail) {
            $this->thumbnail = BASE_URL . PageTheme::getSiteTheme()->getThemeURL() . '/assets/images/placeholder.jpg';
        }
        return $this->thumbnail;
    }

    public function getBannerImage($width = 1000, $height = 1000, $crop = false)
    {
        /** @var File $file */
        /** @var \Concrete\Core\File\Image\BasicThumbnailer $ih */
        $ih = Core::make('helper/image');

        if (!$this->banner) {
            $image = $this->getAttribute('banner_image');
            if ($image && $image instanceof File) {
                $image = $ih->getThumbnail($image, $width, $height, $crop);
                if ($image) {
                    $this->banner = $image->src;
                }
            }
        }
        if (!$this->banner) {
            $this->banner = BASE_URL . PageTheme::getSiteTheme()->getThemeURL() . '/assets/images/placeholder.jpg';
        }
        return $this->banner;
    }

    public function getPageLocale()
    {
        return $this->getSiteTreeObject()->getLocale()->getLocale() === App::LOCALE_EN ? App::ENGLISH_SLUG : App::ARABIC_SLUG;
    }

    public function getAlternateLocalePage($width = 400, $height = 400, $crop = false)
    {
        if ($this->getPageLocale() === App::ENGLISH_SLUG) {
            $english_page = $this;
            $lang = Section::getByLocale(App::LOCALE_AR);
            $ar_id = $lang->getTranslatedPageID($english_page);
            $arabic_page = $ar_id ? static::getByID($ar_id) : null;
        } else {
            $arabic_page = $this;
            $lang = Section::getByLocale(App::LOCALE_EN);
            $en_id = $lang->getTranslatedPageID($arabic_page);
            $english_page = $en_id ? static::getByID($en_id) : null;
        }
        return [
            'en' => $english_page,
            'ar' => $arabic_page,
        ];
    }

    /**
     * * Get a page given its ID.
     *
     * @param int $cID the ID of the page
     * @param string $version the page version ('RECENT' for the most recent version, 'ACTIVE' for the currently published version, 'SCHEDULED' for the currently scheduled version, or an integer to retrieve a specific version ID)
     *
     * @return static | \Concrete\Core\Page\Page
     */
    public static function getByID($cID, $version = 'RECENT')
    {
        $c = parent::getByID($cID, $version);
        return $c && $c->getCollectionID() ? $c : null;
    }

    /**
     * Get a page given its path.
     *
     * @param string $path the page path (example: /path/to/page)
     * @param string $version the page version ('RECENT' for the most recent version, 'ACTIVE' for the currently published version, 'SCHEDULED' for the currently scheduled version, or an integer to retrieve a specific version ID)
     * @param \Concrete\Core\Site\Tree\TreeInterface|null $tree
     *
     * @return static | \Concrete\Core\Page\Page
     */
    public static function getByPath($path, $version = 'RECENT', TreeInterface $tree = null)
    {
        return parent::getByPath($path, $version, $tree);
    }

    /**
     * Get the currently requested page.
     *
     * @return static|null
     */
    public static function getCurrentPage()
    {
        /** @var \Concrete\Core\Page\Page $current */
        $req = \Request::getInstance();
        $current = $req->getCurrentPage();

        return static::getByID($current->getCollectionID());
    }
}