<?php namespace Application\Block\Technology;

defined("C5_EXECUTE") or die("Access Denied.");

use AssetList;
use Concrete\Core\Block\BlockController;
use Core;
use File;
use Page;
use Permissions;

class Controller extends BlockController
{
    public $btFieldsRequired = [];
    protected $btExportFileColumns = ['desktopImage', 'mobileImage', 'svg'];
    protected $btTable = 'btTechnology';
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
        return t("Technology Block");
    }

    public function getSearchableContent()
    {
        $content = [];
        $content[] = $this->title;
        $content[] = $this->desc_1;
        $content[] = $this->vimeoLink;
        return implode(" ", $content);
    }

    public function view()
    {
        
        if ($this->desktopImage && ($f = File::getByID($this->desktopImage)) && is_object($f)) {
            $this->set("desktopImage", $f);
        } else {
            $this->set("desktopImage", false);
        }
        
        if ($this->mobileImage && ($f = File::getByID($this->mobileImage)) && is_object($f)) {
            $this->set("mobileImage", $f);
        } else {
            $this->set("mobileImage", false);
        }
        $link_URL = null;
		$link_Object = null;
		$link_Title = trim($this->link_Title);
		if (trim($this->link) != '') {
			switch ($this->link) {
				case 'page':
					if ($this->link_Page > 0 && ($link_Page_c = Page::getByID($this->link_Page)) && !$link_Page_c->error && !$link_Page_c->isInTrash()) {
						$link_Object = $link_Page_c;
						$link_URL = $link_Page_c->getCollectionLink();
						if ($link_Title == '') {
							$link_Title = $link_Page_c->getCollectionName();
						}
					}
					break;
				case 'file':
					$link_File_id = (int)$this->link_File;
					if ($link_File_id > 0 && ($link_File_object = File::getByID($link_File_id)) && is_object($link_File_object)) {
						$fp = new Permissions($link_File_object);
						if ($fp->canViewFile()) {
							$link_Object = $link_File_object;
							$link_URL = $link_File_object->getRelativePath();
							if ($link_Title == '') {
								$link_Title = $link_File_object->getTitle();
							}
						}
					}
					break;
				case 'url':
					$link_URL = $this->link_URL;
					if ($link_Title == '') {
						$link_Title = $link_URL;
					}
					break;
				case 'relative_url':
					$link_URL = $this->link_Relative_URL;
					if ($link_Title == '') {
						$link_Title = $this->link_Relative_URL;
					}
					break;
				case 'image':
					if ($this->link_Image && ($link_Image_object = File::getByID($this->link_Image)) && is_object($link_Image_object)) {
						$link_URL = $link_Image_object->getURL();
						$link_Object = $link_Image_object;
						if ($link_Title == '') {
							$link_Title = $link_Image_object->getTitle();
						}
					}
					break;
			}
		}
		$this->set("link_URL", $link_URL);
		$this->set("link_Object", $link_Object);
		$this->set("link_Title", $link_Title);
        
        if ($this->svg && ($f = File::getByID($this->svg)) && is_object($f)) {
            $this->set("svg", $f);
        } else {
            $this->set("svg", false);
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
        $this->set("link_Options", $this->getSmartLinkTypeOptions([
  'page',
  'file',
  'image',
  'url',
  'relative_url',
], true));
        $this->requireAsset('core/file-manager');
        $this->set('btFieldsRequired', $this->btFieldsRequired);
        $this->set('identifier_getString', Core::make('helper/validation/identifier')->getString(18));
    }

    public function save($args)
    {
        $args['desktopImage'] = isset($args['desktopImage']) && is_numeric($args['desktopImage']) ? $args['desktopImage'] : 0;
        $args['mobileImage'] = isset($args['mobileImage']) && is_numeric($args['mobileImage']) ? $args['mobileImage'] : 0;
        if (isset($args["link"]) && trim($args["link"]) != '') {
			switch ($args["link"]) {
				case 'page':
					$args["link_File"] = '0';
					$args["link_URL"] = '';
					$args["link_Relative_URL"] = '';
					$args["link_Image"] = '0';
					break;
				case 'file':
					$args["link_Page"] = '0';
					$args["link_URL"] = '';
					$args["link_Relative_URL"] = '';
					$args["link_Image"] = '0';
					break;
				case 'url':
					$args["link_Page"] = '0';
					$args["link_Relative_URL"] = '';
					$args["link_File"] = '0';
					$args["link_Image"] = '0';
					break;
				case 'relative_url':
					$args["link_Page"] = '0';
					$args["link_URL"] = '';
					$args["link_File"] = '0';
					$args["link_Image"] = '0';
					break;
				case 'image':
					$args["link_Page"] = '0';
					$args["link_File"] = '0';
					$args["link_URL"] = '';
					$args["link_Relative_URL"] = '';
					break;
				default:
					$args["link_Title"] = '';
					$args["link_Page"] = '0';
					$args["link_File"] = '0';
					$args["link_URL"] = '';
					$args["link_Relative_URL"] = '';
					$args["link_Image"] = '0';
					break;	
			}
		}
		else {
			$args["link_Title"] = '';
			$args["link_Page"] = '0';
			$args["link_File"] = '0';
			$args["link_URL"] = '';
			$args["link_Relative_URL"] = '';
			$args["link_Image"] = '0';
		}
        $args['svg'] = isset($args['svg']) && is_numeric($args['svg']) ? $args['svg'] : 0;
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
        if (in_array("desktopImage", $this->btFieldsRequired) && (trim($args["desktopImage"]) == "" || !is_object(File::getByID($args["desktopImage"])))) {
            $e->add(t("The %s field is required.", t("desktop image (2000X1616)")));
        }
        if (in_array("mobileImage", $this->btFieldsRequired) && (trim($args["mobileImage"]) == "" || !is_object(File::getByID($args["mobileImage"])))) {
            $e->add(t("The %s field is required.", t("Mobile Image(800X1400)")));
        }
        if (in_array("title", $this->btFieldsRequired) && (trim($args["title"]) == "")) {
            $e->add(t("The %s field is required.", t("Title")));
        }
        if (in_array("desc_1", $this->btFieldsRequired) && trim($args["desc_1"]) == "") {
            $e->add(t("The %s field is required.", t("description")));
        }
        if ((in_array("link", $this->btFieldsRequired) && (!isset($args["link"]) || trim($args["link"]) == "")) || (isset($args["link"]) && trim($args["link"]) != "" && !array_key_exists($args["link"], $this->getSmartLinkTypeOptions(['page', 'file', 'image', 'url', 'relative_url'])))) {
			$e->add(t("The %s field has an invalid value.", t("Link")));
		} elseif (array_key_exists($args["link"], $this->getSmartLinkTypeOptions(['page', 'file', 'image', 'url', 'relative_url']))) {
			switch ($args["link"]) {
				case 'page':
					if (!isset($args["link_Page"]) || trim($args["link_Page"]) == "" || $args["link_Page"] == "0" || (($page = Page::getByID($args["link_Page"])) && $page->error !== false)) {
						$e->add(t("The %s field for '%s' is required.", t("Page"), t("Link")));
					}
					break;
				case 'file':
					if (!isset($args["link_File"]) || trim($args["link_File"]) == "" || !is_object(File::getByID($args["link_File"]))) {
						$e->add(t("The %s field for '%s' is required.", t("File"), t("Link")));
					}
					break;
				case 'url':
					if (!isset($args["link_URL"]) || trim($args["link_URL"]) == "" || !filter_var($args["link_URL"], FILTER_VALIDATE_URL)) {
						$e->add(t("The %s field for '%s' does not have a valid URL.", t("URL"), t("Link")));
					}
					break;
				case 'relative_url':
					if (!isset($args["link_Relative_URL"]) || trim($args["link_Relative_URL"]) == "") {
						$e->add(t("The %s field for '%s' is required.", t("Relative URL"), t("Link")));
					}
					break;
				case 'image':
					if (!isset($args["link_Image"]) || trim($args["link_Image"]) == "" || !is_object(File::getByID($args["link_Image"]))) {
						$e->add(t("The %s field for '%s' is required.", t("Image"), t("Link")));
					}
					break;	
			}
		}
        if (in_array("svg", $this->btFieldsRequired) && (trim($args["svg"]) == "" || !is_object(File::getByID($args["svg"])))) {
            $e->add(t("The %s field is required.", t("svg (330X290)")));
        }
        if (in_array("vimeoLink", $this->btFieldsRequired) && (trim($args["vimeoLink"]) == "")) {
            $e->add(t("The %s field is required.", t(" video link (vimeo sharer link)")));
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
        $al = AssetList::getInstance();
        $al->register('javascript', 'auto-js-' . $this->btHandle, 'blocks/' . $this->btHandle . '/auto.js', [], $this->pkg);
        $this->requireAsset('javascript', 'auto-js-' . $this->btHandle);
        $this->edit();
    }

    protected function getSmartLinkTypeOptions($include = [], $checkNone = false)
	{
		$options = [
			''             => sprintf("-- %s--", t("None")),
			'page'         => t("Page"),
			'url'          => t("External URL"),
			'relative_url' => t("Relative URL"),
			'file'         => t("File"),
			'image'        => t("Image")
		];
		if ($checkNone) {
            $include = array_merge([''], $include);
        }
		$return = [];
		foreach($include as $v){
		    if(isset($options[$v])){
		        $return[$v] = $options[$v];
		    }
		}
		return $return;
	}
}