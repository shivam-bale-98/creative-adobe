<?php
namespace Application\Concrete\Api\V1;

use Application\Concrete\Helpers\SelectOptionsHelper;
use Concrete\Core\Block\Block;
use \Concrete\Core\Controller\Controller;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\Service\Ajax;
use Concrete\Core\Utility\Service\Text;
use Concrete\Core\View\View as CurrentView;
use Core;

class BlocksApi extends Controller
{
    public function getListingBlock() {
        $th = new Text();
        $ajax = new Ajax();

        if ($ajax->isAjaxRequest(Request::getInstance())) {
            $data = '';

            $bID = $th->sanitize($this->get("bID"));
            if(!$bID)
                return;

            $block = Block::getByID($bID);
            $list = $block->getController()->getPages();

            ob_start();
            CurrentView::element('blocks/listing_block/page_item', ['pages' => $list['pages']]);
            $data .= ob_get_clean();

            $ajax->sendResult(['success' => true, 'data' => $data, 'loadMore' => $list["loadMore"]]);
        }
    }

    public function getSliderBlockAttributeOptions() {
        $th = new Text();
        $ajax = new Ajax();

        if ($ajax->isAjaxRequest(Request::getInstance())) {
            $data = '';

            $attributeHandle = $th->sanitize($this->get("attributeHandle"));
            if($attributeHandle) {
                $options = SelectOptionsHelper::getOptions($attributeHandle);

                foreach ($options as $option) {
                    $data .= "<option value='{$option}'>{$option}</option>";
                }
            }

            $ajax->sendResult(['success' => true, 'data' => $data]);
        }
    }
}