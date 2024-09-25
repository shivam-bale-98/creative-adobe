<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results;

defined('C5_EXECUTE') or die('Access Denied.');

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\DatabaseORM as dbORM;
use Doctrine\Common\Collections\ArrayCollection;
use Concrete\Core\User\User;
use Concrete\Core\Page\Page;
use Concrete\Core\Block\Block;
use Concrete\Core\Localization\Localization;
use Concrete\Package\Formidable\Src\Formidable\Forms\Form;

/**
 * @ORM\Entity
 * @ORM\Table(name="FormidableResult", indexes={
 * })
 */

class Result {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $resultID;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $resultIP;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $resultDevice;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $resultBrowser;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $resultResolution;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $resultOperatingSystem;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $resultLocale;

    /**
     * @ORM\Column(type="integer", length=10, options={"default" : 0})
     */
    protected $resultUser = 0;

    /**
     * @ORM\Column(type="integer", length=10, options={"default" : 0})
     */
    protected $resultPage = 0;

    /**
     * @ORM\Column(type="integer", length=10, options={"default" : 0})
     */
    protected $resultBlock = 0;

    /**
     * @ORM\Column(type="datetime", options={"default" : "CURRENT_TIMESTAMP"})
     */
    protected $resultDateAdded;

    /**
     * @ORM\Column(type="datetime", options={"default" : "CURRENT_TIMESTAMP"})
     */
    protected $resultDateModified;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Forms\Form", inversedBy="results")
     * @ORM\JoinColumn(name="formID", referencedColumnName="formID")
     */
    protected $form;

    /**
    * @ORM\OneToMany(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Results\ResultElement", mappedBy="result", cascade={"persist"}, orphanRemoval=true, indexBy="elementID")
    */
    protected $elementData;

    /**
    * @ORM\OneToMany(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Results\ResultLog", mappedBy="result", cascade={"persist"}, orphanRemoval=true, indexBy="logID")
    * @ORM\OrderBy({"resultLogID" = "DESC"})
    */
    protected $logs;

    protected $resultPageObject;
    protected $resultBlockObject;
    protected $resultUserObject;

    public function __construct()
    {
        $this->elementData = new ArrayCollection();
        $this->logs = new ArrayCollection();
    }

    public function __clone()
    {
        if ($this->resultID) {
            $this->resultID = null;

            $temp = new ArrayCollection();
            foreach ($this->elementData as $item) {
                $clone = clone $item;
                $clone->setResult($this);
                $temp->add($clone);
            }
            $this->elementData = $temp;

            $temp = new ArrayCollection();
            foreach ($this->logs as $item) {
                $clone = clone $item;
                $clone->setResult($this);
                $temp->add($clone);
            }
            $this->logs = $temp;
        }
    }

	public static function getByID($resultID)
    {
        $em = dbORM::entityManager();
        return $em->find(get_class(), $resultID);
    }

    public function getItemID()
    {
        return $this->resultID;
    }

    public function getForm()
    {
        return $this->form;
    }
    public function setForm($form)
    {
        if (!is_object($form)) {
            $form = Form::getByID($form);
        }
        $this->form = $form;
    }
    public function getFormID()
    {
        return $this->getForm()->getItemID();
    }

    public function getIP()
    {
        return $this->resultIP;
    }
    public function setIP($resultIP)
    {
        $this->resultIP = $resultIP;
    }

    public function getBrowser()
    {
        return $this->resultBrowser;
    }
    public function setBrowser($resultBrowser)
    {
        $this->resultBrowser = $resultBrowser;
    }

    public function getDevice()
    {
        return $this->resultDevice;
    }
    public function setDevice($resultDevice)
    {
        $this->resultDevice = $resultDevice;
    }

    public function getLocale($display = false)
    {
        if ($display) {
            return Localization::getLanguageDescription($this->resultLocale);
        }
        return $this->resultLocale;
    }
    public function setLocale($resultLocale)
    {
        $this->resultLocale = $resultLocale;
    }

    public function getResolution()
    {
        return $this->resultResolution;
    }
    public function setResolution($resultResolution)
    {
        $this->resultResolution = $resultResolution;
    }

