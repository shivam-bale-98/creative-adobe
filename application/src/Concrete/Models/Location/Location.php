<?php

namespace Application\Concrete\Models\Location;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Support\Facade\Express;
use Concrete\Theme\Concrete\PageTheme;
use Doctrine\ORM\EntityManager;
use Core;
use Doctrine\ORM\EntityManagerInterface;

class Location {
    protected $entityManager;
    protected $entry;

    protected $title;
    protected $email;
    protected $phone;
    protected $coverage_area;
    protected $latitude;
    protected $longitude;
    protected $img;
    protected $contact_location;
    protected $name;

    const HANDLE = 'location';

    public function __construct($entry)
    {
        $this->entityManager = Core::make(EntityManagerInterface::class);
        $this->entry         = $entry;
    }

    public static function add($resourceID, $name)
    {
        /** @var Entry $entry * */
        $entry = Express::buildEntry(self::HANDLE)
            ->setAttribute('resource_id', $resourceID)
            ->setAttribute('name', $name)
            ->save();

        return new self($entry);
    }

    public function updateName($name) {
        $this->getEntry()->setAttribute("name", $name);

        $this->persist();

        return $this;
    }

    public function delete()
    {
        Express::deleteEntry($this->getEntry());
    }

    public function persist()
    {
        if ($this->getEntry()) {
            $this->entityManager->persist($this->entry);
            $this->entityManager->refresh($this->entry);
        }
    }

    public function flush()
    {
        $this->entityManager->flush();
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function getEntry()
    {
        return $this->entry;
    }

    public function resetEntityManager()
    {
        if (!$this->entityManager->isOpen()) {
            $this->entityManager = EntityManager::create($this->entityManager->getConnection(), $this->entityManager->getConfiguration(), $this->entityManager->getEventManager());
        }
    }

    public function getTitle(){
        if(!isset($this->title)) $this->title = $this->getEntry()->getTitle();
        return $this->title;
    }

    public function getName(){
        if(!isset($this->name)) $this->name = $this->getEntry()->getName();
        return $this->name;
    }

    public function getPhoneNumber(){
        if(!isset($this->phone)) $this->phone = $this->getEntry()->getPhoneNumber();
        return $this->phone;
    }

    public function getEmail(){
        if(!isset($this->email)) $this->email = $this->getEntry()->getEmail();
        return $this->email;
    }

    public function getCoverageArea(){
        if(!isset($this->coverage_area)) $this->coverage_area = $this->getEntry()->getCoverageArea();
        return implode(', ',(array) $this->coverage_area->getIterator());
    }

    public function getLatitude(){
        if(!isset($this->latitude)) $this->latitude = $this->getEntry()->getLatitude();
        return $this->latitude;
    }

    public function getLongitude(){
        if(!isset($this->longitude)) $this->longitude = $this->getEntry()->getLongitude();
        return $this->longitude;
    }

    public function getContactLocations(){
        if(!isset($this->contact_location)) $this->contact_location = $this->getEntry()->getContactLocations();
        return $this->contact_location;
    }

    public function getImage($width = 1000, $height = 1000, $crop = false, $regenerate = false)
    {
        /** @var File $file */
        /** @var \Concrete\Core\File\Image\BasicThumbnailer $ih */
        $ih = Core::make('helper/image');

        if (!$this->img || $regenerate) {
            $image = $this->getEntry()->getLocationImg();
            if ($image && $image instanceof File) {
                $image = $ih->getThumbnail($image, $width, $height, $crop);
                if ($image) {
                    $this->img = $image->src;
                }
            }
        }
        if (!$this->img) {
            $this->img = BASE_URL . PageTheme::getSiteTheme()->getThemeURL() . '/assets/images/placeholder.jpg';
        }
        return $this->img;
    }

}