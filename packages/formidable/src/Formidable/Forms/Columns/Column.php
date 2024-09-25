<?php
namespace Concrete\Package\Formidable\Src\Formidable\Forms\Columns;

defined('C5_EXECUTE') or die('Access Denied.');

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\DatabaseORM as dbORM;
use Concrete\Core\Support\Facade\Application;
use Doctrine\Common\Collections\ArrayCollection;
use Concrete\Package\Formidable\Src\Formidable\Forms\Rows\Row;

/**
 * @ORM\Entity
 * @ORM\Table(name="FormidableFormColumn", indexes={
 *  @ORM\Index(name="idx_rowID", columns={"rowID"}),
 *  @ORM\Index(name="idx_columnHandle", columns={"columnHandle"}),
 *  @ORM\Index(name="idx_columnWidth", columns={"columnWidth"}),
 * })
 */

class Column {

    const COLUMN_TYPE_COLUMN = 'column';
    const COLUMN_TYPE_FIELDSET = 'fieldset';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $columnID;

    /**
     * @ORM\Column(type="string", length=10, nullable=true, options={"default": "column"})
     */
    protected $columnType = 'column';

    /**
     * @ORM\Column(type="string", length=150)
     */
    protected $columnHandle;

	/**
     * @ORM\Column(type="string", length=150)
     */
    protected $columnName;

    /**
     * @ORM\Column(type="integer", length=10, options={"default" : 12})
     */
    protected $columnWidth = 12;

    /**
     * @ORM\Column(type="integer", length=1, options={"default" : 0})
     */
    protected $columnCss = 0;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $columnCssValue;

    /**
     * @ORM\Column(type="integer", length=10, options={"default" : 0})
     */
    protected $columnOrder = 0;


    /**
     * @ORM\Column(type="datetime", options={"default" : "CURRENT_TIMESTAMP"})
     */
    protected $columnAdded;

    /**
     * @ORM\Column(type="datetime", options={"default" : "CURRENT_TIMESTAMP"})
     */
    protected $columnModified;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Forms\Rows\Row", inversedBy="columns")
     * @ORM\JoinColumn(name="rowID", referencedColumnName="rowID")
     */
    protected $row;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Forms\Elements\Element", mappedBy="column", cascade={"persist"}, orphanRemoval=true, indexBy="elementID")
     * @ORM\OrderBy({"elementOrder" = "ASC"})
     */
    protected $elements;


    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

    public function __clone()
    {
        if ($this->columnID) {
            $this->columnID = null;

            // Disable the setting of name and handle for now
            // The name will be set by the editor
            //$th = Application::getFacadeApplication()->make('helper/text');
            //$this->setName(t('%s (copy)', $this->getName()));
            //$this->setHandle($th->handle($this->getName()));
            $this->setDateAdded(new \DateTime());
            $this->setDateModified(new \DateTime());

            $temp = new ArrayCollection();
            foreach ($this->elements as $item) {
                $clone = clone $item;
                $clone->setColumn($this);
                $temp->add($clone);
            }
            $this->elements = $temp;
        }
    }


	public static function getByID($columnID)
    {
        $em = dbORM::entityManager();
        return $em->find(get_class(), $columnID);
    }

    public static function getByHandle($columnHandle, $form)
    {
        //$em = dbORM::entityManager();
        //return $em->getRepository(get_class())->findOneBy(['columnHandle' => $columnHandle]);

        $db = Application::getFacadeApplication()->make('database');
        $query = $db->connection()->createQueryBuilder();
        $query->select('c.columnID')
              ->from('FormidableFormColumn', 'c')
              ->leftjoin('c', 'FormidableFormRow', 'r', 'c.rowID = r.rowID')
              ->leftjoin('r', 'FormidableForm', 'f', 'r.formID = f.formID')
              ->where('c.columnHandle = :columnHandle AND f.formID = :formID');
        $query->setParameters(['columnHandle' => $columnHandle, 'formID' => $form->getItemID()]);
        $item = $query->execute()->fetchOne();
        if ($item) {
            return self::getByID($item);
        }
        return false;
    }


