<?php    
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;

class Textarea extends Element {

    public function getName()
    {
        return t('Textarea');
    }

    public function getDescription() 
    {
        return t('Multi-line textfield');
    }

    public function getEditableOptions() 
    {       
        $options = [
            // disable            
            'option' => false, 
            'view' => false,
            
            // enable
            'default' => true,
            'placeholder' => true,
            'help' => true,          
            'range' => [
                'types' => [
                    'words' => t('Words'),
                    'chars' => t('Characters')
                ]
            ]            	    
        ];
        return array_merge(parent::getElementEditableOptions(), $options);
    }

    public function field() 
    {   
        return $this->app->make(Form::class)->textarea($this->element->getHandle(), $this->getPostData(), $this->tags());   
    }
}