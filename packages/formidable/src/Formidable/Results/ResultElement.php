<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results;

defined('C5_EXECUTE') or die('Access Denied.');

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\DatabaseORM as dbORM;
use Concrete\Package\Formidable\Src\Formidable\Forms\Elements\Element;
use Concrete\Package\Formidable\Src\Formidable\Results\Result;
use Concrete\Core\Http\Service\Json;

/**
 * @ORM\Entity
 * @ORM\Table(name="FormidableResultElement", indexes={
 *  @ORM\Index(name="idx_resultID", columns={"resultID"}),
 *  @ORM\Index(name="idx_elementID", columns={"elementID"}),
 * })
 */

class ResultElement {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $resultElementID;

	/**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $resultElementValueDisplay;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $resultElementValuePost;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Results\Result", inversedBy="answers")
     * @ORM\JoinColumn(name="resultID", referencedColumnName="resultID")
     */
    protected $result;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Forms\Elements\Element", inversedBy="results")
     * @ORM\JoinColumn(name="elementID", referencedColumnName="elementID")
     */
    protected $element;

    // other variables
    protected $resultElementHandle;

	public static function getByID($resultElementID)
    {
        $em = dbORM::entityManager();
        return $em->find(get_class(), $resultElementID);
    }

    public function getResult()
    {
        return $this->result;
    }
    public function setResult($result)
    {
        if (!is_object($result)) {
            $result = Result::getByID($result);
        }
        $this->result = $result;
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
        return $this->resultElementID;
    }

    public function getDisplayValue()
    {
        return $this->resultElementValueDisplay;
    }
    public function setDisplayValue($resultElementValueDisplay)
    {
        $this->resultElementValueDisplay = $resultElementValueDisplay;
    }

    public function getPostValue()
    {
        if (preg_match('/^\[\{.*\}\]$/', $this->resultElementValuePost) != 0) {
            return (new Json())->decode($this->resultElementValuePost, true);
        }
        return $this->resultElementValuePost;
    }
    public function setPostValue($resultElementValuePost)
    {
        if (is_array($resultElementValuePost)) {
            $resultElementValuePost = (new Json())->encode($resultElementValuePost);
        }
        $this->resultElementValuePost = $resultElementValuePost;
    }

    public function __toString()
    {
        return (string)$this->getDisplayValue();
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


