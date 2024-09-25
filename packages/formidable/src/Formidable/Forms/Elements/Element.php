<?php
namespace Concrete\Package\Formidable\Src\Formidable\Forms\Elements;

defined('C5_EXECUTE') or die('Access Denied.');

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\DatabaseORM as dbORM;
use Concrete\Core\Support\Facade\Application;
use Doctrine\Common\Collections\ArrayCollection;
use Concrete\Package\Formidable\Src\Formidable\Formidable;

/**
 * @ORM\Entity
 * @ORM\Table(name="FormidableFormElement", indexes={
 *  @ORM\Index(name="idx_columnID", columns={"columnID"}),
 *  @ORM\Index(name="idx_elementHandle", columns={"elementHandle"}),
 *  @ORM\Index(name="idx_elementType", columns={"elementType"}),
 *  @ORM\Index(name="idx_elementOrder", columns={"elementOrder"}),
 * })
 */

class Element {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $elementID;

    /**
     * @ORM\Column(type="string", length=10, options={"default" : "text"})
     */
    protected $elementType = 'text';


    /**
     * @ORM\Column(type="string", length=150)
     */
    protected $elementHandle;

	/**
     * @ORM\Column(type="string", length=150)
     */
    protected $elementName;


    /**
     * @ORM\Column(type="integer", length=1, options={"default" : 0})
     */
    protected $elementRequired = 0;


    /**
     * @ORM\Column(type="integer", length=10, options={"default" : 0})
     */
    protected $elementOrder = 0;


    /**
     * @ORM\Column(type="datetime", options={"default" : "CURRENT_TIMESTAMP"})
     */
    protected $elementAdded;

    /**
     * @ORM\Column(type="datetime", options={"default" : "CURRENT_TIMESTAMP"})
     */
    protected $elementModified;

    /**
    * @ORM\OneToMany(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Forms\Elements\ElementProperty", mappedBy="element", cascade={"persist"}, orphanRemoval=true, indexBy="propertyHandle")
    * @ORM\OrderBy({"propertyID" = "ASC"})
    */
    protected $properties;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Forms\Columns\Column", inversedBy="elements")
     * @ORM\JoinColumn(name="columnID", referencedColumnName="columnID")
     */
    protected $column;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Results\ResultElement", mappedBy="element", cascade={"persist"}, orphanRemoval=true)
     */
    protected $results;

    /**
     * Variable for the Type of element
     */
    protected $elementTypeObject;
    protected $assets;

    public function __construct()
    {
        $this->properties = new ArrayCollection();
        $this->assets = new ArrayCollection();
        $this->results = new ArrayCollection();
    }

    public function __clone()
    {
        if ($this->elementID) {
            $this->elementID = null;

            // Disable the setting of name and handle for now
            // The name will be set by the editor
            //$th = Application::getFacadeApplication()->make('helper/text');
            //$this->setName(t('%s (copy)', $this->getName()));
            //$this->setHandle($th->handle($this->getName()));
            $this->setDateAdded(new \DateTime());
            $this->setDateModified(new \DateTime());

            $temp = new ArrayCollection();
            foreach ($this->properties as $item) {
                $clone = clone $item;
                $clone->setElement($this);
                $temp->add($clone);
            }
            $this->properties = $temp;
        }
    }

	public static function getByID($elementID)
    {
        $em = dbORM::entityManager();
        return $em->find(get_class(), $elementID);
    }

