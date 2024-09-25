<?php
namespace Concrete\Package\Formidable\Src\Formidable\Forms;

defined('C5_EXECUTE') or die('Access Denied.');

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\DatabaseORM as dbORM;
use Concrete\Core\Support\Facade\Application;
use Doctrine\Common\Collections\ArrayCollection;
use Concrete\Core\Page\Page;
use Concrete\Package\Formidable\Src\Formidable\Forms\Elements\Element;
use Concrete\Package\Formidable\Src\Formidable\Helpers\Common as CommonHelper;
use Concrete\Package\Formidable\Src\Formidable\Results\ResultList;
use Concrete\Core\User\User;
use Concrete\Core\Http\Service\Json;

/**
 * @ORM\Entity
 * @ORM\Table(name="FormidableForm", indexes={
 *  @ORM\Index(name="idx_formHandle", columns={"formHandle"}),
 *  @ORM\Index(name="idx_formLimit", columns={"formLimit"}),
 *  @ORM\Index(name="idx_formSchedule", columns={"formSchedule"}),
 *  @ORM\Index(name="idx_formPrivacy", columns={"formPrivacy"}),
 *  @ORM\Index(name="idx_formEnabled", columns={"formEnabled"})
 * })
 */

class Form {

    const LIMIT_ENABLE = 1;
    const LIMIT_DISABLE = 0;

    const LIMIT_TYPE_TOTAL = 'total';
    const LIMIT_TYPE_IP = 'ip';
    const LIMIT_TYPE_USER = 'user';
    const LIMIT_TYPE_PAGE = 'page';
    //const LIMIT_TYPE_ELEMENT = 'element';

    const LIMIT_MESSAGE_CONTENT = 'content';
    const LIMIT_MESSAGE_REDIRECT = 'redirect';

    const SCHEDULE_ENABLE = 1;
    const SCHEDULE_DISABLE = 0;

    const SCHEDULE_MESSAGE_CONTENT = 'content';
    const SCHEDULE_MESSAGE_REDIRECT = 'redirect';

    const PRIVACY_ENABLE = 1;
    const PRIVACY_DISABLE = 0;

    const PRIVACY_REMOVE_ENABLE = 1;
    const PRIVACY_REMOVE_DISABLE = 0;

    const PRIVACY_REMOVE_TYPE_DAYS = 'days';
    const PRIVACY_REMOVE_TYPE_MONTHS = 'months';

    const ENABLED_ENABLE = 1;
    const ENABLED_DISABLE = 0;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $formID;

    /**
     * @ORM\Column(type="string", length=150)
     */
    protected $formHandle;

	/**
     * @ORM\Column(type="string", length=150)
     */
    protected $formName;


    /**
     * @ORM\Column(type="integer", length=1, options={"default" : 0})
     */
    protected $formLimit = 0;

    /**
     * @ORM\Column(type="integer", length=10, options={"default" : 0})
     */
    protected $formLimitValue = 0;

    /**
     * @ORM\Column(type="string", length=10, nullable=true, options={"default" : "total"})
     */
    protected $formLimitType = 'total';

    /**
     * @ORM\Column(type="string", length=10, nullable=true, options={"default" : "content"})
     */
    protected $formLimitMessage = 'content';

	/**
     * @ORM\Column(type="text")
     */
	protected $formLimitMessageContent;

    /**
     * @ORM\Column(type="integer", length=10, options={"default" : 0})
     */
    protected $formLimitMessagePage = 0;


    /**
     * @ORM\Column(type="integer", length=1, options={"default" : 0})
     */
    protected $formSchedule = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $formScheduleDateFrom;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $formScheduleDateTo;

    /**
     * @ORM\Column(type="string", length=10, nullable=true, options={"default" : "content"})
     */
    protected $formScheduleMessage = 'content';

	/**
     * @ORM\Column(type="text")
     */
	protected $formScheduleMessageContent;

    /**
     * @ORM\Column(type="integer", length=10, options={"default" : 0})
     */
    protected $formScheduleMessagePage = 0;


    /**
     * @ORM\Column(type="integer", length=1, options={"default" : 0})
     */
    protected $formPrivacy = 0;

    /**
     * @ORM\Column(type="integer", length=1, options={"default" : 0})
     */
    protected $formPrivacyIP = 0;

    /**
     * @ORM\Column(type="integer", length=1, options={"default" : 0})
     */
    protected $formPrivacyBrowser = 0;

