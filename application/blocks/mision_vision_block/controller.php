<?php namespace Application\Block\MisionVisionBlock;

defined("C5_EXECUTE") or die("Access Denied.");

use Concrete\Core\Block\BlockController;
use Core;
use File;
use Page;

class Controller extends BlockController
{
    public $btFieldsRequired = [];
    protected $btExportFileColumns = ['leftImage', 'rightImage'];
    protected $btTable = 'btMisionVisionBlock';
    protected $btInterfaceWidth = 400;
    protected $btInterfaceHeight = 500;
    protected $btIgnorePageThemeGridFrameworkContainer = false;
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $pkg = false;
    
    public function getBlockTypeName()
    {
        return t("Mission Vision Block");
    }

    public function getSearchableContent()
    {
        $content = [];
        $content[] = $this->leftTitle;
        $content[] = $this->leftDesc;
        $content[] = $this->rightTitle;
        $content[] = $this->rightDesc;
        $content[] = $this->bgColor;
        return implode(" ", $content);
    }

    public function view()
    {
        
        if ($this->leftImage && ($f = File::getByID($this->leftImage)) && is_object($f)) {
            $this->set("leftImage", $f);
        } else {
            $this->set("leftImage", false);
        }
        
        if ($this->rightImage && ($f = File::getByID($this->rightImage)) && is_object($f)) {
            $this->set("rightImage", $f);
        } else {
            $this->set("rightImage", false);
        }
    }

    public function add()
    {
        $this->addEdit();
    }

    public function edit()
    {
        $this->addEdit();
    }

    protected function addEdit()
    {
        $this->requireAsset('core/file-manager');
        $this->set('btFieldsRequired', $this->btFieldsRequired);
        $this->set('identifier_getString', Core::make('helper/validation/identifier')->getString(18));
    }

    public function save($args)
    {
        $args['leftImage'] = isset($args['leftImage']) && is_numeric($args['leftImage']) ? $args['leftImage'] : 0;
        $args['rightImage'] = isset($args['rightImage']) && is_numeric($args['rightImage']) ? $args['rightImage'] : 0;
        if (!isset($args["hideBlock"]) || trim($args["hideBlock"]) == "" || !in_array($args["hideBlock"], [0, 1])) {
            $args["hideBlock"] = '';
        }
        if (!isset($args["removePaddingTop"]) || trim($args["removePaddingTop"]) == "" || !in_array($args["removePaddingTop"], [0, 1])) {
            $args["removePaddingTop"] = '';
        }
        if (!isset($args["removePaddingBottom"]) || trim($args["removePaddingBottom"]) == "" || !in_array($args["removePaddingBottom"], [0, 1])) {
            $args["removePaddingBottom"] = '';
        }
        parent::save($args);
    }

    public function validate($args)
    {
        $e = Core::make("helper/validation/error");
        if (in_array("leftImage", $this->btFieldsRequired) && (trim($args["leftImage"]) == "" || !is_object(File::getByID($args["leftImage"])))) {
            $e->add(t("The %s field is required.", t("Left Image")));
        }
        if (in_array("leftTitle", $this->btFieldsRequired) && (trim($args["leftTitle"]) == "")) {
            $e->add(t("The %s field is required.", t("Left Title")));
        }
        if (in_array("leftDesc", $this->btFieldsRequired) && trim($args["leftDesc"]) == "") {
            $e->add(t("The %s field is required.", t("Left Description")));
        }
        if (in_array("rightImage", $this->btFieldsRequired) && (trim($args["rightImage"]) == "" || !is_object(File::getByID($args["rightImage"])))) {
            $e->add(t("The %s field is required.", t("right image")));
        }
        if (in_array("rightTitle", $this->btFieldsRequired) && (trim($args["rightTitle"]) == "")) {
            $e->add(t("The %s field is required.", t("Right Title")));
        }
        if (in_array("rightDesc", $this->btFieldsRequired) && trim($args["rightDesc"]) == "") {
            $e->add(t("The %s field is required.", t("Right Description")));
        }
        if (in_array("hideBlock", $this->btFieldsRequired) && (trim($args["hideBlock"]) == "" || !in_array($args["hideBlock"], [0, 1]))) {
            $e->add(t("The %s field is required.", t("Hide Block")));
        }
        if (in_array("bgColor", $this->btFieldsRequired) && (trim($args["bgColor"]) == "")) {
            $e->add(t("The %s field is required.", t("background color")));
        }
        if (in_array("removePaddingTop", $this->btFieldsRequired) && (trim($args["removePaddingTop"]) == "" || !in_array($args["removePaddingTop"], [0, 1]))) {
            $e->add(t("The %s field is required.", t("remove padding top")));
        }
        if (in_array("removePaddingBottom", $this->btFieldsRequired) && (trim($args["removePaddingBottom"]) == "" || !in_array($args["removePaddingBottom"], [0, 1]))) {
            $e->add(t("The %s field is required.", t("remove padding bottom")));
        }
        return $e;
    }

    public function composer()
    {
        $this->edit();
    }
}