<?php
namespace Concrete\Package\Formidable\Src\Formidable\Forms\Elements;

defined('C5_EXECUTE') or die('Access Denied.');

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\DatabaseORM as dbORM;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;
use Concrete\Core\Http\Service\Json;

/**
 * @ORM\Entity
 * @ORM\Table(name="FormidableFormElementProperty", indexes={
 *  @ORM\Index(name="idx_propertyHandle", columns={"propertyHandle"}),
 *  @ORM\Index(name="idx_elementID", columns={"elementID"}),
 * })
 */

class ElementProperty {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $propertyID;

    /**
     * @ORM\Column(type="string", length=150)
     */
    protected $propertyHandle;

	/**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $propertyValue;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Forms\Elements\Element", inversedBy="properties", cascade={"persist"})
     * @ORM\JoinColumn(name="elementID", referencedColumnName="elementID", nullable=true, onDelete="CASCADE")
     */
    protected $element;


    public function __clone()
    {
        if ($this->propertyID) {
            $this->propertyID = null;
        }
    }


	public static function getByID($propertyID)
    {
        $em = dbORM::entityManager();
        return $em->find(get_class(), $propertyID);
    }

    public static function getByHandle($propertyHandle)
    {
        $em = dbORM::entityManager();
        return $em->getRepository(get_class())->findOneBy(['propertyHandle' => $propertyHandle]);
    }


    public function getElement()
    {
        return $this->element;
    }
    public function setElement($element)
    {
        if (!is_object($element)) {
            $element = Element::getByID($element);
        }
        $this->element = $element;
    }


    public function getItemID()
    {
        return $this->propertyID;
    }


    public function getHandle()
    {
        return $this->propertyHandle;
    }
    public function setHandle($propertyHandle)
    {
        $this->propertyHandle = $propertyHandle;
    }


    public function getValue($type = 'string')
    {
        if (preg_match('/^\[.*\]$/', $this->propertyValue) != 0) {
            return (array)(new Json())->decode($this->propertyValue, true);
        }
        switch ($type) {
            case 'bool':
            case 'boolean':
                return (int)$this->propertyValue==1?true:false;
            break;

            case 'array':
                return (array)$this->propertyValue;
            break;

            case 'int':
            case 'integer':
                return (int)$this->propertyValue;
            break;

            case 'float':
            case 'double':
                return (float)$this->propertyValue;
            break;

            case 'string':
            default:
                return (string)$this->propertyValue;
            break;
        }
        return false;
    }
    public function setValue($propertyValue)
    {
        if (is_array($propertyValue)) {
            $propertyValue = (new Json())->encode($propertyValue);
        }
        $this->propertyValue = $propertyValue;
    }

    public function __toString()
    {
        return $this->getValue('string');
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


