<?php
namespace Concrete\Package\Formidable\Src\Formidable\Forms\Rows;

defined('C5_EXECUTE') or die('Access Denied.');

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\DatabaseORM as dbORM;
use Concrete\Core\Support\Facade\Application;
use Doctrine\Common\Collections\ArrayCollection;
use Concrete\Package\Formidable\Src\Formidable\Forms\Form;

/**
 * @ORM\Entity
 * @ORM\Table(name="FormidableFormRow", indexes={
 *  @ORM\Index(name="idx_formID", columns={"formID"}),
 *  @ORM\Index(name="idx_rowHandle", columns={"rowHandle"}),
 * })
 */

class Row {

    const ROW_TYPE_ROW = 'row';
    const ROW_TYPE_STEP = 'step';
    const ROW_TYPE_REPEAT = 'repeat';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $rowID;

    /**
     * @ORM\Column(type="string", length=10, nullable=true, options={"default" : "row"})
     */
    protected $rowType = 'row';


    /**
     * @ORM\Column(type="string", length=150)
     */
    protected $rowHandle;

	/**
     * @ORM\Column(type="string", length=150)
     */
    protected $rowName;


    /**
     * @ORM\Column(type="integer", length=1, options={"default" : 0})
     */
    protected $rowCss = 0;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $rowCssValue;

    /**
     * @ORM\Column(type="integer", length=1, options={"default" : 0})
     */
    protected $rowButton = 0;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $rowButtonPreviousName;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $rowButtonPreviousHandle;

    /**
     * @ORM\Column(type="integer", length=1, options={"default" : 0})
     */
    protected $rowButtonPreviousCss = 0;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $rowButtonPreviousCssValue;


    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $rowButtonNextName;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $rowButtonNextHandle;

    /**
     * @ORM\Column(type="integer", length=1, options={"default" : 0})
     */
    protected $rowButtonNextCss = 0;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $rowButtonNextCssValue;


    /**
     * @ORM\Column(type="integer", length=10, options={"default" : 0})
     */
    protected $rowOrder = 0;


    /**
     * @ORM\Column(type="datetime", options={"default" : "CURRENT_TIMESTAMP"})
     */
    protected $rowAdded;

    /**
     * @ORM\Column(type="datetime", options={"default" : "CURRENT_TIMESTAMP"})
     */
    protected $rowModified;


    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Forms\Form", inversedBy="rows")
     * @ORM\JoinColumn(name="formID", referencedColumnName="formID")
     */
    protected $form;

    /**
    * @ORM\OneToMany(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Forms\Columns\Column", mappedBy="row", cascade={"persist"}, orphanRemoval=true, indexBy="columnID")
    * @ORM\OrderBy({"columnOrder" = "ASC"})
    */
    protected $columns;



    public function __construct()
    {
        $this->columns = new ArrayCollection();
    }

    public function __clone()
    {
        if ($this->rowID) {
            $this->rowID = null;

            // Disable the setting of name and handle for now
            // The name will be set by the editor
            //$th = Application::getFacadeApplication()->make('helper/text');
            //$this->setName(t('%s (copy)', $this->getName()));
            //$this->setHandle($th->handle($this->getName()));
            $this->setDateAdded(new \DateTime());
            $this->setDateModified(new \DateTime());

            $temp = new ArrayCollection();
            foreach ($this->columns as $item) {
                $clone = clone $item;
                $clone->setRow($this);
                $temp->add($clone);
            }
            $this->columns = $temp;
        }
    }

	public static function getByID($rowID)
    {
        $em = dbORM::entityManager();
        return $em->find(get_class(), $rowID);
    }

