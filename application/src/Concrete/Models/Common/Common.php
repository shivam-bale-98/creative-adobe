<?php

namespace Application\Concrete\Models\Common;

use Application\Concrete\Helpers\AttributeHelper;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Localization\Service\Date;
use Application\Concrete\Page\Page;
use Concrete\Theme\Concrete\PageTheme;
use Application\Concrete\Helpers\GeneralHelper;
use Core;

class Common
{
    protected $id;
    protected $name;
    protected $url;
    protected $collection_description;
    protected $thumbnail;
    protected $banner;
    protected $public_date;
    protected $collectionObject;
    protected $date;
    public $product_category;

    /**
     * Author constructor.
     *
     * @param Page| \Application\Concrete\Page\Page $page
     */
    public function __construct($page)
    {
        $this->collectionObject = $page;
    }

    /**
     * @return Page | \Application\Concrete\Page\Page
     */
    public function getCollectionObject()
    {
        return $this->collectionObject;
    }

    public function getID()
    {
        if (!$this->id) {
            $this->id = $this->collectionObject->getCollectionID();
        }
        return $this->id;
    }

    public function getTitle($characters = 250)
    {
        /** @var \Concrete\Core\Utility\Service\Text $th */
        $th = Core::make('helper/text');

        if (!$this->name) {
            $this->name = $th->wordSafeShortText($this->collectionObject->getCollectionName(), $characters);
        }
        return $this->name;
    }

    public function getUrl()
    {
        if (!$this->url) {
            $this->url = $this->getCollectionObject()->getCollectionLink();
        }

        return $this->url;
    }

    public function getCollectionDescription()
    {
        if (!$this->collection_description) {
            $this->collection_description = $this->collectionObject->getCollectionDescription();
        }
        return $this->collection_description;
    }

    public function getPublicDate($format ="d / m / Y" )
    {
        $dh = new Date();
        if (!$this->public_date) {
            $this->public_date = $this->collectionObject->getCollectionDatePublicObject();
        }
        return $dh->formatCustom($format, $this->public_date);
    }

    public function __call($method, $arguments)
    {
        $controller = $this->getCollectionObject();

        return call_user_func_array(array($controller, $method), $arguments);
    }

    public static function getByID($cID)
    {
        $class = get_called_class();
        $c = Page::getByID($cID);
        return $c ? new $class($c) : null;
    }

    /* Priority Thumnbnail Image -> Thumbnail -> Placeholder*/
    public function getThumbnailImage($width = 1000, $height = 1000, $crop = false)
    {
        $thumbnail      = $this->collectionObject->getAttribute('thumbnail');
        $thumbnailImage = $this->collectionObject->getAttribute('thumbnail_image');

        if ($thumbnailImage && $thumbnailImage instanceof File) {
            $this->thumbnail_image = $this->getImageByAttributeHandle("thumbnail_image", $width, $height, $crop);
        } elseif ($thumbnail && $thumbnail instanceof File) {
            $this->thumbnail_image = $this->getImageByAttributeHandle("thumbnail", $width, $height, $crop);
        } else {
            $this->thumbnail_image = BASE_URL . PageTheme::getSiteTheme()->getThemeURL() . '/assets/images/placeholder.jpg';
        }
        return $this->thumbnail_image;
    }
    /* Priority Thumnbnail -> Thumbnail Image -> Placeholder*/

    public function getThumbnail($width = 1000, $height = 1000, $crop = false)
    {
        $thumbnail = $this->collectionObject->getAttribute('thumbnail');
        $thumbnailImage = $this->collectionObject->getAttribute('thumbnail_image');

        if ($thumbnail && $thumbnail instanceof File) {
            $this->thumbnail = $this->getImageByAttributeHandle("thumbnail", $width, $height, $crop);
        } elseif ($thumbnailImage && $thumbnailImage instanceof File) {
            $this->thumbnail = $this->getImageByAttributeHandle("thumbnail_image", $width, $height, $crop);
        } else {
            $this->thumbnail = BASE_URL . PageTheme::getSiteTheme()->getThemeURL() . '/assets/images/placeholder.jpg';
        }


        return $this->thumbnail;
    }

    public function getImageByAttributeHandle($aHandle, $width = 400, $height = 400, $crop = false, $placeholder = true) {
        /** @var File $file */
        /** @var \Concrete\Core\File\Image\BasicThumbnailer $ih */
        $ih = Core::make('helper/image');
        $image = null;

        $imageFile = $this->collectionObject->getAttribute($aHandle);
        if ($imageFile && $imageFile instanceof File) {
            $imageFile = $ih->getThumbnail($imageFile, $width, $height, $crop);
            if ($imageFile) {
                $image = $imageFile->src;
            }
        }

        if (!$image && $placeholder) {
            $image = BASE_URL . PageTheme::getSiteTheme()->getThemeURL() . '/assets/images/placeholder.jpg';
        }

        return $image;
    }

    public function getSearchImage($width = 400, $height = 400, $crop = false, $placeholder = false)
    {
        return $this->getImageByAttributeHandle(AttributeHelper::ATTR_SEARCH_IMG, $width, $height, $crop, $placeholder);
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

    public function getBreadCrumb()
    {
        return GeneralHelper::getAutoNavLink($this->getCollectionObject());
    }

    public function getCustomAttributeDate($handle, $format ="d/m/Y" )
    {
        if (!$this->date) {
            $dh = new Date();
            $this->date = $dh->formatCustom($format, $this->collectionObject->getAttribute($handle));
        }
        return $this->date;
    }

    public function getProductCategory()
    {
        if (!$this->product_category) {
            $this->product_category = (string) $this->getCollectionObject()->getAttribute('category');
        }
        return $this->product_category;
    }

    public function getCertificateLogo($aHandle)
    {
        $imageFile = $this->collectionObject->getAttribute($aHandle);
        if ($imageFile && $imageFile instanceof File) {

            if ($imageFile) {
                $image = $imageFile->getUrl();
            }
        }

        if (!isset($image)) {
            $image = BASE_URL . PageTheme::getSiteTheme()->getThemeURL() . '/assets/images/placeholder.jpg';
        }
        return $image;
    }
}