    public static function getByHandle($elementHandle, $form)
    {
        //$em = dbORM::entityManager();
        //return $em->getRepository(get_class())->findOneBy(['elementHandle' => $elementHandle]);
        $db = Application::getFacadeApplication()->make('database');
        $query = $db->connection()->createQueryBuilder();
        $query->select('e.elementID')
              ->from('FormidableFormElement', 'e')
              ->leftjoin('e', 'FormidableFormColumn', 'c', 'e.columnID = c.columnID')
              ->leftjoin('c', 'FormidableFormRow', 'r', 'c.rowID = r.rowID')
              ->leftjoin('r', 'FormidableForm', 'f', 'r.formID = f.formID')
              ->where('e.elementHandle = :elementHandle AND f.formID = :formID');
        $query->setParameters(['elementHandle' => $elementHandle, 'formID' => $form->getItemID()]);
        $item = $query->execute()->fetchOne();
        if ($item) {
            return self::getByID($item);
        }
        return false;
    }

    public function getType()
    {
        return $this->elementType;
    }
    public function setType($elementType)
    {
        if (is_object($elementType)) {
            $elementType = $elementType->getHandle();
        }
        $this->elementType = $elementType;
    }
    public function getTypeName()
    {
        $type = $this->getTypeObject();
        if (!is_object($type)) {
           return t('(unknown)');
        }
        return $type->getName();
    }

    public function getTypeObject()
    {
        if (!is_object($this->elementTypeObject)) {
            $this->getTypeObjectByHandle();
        }
        return $this->elementTypeObject;
    }
    public function setTypeObject($elementTypeObject)
    {
        $this->elementTypeObject = $elementTypeObject;
    }
    public function getTypeObjectByHandle()
    {
        $type = Formidable::getElementTypeByHandle($this->getType());
        if (!is_object($type)) {
            return false;
        }
        $type->setElement($this);
        $this->setTypeObject($type);
        return $type;
    }


    public function getItemID()
    {
        return $this->elementID;
    }


    public function getHandle()
    {
        return $this->elementHandle;
    }
    public function setHandle($elementHandle)
    {
        $this->elementHandle = $elementHandle;
    }


    public function getName()
    {
        return $this->elementName;
    }
    public function setName($elementName)
    {
        $this->elementName = $elementName;
    }


    /**
     * Required methods
     */
    public function isRequired()
    {
        return $this->elementRequired==1?true:false;
    }
    public function getRequired()
    {
        return (float)$this->elementRequired;
    }
    public function setRequired($elementRequired)
    {
        $this->elementRequired = (float)$elementRequired;
    }


    /**
     * Order
     */
    public function getOrder()
    {
        return (float)$this->elementOrder;
    }
    public function setOrder($elementOrder)
    {
        $this->elementOrder = (float)$elementOrder;
    }


    /**
     * Dates methods
     */
    public function getDateAdded($display = false, $format = 'Y-m-d H:i:s')
    {
        if (!$display) {
            return $this->elementAdded;
        }
        return Application::getFacadeApplication()->make('helper/date')->formatDateTime(strtotime($this->elementAdded->format($format)));
    }
    public function setDateAdded($elementAdded)
    {
        $this->elementAdded = $elementAdded;
	}

    public function getDateModified($display = false, $format = 'Y-m-d H:i:s')
    {
        if (!$display) {
            return $this->elementModified;
        }
        return Application::getFacadeApplication()->make('helper/date')->formatDateTime(strtotime($this->elementModified->format($format)));
    }
    public function setDateModified($elementModified)
    {
        $this->elementModified = $elementModified;
	}


    /**
     * Assets
     */
    public function getProperties()
    {
        return $this->properties;
    }
    public function addProperty($property)
    {
        return $this->properties->add($property);
    }
    public function getProperty($handle, $type = 'object')
    {
        $prop = $this->properties->get($handle);
        if (!is_object($prop)) {
            switch ($type) {
                case 'int':
                case 'integer':
                case 'float':
                case 'double':
                    return 0;
                break;
                case 'string':
                    return '';
                break;
                case 'array':
                    return [];
                break;
                case 'object':
                case 'bool':
                case 'boolean':
                default:
                    return false;
                break;
            }
        }
        if ($type == 'object') {
            return $prop;
        }
        return $prop->getValue($type);
    }

