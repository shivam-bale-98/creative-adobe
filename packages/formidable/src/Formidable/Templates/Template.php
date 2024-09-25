<?php
namespace Concrete\Package\Formidable\Src\Formidable\Templates;

defined('C5_EXECUTE') or die('Access Denied.');

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\DatabaseORM as dbORM;
use Concrete\Core\Support\Facade\Application;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="FormidableTemplate", indexes={
 *     @ORM\Index(name="idx_templateHandle", columns={"templateHandle"}),
 * })
 */

class Template {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $templateID;

    /**
     * @ORM\Column(type="string", length=150)
     */
    protected $templateHandle;

	/**
     * @ORM\Column(type="string", length=150)
     */
    protected $templateName;

    /**
     * @ORM\Column(type="text")
     */
	protected $templateStyle;

	/**
     * @ORM\Column(type="text")
     */
	protected $templateContent;

    /**
     * @ORM\Column(type="datetime", options={"default" : "CURRENT_TIMESTAMP"})
     */
    protected $templateDateAdded;

    /**
     * @ORM\Column(type="datetime", options={"default" : "CURRENT_TIMESTAMP"})
     */
    protected $templateDateModified;

    /**
    * @ORM\OneToMany(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Mails\Mail", mappedBy="template", indexBy="mailID")
    * @ORM\OrderBy({"mailID" = "DESC"})
    */
    protected $mails;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Site\Site")
     * @ORM\JoinColumn(name="siteID", referencedColumnName="siteID")
     */
    protected $site;

    public function __construct()
    {
        $this->mails = new ArrayCollection();
    }

    public function __clone()
    {
        if ($this->templateID) {
            $this->templateID = null;

            $this->setDateAdded(new \DateTime());
            $this->setDateModified(new \DateTime());
        }
    }

	public static function getByID($templateID)
    {
        $em = dbORM::entityManager();
        return $em->find(get_class(), $templateID);
    }

    public static function getByHandle($templateHandle)
    {
        $em = dbORM::entityManager();
        return $em->getRepository(get_class())->findOneBy(['templateHandle' => $templateHandle]);
    }

    public function getItemID()
    {
        return $this->templateID;
    }

    public function getName()
    {
        return $this->templateName;
    }
    public function setName($templateName)
    {
        $this->templateName = $templateName;
    }

    public function getHandle()
    {
        return $this->templateHandle;
    }
    public function setHandle($templateHandle)
    {
        $this->templateHandle = $templateHandle;
    }

	public function getStyle()
    {
        return $this->templateStyle;
    }
    public function setStyle($templateStyle)
    {
        $this->templateStyle = $templateStyle;
	}

    public function getContent()
    {
        return $this->templateContent;
    }
    public function setContent($templateContent)
    {
        $this->templateContent = $templateContent;
	}

    public function getSite()
    {
        return $this->site;
    }
    public function setSite($site)
    {
        $this->site = $site;
    }

    public function getTotalMails()
    {
        return $this->mails->count();
    }

    public function getDateAdded($display = false, $format = 'Y-m-d H:i:s')
    {
        if (!$display) {
            return $this->templateDateAdded;
        }
        return Application::getFacadeApplication()->make('helper/date')->formatDateTime(strtotime($this->templateDateAdded->format($format)));
    }
    public function setDateAdded($templateDateAdded)
    {
        $this->templateDateAdded = $templateDateAdded;
	}

    public function getDateModified($display = false, $format = 'Y-m-d H:i:s')
    {
        if (!$display) {
            return $this->templateDateModified;
        }
        return Application::getFacadeApplication()->make('helper/date')->formatDateTime(strtotime($this->templateDateModified->format($format)));
    }
    public function setDateModified($templateDateModified)
    {
        $this->templateDateModified = $templateDateModified;
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


