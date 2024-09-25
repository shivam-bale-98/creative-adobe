<?php namespace Application\Block\HomeBannerSlider;

defined("C5_EXECUTE") or die("Access Denied.");

use AssetList;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Core;
use Database;
use File;
use Page;

class Controller extends BlockController
{
    public $btFieldsRequired = ['bannerItems' => []];
    protected $btExportFileColumns = ['Dimage', 'Mimage'];
    protected $btExportPageColumns = ['ctaLink'];
    protected $btExportTables = ['btHomeBannerSlider', 'btHomeBannerSliderBannerItemsEntries'];
    protected $btTable = 'btHomeBannerSlider';
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
        return t("Home Banner Slider");
    }

    public function getSearchableContent()
    {
        $content = [];
        $db = Database::connection();
        $bannerItems_items = $db->fetchAll('SELECT * FROM btHomeBannerSliderBannerItemsEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($bannerItems_items as $bannerItems_item_k => $bannerItems_item_v) {
            if (isset($bannerItems_item_v["videoURL"]) && trim($bannerItems_item_v["videoURL"]) != "") {
                $content[] = $bannerItems_item_v["videoURL"];
            }
            if (isset($bannerItems_item_v["subTitle"]) && trim($bannerItems_item_v["subTitle"]) != "") {
                $content[] = $bannerItems_item_v["subTitle"];
            }
            if (isset($bannerItems_item_v["title"]) && trim($bannerItems_item_v["title"]) != "") {
                $content[] = $bannerItems_item_v["title"];
            }
        }
        return implode(" ", $content);
    }

    public function view()
    {
        $db = Database::connection();
        $bannerItems = [];
        $bannerItems_items = $db->fetchAll('SELECT * FROM btHomeBannerSliderBannerItemsEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($bannerItems_items as $bannerItems_item_k => &$bannerItems_item_v) {
            if (isset($bannerItems_item_v['Dimage']) && trim($bannerItems_item_v['Dimage']) != "" && ($f = File::getByID($bannerItems_item_v['Dimage'])) && is_object($f)) {
                $bannerItems_item_v['Dimage'] = $f;
            } else {
                $bannerItems_item_v['Dimage'] = false;
            }
            if (isset($bannerItems_item_v['Mimage']) && trim($bannerItems_item_v['Mimage']) != "" && ($f = File::getByID($bannerItems_item_v['Mimage'])) && is_object($f)) {
                $bannerItems_item_v['Mimage'] = $f;
            } else {
                $bannerItems_item_v['Mimage'] = false;
            }
            $bannerItems_item_v["title"] = isset($bannerItems_item_v["title"]) ? LinkAbstractor::translateFrom($bannerItems_item_v["title"]) : null;
        }
        $this->set('bannerItems_items', $bannerItems_items);
        $this->set('bannerItems', $bannerItems);
    }

    public function delete()
    {
        $db = Database::connection();
        $db->delete('btHomeBannerSliderBannerItemsEntries', ['bID' => $this->bID]);
        parent::delete();
    }

    public function duplicate($newBID)
    {
        $db = Database::connection();
        $bannerItems_items = $db->fetchAll('SELECT * FROM btHomeBannerSliderBannerItemsEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($bannerItems_items as $bannerItems_item) {
            unset($bannerItems_item['id']);
            $bannerItems_item['bID'] = $newBID;
            $db->insert('btHomeBannerSliderBannerItemsEntries', $bannerItems_item);
        }
        parent::duplicate($newBID);
    }

    public function add()
    {
        $this->addEdit();
        $bannerItems = $this->get('bannerItems');
        $this->set('bannerItems_items', []);
        $this->set('bannerItems', $bannerItems);
    }

    public function edit()
    {
        $db = Database::connection();
        $this->addEdit();
        $bannerItems = $this->get('bannerItems');
        $bannerItems_items = $db->fetchAll('SELECT * FROM btHomeBannerSliderBannerItemsEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($bannerItems_items as &$bannerItems_item) {
            if (!File::getByID($bannerItems_item['Dimage'])) {
                unset($bannerItems_item['Dimage']);
            }
        }
        foreach ($bannerItems_items as &$bannerItems_item) {
            if (!File::getByID($bannerItems_item['Mimage'])) {
                unset($bannerItems_item['Mimage']);
            }
        }
        
        foreach ($bannerItems_items as &$bannerItems_item) {
            $bannerItems_item['title'] = isset($bannerItems_item['title']) ? LinkAbstractor::translateFromEditMode($bannerItems_item['title']) : null;
        }
        $this->set('bannerItems', $bannerItems);
        $this->set('bannerItems_items', $bannerItems_items);
    }

    protected function addEdit()
    {
        $bannerItems = [];
        $this->set('bannerItems', $bannerItems);
        $this->set('identifier', new \Concrete\Core\Utility\Service\Identifier());
        $al = AssetList::getInstance();
        $al->register('css', 'repeatable-ft.form', 'blocks/home_banner_slider/css_form/repeatable-ft.form.css', [], $this->pkg);
        $al->register('javascript', 'handlebars', 'blocks/home_banner_slider/js_form/handlebars-v4.0.4.js', [], $this->pkg);
        $al->register('javascript', 'handlebars-helpers', 'blocks/home_banner_slider/js_form/handlebars-helpers.js', [], $this->pkg);
        $this->requireAsset('core/sitemap');
        $this->requireAsset('css', 'repeatable-ft.form');
        $this->requireAsset('javascript', 'handlebars');
        $this->requireAsset('javascript', 'handlebars-helpers');
        $this->requireAsset('core/file-manager');
        $this->requireAsset('redactor');
        $this->set('btFieldsRequired', $this->btFieldsRequired);
        $this->set('identifier_getString', Core::make('helper/validation/identifier')->getString(18));
    }

    public function save($args)
    {
        $db = Database::connection();
        if (!isset($args["hideBlock"]) || trim($args["hideBlock"]) == "" || !in_array($args["hideBlock"], [0, 1])) {
            $args["hideBlock"] = '';
        }
        $rows = $db->fetchAll('SELECT * FROM btHomeBannerSliderBannerItemsEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        $bannerItems_items = isset($args['bannerItems']) && is_array($args['bannerItems']) ? $args['bannerItems'] : [];
        $queries = [];
        if (!empty($bannerItems_items)) {
            $i = 0;
            foreach ($bannerItems_items as $bannerItems_item) {
                $data = [
                    'sortOrder' => $i + 1,
                ];
                if (isset($bannerItems_item['Dimage']) && trim($bannerItems_item['Dimage']) != '') {
                    $data['Dimage'] = trim($bannerItems_item['Dimage']);
                } else {
                    $data['Dimage'] = null;
                }
                if (isset($bannerItems_item['Mimage']) && trim($bannerItems_item['Mimage']) != '') {
                    $data['Mimage'] = trim($bannerItems_item['Mimage']);
                } else {
                    $data['Mimage'] = null;
                }
                if (isset($bannerItems_item['videoURL']) && trim($bannerItems_item['videoURL']) != '') {
                    $data['videoURL'] = trim($bannerItems_item['videoURL']);
                } else {
                    $data['videoURL'] = null;
                }
                if (isset($bannerItems_item['subTitle']) && trim($bannerItems_item['subTitle']) != '') {
                    $data['subTitle'] = trim($bannerItems_item['subTitle']);
                } else {
                    $data['subTitle'] = null;
                }
                $data['title'] = isset($bannerItems_item['title']) ? LinkAbstractor::translateTo($bannerItems_item['title']) : null;
                if (isset($bannerItems_item['ctaLink']) && trim($bannerItems_item['ctaLink']) != '' && (($bannerItems_item['ctaLink_c'] = Page::getByID($bannerItems_item['ctaLink'])) && !$bannerItems_item['ctaLink_c']->error)) {
                    $data['ctaLink'] = trim($bannerItems_item['ctaLink']);
                } else {
                    $data['ctaLink'] = null;
                }
                if (isset($bannerItems_item['ctaLink_text']) && trim($bannerItems_item['ctaLink_text']) != '') {
                    $data['ctaLink_text'] = trim($bannerItems_item['ctaLink_text']);
                } else {
                    $data['ctaLink_text'] = null;
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
                                $db->update('btHomeBannerSliderBannerItemsEntries', $data, ['id' => $id]);
                            }
                            break;
                        case 'insert':
                            foreach ($values as $data) {
                                $db->insert('btHomeBannerSliderBannerItemsEntries', $data);
                            }
                            break;
                        case 'delete':
                            foreach ($values as $value) {
                                $db->delete('btHomeBannerSliderBannerItemsEntries', ['id' => $value]);
                            }
                            break;
                    }
                }
            }
        }
        parent::save($args);
    }

    public function validate($args)
    {
        $e = Core::make("helper/validation/error");
        if (in_array("hideBlock", $this->btFieldsRequired) && (trim($args["hideBlock"]) == "" || !in_array($args["hideBlock"], [0, 1]))) {
            $e->add(t("The %s field is required.", t("Hide  Block")));
        }
        $bannerItemsEntriesMin = 0;
        $bannerItemsEntriesMax = 0;
        $bannerItemsEntriesErrors = 0;
        $bannerItems = [];
        if (isset($args['bannerItems']) && is_array($args['bannerItems']) && !empty($args['bannerItems'])) {
            if ($bannerItemsEntriesMin >= 1 && count($args['bannerItems']) < $bannerItemsEntriesMin) {
                $e->add(t("The %s field requires at least %s entries, %s entered.", t("banner Items"), $bannerItemsEntriesMin, count($args['bannerItems'])));
                $bannerItemsEntriesErrors++;
            }
            if ($bannerItemsEntriesMax >= 1 && count($args['bannerItems']) > $bannerItemsEntriesMax) {
                $e->add(t("The %s field is set to a maximum of %s entries, %s entered.", t("banner Items"), $bannerItemsEntriesMax, count($args['bannerItems'])));
                $bannerItemsEntriesErrors++;
            }
            if ($bannerItemsEntriesErrors == 0) {
                foreach ($args['bannerItems'] as $bannerItems_k => $bannerItems_v) {
                    if (is_array($bannerItems_v)) {
                        if (in_array("Dimage", $this->btFieldsRequired['bannerItems']) && (!isset($bannerItems_v['Dimage']) || trim($bannerItems_v['Dimage']) == "" || !is_object(File::getByID($bannerItems_v['Dimage'])))) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("Desktop Image (2000X2000)"), t("banner Items"), $bannerItems_k));
                        }
                        if (in_array("Mimage", $this->btFieldsRequired['bannerItems']) && (!isset($bannerItems_v['Mimage']) || trim($bannerItems_v['Mimage']) == "" || !is_object(File::getByID($bannerItems_v['Mimage'])))) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("Mobile Image"), t("banner Items"), $bannerItems_k));
                        }
                        if (in_array("videoURL", $this->btFieldsRequired['bannerItems']) && (!isset($bannerItems_v['videoURL']) || trim($bannerItems_v['videoURL']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("Video Link(Distribution Link)"), t("banner Items"), $bannerItems_k));
                        }
                        if (in_array("subTitle", $this->btFieldsRequired['bannerItems']) && (!isset($bannerItems_v['subTitle']) || trim($bannerItems_v['subTitle']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("Sub Title"), t("banner Items"), $bannerItems_k));
                        }
                        if (in_array("title", $this->btFieldsRequired['bannerItems']) && (!isset($bannerItems_v['title']) || trim($bannerItems_v['title']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("Title(use &lt;heading1&gt;)"), t("banner Items"), $bannerItems_k));
                        }
                        if ((in_array("ctaLink", $this->btFieldsRequired['bannerItems']) || (isset($bannerItems_v['ctaLink']) && trim($bannerItems_v['ctaLink']) != '')) && (trim($bannerItems_v['ctaLink']) == "" || (($page = Page::getByID($bannerItems_v['ctaLink'])) && $page->error !== false))) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("cta Link"), t("banner Items"), $bannerItems_k));
                        }
                    } else {
                        $e->add(t("The values for the %s field, row #%s, are incomplete.", t('banner Items'), $bannerItems_k));
                    }
                }
            }
        } else {
            if ($bannerItemsEntriesMin >= 1) {
                $e->add(t("The %s field requires at least %s entries, none entered.", t("banner Items"), $bannerItemsEntriesMin));
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
}