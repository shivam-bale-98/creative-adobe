<?php namespace Application\Block\RelatedListing;

defined("C5_EXECUTE") or die("Access Denied.");

use Application\Concrete\Models\Common\CommonList;
use AssetList;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Attribute\Key\PageKey;
use Concrete\Core\Page\Type\Type;
use Core;
use Database;
use Page;

class Controller extends BlockController
{
    public $btFieldsRequired = [];
    protected $btExportTables = ['btRelatedListing', 'btRelatedListingPageType_MultipleSelectEntries', 'btRelatedListingAttributes_MultipleSelectEntries'];
    protected $btTable = 'btRelatedListing';
    protected $btInterfaceWidth = 400;
    protected $btInterfaceHeight = 500;
    protected $btIgnorePageThemeGridFrameworkContainer = false;
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $pkg = false;

    const ITEMS_PER_PAGE = 10;
    
    public function getBlockTypeName()
    {
        return t("Related Listing");
    }

    public function getSearchableContent()
    {
        $content = [];
        $content[] = $this->title;
        $content[] = $this->subTitle;
        $content[] = $this->bgColor;
        $content[] = $this->paddingTop;
        $content[] = $this->paddingBottom;
        $content[] = $this->itemCount;
        $pageType_options = [
            'concrete5_old' => "Concrete5 CMS 5.6",
            'concrete5' => "Concrete5 CMS 5.7",
            'wordpress' => "WordPress"
        ];
        foreach($this->getMultipleSelectSelections('btRelatedListingPageType_MultipleSelectEntries', $this->bID, $pageType_options) as $pageType_key => $pageType_value){
            $content[] = $pageType_value;
        }
        $attributes_options = [
            'concrete5_old' => "Concrete5 CMS 5.6",
            'concrete5' => "Concrete5 CMS 5.7",
            'wordpress' => "WordPress"
        ];
        foreach($this->getMultipleSelectSelections('btRelatedListingAttributes_MultipleSelectEntries', $this->bID, $attributes_options) as $attributes_key => $attributes_value){
            $content[] = $attributes_value;
        }
        return implode(" ", $content);
    }

