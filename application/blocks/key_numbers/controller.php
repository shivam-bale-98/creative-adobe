<?php namespace Application\Block\KeyNumbers;

defined("C5_EXECUTE") or die("Access Denied.");

use AssetList;
use Concrete\Core\Block\BlockController;
use Core;
use Database;

class Controller extends BlockController
{
    public $btFieldsRequired = ['keyNumbersLeft' => [], 'keyNumbersRight' => []];
    protected $btExportTables = ['btKeyNumbers', 'btKeyNumbersKeyNumbersLeftEntries', 'btKeyNumbersKeyNumbersRightEntries'];
    protected $btTable = 'btKeyNumbers';
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
        return t("Key Numbers");
    }

    public function getSearchableContent()
    {
        $content = [];
        $content[] = $this->bgColor;
        $db = Database::connection();
        $keyNumbersLeft_items = $db->fetchAll('SELECT * FROM btKeyNumbersKeyNumbersLeftEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($keyNumbersLeft_items as $keyNumbersLeft_item_k => $keyNumbersLeft_item_v) {
            if (isset($keyNumbersLeft_item_v["leftValues"]) && trim($keyNumbersLeft_item_v["leftValues"]) != "") {
                $content[] = $keyNumbersLeft_item_v["leftValues"];
            }
            if (isset($keyNumbersLeft_item_v["leftDesc"]) && trim($keyNumbersLeft_item_v["leftDesc"]) != "") {
                $content[] = $keyNumbersLeft_item_v["leftDesc"];
            }
        }
        $db = Database::connection();
        $keyNumbersRight_items = $db->fetchAll('SELECT * FROM btKeyNumbersKeyNumbersRightEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($keyNumbersRight_items as $keyNumbersRight_item_k => $keyNumbersRight_item_v) {
            if (isset($keyNumbersRight_item_v["rightValues"]) && trim($keyNumbersRight_item_v["rightValues"]) != "") {
                $content[] = $keyNumbersRight_item_v["rightValues"];
            }
            if (isset($keyNumbersRight_item_v["rightDesc"]) && trim($keyNumbersRight_item_v["rightDesc"]) != "") {
                $content[] = $keyNumbersRight_item_v["rightDesc"];
            }
        }
        return implode(" ", $content);
    }

    public function view()
    {
        $db = Database::connection();
        $keyNumbersLeft = [];
        $keyNumbersLeft_items = $db->fetchAll('SELECT * FROM btKeyNumbersKeyNumbersLeftEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        $this->set('keyNumbersLeft_items', $keyNumbersLeft_items);
        $this->set('keyNumbersLeft', $keyNumbersLeft);
        $keyNumbersRight = [];
        $keyNumbersRight_items = $db->fetchAll('SELECT * FROM btKeyNumbersKeyNumbersRightEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        $this->set('keyNumbersRight_items', $keyNumbersRight_items);
        $this->set('keyNumbersRight', $keyNumbersRight);
    }

    public function delete()
    {
        $db = Database::connection();
        $db->delete('btKeyNumbersKeyNumbersLeftEntries', ['bID' => $this->bID]);
        $db->delete('btKeyNumbersKeyNumbersRightEntries', ['bID' => $this->bID]);
        parent::delete();
    }

    public function duplicate($newBID)
    {
        $db = Database::connection();
        $keyNumbersLeft_items = $db->fetchAll('SELECT * FROM btKeyNumbersKeyNumbersLeftEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($keyNumbersLeft_items as $keyNumbersLeft_item) {
            unset($keyNumbersLeft_item['id']);
            $keyNumbersLeft_item['bID'] = $newBID;
            $db->insert('btKeyNumbersKeyNumbersLeftEntries', $keyNumbersLeft_item);
        }
        $keyNumbersRight_items = $db->fetchAll('SELECT * FROM btKeyNumbersKeyNumbersRightEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($keyNumbersRight_items as $keyNumbersRight_item) {
            unset($keyNumbersRight_item['id']);
            $keyNumbersRight_item['bID'] = $newBID;
            $db->insert('btKeyNumbersKeyNumbersRightEntries', $keyNumbersRight_item);
        }
        parent::duplicate($newBID);
    }

    public function add()
    {
        $this->addEdit();
        $keyNumbersLeft = $this->get('keyNumbersLeft');
        $this->set('keyNumbersLeft_items', []);
        $this->set('keyNumbersLeft', $keyNumbersLeft);
        $keyNumbersRight = $this->get('keyNumbersRight');
        $this->set('keyNumbersRight_items', []);
        $this->set('keyNumbersRight', $keyNumbersRight);
    }

    public function edit()
    {
        $db = Database::connection();
        $this->addEdit();
        $keyNumbersLeft = $this->get('keyNumbersLeft');
        $keyNumbersLeft_items = $db->fetchAll('SELECT * FROM btKeyNumbersKeyNumbersLeftEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        $this->set('keyNumbersLeft', $keyNumbersLeft);
        $this->set('keyNumbersLeft_items', $keyNumbersLeft_items);
        $keyNumbersRight = $this->get('keyNumbersRight');
        $keyNumbersRight_items = $db->fetchAll('SELECT * FROM btKeyNumbersKeyNumbersRightEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        $this->set('keyNumbersRight', $keyNumbersRight);
        $this->set('keyNumbersRight_items', $keyNumbersRight_items);
    }

    protected function addEdit()
    {
        $keyNumbersLeft = [];
        $this->set('keyNumbersLeft', $keyNumbersLeft);
        $this->set('identifier', new \Concrete\Core\Utility\Service\Identifier());
        $keyNumbersRight = [];
        $this->set('keyNumbersRight', $keyNumbersRight);
        $al = AssetList::getInstance();
        $al->register('css', 'repeatable-ft.form', 'blocks/key_numbers/css_form/repeatable-ft.form.css', [], $this->pkg);
        $al->register('javascript', 'handlebars', 'blocks/key_numbers/js_form/handlebars-v4.0.4.js', [], $this->pkg);
        $al->register('javascript', 'handlebars-helpers', 'blocks/key_numbers/js_form/handlebars-helpers.js', [], $this->pkg);
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
        if (!isset($args["borders"]) || trim($args["borders"]) == "" || !in_array($args["borders"], [0, 1])) {
            $args["borders"] = '';
        }
        $rows = $db->fetchAll('SELECT * FROM btKeyNumbersKeyNumbersLeftEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        $keyNumbersLeft_items = isset($args['keyNumbersLeft']) && is_array($args['keyNumbersLeft']) ? $args['keyNumbersLeft'] : [];
        $queries = [];
        if (!empty($keyNumbersLeft_items)) {
            $i = 0;
            foreach ($keyNumbersLeft_items as $keyNumbersLeft_item) {
                $data = [
                    'sortOrder' => $i + 1,
                ];
                if (isset($keyNumbersLeft_item['leftValues']) && trim($keyNumbersLeft_item['leftValues']) != '') {
                    $data['leftValues'] = trim($keyNumbersLeft_item['leftValues']);
                } else {
                    $data['leftValues'] = null;
                }
                if (isset($keyNumbersLeft_item['leftDesc']) && trim($keyNumbersLeft_item['leftDesc']) != '') {
                    $data['leftDesc'] = trim($keyNumbersLeft_item['leftDesc']);
                } else {
                    $data['leftDesc'] = null;
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
                                $db->update('btKeyNumbersKeyNumbersLeftEntries', $data, ['id' => $id]);
                            }
                            break;
                        case 'insert':
                            foreach ($values as $data) {
                                $db->insert('btKeyNumbersKeyNumbersLeftEntries', $data);
                            }
                            break;
                        case 'delete':
                            foreach ($values as $value) {
                                $db->delete('btKeyNumbersKeyNumbersLeftEntries', ['id' => $value]);
                            }
                            break;
                    }
                }
            }
        }
        $rows = $db->fetchAll('SELECT * FROM btKeyNumbersKeyNumbersRightEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        $keyNumbersRight_items = isset($args['keyNumbersRight']) && is_array($args['keyNumbersRight']) ? $args['keyNumbersRight'] : [];
        $queries = [];
        if (!empty($keyNumbersRight_items)) {
            $i = 0;
            foreach ($keyNumbersRight_items as $keyNumbersRight_item) {
                $data = [
                    'sortOrder' => $i + 1,
                ];
                if (isset($keyNumbersRight_item['rightValues']) && trim($keyNumbersRight_item['rightValues']) != '') {
                    $data['rightValues'] = trim($keyNumbersRight_item['rightValues']);
                } else {
                    $data['rightValues'] = null;
                }
                if (isset($keyNumbersRight_item['rightDesc']) && trim($keyNumbersRight_item['rightDesc']) != '') {
                    $data['rightDesc'] = trim($keyNumbersRight_item['rightDesc']);
                } else {
                    $data['rightDesc'] = null;
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
                                $db->update('btKeyNumbersKeyNumbersRightEntries', $data, ['id' => $id]);
                            }
                            break;
                        case 'insert':
                            foreach ($values as $data) {
                                $db->insert('btKeyNumbersKeyNumbersRightEntries', $data);
                            }
                            break;
                        case 'delete':
                            foreach ($values as $value) {
                                $db->delete('btKeyNumbersKeyNumbersRightEntries', ['id' => $value]);
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
            $e->add(t("The %s field is required.", t("backgroundColor(romance: #F2F1EF, rustic-red: #1A0A0C, wheat: #ECE6E4)")));
        }
        if (in_array("borders", $this->btFieldsRequired) && (trim($args["borders"]) == "" || !in_array($args["borders"], [0, 1]))) {
            $e->add(t("The %s field is required.", t("select borders")));
        }
        $keyNumbersLeftEntriesMin = 0;
        $keyNumbersLeftEntriesMax = 0;
        $keyNumbersLeftEntriesErrors = 0;
        $keyNumbersLeft = [];
        if (isset($args['keyNumbersLeft']) && is_array($args['keyNumbersLeft']) && !empty($args['keyNumbersLeft'])) {
            if ($keyNumbersLeftEntriesMin >= 1 && count($args['keyNumbersLeft']) < $keyNumbersLeftEntriesMin) {
                $e->add(t("The %s field requires at least %s entries, %s entered.", t("key numbers left"), $keyNumbersLeftEntriesMin, count($args['keyNumbersLeft'])));
                $keyNumbersLeftEntriesErrors++;
            }
            if ($keyNumbersLeftEntriesMax >= 1 && count($args['keyNumbersLeft']) > $keyNumbersLeftEntriesMax) {
                $e->add(t("The %s field is set to a maximum of %s entries, %s entered.", t("key numbers left"), $keyNumbersLeftEntriesMax, count($args['keyNumbersLeft'])));
                $keyNumbersLeftEntriesErrors++;
            }
            if ($keyNumbersLeftEntriesErrors == 0) {
                foreach ($args['keyNumbersLeft'] as $keyNumbersLeft_k => $keyNumbersLeft_v) {
                    if (is_array($keyNumbersLeft_v)) {
                        if (in_array("leftValues", $this->btFieldsRequired['keyNumbersLeft']) && (!isset($keyNumbersLeft_v['leftValues']) || trim($keyNumbersLeft_v['leftValues']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("left values"), t("key numbers left"), $keyNumbersLeft_k));
                        }
                        if (in_array("leftDesc", $this->btFieldsRequired['keyNumbersLeft']) && (!isset($keyNumbersLeft_v['leftDesc']) || trim($keyNumbersLeft_v['leftDesc']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("left desc"), t("key numbers left"), $keyNumbersLeft_k));
                        }
                    } else {
                        $e->add(t("The values for the %s field, row #%s, are incomplete.", t('key numbers left'), $keyNumbersLeft_k));
                    }
                }
            }
        } else {
            if ($keyNumbersLeftEntriesMin >= 1) {
                $e->add(t("The %s field requires at least %s entries, none entered.", t("key numbers left"), $keyNumbersLeftEntriesMin));
            }
        }
        $keyNumbersRightEntriesMin = 0;
        $keyNumbersRightEntriesMax = 0;
        $keyNumbersRightEntriesErrors = 0;
        $keyNumbersRight = [];
        if (isset($args['keyNumbersRight']) && is_array($args['keyNumbersRight']) && !empty($args['keyNumbersRight'])) {
            if ($keyNumbersRightEntriesMin >= 1 && count($args['keyNumbersRight']) < $keyNumbersRightEntriesMin) {
                $e->add(t("The %s field requires at least %s entries, %s entered.", t("key numbers right"), $keyNumbersRightEntriesMin, count($args['keyNumbersRight'])));
                $keyNumbersRightEntriesErrors++;
            }
            if ($keyNumbersRightEntriesMax >= 1 && count($args['keyNumbersRight']) > $keyNumbersRightEntriesMax) {
                $e->add(t("The %s field is set to a maximum of %s entries, %s entered.", t("key numbers right"), $keyNumbersRightEntriesMax, count($args['keyNumbersRight'])));
                $keyNumbersRightEntriesErrors++;
            }
            if ($keyNumbersRightEntriesErrors == 0) {
                foreach ($args['keyNumbersRight'] as $keyNumbersRight_k => $keyNumbersRight_v) {
                    if (is_array($keyNumbersRight_v)) {
                        if (in_array("rightValues", $this->btFieldsRequired['keyNumbersRight']) && (!isset($keyNumbersRight_v['rightValues']) || trim($keyNumbersRight_v['rightValues']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("right value"), t("key numbers right"), $keyNumbersRight_k));
                        }
                        if (in_array("rightDesc", $this->btFieldsRequired['keyNumbersRight']) && (!isset($keyNumbersRight_v['rightDesc']) || trim($keyNumbersRight_v['rightDesc']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("right desc"), t("key numbers right"), $keyNumbersRight_k));
                        }
                    } else {
                        $e->add(t("The values for the %s field, row #%s, are incomplete.", t('key numbers right'), $keyNumbersRight_k));
                    }
                }
            }
        } else {
            if ($keyNumbersRightEntriesMin >= 1) {
                $e->add(t("The %s field requires at least %s entries, none entered.", t("key numbers right"), $keyNumbersRightEntriesMin));
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