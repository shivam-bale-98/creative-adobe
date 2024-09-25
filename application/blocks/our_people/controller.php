<?php namespace Application\Block\OurPeople;

defined("C5_EXECUTE") or die("Access Denied.");

use Application\Concrete\Helpers\GeneralHelper;
use Application\Concrete\Models\OurPeople\OurPeopleList;
use Concrete\Core\Block\BlockController;
use Application\Concrete\View\View;
use Concrete\Core\Utility\Service\Text;
use Core;
use Concrete\Core\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends BlockController
{
    public $btFieldsRequired = ['title'];
    protected $btTable = 'btOurPeople';
    protected $btInterfaceWidth = 400;
    protected $btInterfaceHeight = 500;
    protected $btIgnorePageThemeGridFrameworkContainer = false;
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $pkg = false;
    public $page;
    const VIEW_TYPE = 'our_people/view';
    CONST ACTION_HANDLE = "our_people";
    const ITEMS_PER_PAGE     = 5;

    public function getBlockTypeName()
    {
        return t("Our People");
    }

    public function getSearchableContent()
    {
        $content = [];
        $content[] = $this->title;
        $content[] = $this->bgColor;
        $content[] = $this->paddingTop;
        $content[] = $this->paddingBottom;
        return implode(" ", $content);
    }

    public function add()
    {
        $this->addEdit();
    }

    public function edit()
    {
        $this->addEdit();
    }

    protected function addEdit()
    {
        $this->set('btFieldsRequired', $this->btFieldsRequired);
        $this->set('identifier_getString', Core::make('helper/validation/identifier')->getString(18));
    }

    function on_start()
    {
        parent::on_start();
        $th     = new Text();
        $this->page     = $th->sanitize($this->get('page'));
        $this->page     = (int) $this->page ? $this->page : 1;
    }

    public function view()
    {
        $pages = $this->getPages();
        $this->set("token", Core::make('helper/validation/token')->output(self::ACTION_HANDLE, true));
        $this->set('loadMoreFlag', $pages['total'] <= self::ITEMS_PER_PAGE ? false : true);
        $this->set('pages', $pages['pages']);
        $this->set('viewType', self::VIEW_TYPE);
    }

    public function validate($args)
    {
        $e = Core::make("helper/validation/error");
        if (in_array("title", $this->btFieldsRequired) && (trim($args["title"]) == "")) {
            $e->add(t("The %s field is required.", t("Title")));
        }
        return $e;
    }

    public function composer()
    {
        $this->edit();
    }

    public function action_our_people_block($parameters = null) {
        if(!Core::make('helper/validation/token')->validate(self::ACTION_HANDLE)) return new JsonResponse([
            "title" => t("Error"),
            "message" => t("Please refresh the page and try again!"),
        ], Response::HTTP_BAD_REQUEST);

        $pageResponse = $this->getPages();
        $data = [
            "data" => View::elementRender(self::VIEW_TYPE, ["pages" => $pageResponse['pages']]),
            'success' => $pageResponse ? true:false,
            "loadMore" => $pageResponse['loadMore']
        ];
        return new JsonResponse($data);
    }

    public function getPages()
    {
        $pl = new OurPeopleList();
        $pl->setItemsPerPage(static::ITEMS_PER_PAGE);
        $result = $pl->getPage($this->page);
        return $result;
    }
}