    public static function getByHandle($rowHandle, $form)
    {
        //$em = dbORM::entityManager();
        //return $em->getRepository(get_class())->findOneBy(['rowHandle' => $rowHandle, 'form' => $form]);
        $db = Application::getFacadeApplication()->make('database');
        $query = $db->connection()->createQueryBuilder();
        $query->select('r.rowID')
              ->from('FormidableFormRow', 'r')
              ->leftjoin('r', 'FormidableForm', 'f', 'r.formID = f.formID')
              ->where('r.rowHandle = :rowHandle AND f.formID = :formID');
        $query->setParameters(['rowHandle' => $rowHandle, 'formID' => $form->getItemID()]);
        $item = $query->execute()->fetchOne();
        if ($item) {
            return self::getByID($item);
        }
        return false;
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


    public function getItemID()
    {
        return $this->rowID;
    }


    public function getHandle()
    {
        return $this->rowHandle;
    }
    public function setHandle($rowHandle)
    {
        $this->rowHandle = $rowHandle;
    }


    public function getName()
    {
        return $this->rowName;
    }
    public function setName($rowName)
    {
        $this->rowName = $rowName;
    }


    /**
     * Row type
     */
    public function getType()
    {
        return $this->rowType;
    }
    public function setType($rowType)
    {
        $this->rowType = $rowType;
    }
    public static function getTypes()
    {
		$obj = new self;
        $available = [
			self::ROW_TYPE_ROW,
            self::ROW_TYPE_STEP,
		];
		$options = [];
		foreach($available as $v) {
			$options[$v] = $obj->getTypeText($v);
		}
		return $options;
	}
    public function getTypeText($option = null)
    {
        if (empty($option)) {
           $option = $this->getType();
        }
        $text = '';
        switch ($option) {
			case self::ROW_TYPE_ROW:
                $text = t('Row');
			break;
			case self::ROW_TYPE_STEP:
                $text = t('Step');
            break;

		}
		return $text;
    }


    /**
     * CSS
     */
    public function getCss()
    {
        return (float)$this->rowCss;
    }
    public function setCss($rowCss)
    {
        $this->rowCss = (float)$rowCss;
    }
    public static function getCssOptions()
    {
		$obj = new self;
		$options = [];
		foreach([1, 0] as $v) {
			$options[$v] = $obj->getCssOptionText($v);
		}
		return $options;
	}
    public function getCssOptionText($option = null)
    {
        if (empty($option)) {
           $option = $this->getCss();
        }
        $text = '';
        switch ($option) {
			case 1:
                $text = t('Enable CSS');
			break;
			case 0:
                $text = t('Disable CSS');
            break;
		}
		return $text;
    }

    public function getCssValue()
    {
        return $this->rowCssValue;
    }
    public function setCssValue($rowCssValue)
    {
        $this->rowCssValue = $rowCssValue;
    }


    /**
     * Rendering
     */
    public function renderStart($template = '')
    {
        $class = '';
        if (empty($template)) {
            $template = '<div class="%s">';
            $class = 'row ';
        }
        if ($this->getCss()) {
            $class .= $this->getCssValue();
        }
        echo t($template, $class);
    }

    public function renderEnd($template = '')
    {
        if (empty($template)) {
            $template = '</div>';
        }
        echo $template;
    }


    /**
     * Buttons methods
     */
    public function getButton()
    {
        return (float)$this->rowButton;
    }
    public function setButton($rowButton)
    {
        $this->rowButton = (float)$rowButton;
    }
    public static function getButtons()
    {
		$obj = new self;
		$options = [];
		foreach([1, 0] as $v) {
			$options[$v] = $obj->getButtonText($v);
		}
		return $options;
	}
    public function getButtonText($option = null)
    {
        if (empty($option)) {
           $option = $this->getButton();
        }
        $text = '';
        switch ($option) {
			case 1:
                $text = t('Enable custom buttons');
			break;
			case 0:
                $text = t('Disable custom buttons');
            break;
		}
		return $text;
    }

    public function getButtonPreviousHandle()
    {
        return $this->rowButtonPreviousHandle;
    }
    public function setButtonPreviousHandle($rowButtonPreviousHandle)
    {
        $this->rowButtonPreviousHandle = $rowButtonPreviousHandle;
    }
    public function getButtonPreviousName()
    {
        return $this->rowButtonPreviousName;
    }
    public function setButtonPreviousName($rowButtonPreviousName)
    {
        $this->rowButtonPreviousName = $rowButtonPreviousName;
    }
    public function getButtonPreviousCss()
    {
        return (float)$this->rowButtonPreviousCss;
    }
    public function setButtonPreviousCss($rowButtonPreviousCss)
    {
        $this->rowButtonPreviousCss = (float)$rowButtonPreviousCss;
    }
    public function getButtonPreviousCssValue()
    {
        return $this->rowButtonPreviousCssValue;
    }
    public function setButtonPreviousCssValue($rowButtonPreviousCssValue)
    {
        $this->rowButtonPreviousCssValue = $rowButtonPreviousCssValue;
    }

    public function getButtonNextHandle()
    {
        return $this->rowButtonNextHandle;
    }
    public function setButtonNextHandle($rowButtonNextHandle)
    {
        $this->rowButtonNextHandle = $rowButtonNextHandle;
    }
    public function getButtonNextName()
    {
        return $this->rowButtonNextName;
    }
    public function setButtonNextName($rowButtonNextName)
    {
        $this->rowButtonNextName = $rowButtonNextName;
    }
    public function getButtonNextCss()
    {
        return (float)$this->rowButtonNextCss;
    }
    public function setButtonNextCss($rowButtonNextCss)
    {
        $this->rowButtonNextCss = (float)$rowButtonNextCss;
    }
    public function getButtonNextCssValue()
    {
        return $this->rowButtonNextCssValue;
    }
    public function setButtonNextCssValue($rowButtonNextCssValue)
    {
        $this->rowButtonNextCssValue = $rowButtonNextCssValue;
    }


    /**
     * Order
     */
    public function getOrder()
    {
        return (float)$this->rowOrder;
    }
    public function setOrder($rowOrder)
    {
        $this->rowOrder = (float)$rowOrder;
    }


    /**
     * Dates methods
     */
    public function getDateAdded($display = false, $format = 'Y-m-d H:i:s')
    {
        if (!$display) {
            return $this->rowAdded;
        }
        return Application::getFacadeApplication()->make('helper/date')->formatDateTime(strtotime($this->rowAdded->format($format)));
    }
    public function setDateAdded($rowAdded)
    {
        $this->rowAdded = $rowAdded;
	}

    public function getDateModified($display = false, $format = 'Y-m-d H:i:s')
    {
        if (!$display) {
            return $this->rowModified;
        }
        return Application::getFacadeApplication()->make('helper/date')->formatDateTime(strtotime($this->rowModified->format($format)));
    }
    public function setDateModified($rowModified)
    {
        $this->rowModified = $rowModified;
	}



    /**
     * Columns methods
     */
    public function getColumns()
    {
        return $this->columns;
    }
    public function getColumnByID($rowID)
    {
        return $this->columns->get($rowID);
    }
    public function clearColumns()
    {
        return $this->columns->clear();
    }
    public function getTotalColumns()
    {
        return $this->columns->count();
    }
    public function getNextColumnOrder()
    {
        $last = $this->columns->last();
        if (!is_object($last)) {
            return 0;
        }
        return $last->getOrder() + 1;
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


