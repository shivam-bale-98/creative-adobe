<?php namespace Application\Block\OurClients;

defined("C5_EXECUTE") or die("Access Denied.");

use AssetList;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Core;
use Database;
use File;
use Page;
use Permissions;

class Controller extends BlockController
{
    public $btFieldsRequired = ['logos' => []];
    protected $btExportFileColumns = ['desktopLogos', 'mobileLogos'];
    protected $btExportTables = ['btOurClients', 'btOurClientsLogosEntries'];
    protected $btTable = 'btOurClients';
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
        return t("Clients");
    }

    public function getSearchableContent()
    {
        $content = [];
        $content[] = $this->bgColor;
        $content[] = $this->subTitle;
        $content[] = $this->title;
        $content[] = $this->desc_1;
        return implode(" ", $content);
    }

    public function view()
    {
        $db = Database::connection();
        $this->set('title', LinkAbstractor::translateFrom($this->title));
        $this->set('desc_1', LinkAbstractor::translateFrom($this->desc_1));
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
        $logos = [];
        $logos_items = $db->fetchAll('SELECT * FROM btOurClientsLogosEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($logos_items as $logos_item_k => &$logos_item_v) {
            if (isset($logos_item_v['desktopLogos']) && trim($logos_item_v['desktopLogos']) != "" && ($f = File::getByID($logos_item_v['desktopLogos'])) && is_object($f)) {
                $logos_item_v['desktopLogos'] = $f;
            } else {
                $logos_item_v['desktopLogos'] = false;
            }
            if (isset($logos_item_v['mobileLogos']) && trim($logos_item_v['mobileLogos']) != "" && ($f = File::getByID($logos_item_v['mobileLogos'])) && is_object($f)) {
                $logos_item_v['mobileLogos'] = $f;
            } else {
                $logos_item_v['mobileLogos'] = false;
            }
        }
        $this->set('logos_items', $logos_items);
        $this->set('logos', $logos);
    }

    public function delete()
    {
        $db = Database::connection();
        $db->delete('btOurClientsLogosEntries', ['bID' => $this->bID]);
        parent::delete();
    }

    public function duplicate($newBID)
    {
        $db = Database::connection();
        $logos_items = $db->fetchAll('SELECT * FROM btOurClientsLogosEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($logos_items as $logos_item) {
            unset($logos_item['id']);
            $logos_item['bID'] = $newBID;
            $db->insert('btOurClientsLogosEntries', $logos_item);
        }
        parent::duplicate($newBID);
    }

    public function add()
    {
        $this->addEdit();
        $logos = $this->get('logos');
        $this->set('logos_items', []);
        $this->set('logos', $logos);
    }

    public function edit()
    {
        $db = Database::connection();
        $this->addEdit();
        
        $this->set('title', LinkAbstractor::translateFromEditMode($this->title));
        
        $this->set('desc_1', LinkAbstractor::translateFromEditMode($this->desc_1));
        $logos = $this->get('logos');
        $logos_items = $db->fetchAll('SELECT * FROM btOurClientsLogosEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($logos_items as &$logos_item) {
            if (!File::getByID($logos_item['desktopLogos'])) {
                unset($logos_item['desktopLogos']);
            }
        }
        foreach ($logos_items as &$logos_item) {
            if (!File::getByID($logos_item['mobileLogos'])) {
                unset($logos_item['mobileLogos']);
            }
        }
        $this->set('logos', $logos);
        $this->set('logos_items', $logos_items);
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
        $logos = [];
        $this->set('logos', $logos);
        $this->set('identifier', new \Concrete\Core\Utility\Service\Identifier());
        $al = AssetList::getInstance();
        $al->register('css', 'repeatable-ft.form', 'blocks/our_clients/css_form/repeatable-ft.form.css', [], $this->pkg);
        $al->register('javascript', 'handlebars', 'blocks/our_clients/js_form/handlebars-v4.0.4.js', [], $this->pkg);
        $al->register('javascript', 'handlebars-helpers', 'blocks/our_clients/js_form/handlebars-helpers.js', [], $this->pkg);
        $this->requireAsset('redactor');
        $this->requireAsset('core/file-manager');
        $this->requireAsset('core/sitemap');
        $this->requireAsset('css', 'repeatable-ft.form');
        $this->requireAsset('javascript', 'handlebars');
        $this->requireAsset('javascript', 'handlebars-helpers');
        $this->set('btFieldsRequired', $this->btFieldsRequired);
        $this->set('identifier_getString', Core::make('helper/validation/identifier')->getString(18));
    }

    public function save($args)
    {
        $db = Database::connection();
        if (!isset($args["hideBlock"]) || trim($args["hideBlock"]) == "" || !in_array($args["hideBlock"], [0, 1])) {
            $args["hideBlock"] = '';
        }
        $args['title'] = LinkAbstractor::translateTo($args['title']);
        $args['desc_1'] = LinkAbstractor::translateTo($args['desc_1']);
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
        $rows = $db->fetchAll('SELECT * FROM btOurClientsLogosEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        $logos_items = isset($args['logos']) && is_array($args['logos']) ? $args['logos'] : [];
        $queries = [];
        if (!empty($logos_items)) {
            $i = 0;
            foreach ($logos_items as $logos_item) {
                $data = [
                    'sortOrder' => $i + 1,
                ];
                if (isset($logos_item['desktopLogos']) && trim($logos_item['desktopLogos']) != '') {
                    $data['desktopLogos'] = trim($logos_item['desktopLogos']);
                } else {
                    $data['desktopLogos'] = null;
                }
                if (isset($logos_item['mobileLogos']) && trim($logos_item['mobileLogos']) != '') {
                    $data['mobileLogos'] = trim($logos_item['mobileLogos']);
                } else {
                    $data['mobileLogos'] = null;
                }
                if (isset($rows[$i])) {
                    $queries['update'][$rows[$i]['id']] = $data;
                    unset($rows[$i]);
                } else {
                    $data['bID'] = $this->bID;
                    $queries['insert'][] = $data;
                }
                $i++;
            }
        }
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $queries['delete'][] = $row['id'];
            }
        }
        if (!empty($queries)) {
            foreach ($queries as $type => $values) {
                if (!empty($values)) {
                    switch ($type) {
                        case 'update':
                            foreach ($values as $id => $data) {
                                $db->update('btOurClientsLogosEntries', $data, ['id' => $id]);
                            }
                            break;
                        case 'insert':
                            foreach ($values as $data) {
                                $db->insert('btOurClientsLogosEntries', $data);
                            }
                            break;
                        case 'delete':
                            foreach ($values as $value) {
                                $db->delete('btOurClientsLogosEntries', ['id' => $value]);
                            }
                            break;
                    }
                }
            }
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
        if (in_array("hideBlock", $this->btFieldsRequired) && (trim($args["hideBlock"]) == "" || !in_array($args["hideBlock"], [0, 1]))) {
            $e->add(t("The %s field is required.", t("Hide Block")));
        }
        if (in_array("bgColor", $this->btFieldsRequired) && (trim($args["bgColor"]) == "")) {
            $e->add(t("The %s field is required.", t("background color (rustic red : #1A0A0C, wheat: #ECE6E4; romance : #F2F1EF, berry-red: #80151A)")));
        }
        if (in_array("subTitle", $this->btFieldsRequired) && (trim($args["subTitle"]) == "")) {
            $e->add(t("The %s field is required.", t("Sub Title")));
        }
        if (in_array("title", $this->btFieldsRequired) && (trim($args["title"]) == "")) {
            $e->add(t("The %s field is required.", t("Title")));
        }
        if (in_array("desc_1", $this->btFieldsRequired) && (trim($args["desc_1"]) == "")) {
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
        $logosEntriesMin = 0;
        $logosEntriesMax = 0;
        $logosEntriesErrors = 0;
        $logos = [];
        if (isset($args['logos']) && is_array($args['logos']) && !empty($args['logos'])) {
            if ($logosEntriesMin >= 1 && count($args['logos']) < $logosEntriesMin) {
                $e->add(t("The %s field requires at least %s entries, %s entered.", t("client logos"), $logosEntriesMin, count($args['logos'])));
                $logosEntriesErrors++;
            }
            if ($logosEntriesMax >= 1 && count($args['logos']) > $logosEntriesMax) {
                $e->add(t("The %s field is set to a maximum of %s entries, %s entered.", t("client logos"), $logosEntriesMax, count($args['logos'])));
                $logosEntriesErrors++;
            }
            if ($logosEntriesErrors == 0) {
                foreach ($args['logos'] as $logos_k => $logos_v) {
                    if (is_array($logos_v)) {
                        if (in_array("desktopLogos", $this->btFieldsRequired['logos']) && (!isset($logos_v['desktopLogos']) || trim($logos_v['desktopLogos']) == "" || !is_object(File::getByID($logos_v['desktopLogos'])))) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("logos desktop (only svg&#039;s not greater then (120X50))"), t("client logos"), $logos_k));
                        }
                        if (in_array("mobileLogos", $this->btFieldsRequired['logos']) && (!isset($logos_v['mobileLogos']) || trim($logos_v['mobileLogos']) == "" || !is_object(File::getByID($logos_v['mobileLogos'])))) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("mobile logos"), t("client logos"), $logos_k));
                        }
                    } else {
                        $e->add(t("The values for the %s field, row #%s, are incomplete.", t('client logos'), $logos_k));
                    }
                }
            }
        } else {
            if ($logosEntriesMin >= 1) {
                $e->add(t("The %s field requires at least %s entries, none entered.", t("client logos"), $logosEntriesMin));
            }
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