    public function getOperatingSystem()
    {
        return $this->resultOperatingSystem;
    }
    public function setOperatingSystem($resultOperatingSystem)
    {
        $this->resultOperatingSystem = $resultOperatingSystem;
    }

    public function getUser()
    {
        return $this->resultUser;
    }
    public function getUserObject()
    {
        if (is_object($this->resultUserObject)) {
            return $this->resultUserObject;
        }
        $this->resultUserObject = User::getByUserID($this->resultUser);
        return $this->resultUserObject;
    }
    public function setUser($resultUser)
    {
        if (!is_object($resultUser)) {
            $resultUser = (int)$resultUser;
            if ($resultUser > 0) {
                $resultUser = User::getByUserID($resultUser);
            }
        }
        if (is_object($resultUser)) {
            if ((int)$resultUser->getUserID() == 0) {
                $resultUser = 0;
            }
        }
        $this->resultUserObject = $resultUser;
        $this->resultUser = is_object($resultUser)?$resultUser->getUserID():(int)$resultUser;
    }

    public function getPage()
    {
        return $this->resultPage;
    }
    public function getPageObject()
    {
        if (is_object($this->resultPageObject)) {
            return $this->resultPageObject;
        }
        $this->resultPageObject = Page::getByID($this->resultPage);
        return $this->resultPageObject;
    }
    public function setPage($resultPage)
    {
        if (!is_object($resultPage)) {
            $resultPage = Page::getByID(Page::getHomePageID());
            $resultPageID = (int)$resultPage;
            if ($resultPageID != 0) {
                $resultPage = Page::getByID($resultPageID);
            }
        }
        $this->resultPageObject = $resultPage;
        $this->resultPage = is_object($resultPage)?$resultPage->getCollectionID():(int)$resultPage;
    }

    public function getBlock()
    {
        return $this->resultBlock;
    }
    public function getBlockObject()
    {
        if (is_object($this->resultBlockObject)) {
            return $this->resultBlockObject;
        }
        $this->resultBlockObject = Block::getByID($this->resultBlock);
        return $this->resultBlockObject;
    }
    public function setBlock($resultBlockObject)
    {
        if (!is_object($resultBlockObject)) {
            $resultBlockObject = Block::getByID($resultBlockObject);
        }
        $this->resultBlock = $resultBlockObject->getBlockID();
    }


    /**
     * ElementData
     */
    public function getElementData()
    {
        return $this->elementData;
    }
    public function addElementData($elementID, $data)
    {
        if (is_object($elementID)) {
            $elementID = $elementID->getItemID();
        }
        return $this->elementData->set($elementID, $data);
    }
    public function getElementDataByElement($elementID)
    {
        if (is_object($elementID)) {
            $elementID = $elementID->getItemID();
        }
        return $this->elementData->get($elementID);
    }
    public function removeElementData($data)
    {
        if (is_object($data)) {
            return $this->elementData->removeElement($data);
        }
        return $this->elementData->remove($data);
    }
    public function getTotalElementData()
    {
        return $this->elementData->count();
    }


    /**
     * Logs
     */
    public function getLogs()
    {
        return $this->logs;
    }
    public function addLogs($data)
    {
        return $this->logs->add($data);
    }
    public function getLogsByID($logID)
    {
        return $this->logs->get($logID);
    }
    public function removeLogs($data)
    {
        if (is_object($data)) {
            return $this->logs->removeElement($data);
        }
        return $this->logs->remove($data);
    }


    public function getDateAdded($display = false, $format = 'Y-m-d H:i:s')
    {
        if (!$display) {
            return $this->resultDateAdded;
        }
        return $this->resultDateAdded->format($format);
    }
    public function setDateAdded($resultDateAdded)
    {
        $this->resultDateAdded = $resultDateAdded;
	}

    public function getDateModified($display = false, $format = 'Y-m-d H:i:s')
    {
        if (!$display) {
            return $this->resultDateModified;
        }
        return $this->resultDateModified->format($format);
    }
    public function setDateModified($resultDateModified)
    {
        $this->resultDateModified = $resultDateModified;
	}


	public function save()
    {
        $em = dbORM::entityManager();
        $em->persist($this);
        $em->flush();
    }

    public function delete()
    {
        $em = dbORM::entityManager();
        $em->remove($this);
        $em->flush();
    }
}


