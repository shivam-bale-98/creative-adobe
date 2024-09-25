<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search\RequestModifier;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Search\Query\Modifier\AbstractRequestModifier;

class FormID extends AbstractRequestModifier 
{
    public function modify($query)
    {
        $bag = $this->getParameterBag();
        if ($bag->has('formID')) {
            $formID = (int)$bag->get('formID');
            if (in_array($formID, array_keys($this->provider->getFormOptions()), false)) {
                $query->setFormID($formID);
            }
        }

    }

}

