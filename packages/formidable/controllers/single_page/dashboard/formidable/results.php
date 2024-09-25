<?php
namespace Concrete\Package\Formidable\Controller\SinglePage\Dashboard\Formidable;

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Package\Formidable\Controller\SinglePage\Dashboard\Formidable;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\Query;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\SavedSearch;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\Menu\MenuFactory;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\SearchProvider;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\RequestModifier\FormID as FormIDRequestModifier;
use Concrete\Package\Formidable\Src\Formidable\Results\Search\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Package\Formidable\Src\Formidable\Forms\Form as FormidableForm;
use Concrete\Package\Formidable\Src\Formidable\Results\Result as FormidableResult;
use Concrete\Package\Formidable\Src\Formidable\Results\ResultExport as FormidableResultExport;
use Concrete\Package\Formidable\Src\Formidable\Mails\Mail as FormidableMail;
use Concrete\Package\Formidable\Src\Formidable\Helpers\Common as CommonHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete\Core\Csv\WriterFactory;
use Exception;

class Results extends Formidable
{
    /**
     * @var Element
     */
    protected $headerMenu;

    /**
     * @var Element
     */
    protected $headerSearch;

    /**
     * @return SearchProvider
     */
    protected function getSearchProvider()
    {
        return $this->app->make(SearchProvider::class);
    }

    /**
     * @return QueryFactory
     */
    protected function getQueryFactory()
    {
        return $this->app->make(QueryFactory::class);
    }

    protected function getHeaderMenu()
    {
        if (!isset($this->headerMenu)) {
            $this->headerMenu = $this->app->make(ElementManager::class)->get('results/search/menu', [], 'formidable');
        }
        return $this->headerMenu;
    }

    protected function getHeaderSearch()
    {
        if (!isset($this->headerSearch)) {
            $this->headerSearch = $this->app->make(ElementManager::class)->get('results/search/search', [], 'formidable');
        }
        return $this->headerSearch;
    }

    /**
     * @param Result $result
     */
    protected function renderSearchResult(Result $result)
    {
        $headerMenu = $this->getHeaderMenu();
        $headerSearch = $this->getHeaderSearch();

        $headerMenu->getElementController()->setQuery($result->getQuery());
        $headerSearch->getElementController()->setQuery($result->getQuery());

        $this->set('resultsBulkMenu', $this->app->make(MenuFactory::class)->createBulkMenu());
        $this->set('result', $result);
        $this->set('headerMenu', $headerMenu);
        $this->set('headerSearch', $headerSearch);

        $this->setThemeViewTemplate('full.php');
    }

    protected function createSearchResult(Query $query)
    {
        $provider = $this->app->make(SearchProvider::class);

        $queryModifier = $this->app->make(QueryModifier::class);
        $queryModifier->addModifier(new AutoSortColumnRequestModifier($provider, $this->request, Request::METHOD_GET));
        $queryModifier->addModifier(new ItemsPerPageRequestModifier($provider, $this->request, Request::METHOD_GET));
        $queryModifier->addModifier(new FormIDRequestModifier($provider, $this->request, Request::METHOD_GET));
        $query = $queryModifier->process($query);

        $list = $provider->getItemList();
        $list->filterByForm($query->getFormID());

        $columns = $query->getColumns();
        if (is_object($columns)) {
            $column = $columns->getDefaultSortColumn();
            $list->sortBySearchColumn($column);
        } else {
            $columns = $provider->getDefaultColumnSet();
        }

        foreach ($query->getFields() as $field) {
            $field->filterList($list);
        }

        if ($list instanceof PagerProviderInterface) {
            $manager = $list->getPagerManager();
            $manager->sortListByCursor($list, $list->getActiveSortDirection());
        }

        $list->setItemsPerPage($query->getItemsPerPage());
        $result = $provider->createSearchResultObject($columns, $list);
        $result->setQuery($query);

        return $result;
    }


    protected function getSearchKeywordsField()
    {
        $keywords = null;
        if ($this->request->query->has('keywords')) {
            $keywords = $this->request->query->get('keywords');
        }
        return new KeywordsField($keywords);
    }

