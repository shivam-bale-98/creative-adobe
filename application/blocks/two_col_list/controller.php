<?php namespace Application\Block\TwoColList;

defined("C5_EXECUTE") or die("Access Denied.");

use AssetList;
use Concrete\Core\Block\BlockController;
use Core;
use Database;
use File;
use Page;
use Permissions;

class Controller extends BlockController
{
    public $btFieldsRequired = ['list' => []];
    protected $btExportFileColumns = ['image'];
    protected $btExportTables = ['btTwoColList', 'btTwoColListListEntries'];
    protected $btTable = 'btTwoColList';
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
        return t("Two Col List");
    }

    public function getSearchableContent()
    {
        $content = [];
        $content[] = $this->bgColor;
        $db = Database::connection();
        $list_items = $db->fetchAll('SELECT * FROM btTwoColListListEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($list_items as $list_item_k => $list_item_v) {
            if (isset($list_item_v["title"]) && trim($list_item_v["title"]) != "") {
                $content[] = $list_item_v["title"];
            }
            if (isset($list_item_v["desc_1"]) && trim($list_item_v["desc_1"]) != "") {
                $content[] = $list_item_v["desc_1"];
            }
        }
        return implode(" ", $content);
    }

    public function view()
    {
        $db = Database::connection();
        $list = [];
        $list_items = $db->fetchAll('SELECT * FROM btTwoColListListEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($list_items as $list_item_k => &$list_item_v) {
            $list_item_v["link_Object"] = null;
			$list_item_v["link_Title"] = trim($list_item_v["link_Title"]);
			if (isset($list_item_v["link"]) && trim($list_item_v["link"]) != '') {
				switch ($list_item_v["link"]) {
					case 'page':
						if ($list_item_v["link_Page"] > 0 && ($list_item_v["link_Page_c"] = Page::getByID($list_item_v["link_Page"])) && !$list_item_v["link_Page_c"]->error && !$list_item_v["link_Page_c"]->isInTrash()) {
							$list_item_v["link_Object"] = $list_item_v["link_Page_c"];
							$list_item_v["link_URL"] = $list_item_v["link_Page_c"]->getCollectionLink();
							if ($list_item_v["link_Title"] == '') {
								$list_item_v["link_Title"] = $list_item_v["link_Page_c"]->getCollectionName();
							}
						}
						break;
				    case 'file':
						$list_item_v["link_File_id"] = (int)$list_item_v["link_File"];
						if ($list_item_v["link_File_id"] > 0 && ($list_item_v["link_File_object"] = File::getByID($list_item_v["link_File_id"])) && is_object($list_item_v["link_File_object"])) {
							$fp = new Permissions($list_item_v["link_File_object"]);
							if ($fp->canViewFile()) {
								$list_item_v["link_Object"] = $list_item_v["link_File_object"];
								$list_item_v["link_URL"] = $list_item_v["link_File_object"]->getRelativePath();
								if ($list_item_v["link_Title"] == '') {
									$list_item_v["link_Title"] = $list_item_v["link_File_object"]->getTitle();
								}
							}
						}
						break;
				    case 'url':
						if ($list_item_v["link_Title"] == '') {
							$list_item_v["link_Title"] = $list_item_v["link_URL"];
						}
						break;
				    case 'relative_url':
						if ($list_item_v["link_Title"] == '') {
							$list_item_v["link_Title"] = $list_item_v["link_Relative_URL"];
						}
						$list_item_v["link_URL"] = $list_item_v["link_Relative_URL"];
						break;
				    case 'image':
						if ($list_item_v["link_Image"] > 0 && ($list_item_v["link_Image_object"] = File::getByID($list_item_v["link_Image"])) && is_object($list_item_v["link_Image_object"])) {
							$list_item_v["link_URL"] = $list_item_v["link_Image_object"]->getURL();
							$list_item_v["link_Object"] = $list_item_v["link_Image_object"];
							if ($list_item_v["link_Title"] == '') {
								$list_item_v["link_Title"] = $list_item_v["link_Image_object"]->getTitle();
							}
						}
						break;
				}
			}
            if (isset($list_item_v['image']) && trim($list_item_v['image']) != "" && ($f = File::getByID($list_item_v['image'])) && is_object($f)) {
                $list_item_v['image'] = $f;
            } else {
                $list_item_v['image'] = false;
            }
        }
        $this->set('list_items', $list_items);
        $this->set('list', $list);
    }

    public function delete()
    {
        $db = Database::connection();
        $db->delete('btTwoColListListEntries', ['bID' => $this->bID]);
        parent::delete();
    }

    public function duplicate($newBID)
    {
        $db = Database::connection();
        $list_items = $db->fetchAll('SELECT * FROM btTwoColListListEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($list_items as $list_item) {
            unset($list_item['id']);
            $list_item['bID'] = $newBID;
            $db->insert('btTwoColListListEntries', $list_item);
        }
        parent::duplicate($newBID);
    }

    public function add()
    {
        $this->addEdit();
        $list = $this->get('list');
        $this->set('list_items', []);
        $this->set('list', $list);
    }

    public function edit()
    {
        $db = Database::connection();
        $this->addEdit();
        $list = $this->get('list');
        $list_items = $db->fetchAll('SELECT * FROM btTwoColListListEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($list_items as &$list_item) {
            if (!File::getByID($list_item['image'])) {
                unset($list_item['image']);
            }
        }
        $this->set('list', $list);
        $this->set('list_items', $list_items);
    }

    protected function addEdit()
    {
        $list = [];
        $this->set("link_Options", $this->getSmartLinkTypeOptions([
  'page',
  'file',
  'image',
  'url',
  'relative_url',
], true));
        $this->set('list', $list);
        $this->set('identifier', new \Concrete\Core\Utility\Service\Identifier());
        $al = AssetList::getInstance();
        $al->register('css', 'repeatable-ft.form', 'blocks/two_col_list/css_form/repeatable-ft.form.css', [], $this->pkg);
        $al->register('javascript', 'handlebars', 'blocks/two_col_list/js_form/handlebars-v4.0.4.js', [], $this->pkg);
        $al->register('javascript', 'handlebars-helpers', 'blocks/two_col_list/js_form/handlebars-helpers.js', [], $this->pkg);
        $this->requireAsset('core/sitemap');
        $this->requireAsset('css', 'repeatable-ft.form');
        $this->requireAsset('javascript', 'handlebars');
        $this->requireAsset('javascript', 'handlebars-helpers');
        $this->requireAsset('core/file-manager');
        $this->set('btFieldsRequired', $this->btFieldsRequired);
        $this->set('identifier_getString', Core::make('helper/validation/identifier')->getString(18));
    }

    public function save($args)
    {
        $db = Database::connection();
        if (!isset($args["hideBlock"]) || trim($args["hideBlock"]) == "" || !in_array($args["hideBlock"], [0, 1])) {
            $args["hideBlock"] = '';
        }
        $rows = $db->fetchAll('SELECT * FROM btTwoColListListEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        $list_items = isset($args['list']) && is_array($args['list']) ? $args['list'] : [];
        $queries = [];
        if (!empty($list_items)) {
            $i = 0;
            foreach ($list_items as $list_item) {
                $data = [
                    'sortOrder' => $i + 1,
                ];
                if (isset($list_item['title']) && trim($list_item['title']) != '') {
                    $data['title'] = trim($list_item['title']);
                } else {
                    $data['title'] = null;
                }
                if (isset($list_item['desc_1']) && trim($list_item['desc_1']) != '') {
                    $data['desc_1'] = trim($list_item['desc_1']);
                } else {
                    $data['desc_1'] = null;
                }
                if (isset($list_item['link']) && trim($list_item['link']) != '') {
					$data['link_Title'] = $list_item['link_Title'];
					$data['link'] = $list_item['link'];
					switch ($list_item['link']) {
						case 'page':
							$data['link_Page'] = $list_item['link_Page'];
							$data['link_File'] = '0';
							$data['link_URL'] = '';
							$data['link_Relative_URL'] = '';
							$data['link_Image'] = '0';
							break;
                        case 'file':
							$data['link_File'] = $list_item['link_File'];
							$data['link_Page'] = '0';
							$data['link_URL'] = '';
							$data['link_Relative_URL'] = '';
							$data['link_Image'] = '0';
							break;
                        case 'url':
							$data['link_URL'] = $list_item['link_URL'];
							$data['link_Page'] = '0';
							$data['link_File'] = '0';
							$data['link_Relative_URL'] = '';
							$data['link_Image'] = '0';
							break;
                        case 'relative_url':
							$data['link_Relative_URL'] = $list_item['link_Relative_URL'];
							$data['link_Page'] = '0';
							$data['link_File'] = '0';
							$data['link_URL'] = '';
							$data['link_Image'] = '0';
							break;
                        case 'image':
							$data['link_Image'] = $list_item['link_Image'];
							$data['link_Page'] = '0';
							$data['link_File'] = '0';
							$data['link_URL'] = '';
							$data['link_Relative_URL'] = '';
							break;
                        default:
							$data['link'] = '';
							$data['link_Page'] = '0';
							$data['link_File'] = '0';
							$data['link_URL'] = '';
							$data['link_Relative_URL'] = '';
							$data['link_Image'] = '0';
							break;	
					}
				}
				else {
					$data['link'] = '';
					$data['link_Title'] = '';
					$data['link_Page'] = '0';
					$data['link_File'] = '0';
					$data['link_URL'] = '';
					$data['link_Relative_URL'] = '';
					$data['link_Image'] = '0';
				}
                if (isset($list_item['image']) && trim($list_item['image']) != '') {
                    $data['image'] = trim($list_item['image']);
                } else {
                    $data['image'] = null;
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
                                $db->update('btTwoColListListEntries', $data, ['id' => $id]);
                            }
                            break;
                        case 'insert':
                            foreach ($values as $data) {
                                $db->insert('btTwoColListListEntries', $data);
                            }
                            break;
                        case 'delete':
                            foreach ($values as $value) {
                                $db->delete('btTwoColListListEntries', ['id' => $value]);
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
        if (in_array("bgColor", $this->btFieldsRequired) && (trim($args["bgColor"]) == "")) {
            $e->add(t("The %s field is required.", t("background color")));
        }
        if (in_array("hideBlock", $this->btFieldsRequired) && (trim($args["hideBlock"]) == "" || !in_array($args["hideBlock"], [0, 1]))) {
            $e->add(t("The %s field is required.", t("Hide Block")));
        }
        $listEntriesMin = 0;
        $listEntriesMax = 0;
        $listEntriesErrors = 0;
        $list = [];
        if (isset($args['list']) && is_array($args['list']) && !empty($args['list'])) {
            if ($listEntriesMin >= 1 && count($args['list']) < $listEntriesMin) {
                $e->add(t("The %s field requires at least %s entries, %s entered.", t("list"), $listEntriesMin, count($args['list'])));
                $listEntriesErrors++;
            }
            if ($listEntriesMax >= 1 && count($args['list']) > $listEntriesMax) {
                $e->add(t("The %s field is set to a maximum of %s entries, %s entered.", t("list"), $listEntriesMax, count($args['list'])));
                $listEntriesErrors++;
            }
            if ($listEntriesErrors == 0) {
                foreach ($args['list'] as $list_k => $list_v) {
                    if (is_array($list_v)) {
                        if (in_array("title", $this->btFieldsRequired['list']) && (!isset($list_v['title']) || trim($list_v['title']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("title"), t("list"), $list_k));
                        }
                        if (in_array("desc_1", $this->btFieldsRequired['list']) && (!isset($list_v['desc_1']) || trim($list_v['desc_1']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("desc"), t("list"), $list_k));
                        }
                        if ((in_array("link", $this->btFieldsRequired['list']) && (!isset($list_v['link']) || trim($list_v['link']) == "")) || (isset($list_v['link']) && trim($list_v['link']) != "" && !array_key_exists($list_v['link'], $this->getSmartLinkTypeOptions(['page', 'file', 'image', 'url', 'relative_url'])))) {
							$e->add(t("The %s field has an invalid value.", t("link")));
						} elseif (array_key_exists($list_v['link'], $this->getSmartLinkTypeOptions(['page', 'file', 'image', 'url', 'relative_url']))) {
							switch ($list_v['link']) {
								case 'page':
									if (!isset($list_v['link_Page']) || trim($list_v['link_Page']) == "" || $list_v['link_Page'] == "0" || (($page = Page::getByID($list_v['link_Page'])) && $page->error !== false)) {
										$e->add(t("The %s field for '%s' is required (%s, row #%s).", t("Page"), t("link"), t("list"), $list_k));
									}
									break;
				                case 'file':
									if (!isset($list_v['link_File']) || trim($list_v['link_File']) == "" || !is_object(File::getByID($list_v['link_File']))) {
										$e->add(t("The %s field for '%s' is required (%s, row #%s).", t("File"), t("link"), t("list"), $list_k));
									}
									break;
				                case 'url':
									if (!isset($list_v['link_URL']) || trim($list_v['link_URL']) == "" || !filter_var($list_v['link_URL'], FILTER_VALIDATE_URL)) {
										$e->add(t("The %s field for '%s' does not have a valid URL (%s, row #%s).", t("URL"), t("link"), t("list"), $list_k));
									}
									break;
				                case 'relative_url':
									if (!isset($list_v['link_Relative_URL']) || trim($list_v['link_Relative_URL']) == "") {
										$e->add(t("The %s field for '%s' is required (%s, row #%s).", t("Relative URL"), t("link"), t("list"), $list_k));
									}
									break;
				                case 'image':
									if (!isset($list_v['link_Image']) || trim($list_v['link_Image']) == "" || !is_object(File::getByID($list_v['link_Image']))) {
										$e->add(t("The %s field for '%s' is required (%s, row #%s).", t("Image"), t("link"), t("list"), $list_k));
									}
									break;	
							}
						}
                        if (in_array("image", $this->btFieldsRequired['list']) && (!isset($list_v['image']) || trim($list_v['image']) == "" || !is_object(File::getByID($list_v['image'])))) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("Image"), t("list"), $list_k));
                        }
                    } else {
                        $e->add(t("The values for the %s field, row #%s, are incomplete.", t('list'), $list_k));
                    }
                }
            }
        } else {
            if ($listEntriesMin >= 1) {
                $e->add(t("The %s field requires at least %s entries, none entered.", t("list"), $listEntriesMin));
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