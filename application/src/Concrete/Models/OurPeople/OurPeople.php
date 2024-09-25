<?php

namespace Application\Concrete\Models\OurPeople;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Support\Facade\Express;
use Concrete\Theme\Concrete\PageTheme;
use Doctrine\ORM\EntityManager;
use Core;
use Doctrine\ORM\EntityManagerInterface;

class OurPeople {
    protected $entityManager;
    protected $entry;

    protected $name;
    protected $designation;
    protected $img;

    const HANDLE = 'our_people';

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

    public function getName(){
        if(!isset($this->name)) $this->name = $this->getEntry()->getName();
        return $this->name;
    }

    public function getDesignation(){
        if(!isset($this->designation)) $this->designation = $this->getEntry()->getDesignation();
        return $this->designation;
    }

    public function getImage($width = 1000, $height = 1000, $crop = false, $regenerate = false)
    {
        /** @var File $file */
        /** @var \Concrete\Core\File\Image\BasicThumbnailer $ih */
        $ih = Core::make('helper/image');

        if (!$this->img || $regenerate) {
            $image = $this->getEntry()->getProfilePicture();
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