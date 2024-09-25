<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search;

defined('C5_EXECUTE') or die('Access Denied.');

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Entity\Search\SavedSearch as SavedSearchParent;

/**
 * @ORM\Entity
 * @ORM\Table(name="SavedFormidableSearchQueries")
 */
class SavedSearch extends SavedSearchParent
{

    /**
     * @ORM\Id 
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;  
    
    /**
     * @ORM\Column(type="string")
     */
    protected $presetName;

    /** @ORM\Embedded(class = "\Concrete\Package\Formidable\Src\Formidable\Results\Search\Query") */
    protected $query = null;
    
}