    public function view()
    {
        $db = Database::connection();
        $sortOrder_options = [
            '' => "-- " . t("None") . " --",
            'display_asc' => "Sitemap order",
            'display_desc' => "Reverse sitemap order",
            'chrono_desc' => "Most recent first",
            'chrono_asc' => "Earliest first",
            'alpha_asc' => "Alphabetical order",
            'alpha_desc' => "Reverse alphabetical order",
            'modified_desc' => "Most recently modified first",
            'random' => "Random"
        ];
        $this->set("sortOrder_options", $sortOrder_options);
        $pageType_options = [
            'concrete5_old' => "Concrete5 CMS 5.6",
            'concrete5' => "Concrete5 CMS 5.7",
            'wordpress' => "WordPress"
        ];
        $this->set("pageType_options", $pageType_options);
        $this->set("pageType", $this->getMultipleSelectSelections('btRelatedListingPageType_MultipleSelectEntries', $this->bID, $pageType_options));
        $attributes_options = [
            'concrete5_old' => "Concrete5 CMS 5.6",
            'concrete5' => "Concrete5 CMS 5.7",
            'wordpress' => "WordPress"
        ];
        $this->set("attributes_options", $attributes_options);
        $this->set("attributes", $this->getMultipleSelectSelections('btRelatedListingAttributes_MultipleSelectEntries', $this->bID, $attributes_options));
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
			}
		}
		$this->set("link_URL", $link_URL);
		$this->set("link_Object", $link_Object);
		$this->set("link_Title", $link_Title);
		$this->set("pages", $this->getPages());
    }

    public function delete()
    {
        $db = Database::connection();
        $db->delete('btRelatedListingPageType_MultipleSelectEntries', ['bvID' => $this->bID]);
        $db->delete('btRelatedListingAttributes_MultipleSelectEntries', ['bvID' => $this->bID]);
        parent::delete();
    }

    public function duplicate($newBID)
    {
        $db = Database::connection();
        $pageType_entries = $db->fetchAll('SELECT * FROM btRelatedListingPageType_MultipleSelectEntries WHERE bvID = ? ORDER BY sortOrder ASC', [$this->bID]);
        foreach ($pageType_entries as $pageType_entry) {
            unset($pageType_entry['msID']);
            $db->insert('btRelatedListingPageType_MultipleSelectEntries', $pageType_entry);
        }
        $attributes_entries = $db->fetchAll('SELECT * FROM btRelatedListingAttributes_MultipleSelectEntries WHERE bvID = ? ORDER BY sortOrder ASC', [$this->bID]);
        foreach ($attributes_entries as $attributes_entry) {
            unset($attributes_entry['msID']);
            $db->insert('btRelatedListingAttributes_MultipleSelectEntries', $attributes_entry);
        }
        parent::duplicate($newBID);
    }

    public function add()
    {
        $this->addEdit();
        $pageType = [];
		$pageType_defaults = array_unique([]);
		$pageType_options = $this->get("pageType_options");
		if (!empty($pageType_defaults)) {
			foreach ($pageType_defaults as $pageType_default) {
				if (isset($pageType_options[$pageType_default])) {
					$pageType[$pageType_default] = $pageType_options[$pageType_default];
				}
			}
		}
		$this->set("pageType", $pageType);
        $attributes = [];
		$attributes_defaults = array_unique([]);
		$attributes_options = $this->get("attributes_options");
		if (!empty($attributes_defaults)) {
			foreach ($attributes_defaults as $attributes_default) {
				if (isset($attributes_options[$attributes_default])) {
					$attributes[$attributes_default] = $attributes_options[$attributes_default];
				}
			}
		}
		$this->set("attributes", $attributes);
    }

    public function edit()
    {
        $db = Database::connection();
        $this->addEdit();
        $this->set("pageType", $this->getMultipleSelectSelections('btRelatedListingPageType_MultipleSelectEntries', $this->bID, $this->get("pageType_options")));
        $this->set("attributes", $this->getMultipleSelectSelections('btRelatedListingAttributes_MultipleSelectEntries', $this->bID, $this->get("attributes_options")));
    }

    protected function addEdit()
    {
        $this->set("sortOrder_options", $this->getSortOptions());
        $this->set("pageType_options", $this->getPageTypeOptions());
        $this->set("attributes_options", $this->getAttributeOptions());
        $this->set("link_Options", $this->getSmartLinkTypeOptions([
  'page',
  'url',
  'relative_url',
], true));
        $al = AssetList::getInstance();
        $al->register('css', 'auto-css-' . $this->btHandle, 'blocks/' . $this->btHandle . '/auto.css', [], $this->pkg);
        $this->requireAsset('css', 'select2');
        $this->requireAsset('javascript', 'select2');
        $this->requireAsset('css', 'auto-css-' . $this->btHandle);
        $this->set('btFieldsRequired', $this->btFieldsRequired);
        $this->set('identifier_getString', Core::make('helper/validation/identifier')->getString(18));
    }

    public function save($args)
    {
        $db = Database::connection();
        $pageType_table = 'btRelatedListingPageType_MultipleSelectEntries';
		$pageType_entries = $this->getMultipleSelectSelections($pageType_table, $this->bID, [], true);
		if (isset($args['pageType']) && is_array($args['pageType'])) {
			$pageType_sortOrder = 1;
			foreach ($args['pageType'] as $pageType_value) {
				$pageType_data = [
					'value'     => $pageType_value,
					'sortOrder' => $pageType_sortOrder,
					'bvID'      => $this->bID,
				];
				if (!empty($pageType_entries)) {
					$pageType_entryID = key($pageType_entries);
					$db->update($pageType_table, $pageType_data, ['msID' => $pageType_entryID]);
					unset($pageType_entries[$pageType_entryID]);
				} else {
					$db->insert($pageType_table, $pageType_data);
				}
				$pageType_sortOrder++;
			}
		}
		if (!empty($pageType_entries)) {
			foreach (array_keys($pageType_entries) as $pageType_entry) {
				$db->delete($pageType_table, ['msID' => $pageType_entry]);
			}
		}
        $attributes_table = 'btRelatedListingAttributes_MultipleSelectEntries';
		$attributes_entries = $this->getMultipleSelectSelections($attributes_table, $this->bID, [], true);
		if (isset($args['attributes']) && is_array($args['attributes'])) {
			$attributes_sortOrder = 1;
			foreach ($args['attributes'] as $attributes_value) {
				$attributes_data = [
					'value'     => $attributes_value,
					'sortOrder' => $attributes_sortOrder,
					'bvID'      => $this->bID,
				];
				if (!empty($attributes_entries)) {
					$attributes_entryID = key($attributes_entries);
					$db->update($attributes_table, $attributes_data, ['msID' => $attributes_entryID]);
					unset($attributes_entries[$attributes_entryID]);
				} else {
					$db->insert($attributes_table, $attributes_data);
				}
				$attributes_sortOrder++;
			}
		}
		if (!empty($attributes_entries)) {
			foreach (array_keys($attributes_entries) as $attributes_entry) {
				$db->delete($attributes_table, ['msID' => $attributes_entry]);
			}
		}
        if (isset($args["link"]) && trim($args["link"]) != '') {
			switch ($args["link"]) {
				case 'page':
					$args["link_File"] = '0';
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
        parent::save($args);
    }

    public function validate($args)
    {
        $e = Core::make("helper/validation/error");
        if (in_array("title", $this->btFieldsRequired) && (trim($args["title"]) == "")) {
            $e->add(t("The %s field is required.", t("Title")));
        }
        if (in_array("itemCount", $this->btFieldsRequired) && (trim($args["itemCount"]) == "")) {
            $e->add(t("The %s field is required.", t("Item Count")));
        }
        if ((in_array("sortOrder", $this->btFieldsRequired) && (!isset($args["sortOrder"]) || trim($args["sortOrder"]) == "")) || (isset($args["sortOrder"]) && trim($args["sortOrder"]) != "" && !in_array($args["sortOrder"], ["display_asc", "display_desc", "chrono_desc", "chrono_asc", "alpha_asc", "alpha_desc", "modified_desc", "random"]))) {
            $e->add(t("The %s field has an invalid value.", t("Sort Order")));
        }
        if ((in_array("link", $this->btFieldsRequired) && (!isset($args["link"]) || trim($args["link"]) == "")) || (isset($args["link"]) && trim($args["link"]) != "" && !array_key_exists($args["link"], $this->getSmartLinkTypeOptions(['page', 'url', 'relative_url'])))) {
			$e->add(t("The %s field has an invalid value.", t("Link")));
		} elseif (array_key_exists($args["link"], $this->getSmartLinkTypeOptions(['page', 'url', 'relative_url']))) {
			switch ($args["link"]) {
				case 'page':
					if (!isset($args["link_Page"]) || trim($args["link_Page"]) == "" || $args["link_Page"] == "0" || (($page = Page::getByID($args["link_Page"])) && $page->error !== false)) {
						$e->add(t("The %s field for '%s' is required.", t("Page"), t("Link")));
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
			}
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

    protected function getMultipleSelectSelections($table, $bvID, $options = [], $save = false)
	{
		$return = [];
		if (trim($bvID) != '' && $bvID > 0 && ($items = Database::connection()->fetchAll('SELECT * FROM ' . $table . ' WHERE bvID = ? ORDER BY sortOrder', [$bvID]))) {
			foreach ($items as $item) {
				if ($save) {
					$return[$item['msID']] = $item['value'];
				} elseif (isset($options[$item['value']])) {
					$return[$item['value']] = $options[$item['value']];
				}
			}
		}
		return $return;
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

    private function getSortOptions() {
        return  [
            'display_asc' => t("Sitemap order"),
            'display_desc' => t("Reverse sitemap order"),
            'chrono_desc' => t("Most recent first"),
            'chrono_asc' => t("Earliest first"),
            'alpha_asc' => t("Alphabetical order"),
            'alpha_desc' => t("Reverse alphabetical order"),
            'modified_desc' => t("Most recently modified first"),
            'random' => t("Random")
        ];
    }

    private function getPageTypeOptions(){
        $options = [];

        $pageTypes = Type::getList();

        if(is_array($pageTypes)){
            /** @var Type  $pt */
            foreach ($pageTypes as $pt) {
                $options[$pt->getPageTypeID()] = $pt->getPageTypeDisplayName();
            }
        }

        return $options;
    }

    private function getAttributeOptions(){
        $options = [];

        $entity = \Concrete\Core\Attribute\Key\Category::getByHandle('collection');
        $category = $entity->getAttributeKeyCategory();
        $attributes = $category->getList();

        /** @var PageKey $attribute */
        foreach ($attributes as $attribute){
            if($attribute->getAttributeTypeHandle() == "select"){
                $options[$attribute->getAttributeKeyHandle()] = $attribute->getAttributeKeyDisplayName();
            }
        }

        return $options;
    }

    public function getPages() {
        $currentPage = Page::getCurrentPage();
        $pl = new CommonList();

        if($pageType = $this->getMultipleSelectSelections('btRelatedListingPageType_MultipleSelectEntries', $this->bID, $this->getPageTypeOptions())) $pl->filterByPageTypeID(array_keys($pageType));

        switch ($this->sortOrder) {
            case 'display_asc':
                $pl->sortByDisplayOrder();
                break;
            case 'chrono_asc':
                $pl->sortByPublicDate();
                break;
            case 'modified_desc':
                $pl->sortByDateModifiedDescending();
                break;
            case 'random':
                $pl->sortBy('RAND()');
                break;
            case 'alpha_asc':
                $pl->sortByName();
                break;
            case 'alpha_desc':
                $pl->sortByNameDescending();
                break;
            case 'display_desc':
            default:
                $pl->sortByPublicDateDescending();
                break;
        }

        if($attributes = $this->getMultipleSelectSelections('btRelatedListingAttributes_MultipleSelectEntries', $this->bID, $this->getAttributeOptions())) {
            foreach (array_keys($attributes) as $attribute) {
                $value = $currentPage->getAttribute($attribute);

                if(!$value) continue;

                $criteria = [];

                foreach ($value as $key => $v) {
                    if($v) $criteria[] = "ak_{$attribute} LIKE '%\n{$v}\n%'";
                }

                if($criteria) $pl->getQueryObject()->andWhere('(' . implode(' AND ', $criteria) . ')');
            }
        }

        $expr = $pl->getQueryObject()->expr();
        $pl->getQueryObject()->andWhere($expr->neq('p.cID', $currentPage->getCollectionID()));

        $pl->setItemsPerPage((int)$this->itemCount ? $this->itemCount : self::ITEMS_PER_PAGE);

        return $pl->getPage()->getPages();
    }
}