    /**
     * @ORM\Column(type="integer", length=1, options={"default" : 0})
     */
    protected $formPrivacyLog = 0;

    /**
     * @ORM\Column(type="string", length=10, nullable=true, options={"default" : "days"})
     */
    protected $formPrivacyRemove = 'days';

    /**
     * @ORM\Column(type="integer", length=10, options={"default" : 0})
     */
    protected $formPrivacyRemoveValue = 0;

    /**
     * @ORM\Column(type="string", length=10, nullable=true, options={"default" : "days"})
     */
    protected $formPrivacyRemoveType = 'days';

    /**
     * @ORM\Column(type="integer", length=1, options={"default" : 0})
     */
    protected $formPrivacyRemoveFiles = 0;


    /**
     * @ORM\Column(type="integer", length=1, nullable=true, options={"default" : 1})
     */
	protected $formEnabled = 1;

    /**
     * @ORM\Column(type="datetime", options={"default" : "CURRENT_TIMESTAMP"})
     */
    protected $formDateAdded;

    /**
     * @ORM\Column(type="datetime", options={"default" : "CURRENT_TIMESTAMP"})
     */
    protected $formDateModified;

    /**
    * @ORM\OneToMany(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Forms\Rows\Row", mappedBy="form", cascade={"persist"}, orphanRemoval=true, indexBy="rowID")
    * @ORM\OrderBy({"rowOrder" = "ASC"})
    */
    protected $rows;

    /**
    * @ORM\OneToMany(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Mails\Mail", mappedBy="form", cascade={"persist"}, orphanRemoval=true, indexBy="mailID")
    * @ORM\OrderBy({"mailDateAdded" = "DESC"})
    */
    protected $mails;

    /**
    * @ORM\OneToMany(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Results\Result", mappedBy="form", cascade={"persist"}, orphanRemoval=true, indexBy="resultID")
    * @ORM\OrderBy({"resultDateAdded" = "DESC"})
    */
    protected $results;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Site\Site")
     * @ORM\JoinColumn(name="siteID", referencedColumnName="siteID")
     */
    protected $site;

    /** Other variables */
    protected $formLimitMessagePageObject;
    protected $formScheduleMessagePageObject;

    protected $assets;
    protected $dependencies;

    public function __construct()
    {
        $this->rows = new ArrayCollection();
        $this->mails = new ArrayCollection();
        $this->results = new ArrayCollection();
        $this->assets = new ArrayCollection();
        $this->dependencies = new ArrayCollection();
    }

    public function __clone()
    {
        if ($this->formID) {
            $this->formID = null;

            $th = Application::getFacadeApplication()->make('helper/text');

            $temp = new ArrayCollection();
            foreach ($this->rows as $item) {
                $clone = clone $item;
                $clone->setForm($this);
                $temp->add($clone);
            }
            $this->rows = $temp;

            $temp = new ArrayCollection();
            foreach ($this->results as $item) {
                $clone = clone $item;
                $clone->setForm($this);
                $temp->add($clone);
            }
            $this->results = $temp;

            $temp = new ArrayCollection();
            foreach ($this->mails as $item) {
                $clone = clone $item;
                $clone->setForm($this);
                //$clone->setName(t('%s (copy)', $clone->getName()));
                //$clone->setHandle($th->handle($clone->getName()));
                $temp->add($clone);
            }
            $this->mails = $temp;
        }
    }


	public static function getByID($formID)
    {
        $em = dbORM::entityManager();
        return $em->find(get_class(), $formID);
    }

    public static function getByHandle($formHandle)
    {
        $em = dbORM::entityManager();
        return $em->getRepository(get_class())->findOneBy(['formHandle' => $formHandle]);
    }


    public function getItemID()
    {
        return $this->formID;
    }


    public function getHandle()
    {
        return $this->formHandle;
    }
    public function setHandle($formHandle)
    {
        $this->formHandle = $formHandle;
    }


    public function getName()
    {
        return $this->formName;
    }
    public function setName($formName)
    {
        $this->formName = $formName;
    }


    /**
     * Limit methods
     */
    public function getLimit()
    {
        return $this->formLimit;
    }
    public function setLimit($formLimit)
    {
        $this->formLimit = $formLimit;
    }

