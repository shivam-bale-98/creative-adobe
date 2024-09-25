<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search;

defined('C5_EXECUTE') or die('Access Denied.');

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\Field\Field\ElementField;
use Concrete\Core\Entity\Search\Query as QueryParent;

/**
 * @ORM\Embeddable
 */
class Query extends QueryParent
{   
    /**
     * @ORM\Column(type="smallint")
     */
    protected $formID;

    /**
     * @ORM\Column(type="object")
     */
    protected $fields = [];

    /**
     * @ORM\Column(type="object")
     */
    protected $columns;
    
    /**
     * @ORM\Column(type="smallint")
     */
    private $itemsPerPage;
    

    public function setFormID($formID)
    {        
        $this->formID = (int)$formID;
    }

    public function getFormID()
    {
        return $this->formID;
    }

    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = is_numeric($itemsPerPage) ? $itemsPerPage : QueryParent::MAX_ITEMS_PER_PAGE;
    }

    public function getItemsPerPage()
    {
        return (int)$this->itemsPerPage;
    }

    public function jsonSerialize()
    {
        return [
            'formID' => (int)$this->getFormID(),
            'fields' => $this->getFields(),
            'columnSet' => $this->getColumns(),
            'itemsPerPage' => (int)$this->getItemsPerPage(),            
        ];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        $searchProvider = $context['searchProvider'];
        
        $this->setFormID((int)$data['formID']);

        $fieldManager = $searchProvider->getFieldManager();
        foreach($data['fields'] as $fieldRecord) {
            $field = $fieldManager->getFieldByKey($fieldRecord['key']);
            $element = null;
            if ($field instanceof ElementField) {
                $element = $field->getElement();
            }
            $field = $denormalizer->denormalize($fieldRecord, get_class($field), 'json', $context);
            if (is_object($element)) {
                $field->setElement($element);
            }
            $this->addField($field);
        }
        
        $columnSet = $searchProvider->getBaseColumnSet();
        $all = $searchProvider->getAllColumnSet();
        foreach((array)$data['columnSet']['columns'] as $columnRecord) {
            $column = $all->getColumnByKey($columnRecord['columnKey']);
            $columnSet->addColumn($column);
            if ($data['columnSet']['sortColumn'] == $columnRecord['columnKey']) {
                $columnSet->setDefaultSortColumn($column);
            }
        }
        $this->setColumns($columnSet);
        $this->setItemsPerPage((int)$data['itemsPerPage']);  
    }
}