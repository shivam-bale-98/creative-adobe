<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results;

defined('C5_EXECUTE') or die('Access Denied.');

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\DatabaseORM as dbORM;
use Concrete\Package\Formidable\Src\Formidable\Results\Result;
use Concrete\Core\User\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="FormidableResultLog", indexes={
 *  @ORM\Index(name="idx_resultID", columns={"resultID"}),
 * })
 */

class ResultLog {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $resultLogID;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $resultLogAction;

    /**
     * @ORM\Column(type="integer", length=10, options={"default" : 0})
     */
    protected $resultLogUser = 0;

    /**
     * @ORM\Column(type="datetime", options={"default" : "CURRENT_TIMESTAMP"})
     */
    protected $resultLogDateAdded;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Results\Result", inversedBy="logs")
     * @ORM\JoinColumn(name="resultID", referencedColumnName="resultID")
     */
    protected $result;

    /** Other variables */
    protected $resultLogUserObject;

	public static function getByID($resultLogID)
    {
        $em = dbORM::entityManager();
        return $em->find(get_class(), $resultLogID);
    }

    public function getItemID()
    {
        return $this->resultLogID;
    }

    public function getAction()
    {
        return $this->resultLogAction;
    }
    public function setAction($resultLogAction)
    {
        $this->resultLogAction = $resultLogAction;
    }

    public function getUser()
    {
        return $this->resultLogUser;
    }
    public function getUserObject()
    {
        if (is_object($this->resultLogUserObject)) {
            return $this->resultLogUserObject;
        }
        $this->resultLogUserObject = User::getByUserID($this->resultLogUser);
        return $this->resultLogUserObject;
    }
    public function setUser($resultLogUser)
    {
        if (!is_object($resultLogUser)) {
            $resultLogUser = (int)$resultLogUser;
            if ($resultLogUser > 0) {
                $resultLogUser = User::getByUserID($resultLogUser);
            }
        }
        if (is_object($resultLogUser)) {
            if ((int)$resultLogUser->getUserID() == 0) {
                $resultLogUser = 0;
            }
        }
        $this->resultLogUserObject = $resultLogUser;
        $this->resultLogUser = is_object($resultLogUser)?$resultLogUser->getUserID():(float)$resultLogUser;
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

    public function getDateAdded($display = false, $format = 'Y-m-d H:i:s')
    {
        if (!$display) {
            return $this->resultLogDateAdded;
        }
        return $this->resultLogDateAdded->format($format);
    }
    public function setDateAdded($resultLogDateAdded)
    {
        $this->resultLogDateAdded = $resultLogDateAdded;
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