    public function getLimitValue()
    {
        return (int)$this->formLimitValue;
    }
    public function setLimitValue($formLimitValue)
    {
        $this->formLimitValue = (int)$formLimitValue;
    }

    public static function getLimitOptions()
    {
		$obj = new self;
        $available = [
			self::LIMIT_ENABLE,
            self::LIMIT_DISABLE,
		];
		$options = [];
		foreach($available as $v) {
			$options[$v] = $obj->getLimitText($v);
		}
		return $options;
	}
    public function getLimitText($option = null)
    {
        if (!in_array($option, [self::LIMIT_ENABLE, self::LIMIT_DISABLE])) {
            $option = $this->getLimit();
        }
        $text = '';
        switch ($option) {
			case self::LIMIT_ENABLE:
                $text = t('Enable limits');
			break;
			case self::LIMIT_DISABLE:
                $text = t('Disable limits');
            break;
		}
		return $text;
    }

    public function getLimitType()
    {
        return $this->formLimitType;
    }
    public function setLimitType($formLimitType)
    {
        $this->formLimitType = $formLimitType;
    }
    public static function getLimitTypes()
    {
		$obj = new self;
        $available = [
			self::LIMIT_TYPE_TOTAL,
            self::LIMIT_TYPE_IP,
            self::LIMIT_TYPE_USER,
            self::LIMIT_TYPE_PAGE,
            //self::LIMIT_TYPE_ELEMENT,
		];
		$options = [];
		foreach($available as $v) {
			$options[$v] = $obj->getLimitTypeText($v);
		}
		return $options;
	}
    public function getLimitTypeText($option = null)
    {
        if (!in_array($option, [self::LIMIT_TYPE_TOTAL, self::LIMIT_TYPE_IP, self::LIMIT_TYPE_USER, self::LIMIT_TYPE_PAGE])) {
            $option = $this->getLimitTypes();
        }
        $text = '';
        switch ($option) {
			case self::LIMIT_TYPE_TOTAL:
                $text = t('Total submissions');
			break;
			case self::LIMIT_TYPE_IP:
                $text = t('Per IP-address');
            break;
            case self::LIMIT_TYPE_USER:
                $text = t('Per registered user');
            break;
            case self::LIMIT_TYPE_PAGE:
                $text = t('Per page');
            break;
            // case self::LIMIT_TYPE_ELEMENT:
            //     $text = t('Per element');
            // break;
		}
		return $text;
    }

    public function getLimitElement()
    {
        return $this->formLimitElement;
    }
    public function setLimitElement($formLimitElement)
    {
        if (!is_object($formLimitElement)) {
            $formLimitElement = Element::getByID($formLimitElement);
        }
        $this->formLimitElement = $formLimitElement;
    }

    public function getLimitMessage()
    {
        return $this->formLimitMessage;
    }
    public function setLimitMessage($formLimitMessage)
    {
        $this->formLimitMessage = $formLimitMessage;
    }
    public static function getLimitMessages()
    {
		$obj = new self;
        $available = [
			self::LIMIT_MESSAGE_CONTENT,
            self::LIMIT_MESSAGE_REDIRECT,
		];
		$options = [];
		foreach($available as $v) {
			$options[$v] = $obj->getLimitMessageText($v);
		}
		return $options;
	}
    public function getLimitMessageText($option = null)
    {
        if (!in_array($option, [self::LIMIT_MESSAGE_CONTENT, self::LIMIT_MESSAGE_REDIRECT])) {
            $option = $this->getLimitMessage();
        }
        $text = '';
        switch ($option) {
			case self::LIMIT_MESSAGE_CONTENT:
                $text = t('Show message');
			break;
			case self::LIMIT_MESSAGE_REDIRECT:
                $text = t('Redirect to page');
            break;

		}
		return $text;
    }

    public function getLimitMessageContent()
    {
        return $this->formLimitMessageContent;
    }
    public function setLimitMessageContent($formLimitMessageContent)
    {
        $this->formLimitMessageContent = $formLimitMessageContent;
    }

    public function getLimitMessagePage()
    {
        return (int)$this->formLimitMessagePage;
    }
    public function getLimitMessagePageObject()
    {
        if (is_object($this->formLimitMessagePageObject)) {
            return $this->formLimitMessagePageObject;
        }
        $this->formLimitMessagePageObject = Page::getByID($this->formLimitMessagePage);
        return $this->formLimitMessagePageObject;
    }
    public function setLimitMessagePage($formLimitMessagePage)
    {
        if (is_object($formLimitMessagePage)) {
            $formLimitMessagePage = $formLimitMessagePage->getCollectionID();
        }
        $this->formLimitMessagePage = $formLimitMessagePage;
    }

