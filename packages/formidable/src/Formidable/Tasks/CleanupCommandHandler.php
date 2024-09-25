<?php
namespace Concrete\Package\Formidable\Src\Formidable\Tasks;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\File\Service\File;
use Concrete\Package\Formidable\Src\Formidable\Forms\FormList;
use Concrete\Package\Formidable\Src\Formidable\Results\ResultList;
use Concrete\Package\Formidable\Src\Formidable\Tasks\CleanupCommand;

class CleanupCommandHandler {

    public function __invoke(CleanupCommand $command)
    {
        // remove tmp files
        $f = new File();
        $f->removeAll(DIR_APPLICATION.'/files/tmp/formidable/');       
        
        // check gdpr
        $list = new FormList();
        $list->filterByPrivacyEnabled();
        $list->filterByPrivacyRemove();
        $forms = $list->getResults();

        foreach ((array)$forms as $form) {
            
            $value = $form->getPrivacyRemoveValue();
            $type = $form->getPrivacyRemoveType();

            if ((int)$value == 0 || empty($type)) {
                continue;
            }

            $list = new ResultList();
            $list->filterByForm($form);
            $list->filterByDateAdded(null, (new \DateTime('- '.$value.' '.$type))->format("Y-m-d")); // from, to
            $results = $list->getResults();

            foreach ((array)$results as $result) {

                // remove any files if needed
                if ($form->getPrivacyRemoveFiles()) {
                    $elements = $form->getElementsByType(['file']);
                    foreach ((array)$elements as $element) {
                        $data = $result->getElementDataByElement($element);
                        if ($data) {
                            $files = $element->getDisplayData($data->getPostValue(), 'object');
                            foreach ((array)$files as $f) {
                                $f->delete();
                            }
                        }
                    }
                }

                $result->delete();
            }
        }
    }
}