<?php namespace Application\Block\AccordionBlock;

defined("C5_EXECUTE") or die("Access Denied.");

use AssetList;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Core;
use Database;

class Controller extends BlockController
{
    public $btFieldsRequired = ['accordionsItem' => []];
    protected $btExportTables = ['btAccordionBlock', 'btAccordionBlockAccordionsItemEntries'];
    protected $btTable = 'btAccordionBlock';
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
        return t("accordion block");
    }

    public function getSearchableContent()
    {
        $content = [];
        $content[] = $this->blockTitle;
        $db = Database::connection();
        $accordionsItem_items = $db->fetchAll('SELECT * FROM btAccordionBlockAccordionsItemEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($accordionsItem_items as $accordionsItem_item_k => $accordionsItem_item_v) {
            if (isset($accordionsItem_item_v["accordionTitle"]) && trim($accordionsItem_item_v["accordionTitle"]) != "") {
                $content[] = $accordionsItem_item_v["accordionTitle"];
            }
            if (isset($accordionsItem_item_v["accordionContent"]) && trim($accordionsItem_item_v["accordionContent"]) != "") {
                $content[] = $accordionsItem_item_v["accordionContent"];
            }
        }
        $content[] = $this->bgColor;
        return implode(" ", $content);
    }

    public function view()
    {
        $db = Database::connection();
        $this->set('blockTitle', LinkAbstractor::translateFrom($this->blockTitle));
        $accordionsItem = [];
        $accordionsItem_items = $db->fetchAll('SELECT * FROM btAccordionBlockAccordionsItemEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($accordionsItem_items as $accordionsItem_item_k => &$accordionsItem_item_v) {
            $accordionsItem_item_v["accordionContent"] = isset($accordionsItem_item_v["accordionContent"]) ? LinkAbstractor::translateFrom($accordionsItem_item_v["accordionContent"]) : null;
        }
        $this->set('accordionsItem_items', $accordionsItem_items);
        $this->set('accordionsItem', $accordionsItem);
    }

    public function delete()
    {
        $db = Database::connection();
        $db->delete('btAccordionBlockAccordionsItemEntries', ['bID' => $this->bID]);
        parent::delete();
    }

    public function duplicate($newBID)
    {
        $db = Database::connection();
        $accordionsItem_items = $db->fetchAll('SELECT * FROM btAccordionBlockAccordionsItemEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($accordionsItem_items as $accordionsItem_item) {
            unset($accordionsItem_item['id']);
            $accordionsItem_item['bID'] = $newBID;
            $db->insert('btAccordionBlockAccordionsItemEntries', $accordionsItem_item);
        }
        parent::duplicate($newBID);
    }

    public function add()
    {
        $this->addEdit();
        $accordionsItem = $this->get('accordionsItem');
        $this->set('accordionsItem_items', []);
        $this->set('accordionsItem', $accordionsItem);
    }

    public function edit()
    {
        $db = Database::connection();
        $this->addEdit();
        
        $this->set('blockTitle', LinkAbstractor::translateFromEditMode($this->blockTitle));
        $accordionsItem = $this->get('accordionsItem');
        $accordionsItem_items = $db->fetchAll('SELECT * FROM btAccordionBlockAccordionsItemEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        
        foreach ($accordionsItem_items as &$accordionsItem_item) {
            $accordionsItem_item['accordionContent'] = isset($accordionsItem_item['accordionContent']) ? LinkAbstractor::translateFromEditMode($accordionsItem_item['accordionContent']) : null;
        }
        $this->set('accordionsItem', $accordionsItem);
        $this->set('accordionsItem_items', $accordionsItem_items);
    }

    protected function addEdit()
    {
        $accordionsItem = [];
        $this->set('accordionsItem', $accordionsItem);
        $this->set('identifier', new \Concrete\Core\Utility\Service\Identifier());
        $al = AssetList::getInstance();
        $al->register('css', 'repeatable-ft.form', 'blocks/accordion_block/css_form/repeatable-ft.form.css', [], $this->pkg);
        $al->register('javascript', 'handlebars', 'blocks/accordion_block/js_form/handlebars-v4.0.4.js', [], $this->pkg);
        $al->register('javascript', 'handlebars-helpers', 'blocks/accordion_block/js_form/handlebars-helpers.js', [], $this->pkg);
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
        $rows = $db->fetchAll('SELECT * FROM btAccordionBlockAccordionsItemEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        $accordionsItem_items = isset($args['accordionsItem']) && is_array($args['accordionsItem']) ? $args['accordionsItem'] : [];
        $queries = [];
        if (!empty($accordionsItem_items)) {
            $i = 0;
            foreach ($accordionsItem_items as $accordionsItem_item) {
                $data = [
                    'sortOrder' => $i + 1,
                ];
                if (isset($accordionsItem_item['accordionTitle']) && trim($accordionsItem_item['accordionTitle']) != '') {
                    $data['accordionTitle'] = trim($accordionsItem_item['accordionTitle']);
                } else {
                    $data['accordionTitle'] = null;
                }
                $data['accordionContent'] = isset($accordionsItem_item['accordionContent']) ? LinkAbstractor::translateTo($accordionsItem_item['accordionContent']) : null;
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
                                $db->update('btAccordionBlockAccordionsItemEntries', $data, ['id' => $id]);
                            }
                            break;
                        case 'insert':
                            foreach ($values as $data) {
                                $db->insert('btAccordionBlockAccordionsItemEntries', $data);
                            }
                            break;
                        case 'delete':
                            foreach ($values as $value) {
                                $db->delete('btAccordionBlockAccordionsItemEntries', ['id' => $value]);
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
            $e->add(t("The %s field is required.", t("Block Title")));
        }
        $accordionsItemEntriesMin = 0;
        $accordionsItemEntriesMax = 0;
        $accordionsItemEntriesErrors = 0;
        $accordionsItem = [];
        if (isset($args['accordionsItem']) && is_array($args['accordionsItem']) && !empty($args['accordionsItem'])) {
            if ($accordionsItemEntriesMin >= 1 && count($args['accordionsItem']) < $accordionsItemEntriesMin) {
                $e->add(t("The %s field requires at least %s entries, %s entered.", t("accordions_item"), $accordionsItemEntriesMin, count($args['accordionsItem'])));
                $accordionsItemEntriesErrors++;
            }
            if ($accordionsItemEntriesMax >= 1 && count($args['accordionsItem']) > $accordionsItemEntriesMax) {
                $e->add(t("The %s field is set to a maximum of %s entries, %s entered.", t("accordions_item"), $accordionsItemEntriesMax, count($args['accordionsItem'])));
                $accordionsItemEntriesErrors++;
            }
            if ($accordionsItemEntriesErrors == 0) {
                foreach ($args['accordionsItem'] as $accordionsItem_k => $accordionsItem_v) {
                    if (is_array($accordionsItem_v)) {
                        if (in_array("accordionTitle", $this->btFieldsRequired['accordionsItem']) && (!isset($accordionsItem_v['accordionTitle']) || trim($accordionsItem_v['accordionTitle']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("Accordion Title"), t("accordions_item"), $accordionsItem_k));
                        }
                        if (in_array("accordionContent", $this->btFieldsRequired['accordionsItem']) && (!isset($accordionsItem_v['accordionContent']) || trim($accordionsItem_v['accordionContent']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("Accordion Content"), t("accordions_item"), $accordionsItem_k));
                        }
                    } else {
                        $e->add(t("The values for the %s field, row #%s, are incomplete.", t('accordions_item'), $accordionsItem_k));
                    }
                }
            }
        } else {
            if ($accordionsItemEntriesMin >= 1) {
                $e->add(t("The %s field requires at least %s entries, none entered.", t("accordions_item"), $accordionsItemEntriesMin));
            }
        }
        if (in_array("bgColor", $this->btFieldsRequired) && (trim($args["bgColor"]) == "")) {
            $e->add(t("The %s field is required.", t("background Color")));
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