<?php namespace Application\Block\TestimonialSlider;

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
    public $btFieldsRequired = ['testimonials' => []];
    protected $btExportFileColumns = ['redLogo', 'whiteLogo'];
    protected $btExportTables = ['btTestimonialSlider', 'btTestimonialSliderTestimonialsEntries'];
    protected $btTable = 'btTestimonialSlider';
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
        return t("Testimonial Slider");
    }

    public function getSearchableContent()
    {
        $content = [];
        $content[] = $this->bgColor;
        $content[] = $this->subTitle;
        $content[] = $this->title;
        $db = Database::connection();
        $testimonials_items = $db->fetchAll('SELECT * FROM btTestimonialSliderTestimonialsEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($testimonials_items as $testimonials_item_k => $testimonials_item_v) {
            if (isset($testimonials_item_v["description_1"]) && trim($testimonials_item_v["description_1"]) != "") {
                $content[] = $testimonials_item_v["description_1"];
            }
            if (isset($testimonials_item_v["authorName"]) && trim($testimonials_item_v["authorName"]) != "") {
                $content[] = $testimonials_item_v["authorName"];
            }
            if (isset($testimonials_item_v["authorDesignation"]) && trim($testimonials_item_v["authorDesignation"]) != "") {
                $content[] = $testimonials_item_v["authorDesignation"];
            }
        }
        return implode(" ", $content);
    }

    public function view()
    {
        $db = Database::connection();
        $this->set('title', LinkAbstractor::translateFrom($this->title));
        $testimonials = [];
        $testimonials_items = $db->fetchAll('SELECT * FROM btTestimonialSliderTestimonialsEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($testimonials_items as $testimonials_item_k => &$testimonials_item_v) {
            if (isset($testimonials_item_v['redLogo']) && trim($testimonials_item_v['redLogo']) != "" && ($f = File::getByID($testimonials_item_v['redLogo'])) && is_object($f)) {
                $testimonials_item_v['redLogo'] = $f;
            } else {
                $testimonials_item_v['redLogo'] = false;
            }
            if (isset($testimonials_item_v['whiteLogo']) && trim($testimonials_item_v['whiteLogo']) != "" && ($f = File::getByID($testimonials_item_v['whiteLogo'])) && is_object($f)) {
                $testimonials_item_v['whiteLogo'] = $f;
            } else {
                $testimonials_item_v['whiteLogo'] = false;
            }
            $testimonials_item_v["description_1"] = isset($testimonials_item_v["description_1"]) ? LinkAbstractor::translateFrom($testimonials_item_v["description_1"]) : null;
        }
        $this->set('testimonials_items', $testimonials_items);
        $this->set('testimonials', $testimonials);
    }

    public function delete()
    {
        $db = Database::connection();
        $db->delete('btTestimonialSliderTestimonialsEntries', ['bID' => $this->bID]);
        parent::delete();
    }

    public function duplicate($newBID)
    {
        $db = Database::connection();
        $testimonials_items = $db->fetchAll('SELECT * FROM btTestimonialSliderTestimonialsEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($testimonials_items as $testimonials_item) {
            unset($testimonials_item['id']);
            $testimonials_item['bID'] = $newBID;
            $db->insert('btTestimonialSliderTestimonialsEntries', $testimonials_item);
        }
        parent::duplicate($newBID);
    }

    public function add()
    {
        $this->addEdit();
        $testimonials = $this->get('testimonials');
        $this->set('testimonials_items', []);
        $this->set('testimonials', $testimonials);
    }

    public function edit()
    {
        $db = Database::connection();
        $this->addEdit();
        
        $this->set('title', LinkAbstractor::translateFromEditMode($this->title));
        $testimonials = $this->get('testimonials');
        $testimonials_items = $db->fetchAll('SELECT * FROM btTestimonialSliderTestimonialsEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        foreach ($testimonials_items as &$testimonials_item) {
            if (!File::getByID($testimonials_item['redLogo'])) {
                unset($testimonials_item['redLogo']);
            }
        }
        foreach ($testimonials_items as &$testimonials_item) {
            if (!File::getByID($testimonials_item['whiteLogo'])) {
                unset($testimonials_item['whiteLogo']);
            }
        }
        
        foreach ($testimonials_items as &$testimonials_item) {
            $testimonials_item['description_1'] = isset($testimonials_item['description_1']) ? LinkAbstractor::translateFromEditMode($testimonials_item['description_1']) : null;
        }
        $this->set('testimonials', $testimonials);
        $this->set('testimonials_items', $testimonials_items);
    }

    protected function addEdit()
    {
        $testimonials = [];
        $this->set('testimonials', $testimonials);
        $this->set('identifier', new \Concrete\Core\Utility\Service\Identifier());
        $al = AssetList::getInstance();
        $al->register('css', 'repeatable-ft.form', 'blocks/testimonial_slider/css_form/repeatable-ft.form.css', [], $this->pkg);
        $al->register('javascript', 'handlebars', 'blocks/testimonial_slider/js_form/handlebars-v4.0.4.js', [], $this->pkg);
        $al->register('javascript', 'handlebars-helpers', 'blocks/testimonial_slider/js_form/handlebars-helpers.js', [], $this->pkg);
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
        $rows = $db->fetchAll('SELECT * FROM btTestimonialSliderTestimonialsEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        $testimonials_items = isset($args['testimonials']) && is_array($args['testimonials']) ? $args['testimonials'] : [];
        $queries = [];
        if (!empty($testimonials_items)) {
            $i = 0;
            foreach ($testimonials_items as $testimonials_item) {
                $data = [
                    'sortOrder' => $i + 1,
                ];
                if (isset($testimonials_item['redLogo']) && trim($testimonials_item['redLogo']) != '') {
                    $data['redLogo'] = trim($testimonials_item['redLogo']);
                } else {
                    $data['redLogo'] = null;
                }
                if (isset($testimonials_item['whiteLogo']) && trim($testimonials_item['whiteLogo']) != '') {
                    $data['whiteLogo'] = trim($testimonials_item['whiteLogo']);
                } else {
                    $data['whiteLogo'] = null;
                }
                $data['description_1'] = isset($testimonials_item['description_1']) ? LinkAbstractor::translateTo($testimonials_item['description_1']) : null;
                if (isset($testimonials_item['authorName']) && trim($testimonials_item['authorName']) != '') {
                    $data['authorName'] = trim($testimonials_item['authorName']);
                } else {
                    $data['authorName'] = null;
                }
                if (isset($testimonials_item['authorDesignation']) && trim($testimonials_item['authorDesignation']) != '') {
                    $data['authorDesignation'] = trim($testimonials_item['authorDesignation']);
                } else {
                    $data['authorDesignation'] = null;
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
                                $db->update('btTestimonialSliderTestimonialsEntries', $data, ['id' => $id]);
                            }
                            break;
                        case 'insert':
                            foreach ($values as $data) {
                                $db->insert('btTestimonialSliderTestimonialsEntries', $data);
                            }
                            break;
                        case 'delete':
                            foreach ($values as $value) {
                                $db->delete('btTestimonialSliderTestimonialsEntries', ['id' => $value]);
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
            $e->add(t("The %s field is required.", t("background color (rustic red : #1A0A0C, wheat: #ECE6E4; romance : #F2F1EF, berry-red: #80151A)")));
        }
        if (in_array("hideBlock", $this->btFieldsRequired) && (trim($args["hideBlock"]) == "" || !in_array($args["hideBlock"], [0, 1]))) {
            $e->add(t("The %s field is required.", t("Hide Block")));
        }
        if (in_array("subTitle", $this->btFieldsRequired) && (trim($args["subTitle"]) == "")) {
            $e->add(t("The %s field is required.", t("sub title")));
        }
        if (in_array("title", $this->btFieldsRequired) && (trim($args["title"]) == "")) {
            $e->add(t("The %s field is required.", t("title")));
        }
        $testimonialsEntriesMin = 0;
        $testimonialsEntriesMax = 0;
        $testimonialsEntriesErrors = 0;
        $testimonials = [];
        if (isset($args['testimonials']) && is_array($args['testimonials']) && !empty($args['testimonials'])) {
            if ($testimonialsEntriesMin >= 1 && count($args['testimonials']) < $testimonialsEntriesMin) {
                $e->add(t("The %s field requires at least %s entries, %s entered.", t("Testimonials"), $testimonialsEntriesMin, count($args['testimonials'])));
                $testimonialsEntriesErrors++;
            }
            if ($testimonialsEntriesMax >= 1 && count($args['testimonials']) > $testimonialsEntriesMax) {
                $e->add(t("The %s field is set to a maximum of %s entries, %s entered.", t("Testimonials"), $testimonialsEntriesMax, count($args['testimonials'])));
                $testimonialsEntriesErrors++;
            }
            if ($testimonialsEntriesErrors == 0) {
                foreach ($args['testimonials'] as $testimonials_k => $testimonials_v) {
                    if (is_array($testimonials_v)) {
                        if (in_array("redLogo", $this->btFieldsRequired['testimonials']) && (!isset($testimonials_v['redLogo']) || trim($testimonials_v['redLogo']) == "" || !is_object(File::getByID($testimonials_v['redLogo'])))) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("red logo (svg: 280X300 max resolution)"), t("Testimonials"), $testimonials_k));
                        }
                        if (in_array("whiteLogo", $this->btFieldsRequired['testimonials']) && (!isset($testimonials_v['whiteLogo']) || trim($testimonials_v['whiteLogo']) == "" || !is_object(File::getByID($testimonials_v['whiteLogo'])))) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("white logo (svg: 280X300 max resolution)"), t("Testimonials"), $testimonials_k));
                        }
                        if (in_array("description_1", $this->btFieldsRequired['testimonials']) && (!isset($testimonials_v['description_1']) || trim($testimonials_v['description_1']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("desc"), t("Testimonials"), $testimonials_k));
                        }
                        if (in_array("authorName", $this->btFieldsRequired['testimonials']) && (!isset($testimonials_v['authorName']) || trim($testimonials_v['authorName']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("Author Name"), t("Testimonials"), $testimonials_k));
                        }
                        if (in_array("authorDesignation", $this->btFieldsRequired['testimonials']) && (!isset($testimonials_v['authorDesignation']) || trim($testimonials_v['authorDesignation']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("Author Designation"), t("Testimonials"), $testimonials_k));
                        }
                    } else {
                        $e->add(t("The values for the %s field, row #%s, are incomplete.", t('Testimonials'), $testimonials_k));
                    }
                }
            }
        } else {
            if ($testimonialsEntriesMin >= 1) {
                $e->add(t("The %s field requires at least %s entries, none entered.", t("Testimonials"), $testimonialsEntriesMin));
            }
        }
        if (in_array("removePaddingTop", $this->btFieldsRequired) && (trim($args["removePaddingTop"]) == "" || !in_array($args["removePaddingTop"], [0, 1]))) {
            $e->add(t("The %s field is required.", t("remove padding top ")));
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