<?php namespace Application\Block\Quotes;

defined("C5_EXECUTE") or die("Access Denied.");

use Concrete\Core\Block\BlockController;
use Core;

class Controller extends BlockController
{
    public $btFieldsRequired = [];
    protected $btTable = 'btQuotes';
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
        return t("Quotes");
    }

    public function getSearchableContent()
    {
        $content = [];
        $content[] = $this->text;
        $content[] = $this->name;
        $content[] = $this->designation;
        return implode(" ", $content);
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
        $this->set('btFieldsRequired', $this->btFieldsRequired);
        $this->set('identifier_getString', Core::make('helper/validation/identifier')->getString(18));
    }

    public function validate($args)
    {
        $e = Core::make("helper/validation/error");
        if (in_array("text", $this->btFieldsRequired) && (trim($args["text"]) == "")) {
            $e->add(t("The %s field is required.", t("Text")));
        }
        if (in_array("name", $this->btFieldsRequired) && (trim($args["name"]) == "")) {
            $e->add(t("The %s field is required.", t("Name")));
        }
        if (in_array("designation", $this->btFieldsRequired) && (trim($args["designation"]) == "")) {
            $e->add(t("The %s field is required.", t("Designation")));
        }
        return $e;
    }

    public function composer()
    {
        $this->edit();
    }
}