    public function view($formID = 0)
    {
        if ($formID != 0) {
            $provider = $this->getSearchProvider();
            $query = $this->getQueryFactory()->createQuery($provider, [$this->getSearchKeywordsField()]);
            $query->setFormID($formID);
            $provider->setSessionCurrentQuery($query);
            $this->buildRedirect('/dashboard/formidable/results')->send();
        }

        $query = $this->getQueryFactory()->createQuery($this->getSearchProvider(), [$this->getSearchKeywordsField()]);

        $result = $this->createSearchResult($query);
        $this->renderSearchResult($result);

        $this->headerSearch->getElementController()->setQuery($query);

        $this->loadAssets($query->getFormID());
        $this->set('pageTitle', t('Formidable Results'));
    }

    public function advanced_search()
    {

        $query = $this->getQueryFactory()->createFromAdvancedSearchRequest( $this->getSearchProvider(),  $this->request, Request::METHOD_GET);

        $result = $this->createSearchResult($query);
        $this->renderSearchResult($result);

        $this->loadAssets($query->getFormID());
        $this->set('pageTitle', t('Formidable Results'));
    }

    public function preset($presetID = null)
    {
        if ($presetID) {
            $preset = $this->entityManager->find(SavedSearch::class, $presetID);
            if ($preset) {
                $query = $this->getQueryFactory()->createFromSavedSearch($preset);
                if (is_object($query)) {
                    $result = $this->createSearchResult($query);
                    $this->renderSearchResult($result);

                    $this->loadAssets($query->getFormID());
                    $this->set('pageTitle', t('Formidable Results'));

                    return;
                }
            }
        }

        $this->view();
    }

    public function reset()
    {
        $provider = $this->app->make(SearchProvider::class);
        $provider->clearSessionCurrentQuery();
        $this->buildRedirect('/dashboard/formidable/results')->send();

    }

    /* view and edit result */
    public function details($formID, $resultID)
    {
        $this->loadAssets($formID);

        $result = FormidableResult::getByID($resultID);
        if (!is_object($result)) {
            $this->notification('error', t('Invalid form, not found'));
            $this->buildRedirect('/dashboard/formidable/forms')->send();
        }
        $this->result = $result;
        $this->set('result', $this->result);

        $header = $this->app->make(ElementManager::class);
        $this->set('headerMenu', $header->get('results/menu', ['action' => $this->getAction(), 'post' => $this->request->request->all(), 'ff' => $this->ff, 'result' => $result], 'formidable'));

        $this->set('pageTitle', t('View result "#%s"', $this->result->getItemID()));

    }

    /* export results */
    public function export($formID, $searchMethod = null)
    {
        $this->loadAssets($formID);

        $headers = [
            'Content-Type' => 'application/csv',
            'Content-Transfer-Encoding' => 'UTF-8',
            'Content-Disposition' => 'attachment; filename=formidable_results_'.$formID.'.csv',
        ];
        $app = $this->app;
        $config = $this->app->make('config');
        $bom = $config->get('concrete.export.csv.include_bom') ? $config->get('concrete.charset_bom') : '';

        $query = $this->getQueryFactory()->createQuery($this->getSearchProvider(), [$this->getSearchKeywordsField()]);
        if ($searchMethod === 'advanced_search') {
            $query = $this->getQueryFactory()->createFromAdvancedSearchRequest( $this->getSearchProvider(),  $this->request, Request::METHOD_GET );
        }
        $result = $this->createSearchResult($query);

        return StreamedResponse::create(
            function () use ($app, $result, $bom) {
                $writer = $app->make(FormidableResultExport::class, ['writer' => $this->app->make(WriterFactory::class)->createFromPath('php://output', 'w')]);
                echo $bom;
                $writer->setUnloadDoctrineEveryTick(50);
                $writer->insertHeaders($this->ff->getElements());
                $writer->insertList($result->getItemListObject());
            },
            200,
            $headers);
    }


    /* delete items */
    public function delete($type = 'delete', $formID = 0)
    {
        $this->loadAssets($formID);

        switch ($type) {
            case 'result':
                return $this->resultDelete();
            break;
        }
        return $this->setResponse('error', t('Invalid request'));
    }

