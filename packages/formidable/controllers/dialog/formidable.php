<?php
namespace Concrete\Package\Formidable\Controller\Dialog;
use \Concrete\Package\FormidableFull\Src\Formidable as FormidableForm;
use \Concrete\Package\FormidableFull\Src\Formidable\Form;
use \Concrete\Core\Controller\Controller;
use BlockType;
use \Concrete\Core\Http\Service\Json as Json;
use Page;
use Block;
use Core;
use Events;
class Formidable extends Controller {
    protected $form = '';
    public function view() {
        $jsn = new Json();
        $bID = intval($this->post('bID'));
        $token = Core::make('token');
        if (!$token->validate('formidable')) {
            header('Content-type: application/json');
            echo $jsn->encode(array('message' => $token->getErrorMessage()));
            die();
        }
        $bt = BlockType::getByHandle('formidable');
        /** @var \Concrete\Core\Block\Block $bt */
        /** @var \Concrete\Package\Formidable\Block\FormidableForm\Controller $btController */
        if (intval($this->post('bID')) != 0) $bt = Block::getByID($bID);
        if (!is_object($bt)) return false;
        $btController = $bt->getController();
        $btController->on_start();
        switch ($this->post('action')) {
            case 'submit':
                $btController->action_submit($bID);
                break;
            case 'upload_file':
                $btController->action_upload();
                break;
            case 'delete_file':
                $btController->action_delete();
                break;
        }
        header('Content-type: application/json');
        echo $jsn->encode(array('error' => t('Failed')));
        exit();
    }
}