    public function isLimited()
    {
        if ($this->getLimit() == 0) {
            return false;
        }

        $list = new ResultList();
        $list->filterByForm($this);

        $type = $this->getLimitType();
        switch ($type) {
			case self::LIMIT_TYPE_TOTAL:
                // do nothing
			break;
			case self::LIMIT_TYPE_IP:
                $list->filterByIP(CommonHelper::getIP());
            break;
            case self::LIMIT_TYPE_USER:
                $list->filterByUser(new User());
            break;
            case self::LIMIT_TYPE_PAGE:
                $list->filterByPage(Page::getCurrentPage());
            break;
            // TODO
            // case self::LIMIT_TYPE_ELEMENT:
            //     $list->filterByElement();
            // break;
		}

        $total = $list->getTotalResults();

        if ($total >= $this->getLimitValue()) {
            return true;
        }
        return false;
    }


    /**
     * Scheduling
     */
    public function getSchedule()
    {
        return $this->formSchedule;
    }
    public function setSchedule($formSchedule)
    {
        $this->formSchedule = $formSchedule;
    }

    public static function getScheduleOptions()
    {
		$obj = new self;
        $available = [
			self::SCHEDULE_ENABLE,
            self::SCHEDULE_DISABLE,
		];
		$options = [];
		foreach($available as $v) {
			$options[$v] = $obj->getScheduleText($v);
		}
		return $options;
	}
    public function getScheduleText($option = null)
    {
        if (!in_array($option, [self::SCHEDULE_ENABLE, self::SCHEDULE_DISABLE])) {
            $option = $this->getSchedule();
        }
        $text = '';
        switch ($option) {
			case self::SCHEDULE_ENABLE:
                $text = t('Enable scheduling');
			break;
			case self::SCHEDULE_DISABLE:
                $text = t('Disable scheduling');
            break;
		}
		return $text;
    }

    public function getScheduleDateFrom($display = false, $format = 'Y-m-d H:i:s')
    {
        if (!$display) {
            return $this->formScheduleDateFrom;
        }
        return Application::getFacadeApplication()->make('helper/date')->formatDateTime(strtotime($this->formScheduleDateFrom->format($format)));
    }
    public function setScheduleDateFrom($formScheduleDateFrom)
    {
        $this->formScheduleDateFrom = $formScheduleDateFrom;
	}

    public function getScheduleDateTo($display = false, $format = 'Y-m-d H:i:s')
    {
        if (!$display) {
            return $this->formScheduleDateTo;
        }
        return Application::getFacadeApplication()->make('helper/date')->formatDateTime(strtotime($this->formScheduleDateTo->format($format)));
    }
    public function setScheduleDateTo($formScheduleDateTo)
    {
        $this->formScheduleDateTo = $formScheduleDateTo;
	}

    public function getScheduleMessage()
    {
        return $this->formScheduleMessage;
    }
    public function setScheduleMessage($formScheduleMessage)
    {
        $this->formScheduleMessage = $formScheduleMessage;
    }
    public static function getScheduleMessages()
    {
		$obj = new self;
        $available = [
			self::LIMIT_MESSAGE_CONTENT,
            self::LIMIT_MESSAGE_REDIRECT,
		];
		$options = [];
		foreach($available as $v) {
			$options[$v] = $obj->getScheduleMessageText($v);
		}
		return $options;
	}
    public function getScheduleMessageText($option = null)
    {
        if (!in_array($option, [self::LIMIT_MESSAGE_CONTENT, self::LIMIT_MESSAGE_REDIRECT])) {
            $option = $this->getScheduleMessage();
        }
        $text = '';
        switch ($option) {
			case self::LIMIT_MESSAGE_CONTENT:
                $text = t('Show message');
			break;
			case self::LIMIT_MESSAGE_REDIRECT:
                $text = t('Redirect to page');
            break;

		}
		return $text;
    }

    public function getScheduleMessageContent()
    {
        return $this->formScheduleMessageContent;
    }
    public function setScheduleMessageContent($formScheduleMessageContent)
    {
        $this->formScheduleMessageContent = $formScheduleMessageContent;
    }