    /* delete result */
    private function resultDelete()
    {
        $post = $this->request->request;

        if (!$this->token->validate('delete_result')) {
            $this->error->add(t($this->token->getErrorMessage()));
        }

        $res = [];

        /* check if multiple */
        $results = explode(',', $post->get('resultID'));
        if (count($results)) {
            foreach ($results as $resultID) {
                $result = FormidableResult::getByID($resultID);
                if (!is_object($result) || $result->getForm()->getItemID() != $this->ff->getItemID()) {
                    return $this->setResponse('error',  t('Invalid result'));
                }
                $res[] = $result;
            }
        }

        if ($this->error->has()) {
            return $this->setResponse('error', $this->error);
        }
        else {

            // delete result
            foreach ($res as $result) {
                $result->delete();
            }

            $this->notification('success', t('Result(s) successfully deleted!'));

            $redirect = Url::to('/dashboard/formidable/results');
            return $this->setResponse('location', (string)$redirect);
        }
    }


    /* resend items */
    public function resend($type = 'resend', $formID = 0)
    {
        $this->loadAssets($formID);

        switch ($type) {
            case 'result':
                return $this->resendSubmit();
            break;
        }
        return $this->setResponse('error', t('Invalid request'));
    }

    /* resend result */
    private function resendSubmit()
    {
        $post = $this->request->request;

        if (!$this->token->validate('resend_result')) {
            $this->error->add(t($this->token->getErrorMessage()));
        }

        $res = [];

        /* check if multiple */
        $results = explode(',', $post->get('resultID'));
        if (count($results)) {
            foreach ($results as $resultID) {
                $result = FormidableResult::getByID($resultID);
                if (!is_object($result) || $result->getForm()->getItemID() != $this->ff->getItemID()) {
                    return $this->setResponse('error',  t('Invalid result'));
                }
                $res[] = $result;
            }
        }

        $mails = (array)$post->get('mailID');
        if (!count($mails)) {
            $this->error->add(t('No mails selected to resend!'));
        }
        foreach ($mails as $mailID) {
            $mail = FormidableMail::getByID($mailID);
            if (!is_object($mail) || $mail->getForm()->getItemID() != $this->ff->getItemID()) {
                return $this->setResponse('error',  t('Invalid mail'));
            }
            $mls[] = $mail;
        }

        if ($this->error->has()) {
            return $this->setResponse('error', $this->error);
        }
        else {

            $u = new User();

            // resend result
            foreach ($res as $result) {

                foreach ($mls as $mail) {

                    try {
                        // each mail
                        $mail->setResult($result);
                        $mail->send();

                        // log mail
                        CommonHelper::createLog($result, t('Mail "%s" send to: %s', $mail->getName(), $mail->getToDisplayPlain()), $u);

                    }
                    catch (Exception $e) {

                        // log mail
                        CommonHelper::createLog($result, t('Mail "%s" unsuccessful: %s', $mail->getName(), $e->getMessage()), $u);
                        $this->error->add(t('Mail "%s" unsuccessful: %s', $mail->getName(), $e->getMessage()));
                    }
                }
            }

            if ($this->error->has()) {
                $this->notification('warning', t('Result(s) resend, but with errors! Check the logs.'));
            }
            else {
                $this->notification('success', t('Result(s) successfully resend!'));
            }

            $redirect = Url::to('/dashboard/formidable/results');
            return $this->setResponse('location', (string)$redirect);
        }
    }

    /* load common assets for each action */
    private function loadAssets($formID = 0)
    {
        $this->requireAsset('javascript', 'formidable/dashboard/all');
        $this->requireAsset('javascript', 'formidable/dashboard/result');
        $this->requireAsset('css', 'formidable/dashboard/all');

        $ff = FormidableForm::getByID((int)$formID);
        if (!is_object($ff)) {
            $this->notification('error', t('Invalid form, not found'));
            $this->buildRedirect('/dashboard/formidable/forms')->send();
        }
        elseif ($ff->getSite() != $this->app->make('site')->getActiveSiteForEditing()) {
            $this->notification('error', t('Invalid form, not found'));
            $this->buildRedirect('/dashboard/formidable/forms')->send();
        }

        $this->ff = $ff;
        $this->set('ff', $this->ff);

        $this->showFlash();
    }
}
