<?php
namespace Application\Concrete\Api\V1;

use Application\Concrete\Helpers\ConstantHelper;
use Application\Concrete\Helpers\GeneralHelper;
use \Concrete\Core\Controller\Controller;
use Concrete\Core\Http\Service\Ajax;
use Concrete\Core\Utility\Service\Text;
use Concrete\Core\View\View as CurrentView;
use Core;
use Symfony\Component\HttpFoundation\JsonResponse;

class DownloadsBlockApi extends Controller
{
    const VIEW = 'downloads/filesView';

    public function getDownloadsBlock() {
        /** @var Text $th */
        $ajax = new Ajax();
        $th = Core::make("helper/text");
        $filesetName = urldecode($th->sanitize($this->get('category')));

        if($filesetName == '')
        {
            $files[] = GeneralHelper::productDownloads();
            $files[] = GeneralHelper::fileSetDownloads();
            $filesData = collect($files)->flatten(1)->unique('url')->toArray();

        }
        else if($filesetName == 'products')
        {
            $filesData = GeneralHelper::productDownloads();
        }
        else
        {
            $filesData = GeneralHelper::fileSetDownloads($filesetName);
        }
        $listHTML = "";

        if (is_array($filesData) && count($filesData) > 0) {
            ob_start();
            CurrentView::element(self::VIEW, ['files' => $filesData]);
            $listHTML .= ob_get_clean();
        }else {
            ob_start();
            CurrentView::element(ConstantHelper::NOT_RESULTS_FOUND_TEMPLATE, []);
            $listHTML .= ob_get_clean();
        }

        $rParams = [
            'listHTML' => $listHTML,
            'success' => true,
            'countMessage' => count($filesData) > 0 ? t('Data found'): t('No results found'),
            'total' => count($filesData),
        ];
        return new JsonResponse($rParams);

    }
}