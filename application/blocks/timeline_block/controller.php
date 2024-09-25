<?php namespace Application\Block\TimelineBlock;

defined("C5_EXECUTE") or die("Access Denied.");

use AssetList;
use Concrete\Core\Block\BlockController;
use Core;
use Database;
use File;
use Page;

class Controller extends BlockController
{
    public $btFieldsRequired = ['addTimeline' => []];
    protected $btExportFileColumns = ['image'];
    protected $btExportTables = ['btTimelineBlock', 'btTimelineBlockAddTimelineEntries'];
    protected $btTable = 'btTimelineBlock';
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
        return t("Timeline Block");
    }

    public function getSearchableContent()
    {
        $content = [];
        $content[] = $this->bgColor;
        $db = Database::connection();
        $addTimeline_items = $db->fetchAll('SELECT * FROM btTimelineBlockAddTimelineEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($addTimeline_items as $addTimeline_item_k => $addTimeline_item_v) {
            if (isset($addTimeline_item_v["year"]) && trim($addTimeline_item_v["year"]) != "") {
                $content[] = $addTimeline_item_v["year"];
            }
            if (isset($addTimeline_item_v["title"]) && trim($addTimeline_item_v["title"]) != "") {
                $content[] = $addTimeline_item_v["title"];
            }
            if (isset($addTimeline_item_v["desc_1"]) && trim($addTimeline_item_v["desc_1"]) != "") {
                $content[] = $addTimeline_item_v["desc_1"];
            }
        }
        return implode(" ", $content);
    }

    public function view()
    {
        $db = Database::connection();
        $addTimeline = [];
        $addTimeline_items = $db->fetchAll('SELECT * FROM btTimelineBlockAddTimelineEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($addTimeline_items as $addTimeline_item_k => &$addTimeline_item_v) {
            if (isset($addTimeline_item_v['image']) && trim($addTimeline_item_v['image']) != "" && ($f = File::getByID($addTimeline_item_v['image'])) && is_object($f)) {
                $addTimeline_item_v['image'] = $f;
            } else {
                $addTimeline_item_v['image'] = false;
            }
        }
        $this->set('addTimeline_items', $addTimeline_items);
        $this->set('addTimeline', $addTimeline);
    }

    public function delete()
    {
        $db = Database::connection();
        $db->delete('btTimelineBlockAddTimelineEntries', ['bID' => $this->bID]);
        parent::delete();
    }

    public function duplicate($newBID)
    {
        $db = Database::connection();
        $addTimeline_items = $db->fetchAll('SELECT * FROM btTimelineBlockAddTimelineEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($addTimeline_items as $addTimeline_item) {
            unset($addTimeline_item['id']);
            $addTimeline_item['bID'] = $newBID;
            $db->insert('btTimelineBlockAddTimelineEntries', $addTimeline_item);
        }
        parent::duplicate($newBID);
    }

    public function add()
    {
        $this->addEdit();
        $addTimeline = $this->get('addTimeline');
        $this->set('addTimeline_items', []);
        $this->set('addTimeline', $addTimeline);
    }

    public function edit()
    {
        $db = Database::connection();
        $this->addEdit();
        $addTimeline = $this->get('addTimeline');
        $addTimeline_items = $db->fetchAll('SELECT * FROM btTimelineBlockAddTimelineEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($addTimeline_items as &$addTimeline_item) {
            if (!File::getByID($addTimeline_item['image'])) {
                unset($addTimeline_item['image']);
            }
        }
        $this->set('addTimeline', $addTimeline);
        $this->set('addTimeline_items', $addTimeline_items);
    }

    protected function addEdit()
    {
        $addTimeline = [];
        $this->set('addTimeline', $addTimeline);
        $this->set('identifier', new \Concrete\Core\Utility\Service\Identifier());
        $al = AssetList::getInstance();
        $al->register('css', 'repeatable-ft.form', 'blocks/timeline_block/css_form/repeatable-ft.form.css', [], $this->pkg);
        $al->register('javascript', 'handlebars', 'blocks/timeline_block/js_form/handlebars-v4.0.4.js', [], $this->pkg);
        $al->register('javascript', 'handlebars-helpers', 'blocks/timeline_block/js_form/handlebars-helpers.js', [], $this->pkg);
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
        $rows = $db->fetchAll('SELECT * FROM btTimelineBlockAddTimelineEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        $addTimeline_items = isset($args['addTimeline']) && is_array($args['addTimeline']) ? $args['addTimeline'] : [];
        $queries = [];
        if (!empty($addTimeline_items)) {
            $i = 0;
            foreach ($addTimeline_items as $addTimeline_item) {
                $data = [
                    'sortOrder' => $i + 1,
                ];
                if (isset($addTimeline_item['year']) && trim($addTimeline_item['year']) != '') {
                    $data['year'] = trim($addTimeline_item['year']);
                } else {
                    $data['year'] = null;
                }
                if (isset($addTimeline_item['image']) && trim($addTimeline_item['image']) != '') {
                    $data['image'] = trim($addTimeline_item['image']);
                } else {
                    $data['image'] = null;
                }
                if (isset($addTimeline_item['title']) && trim($addTimeline_item['title']) != '') {
                    $data['title'] = trim($addTimeline_item['title']);
                } else {
                    $data['title'] = null;
                }
                if (isset($addTimeline_item['desc_1']) && trim($addTimeline_item['desc_1']) != '') {
                    $data['desc_1'] = trim($addTimeline_item['desc_1']);
                } else {
                    $data['desc_1'] = null;
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
                                $db->update('btTimelineBlockAddTimelineEntries', $data, ['id' => $id]);
                            }
                            break;
                        case 'insert':
                            foreach ($values as $data) {
                                $db->insert('btTimelineBlockAddTimelineEntries', $data);
                            }
                            break;
                        case 'delete':
                            foreach ($values as $value) {
                                $db->delete('btTimelineBlockAddTimelineEntries', ['id' => $value]);
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
            $e->add(t("The %s field is required.", t("background-color")));
        }
        if (in_array("hideBlock", $this->btFieldsRequired) && (trim($args["hideBlock"]) == "" || !in_array($args["hideBlock"], [0, 1]))) {
            $e->add(t("The %s field is required.", t("hide block")));
        }
        $addTimelineEntriesMin = 0;
        $addTimelineEntriesMax = 0;
        $addTimelineEntriesErrors = 0;
        $addTimeline = [];
        if (isset($args['addTimeline']) && is_array($args['addTimeline']) && !empty($args['addTimeline'])) {
            if ($addTimelineEntriesMin >= 1 && count($args['addTimeline']) < $addTimelineEntriesMin) {
                $e->add(t("The %s field requires at least %s entries, %s entered.", t("add timeline"), $addTimelineEntriesMin, count($args['addTimeline'])));
                $addTimelineEntriesErrors++;
            }
            if ($addTimelineEntriesMax >= 1 && count($args['addTimeline']) > $addTimelineEntriesMax) {
                $e->add(t("The %s field is set to a maximum of %s entries, %s entered.", t("add timeline"), $addTimelineEntriesMax, count($args['addTimeline'])));
                $addTimelineEntriesErrors++;
            }
            if ($addTimelineEntriesErrors == 0) {
                foreach ($args['addTimeline'] as $addTimeline_k => $addTimeline_v) {
                    if (is_array($addTimeline_v)) {
                        if (in_array("year", $this->btFieldsRequired['addTimeline']) && (!isset($addTimeline_v['year']) || trim($addTimeline_v['year']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("year"), t("add timeline"), $addTimeline_k));
                        }
                        if (in_array("image", $this->btFieldsRequired['addTimeline']) && (!isset($addTimeline_v['image']) || trim($addTimeline_v['image']) == "" || !is_object(File::getByID($addTimeline_v['image'])))) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("image"), t("add timeline"), $addTimeline_k));
                        }
                        if (in_array("title", $this->btFieldsRequired['addTimeline']) && (!isset($addTimeline_v['title']) || trim($addTimeline_v['title']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("title"), t("add timeline"), $addTimeline_k));
                        }
                        if (in_array("desc_1", $this->btFieldsRequired['addTimeline']) && (!isset($addTimeline_v['desc_1']) || trim($addTimeline_v['desc_1']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("description"), t("add timeline"), $addTimeline_k));
                        }
                    } else {
                        $e->add(t("The values for the %s field, row #%s, are incomplete.", t('add timeline'), $addTimeline_k));
                    }
                }
            }
        } else {
            if ($addTimelineEntriesMin >= 1) {
                $e->add(t("The %s field requires at least %s entries, none entered.", t("add timeline"), $addTimelineEntriesMin));
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