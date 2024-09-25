<?php namespace Application\Block\HomeBanner;

defined("C5_EXECUTE") or die("Access Denied.");

use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Core;
use File;
use Page;

class Controller extends BlockController
{
    public $btFieldsRequired = [];
    protected $btExportFileColumns = ['Dimage', 'Mimage'];
    protected $btTable = 'btHomeBanner';
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
        return t("Home Banner");
    }

    public function getSearchableContent()
    {
        $content = [];
        $content[] = $this->videoURL;
        $content[] = $this->subTitle;
        $content[] = $this->title;
        return implode(" ", $content);
    }

    public function view()
    {
        
        if ($this->Dimage && ($f = File::getByID($this->Dimage)) && is_object($f)) {
            $this->set("Dimage", $f);
        } else {
            $this->set("Dimage", false);
        }
        
        if ($this->Mimage && ($f = File::getByID($this->Mimage)) && is_object($f)) {
            $this->set("Mimage", $f);
        } else {
            $this->set("Mimage", false);
        }
        $this->set('title', LinkAbstractor::translateFrom($this->title));
    }

    public function add()
    {
        $this->addEdit();
    }

    public function edit()
    {
        $this->addEdit();
        
        $this->set('title', LinkAbstractor::translateFromEditMode($this->title));
    }

    protected function addEdit()
    {
        $this->requireAsset('core/file-manager');
        $this->requireAsset('redactor');
        $this->set('btFieldsRequired', $this->btFieldsRequired);
        $this->set('identifier_getString', Core::make('helper/validation/identifier')->getString(18));
    }

    public function save($args)
    {
        if (!isset($args["hideBlock"]) || trim($args["hideBlock"]) == "" || !in_array($args["hideBlock"], [0, 1])) {
            $args["hideBlock"] = '';
        }
        $args['Dimage'] = isset($args['Dimage']) && is_numeric($args['Dimage']) ? $args['Dimage'] : 0;
        $args['Mimage'] = isset($args['Mimage']) && is_numeric($args['Mimage']) ? $args['Mimage'] : 0;
        $args['title'] = LinkAbstractor::translateTo($args['title']);
        parent::save($args);
    }

    public function validate($args)
    {
        $e = Core::make("helper/validation/error");
        if (in_array("hideBlock", $this->btFieldsRequired) && (trim($args["hideBlock"]) == "" || !in_array($args["hideBlock"], [0, 1]))) {
            $e->add(t("The %s field is required.", t("Hide  Block")));
        }
        if (in_array("Dimage", $this->btFieldsRequired) && (trim($args["Dimage"]) == "" || !is_object(File::getByID($args["Dimage"])))) {
            $e->add(t("The %s field is required.", t("Desktop Image (2000X2000)")));
        }
        if (in_array("Mimage", $this->btFieldsRequired) && (trim($args["Mimage"]) == "" || !is_object(File::getByID($args["Mimage"])))) {
            $e->add(t("The %s field is required.", t("Mobile Image")));
        }
        if (in_array("videoURL", $this->btFieldsRequired) && (trim($args["videoURL"]) == "")) {
            $e->add(t("The %s field is required.", t("Video Link(Distribution Link)")));
        }
        if (in_array("subTitle", $this->btFieldsRequired) && (trim($args["subTitle"]) == "")) {
            $e->add(t("The %s field is required.", t("Sub Title")));
        }
        if (in_array("title", $this->btFieldsRequired) && (trim($args["title"]) == "")) {
            $e->add(t("The %s field is required.", t("Title(use &lt;heading1&gt;)")));
        }
        return $e;
    }

    public function composer()
    {
        $this->edit();
    }
}