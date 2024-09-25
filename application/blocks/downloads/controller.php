<?php namespace Application\Block\Downloads;

defined("C5_EXECUTE") or die("Access Denied.");

use Application\Concrete\Helpers\GeneralHelper;
use Application\Concrete\Helpers\SelectOptionsHelper;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\File\Set\SetList as FileSetList;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Support\Facade\Config;
use Core;

class Controller extends BlockController
{
    public $btFieldsRequired = ['title'];
    protected $btTable = 'btDownloads';
    protected $btInterfaceWidth = 400;
    protected $btInterfaceHeight = 500;
    protected $btIgnorePageThemeGridFrameworkContainer = false;
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $pkg = false;
    protected $category;
    protected $filesets;

    public function getBlockTypeName()
    {
        return t("Downloads");
    }

    public function getSearchableContent()
    {
        $content = [];
        $content[] = $this->title;
        $content[] = $this->bgColor;
        $content[] = $this->paddingTop;
        $content[] = $this->paddingBottom;
        $content[] = $this->filesets;
        return implode(" ", $content);
    }

    public function add()
    {
        $this->addEdit();
    }

    public function view()
    {
        $setNames = [];
        if($this->filesets)
        {
            $selectedSets = explode(',',$this->filesets);
            foreach ($selectedSets as $setId)
            {
                $setNames[] = Set::getByID($setId);
            }
        }

        $sets = collect($setNames)->pluck('fsName')->toArray();

        $th = Core::make("helper/text");
        $this->category = urldecode($th->sanitize($this->get('category')));

        $this->set('categoryOptions', $sets);

        $files[] = GeneralHelper::productDownloads();

        $files[] = GeneralHelper::fileSetDownloads();

        $filesData = collect($files)->flatten(1)->unique('url')->toArray();
        
        $this->set('filesData', $filesData);
        $this->set('selectedCategory', $this->category);
    }

    public function edit()
    {
        $this->addEdit();
    }

    protected function addEdit()
    {
        $this->set("filesets_options", $this->getFileSetOptions());
        $this->set('btFieldsRequired', $this->btFieldsRequired);
        $this->set('identifier_getString', Core::make('helper/validation/identifier')->getString(18));
    }

    public function validate($args)
    {
        $e = Core::make("helper/validation/error");
        if (in_array("title", $this->btFieldsRequired) && (trim($args["title"]) == "")) {
            $e->add(t("The %s field is required.", t("Title")));
        }
        if (in_array("filesets", $this->btFieldsRequired) && (trim($args["filesets"]) == "" || !is_object(FileSet::getByID($args['filesets'])))) {
            $e->add(t("The %s field is required.", t("file sets")));
        }
        return $e;
    }

    protected function getFileSetOptions()
    {
        $fsl = new FileSetList();
        $fileSets = $fsl->get();
        $return = [];
        foreach ($fileSets as $fs) {
            $return[$fs->getFileSetID()] = $fs->getFileSetDisplayName();
        }
        return $return;
    }

    public function composer()
    {
        $this->edit();
    }
}