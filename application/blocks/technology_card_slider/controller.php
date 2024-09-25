<?php namespace Application\Block\TechnologyCardSlider;

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
    public $btFieldsRequired = ['addCards' => []];
    protected $btExportFileColumns = ['image', 'icon'];
    protected $btExportTables = ['btTechnologyCardSlider', 'btTechnologyCardSliderAddCardsEntries'];
    protected $btTable = 'btTechnologyCardSlider';
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
        return t("Technology Slider");
    }

    public function getSearchableContent()
    {
        $content = [];
        $content[] = $this->blockTitle;
        $content[] = $this->bgColor;
        $db = Database::connection();
        $addCards_items = $db->fetchAll('SELECT * FROM btTechnologyCardSliderAddCardsEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($addCards_items as $addCards_item_k => $addCards_item_v) {
            if (isset($addCards_item_v["title"]) && trim($addCards_item_v["title"]) != "") {
                $content[] = $addCards_item_v["title"];
            }
            if (isset($addCards_item_v["desc_1"]) && trim($addCards_item_v["desc_1"]) != "") {
                $content[] = $addCards_item_v["desc_1"];
            }
        }
        return implode(" ", $content);
    }

    public function view()
    {
        $db = Database::connection();
        $this->set('blockTitle', LinkAbstractor::translateFrom($this->blockTitle));
        $addCards = [];
        $addCards_items = $db->fetchAll('SELECT * FROM btTechnologyCardSliderAddCardsEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($addCards_items as $addCards_item_k => &$addCards_item_v) {
            if (isset($addCards_item_v['image']) && trim($addCards_item_v['image']) != "" && ($f = File::getByID($addCards_item_v['image'])) && is_object($f)) {
                $addCards_item_v['image'] = $f;
            } else {
                $addCards_item_v['image'] = false;
            }
            if (isset($addCards_item_v['icon']) && trim($addCards_item_v['icon']) != "" && ($f = File::getByID($addCards_item_v['icon'])) && is_object($f)) {
                $addCards_item_v['icon'] = $f;
            } else {
                $addCards_item_v['icon'] = false;
            }
        }
        $this->set('addCards_items', $addCards_items);
        $this->set('addCards', $addCards);
    }

    public function delete()
    {
        $db = Database::connection();
        $db->delete('btTechnologyCardSliderAddCardsEntries', ['bID' => $this->bID]);
        parent::delete();
    }

    public function duplicate($newBID)
    {
        $db = Database::connection();
        $addCards_items = $db->fetchAll('SELECT * FROM btTechnologyCardSliderAddCardsEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($addCards_items as $addCards_item) {
            unset($addCards_item['id']);
            $addCards_item['bID'] = $newBID;
            $db->insert('btTechnologyCardSliderAddCardsEntries', $addCards_item);
        }
        parent::duplicate($newBID);
    }

    public function add()
    {
        $this->addEdit();
        $addCards = $this->get('addCards');
        $this->set('addCards_items', []);
        $this->set('addCards', $addCards);
    }

    public function edit()
    {
        $db = Database::connection();
        $this->addEdit();
        
        $this->set('blockTitle', LinkAbstractor::translateFromEditMode($this->blockTitle));
        $addCards = $this->get('addCards');
        $addCards_items = $db->fetchAll('SELECT * FROM btTechnologyCardSliderAddCardsEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($addCards_items as &$addCards_item) {
            if (!File::getByID($addCards_item['image'])) {
                unset($addCards_item['image']);
            }
        }
        foreach ($addCards_items as &$addCards_item) {
            if (!File::getByID($addCards_item['icon'])) {
                unset($addCards_item['icon']);
            }
        }
        $this->set('addCards', $addCards);
        $this->set('addCards_items', $addCards_items);
    }

    protected function addEdit()
    {
        $addCards = [];
        $this->set('addCards', $addCards);
        $this->set('identifier', new \Concrete\Core\Utility\Service\Identifier());
        $al = AssetList::getInstance();
        $al->register('css', 'repeatable-ft.form', 'blocks/technology_card_slider/css_form/repeatable-ft.form.css', [], $this->pkg);
        $al->register('javascript', 'handlebars', 'blocks/technology_card_slider/js_form/handlebars-v4.0.4.js', [], $this->pkg);
        $al->register('javascript', 'handlebars-helpers', 'blocks/technology_card_slider/js_form/handlebars-helpers.js', [], $this->pkg);
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
        $args['blockTitle'] = LinkAbstractor::translateTo($args['blockTitle']);
        if (!isset($args["hideBlock"]) || trim($args["hideBlock"]) == "" || !in_array($args["hideBlock"], [0, 1])) {
            $args["hideBlock"] = '';
        }
        $rows = $db->fetchAll('SELECT * FROM btTechnologyCardSliderAddCardsEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        $addCards_items = isset($args['addCards']) && is_array($args['addCards']) ? $args['addCards'] : [];
        $queries = [];
        if (!empty($addCards_items)) {
            $i = 0;
            foreach ($addCards_items as $addCards_item) {
                $data = [
                    'sortOrder' => $i + 1,
                ];
                if (isset($addCards_item['image']) && trim($addCards_item['image']) != '') {
                    $data['image'] = trim($addCards_item['image']);
                } else {
                    $data['image'] = null;
                }
                if (isset($addCards_item['title']) && trim($addCards_item['title']) != '') {
                    $data['title'] = trim($addCards_item['title']);
                } else {
                    $data['title'] = null;
                }
                if (isset($addCards_item['desc_1']) && trim($addCards_item['desc_1']) != '') {
                    $data['desc_1'] = trim($addCards_item['desc_1']);
                } else {
                    $data['desc_1'] = null;
                }
                if (isset($addCards_item['icon']) && trim($addCards_item['icon']) != '') {
                    $data['icon'] = trim($addCards_item['icon']);
                } else {
                    $data['icon'] = null;
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
                                $db->update('btTechnologyCardSliderAddCardsEntries', $data, ['id' => $id]);
                            }
                            break;
                        case 'insert':
                            foreach ($values as $data) {
                                $db->insert('btTechnologyCardSliderAddCardsEntries', $data);
                            }
                            break;
                        case 'delete':
                            foreach ($values as $value) {
                                $db->delete('btTechnologyCardSliderAddCardsEntries', ['id' => $value]);
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
        if (in_array("blockTitle", $this->btFieldsRequired) && (trim($args["blockTitle"]) == "")) {
            $e->add(t("The %s field is required.", t("block title")));
        }
        if (in_array("hideBlock", $this->btFieldsRequired) && (trim($args["hideBlock"]) == "" || !in_array($args["hideBlock"], [0, 1]))) {
            $e->add(t("The %s field is required.", t("hide block")));
        }
        if (in_array("bgColor", $this->btFieldsRequired) && (trim($args["bgColor"]) == "")) {
            $e->add(t("The %s field is required.", t("back color")));
        }
        $addCardsEntriesMin = 0;
        $addCardsEntriesMax = 0;
        $addCardsEntriesErrors = 0;
        $addCards = [];
        if (isset($args['addCards']) && is_array($args['addCards']) && !empty($args['addCards'])) {
            if ($addCardsEntriesMin >= 1 && count($args['addCards']) < $addCardsEntriesMin) {
                $e->add(t("The %s field requires at least %s entries, %s entered.", t("add cards"), $addCardsEntriesMin, count($args['addCards'])));
                $addCardsEntriesErrors++;
            }
            if ($addCardsEntriesMax >= 1 && count($args['addCards']) > $addCardsEntriesMax) {
                $e->add(t("The %s field is set to a maximum of %s entries, %s entered.", t("add cards"), $addCardsEntriesMax, count($args['addCards'])));
                $addCardsEntriesErrors++;
            }
            if ($addCardsEntriesErrors == 0) {
                foreach ($args['addCards'] as $addCards_k => $addCards_v) {
                    if (is_array($addCards_v)) {
                        if (in_array("image", $this->btFieldsRequired['addCards']) && (!isset($addCards_v['image']) || trim($addCards_v['image']) == "" || !is_object(File::getByID($addCards_v['image'])))) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("Image"), t("add cards"), $addCards_k));
                        }
                        if (in_array("title", $this->btFieldsRequired['addCards']) && (!isset($addCards_v['title']) || trim($addCards_v['title']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("title"), t("add cards"), $addCards_k));
                        }
                        if (in_array("desc_1", $this->btFieldsRequired['addCards']) && (!isset($addCards_v['desc_1']) || trim($addCards_v['desc_1']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("description"), t("add cards"), $addCards_k));
                        }
                        if (in_array("icon", $this->btFieldsRequired['addCards']) && (!isset($addCards_v['icon']) || trim($addCards_v['icon']) == "" || !is_object(File::getByID($addCards_v['icon'])))) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("icon(svg)"), t("add cards"), $addCards_k));
                        }
                    } else {
                        $e->add(t("The values for the %s field, row #%s, are incomplete.", t('add cards'), $addCards_k));
                    }
                }
            }
        } else {
            if ($addCardsEntriesMin >= 1) {
                $e->add(t("The %s field requires at least %s entries, none entered.", t("add cards"), $addCardsEntriesMin));
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
}