    public function getScheduleMessagePage()
    {
        return (int)$this->formScheduleMessagePage;
    }
    public function getScheduleMessagePageObject()
    {
        if (is_object($this->formScheduleMessagePageObject)) {
            return $this->formScheduleMessagePageObject;
        }
        $this->formScheduleMessagePageObject = Page::getByID($this->formScheduleMessagePage);
        return $this->formScheduleMessagePageObject;
    }
    public function setScheduleMessagePage($formScheduleMessagePage)
    {
        if (is_object($formScheduleMessagePage)) {
            $formScheduleMessagePage = $formScheduleMessagePage->getCollectionID();
        }
        $this->formScheduleMessagePage = $formScheduleMessagePage;
    }

    public function isScheduled()
    {
        if ((int)$this->getSchedule() == self::SCHEDULE_ENABLE) {

            $dateFrom = $this->getScheduleDateFrom();
            if (is_object($dateFrom) && $dateFrom > new \DateTime()) {
                return true;
            }

            $dateTo = $this->getScheduleDateTo();
            if (is_object($dateTo) && $dateTo < new \DateTime()) {
                return true;
            }
        }
        return false;
    }


    /**
     * Privacy methods
     */
    public function getPrivacy()
    {
        return (int)$this->formPrivacy;
    }
    public function setPrivacy($formPrivacy)
    {
        $this->formPrivacy = (int)$formPrivacy;
    }
    public static function getPrivacyOptions()
    {
		$obj = new self;
        $available = [
			self::PRIVACY_ENABLE,
            self::PRIVACY_DISABLE,
		];
		$options = [];
		foreach($available as $v) {
			$options[$v] = $obj->getPrivacyOptionsText($v);
		}
		return $options;
	}
    public function getPrivacyOptionsText($option = null)
    {
        if (!in_array($option, [self::PRIVACY_ENABLE, self::PRIVACY_DISABLE])) {
            $option = $this->getPrivacy();
        }
        $text = '';
        switch ($option) {
			case self::PRIVACY_ENABLE:
                $text = t('Enable GDPR settings');
			break;
			case self::PRIVACY_DISABLE:
                $text = t('Disable GDPR settings');
            break;
		}
		return $text;
    }

    public function getPrivacyIP()
    {
        return (int)$this->formPrivacyIP==1?true:false;
    }
    public function setPrivacyIP($formPrivacyIP)
    {
        $this->formPrivacyIP = (int)$formPrivacyIP;
    }
    public function getPrivacyBrowser()
    {
        return (int)$this->formPrivacyBrowser==1?true:false;
    }
    public function setPrivacyBrowser($formPrivacyBrowser)
    {
        $this->formPrivacyBrowser = (int)$formPrivacyBrowser;
    }
    public function getPrivacyLog()
    {
        return (int)$this->formPrivacyLog==1?true:false;
    }
    public function setPrivacyLog($formPrivacyLog)
    {
        $this->formPrivacyLog = (int)$formPrivacyLog;
    }


    public function getPrivacyRemove()
    {
        return (int)$this->formPrivacyRemove;
    }
    public function setPrivacyRemove($formPrivacyRemove)
    {
        $this->formPrivacyRemove = (int)$formPrivacyRemove;
    }
    public static function getPrivacyRemoveOptions()
    {
		$obj = new self;
        $available = [
			self::PRIVACY_REMOVE_ENABLE,
            self::PRIVACY_REMOVE_DISABLE,
		];
		$options = [];
		foreach($available as $v) {
			$options[$v] = $obj->getPrivacyRemoveOptionsText($v);
		}
		return $options;
	}
    public function getPrivacyRemoveOptionsText($option = null)
    {
        if (!in_array($option, [self::PRIVACY_REMOVE_ENABLE, self::PRIVACY_REMOVE_DISABLE])) {
            $option = $this->getPrivacyRemove();
        }
        $text = '';
        switch ($option) {
			case self::PRIVACY_REMOVE_ENABLE:
                $text = t('Enable auto remove');
			break;
			case self::PRIVACY_REMOVE_DISABLE:
                $text = t('Disable auto remove');
            break;
		}
		return $text;
    }

    public function getPrivacyRemoveValue()
    {
        return (int)$this->formPrivacyRemoveValue;
    }
    public function setPrivacyRemoveValue($formPrivacyRemoveValue)
    {
        $this->formPrivacyRemoveValue = (int)$formPrivacyRemoveValue;
    }