    public function getRow()
    {
        return $this->row;
    }
    public function setRow($row)
    {
        if (!is_object($row)) {
            $row = Row::getByID($row);
        }
        $this->row = $row;
    }


    public function getItemID()
    {
        return $this->columnID;
    }


    public function getHandle()
    {
        return $this->columnHandle;
    }
    public function setHandle($columnHandle)
    {
        $this->columnHandle = $columnHandle;
    }


    public function getName()
    {
        return $this->columnName;
    }
    public function setName($columnName)
    {
        $this->columnName = $columnName;
    }


    /**
     * Column width
     */
    public function getWidth()
    {
        return (float)$this->columnWidth;
    }
    public function setWidth($columnWidth)
    {
        $this->columnWidth = (float)$columnWidth;
    }
    public static function getWidths()
    {
		$obj = new self;
		$options = [];
		foreach(range(1, 12) as $v) {
			$options[$v] = $obj->getWidthText($v);
		}
		return $options;
	}
    public function getWidthText($option = null)
    {
        if (empty($option)) {
           $option = $this->getWidth();
        }
		return t('Column %s - %s%% (col-%s)', $option, round(100 * ($option/12), 1), $option);
    }


    /**
     * Column type
     */
    public function getType()
    {
        return $this->columnType;
    }
    public function setType($columnType)
    {
        $this->columnType = $columnType;
    }
    public static function getTypes()
    {
		$obj = new self;
        $available = [
			self::COLUMN_TYPE_COLUMN,
            self::COLUMN_TYPE_FIELDSET,
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
			case self::COLUMN_TYPE_COLUMN:
                $text = t('Column');
			break;
			case self::COLUMN_TYPE_FIELDSET:
                $text = t('Fieldset');
            break;

		}
		return $text;
    }


    /**
     * CSS
     */
    public function getCss()
    {
        return (float)$this->columnCss;
    }
    public function setCss($columnCss)
    {
        $this->columnCss = (float)$columnCss;
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
        return $this->columnCssValue;
    }
    public function setCssValue($columnCssValue)
    {
        $this->columnCssValue = $columnCssValue;
    }


    /**
     * Rendering
     */
    public function renderStart($template = '')
    {
        $class = '';
        if (empty($template)) {
            $template = '<div class="%s">';
            $class = 'col-12 col-sm-'.$this->getWidth().' ';
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
     * Order
     */
    public function getOrder()
    {
        return (float)$this->columnOrder;
    }
    public function setOrder($columnOrder)
    {
        $this->columnOrder = (float)$columnOrder;
    }


    /**
     * Properties
     */
    public function getProperties()
    {
        return $this->properties;
    }
    public function getPropertyByHandle($handle)
    {
        return $this->properties->get($handle);
    }
    public function getTotalPropertiess()
    {
        return $this->properties->count();
    }


    /**
     * Dates methods
     */
    public function getDateAdded($display = false, $format = 'Y-m-d H:i:s')
    {
        if (!$display) {
            return $this->columnAdded;
        }
        return Application::getFacadeApplication()->make('helper/date')->formatDateTime(strtotime($this->columnAdded->format($format)));
    }
    public function setDateAdded($columnAdded)
    {
        $this->columnAdded = $columnAdded;
	}

    public function getDateModified($display = false, $format = 'Y-m-d H:i:s')
    {
        if (!$display) {
            return $this->columnModified;
        }
        return Application::getFacadeApplication()->make('helper/date')->formatDateTime(strtotime($this->columnModified->format($format)));
    }
    public function setDateModified($columnModified)
    {
        $this->columnModified = $columnModified;
	}


    /**
     * Elements methods
     */
    public function getElements()
    {
        return $this->elements;
    }
    public function getElementByID($elementID)
    {
        return $this->elements->get($elementID);
    }
    public function clearElements()
    {
        return $this->elements->clear();
    }
    public function getTotalElements()
    {
        return $this->elements->count();
    }
    public function getNextElementOrder()
    {
        $last = $this->elements->last();
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


