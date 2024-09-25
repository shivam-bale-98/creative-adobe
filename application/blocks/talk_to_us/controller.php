<?php namespace Application\Block\TalkToUs;

defined("C5_EXECUTE") or die("Access Denied.");

use AssetList;
use CollectionVersion;
use Concrete\Core\Block\BlockController;
use Core;
use Database;
use Stack;
use StackList;

class Controller extends BlockController
{
    public $btFieldsRequired = [];
    protected $btTable = 'btTalkToUs';
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
        return t("Talk To Us");
    }

    public function getSearchableContent()
    {
        $content = [];
        $content[] = $this->title;
        $content[] = $this->Desc_1;
        $content[] = $this->bgColor;
        return implode(" ", $content);
    }

    public function view()
    {
        $db = Database::connection();
        $selectForm = [];
        if ($selectForm_entries = $db->fetchAll('SELECT * FROM btTalkToUsSelectFormEntries WHERE bID = ? ORDER BY sortOrder ASC', [$this->bID])) {
            foreach ($selectForm_entries as $selectForm_entry) {
                $selectForm[$selectForm_entry['stID']] = Stack::getByID($selectForm_entry['stID']);
            }
        }
        $this->set('selectForm', $selectForm);
    }

    public function delete()
    {
        $db = Database::connection();
        $db->delete('btTalkToUsSelectFormEntries', ['bID' => $this->bID]);
        parent::delete();
    }

    public function duplicate($newBID)
    {
        $db = Database::connection();
        $selectForm_entries = $db->fetchAll('SELECT * FROM btTalkToUsSelectFormEntries WHERE bID = ? ORDER BY sortOrder ASC', [$this->bID]);
        foreach ($selectForm_entries as $selectForm_entry) {
            unset($selectForm_entry['id']);
            $db->insert('btTalkToUsSelectFormEntries', $selectForm_entry);
        }
        parent::duplicate($newBID);
    }

    public function add()
    {
        $this->addEdit();
        $selectForm_selected = [];
        $selectForm_options = $this->getStacks();
        $this->set('selectForm_options', $selectForm_options);
        $this->set('selectForm_selected', $selectForm_selected);
    }

    public function edit()
    {
        $db = Database::connection();
        $this->addEdit();
        $selectForm_selected = [];
        $selectForm_ordered = [];
        $selectForm_options = $this->getStacks();
        if ($selectForm_entries = $db->fetchAll('SELECT * FROM btTalkToUsSelectFormEntries WHERE bID = ? ORDER BY sortOrder ASC', [$this->bID])) {
            foreach ($selectForm_entries as $selectForm_entry) {
                $selectForm_selected[] = $selectForm_entry['stID'];
            }
            foreach ($selectForm_selected as $key) {
                if (array_key_exists($key, $selectForm_options)) {
                    $selectForm_ordered[$key] = $selectForm_options[$key];
                    unset($selectForm_options[$key]);
                }
            }
            $selectForm_options = $selectForm_ordered + $selectForm_options;
        }
        $this->set('selectForm_options', $selectForm_options);
        $this->set('selectForm_selected', $selectForm_selected);
    }

    protected function addEdit()
    {
        $al = AssetList::getInstance();
        $al->register('javascript', 'select2sortable', 'blocks/talk_to_us/js_form/select2.sortable.js', [], $this->pkg);
        $al->register('css', 'auto-css-' . $this->btHandle, 'blocks/' . $this->btHandle . '/auto.css', [], $this->pkg);
        $this->requireAsset('css', 'select2');
        $this->requireAsset('javascript', 'select2');
        $this->requireAsset('javascript', 'select2sortable');
        $this->requireAsset('css', 'auto-css-' . $this->btHandle);
        $this->set('btFieldsRequired', $this->btFieldsRequired);
        $this->set('identifier_getString', Core::make('helper/validation/identifier')->getString(18));
    }

    public function save($args)
    {
        $db = Database::connection();
        $selectForm_entries_db = [];
        $selectForm_queries = [];
        if ($selectForm_entries = $db->fetchAll('SELECT * FROM btTalkToUsSelectFormEntries WHERE bID = ? ORDER BY sortOrder ASC', [$this->bID])) {
            foreach ($selectForm_entries as $selectForm_entry) {
                $selectForm_entries_db[] = $selectForm_entry['id'];
            }
        }
        if (isset($args['selectForm']) && is_array($args['selectForm'])) {
            $selectForm_options = $this->getStacks();
            $i = 0;
            foreach ($args['selectForm'] as $stackID) {
                if ($stackID > 0 && array_key_exists($stackID, $selectForm_options)) {
                    $selectForm_data = [
                        'stID'      => $stackID,
                        'sortOrder' => $i,
                    ];
                    if (!empty($selectForm_entries_db)) {
                        $selectForm_entry_db_key = key($selectForm_entries_db);
                        $selectForm_entry_db_value = $selectForm_entries_db[$selectForm_entry_db_key];
                        $selectForm_queries['update'][$selectForm_entry_db_value] = $selectForm_data;
                        unset($selectForm_entries_db[$selectForm_entry_db_key]);
                    } else {
                        $selectForm_data['bID'] = $this->bID;
                        $selectForm_queries['insert'][] = $selectForm_data;
                    }
                    $i++;
                }
            }
        }
        if (!empty($selectForm_entries_db)) {
            foreach ($selectForm_entries_db as $selectForm_entry_db) {
                $selectForm_queries['delete'][] = $selectForm_entry_db;
            }
        }
        if (!empty($selectForm_queries)) {
            foreach ($selectForm_queries as $type => $values) {
                if (!empty($values)) {
                    switch ($type) {
                        case 'update':
                            foreach ($values as $id => $data) {
                                $db->update('btTalkToUsSelectFormEntries', $data, ['id' => $id]);
                            }
                            break;
                        case 'insert':
                            foreach ($values as $data) {
                                $db->insert('btTalkToUsSelectFormEntries', $data);
                            }
                            break;
                        case 'delete':
                            foreach ($values as $value) {
                                $db->delete('btTalkToUsSelectFormEntries', ['id' => $value]);
                            }
                            break;
                    }
                }
            }
        }
        if (!isset($args["hideBlock"]) || trim($args["hideBlock"]) == "" || !in_array($args["hideBlock"], [0, 1])) {
            $args["hideBlock"] = '';
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
        if (in_array("title", $this->btFieldsRequired) && (trim($args["title"]) == "")) {
            $e->add(t("The %s field is required.", t("Title")));
        }
        if (in_array("Desc_1", $this->btFieldsRequired) && trim($args["Desc_1"]) == "") {
            $e->add(t("The %s field is required.", t("desc")));
        }
        if (in_array("selectForm", $this->btFieldsRequired) && (!isset($args['selectForm']) || (!is_array($args['selectForm']) || empty($args['selectForm'])))) {
            $e->add(t("The %s field is required.", t("select Form")));
        } else {
            $stacksPosted = 0;
            $stacksMin = null;
            $stacksMax = null;
            if (isset($args['selectForm']) && is_array($args['selectForm'])) {
                $args['selectForm'] = array_unique($args['selectForm']);
                foreach ($args['selectForm'] as $stID) {
                    if ($st = Stack::getByID($stID)) {
                        $stacksPosted++;
                    }
                }
            }
            if ($stacksMin != null && $stacksMin >= 1 && $stacksPosted < $stacksMin) {
                $e->add(t("The %s field needs a minimum of %s stacks.", t("select Form"), $stacksMin));
            } elseif ($stacksMax != null && $stacksMax >= 1 && $stacksMax > $stacksMin && $stacksPosted > $stacksMax) {
                $e->add(t("The %s field has a maximum of %s stacks.", t("select Form"), $stacksMax));
            }
        }
        if (in_array("hideBlock", $this->btFieldsRequired) && (trim($args["hideBlock"]) == "" || !in_array($args["hideBlock"], [0, 1]))) {
            $e->add(t("The %s field is required.", t("hide Block")));
        }
        if (in_array("bgColor", $this->btFieldsRequired) && (trim($args["bgColor"]) == "")) {
            $e->add(t("The %s field is required.", t("background color")));
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

    private function getStacks()
    {
        $stacksOptions = [];
        $stm = new StackList();
        $stm->filterByUserAdded();
        $stacks = $stm->get();
        foreach ($stacks as $st) {
            $sv = CollectionVersion::get($st, 'ACTIVE');
            $stacksOptions[$st->getCollectionID()] = $sv->getVersionName();
        }
        return $stacksOptions;
    }
}