    public function removeProperty($property)
    {
        if (is_object($property)) {
            return $this->properties->removeElement($property);
        }
        return $this->properties->remove($property);
    }
    public function getTotalProperties()
    {
        return $this->properties->count();
    }


    /**
     * Assets
     */
    public function registerAsset($type, $asset = '')
    {
        $assets = $type;
        if (!empty($asset)) {
            $assets = [[$type, $asset]];
        }
        $this->registerFormAssets($assets);
    }

    public function registerFormAssets($assets)
    {
        $form = $this->getForm();
        $form->registerAssets($assets);
    }


    /**
     * Dependency
     */
    public function registerDependency($dependency)
    {
        $form = $this->getForm();
        $form->registerDependency($dependency);
    }


    /**
     * Column
     */
    public function getColumn()
    {
        return $this->column;
    }
    public function setColumn($column)
    {
        if (!is_object($column)) {
            $column = Column::getByID($column);
        }
        $this->column = $column;
    }

    /**
     * Form
     */
    public function getForm()
    {
        return $this->getColumn()->getRow()->getForm();
    }


    /**
     * Rendering
     */
    public function renderStart($template = '')
    {
        if (empty($template)) {
            $template = '<div class="form-group" data-formidable-type="%s" data-formidable-handle="%s">';
        }
        echo t($template, $this->getType(), $this->getHandle());
    }

    public function renderEnd($template = '')
    {
        if (empty($template)) {
            $template = '</div>';
        }
        echo $template;
    }

    public function label($format = '<label for="%s">%s</label>')
    {
        if (method_exists($this->getTypeObject(), 'label')) {
            return $this->getTypeObject()->label($format);
        }
        return '(no label)';
    }

    public function field()
    {
        if (method_exists($this->getTypeObject(), 'field')) {
            return $this->getTypeObject()->field();
        }
        return '(no field)';
    }

    public function validate()
    {
        if (method_exists($this->getTypeObject(), 'validate')) {
            return $this->getTypeObject()->validate();
        }
        return true;
    }

    public function getPostData()
    {
        if (method_exists($this->getTypeObject(), 'getPostData')) {
            return $this->getTypeObject()->getPostData();
        }
        return '';
    }

    public function getProcessedData()
    {
        if (method_exists($this->getTypeObject(), 'getProcessedData')) {
            return $this->getTypeObject()->getProcessedData();
        }
        return '';
    }

    public function getDisplayData($data, $format = 'plain')
    {
        if (method_exists($this->getTypeObject(), 'getDisplayData')) {
            return $this->getTypeObject()->getDisplayData($data, $format);
        }
        return '';
    }

    public function getResultData($r)
    {
        if (method_exists($this->getTypeObject(), 'getResultData')) {
            return $this->getTypeObject()->getResultData();
        }
        $result = $r->getElementDataByElement($this);
        if (is_object($result)) {
            return $result->getDisplayValue();
        }
        return '';
    }

    public function filterResult(&$list, $value, $comparison = 'LIKE')
    {
        if (method_exists($this->getTypeObject(), 'filterResult')) {
            return $this->getTypeObject()->filterResult($list, $value);
        }

        $elementTableName = 'resultElement'.$this->getItemID();
        $elementResultValue = 'resultElementValue'.$this->getItemID();

        $query = $list->getQueryObject();
        $query->addSelect($elementTableName.'.resultElementValueDisplay as element_'.$this->getHandle());
        $query->leftJoin('result', 'FormidableResultElement', $elementTableName, 'result.resultID = '.$elementTableName.'.resultID AND '.$elementTableName.'.elementID = '.$this->getItemID());
        $query->andWhere($elementTableName.'.resultElementValueDisplay '.$comparison.' :'.$elementResultValue);
        if (in_array($comparison, ['LIKE', 'NOT LIKE'])) {
            $value = '%' . $value . '%';
        }
        $query->setParameter($elementResultValue, $value);
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