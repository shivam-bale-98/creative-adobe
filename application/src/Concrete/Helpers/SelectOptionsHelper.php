<?php

namespace Application\Concrete\Helpers;

use Concrete\Core\Attribute\Key\CollectionKey;

class SelectOptionsHelper
{

    public static function getSelectOptions($attrHandle)
    {
        $selectOptions = [];

        /** @var \Concrete\Core\Entity\Attribute\Key\PageKey $pageKey to getController() */
        $pageKey = CollectionKey::getByHandle($attrHandle);
        $pageKey->getController();

        /** To get the attribute select value */
        /** @var \Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption $option */
        /** @var  $category */
        foreach ($pageKey->getController()->getOptions() as $option) {
            $selectOptions[$option->getSelectAttributeOptionValue()] = $option->getSelectAttributeOptionValue();
        }

        return $selectOptions;
    }

    public static function getOptions($attrHandle, $options =  null)
    {
        $selectOptions = [];

        if($options)
            $selectOptions = $options;

        /** @var \Concrete\Core\Entity\Attribute\Key\PageKey $pageKey to getController() */
        $pageKey = CollectionKey::getByHandle($attrHandle);
        /** To get the attribute select value */ /** @var \Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption $option */
        /** @var  $category */
        if($pageKey) {
            foreach ($pageKey->getController()->getOptions() as $option) {
                $selectOptions[$option->getSelectAttributeOptionValue()] = tc('SelectAttributeValue', $option->getSelectAttributeOptionValue());
            }
            array_unique($selectOptions);
        }
        return $selectOptions;
    }
}