    public function getPrivacyRemoveType()
    {
        return $this->formPrivacyRemoveType;
    }
    public function setPrivacyRemoveType($formPrivacyRemoveType)
    {
        $this->formPrivacyRemoveType = $formPrivacyRemoveType;
    }
    public static function getPrivacyRemoveTypes()
    {
		$obj = new self;
        $available = [
			self::PRIVACY_REMOVE_TYPE_DAYS,
            self::PRIVACY_REMOVE_TYPE_MONTHS,
		];
		$options = [];
		foreach($available as $v) {
			$options[$v] = $obj->getPrivacyRemoveTypeText($v);
		}
		return $options;
	}
    public function getPrivacyRemoveTypeText($option = null)
    {
        if (!in_array($option, [self::PRIVACY_REMOVE_TYPE_DAYS, self::PRIVACY_REMOVE_TYPE_MONTHS])) {
            $option = $this->getPrivacyRemoveType();
        }
        $text = '';
        switch ($option) {
			case self::PRIVACY_REMOVE_TYPE_DAYS:
                $text = t('Days');
			break;
			case self::PRIVACY_REMOVE_TYPE_MONTHS:
                $text = t('Months');
            break;
		}
		return $text;
    }

    public function getPrivacyRemoveFiles()
    {
        return (int)$this->formPrivacyRemoveFiles==1?true:false;
    }
    public function setPrivacyRemoveFiles($formPrivacyRemoveFiles)
    {
        $this->formPrivacyRemoveFiles = (int)$formPrivacyRemoveFiles;
    }


    /**
     * Enable methods
     */
    public function getEnabled()
    {
        return (int)$this->formEnabled;
    }
    public function setEnabled($formEnabled)
    {
        $this->formEnabled = (int)$formEnabled;
    }
    public static function getEnabledOptions()
    {
		$obj = new self;
        $available = [
			self::ENABLED_ENABLE,
            self::ENABLED_DISABLE,
		];
		$options = [];
		foreach($available as $v) {

			$options[$v] = $obj->getEnabledText($v);
		}
		return $options;
	}
    public function getEnabledText($option = null)
    {
        if (!in_array($option, [self::ENABLED_ENABLE, self::ENABLED_DISABLE])) {
           $option = $this->getEnabled();
        }
        $text = '';
        switch ($option) {
			case self::ENABLED_ENABLE:
                $text = t('Enable this form');
			break;
			case self::ENABLED_DISABLE:
                $text = t('Disable this form');
            break;
		}
		return $text;
    }


    /**
     * Set site
     */
    public function getSite()
    {
        return $this->site;
    }
    public function setSite($site)
    {
        $this->site = $site;
    }


    /**
     * Dates methods
     */
    public function getDateAdded($display = false, $format = 'Y-m-d H:i:s')
    {
        if (!$display) {
            return $this->formDateAdded;
        }
        return Application::getFacadeApplication()->make('helper/date')->formatDateTime(strtotime($this->formDateAdded->format($format)));
    }
    public function setDateAdded($formDateAdded)
    {
        $this->formDateAdded = $formDateAdded;
	}

    public function getDateModified($display = false, $format = 'Y-m-d H:i:s')
    {
        if (!$display) {
            return $this->formDateModified;
        }
        return Application::getFacadeApplication()->make('helper/date')->formatDateTime(strtotime($this->formDateModified->format($format)));
    }
    public function setDateModified($formDateModified)
    {
        $this->formDateModified = $formDateModified;
	}


    /**
     * Rows methods
     */
    public function getRows()
    {
        return $this->rows;
    }
    public function getRowByID($rowID)
    {
        return $this->rows->get($rowID);
    }
    public function getTotalRows()
    {
        return $this->rows->count();
    }
    public function getNextRowOrder()
    {
        $last = $this->rows->last();
        if (!is_object($last)) {
            return 0;
        }
        return $last->getOrder() + 1;
    }


