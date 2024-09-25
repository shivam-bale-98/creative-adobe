<?php

namespace Application\Concrete\Models\FileLogos;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Support\Facade\Express;
use Concrete\Theme\Concrete\PageTheme;
use Doctrine\ORM\EntityManager;
use Core;
use Doctrine\ORM\EntityManagerInterface;

class FileLogos {
    protected $entityManager;
    protected $entry;

    protected $title;
    protected $image;

    const HANDLE = 'file_logo';

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

    public function getImage($width = 1000, $height = 1000, $crop = false, $regenerate = false)
    {
        /** @var File $file */
        /** @var \Concrete\Core\File\Image\BasicThumbnailer $ih */
        $ih = Core::make('helper/image');
        if (!$this->image || $regenerate) {
            $image = $this->getEntry()->getFileImage();
            if ($image && $image instanceof File) {
                $this->image = $image->getURL();
            }
        }
        return $this->image;
    }

}