    /**
     * All elements in this form
     */
    public function getElements()
    {
        $elements = [];
        foreach ($this->getRows() as $row) {
            foreach ($row->getColumns() as $col) {
                foreach ($col->getElements() as $el) {
                    $elements[] = $el;
                }
            }
        }
        return $elements;
    }
    /**
     * Get element by ID in this form
     */
    public function getElementByID($id)
    {
        foreach ($this->getRows() as $row) {
            foreach ($row->getColumns() as $col) {
                foreach ($col->getElements() as $el) {
                    if ($el->getItemID() == $id) {
                        return $el;
                    }
                }
            }
        }
        return false;
    }
    /**
     * Get element by handle in this form
     */
    public function getElementByHandle($handle)
    {
        foreach ($this->getRows() as $row) {
            foreach ($row->getColumns() as $col) {
                foreach ($col->getElements() as $el) {
                    if ($el->getHandle() == $handle) {
                        return $el;
                    }
                }
            }
        }
        return false;
    }
    /**
     * Get elements by type
     */
    public function getElementsByType($types = [])
    {
        $elements = [];
        foreach ($this->getRows() as $row) {
            foreach ($row->getColumns() as $col) {
                foreach ($col->getElements() as $el) {
                    if (in_array($el->getTypeObject()->getHandle(), $types)) {
                        $elements[] = $el;
                    }
                }
            }
        }
        return $elements;
    }
    /**
     * Get total elements on form
     */
    public function getTotalElements()
    {
        return count($this->getElements());
    }

    /**
     * Has a submit button
     */
    public function hasSubmitButton()
    {
        foreach ($this->getElements() as $el) {
            if ($el->getTypeObject()->getHandle() == 'submit') {
                return true;
            }
        }
        return false;
    }

    /**
     * Mails of this form
     */
    public function getMails()
    {
        return $this->mails;
    }
    public function getMailByID($mailID)
    {
        return $this->mails->get($mailID);
    }
    public function getTotalMails()
    {
        return $this->mails->count();
    }


    /**
     * Results of this form
     */
    public function getResults()
    {
        return $this->results;
    }
    public function getResultByID($resultID)
    {
        return $this->results->get($resultID);
    }
    public function getTotalResults()
    {
        return $this->results->count();
    }
    public function getLastResult()
    {
        return $this->results->last();
    }
    public function clearResults()
    {
        return $this->results->clear();
    }


    /**
     * Register assets for this form
     */
    public function registerAssets($assets)
    {
        if (!is_object($this->assets)) {
            $this->assets = new ArrayCollection();
        }
        if (!is_array($assets)) {
            $groups = (array)$this->assets->get('group');
            $groups[$assets] = $assets;
            $this->assets->set('groups', $groups);
        }
        else {
            foreach ((array)$assets as $asset) {
                $this->assets->set($asset[0].'/'.$asset[1], $asset);
            }
        }
    }

    public function getAssets()
    {
        if (!$this->assets) {
            return [];
        }
        return $this->assets->toArray();
    }


    /**
     * Dependencies for this form
     */
    public function registerDependency($dependency)
    {
        if (!is_object($this->dependencies)) {
            $this->dependencies = new ArrayCollection();
        }
        $this->dependencies->add($dependency);
    }
    public function getDependencies()
    {
        return $this->dependencies;
    }
    public function getDependenciesJson()
    {
        if (is_object($this->dependencies)) {

            $rules = [];
            $selectors = [];

            foreach ($this->dependencies->toArray() as $dependencies) {
                foreach ($dependencies as $id => $dependency) {

                    if (!isset($dependency['selector']) || !count($dependency['rules'])) {
                        continue;
                    }

                    foreach ($dependency['rules'] as $rule) {
                        foreach ($rule['selectors'] as $selector) {
                            $handle = $selector['handle'];
                            if (!isset($selectors[$handle])) {
                                $selectors[$handle] = [];
                            }
                            if (!in_array($id, $selectors[$handle])) {
                                $selectors[$handle][] = $id;
                            }
                        }
                    }

                    $rls = [];
                    foreach ($dependency['rules'] as $rule) {
                        $rls[] = $rule;
                    }

                    $rules[] = [
                        'id' => $id,
                        'selector' => $dependency['selector'],
                        'rules' => $rls
                    ];
                }
            }

            $dependencies = [
                'selectors' => [],
                'rules' => []
            ];
            if (count($selectors) && count($rules)) {
                foreach ($selectors as $selector => $ids) {
                    $dependencies['selectors'][] = [
                        'selector' => $selector,
                        'id' => $ids
                    ];
                }
                $dependencies['rules'] = $rules;
            }
            return (new Json())->encode($dependencies);
        }
        return '[]';
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


