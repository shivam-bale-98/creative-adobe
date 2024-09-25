<?php
namespace Concrete\Package\Formidable;

defined('C5_EXECUTE') or die('Access Denied.');

use Application\Concrete\Helpers\FormidableHelper;
use Concrete\Core\Support\Facade\Events;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Support\Facade\Log;
use Concrete\Core\Support\Facade\Route;
use Concrete\Core\Package\Package;
use Concrete\Core\Asset\AssetList;
use Concrete\Package\Formidable\Src\Installer;
use Concrete\Core\Command\Task\Manager as TaskManager;
use Concrete\Package\Formidable\Src\Formidable\Tasks\CleanupController;
use Concrete\Package\Formidable\Src\Formidable\Helpers\FormidableFull;
use Concrete\Core\Http\Request;

class Controller extends Package {

	protected $pkgHandle = 'formidable';
	protected $appVersionRequired = '9.0.0';
	protected $pkgVersion = '1.1.4.2';

	protected $pkgAutoloaderRegistries = [
        'src' => '\Concrete\Package\Formidable\Src',
    ];

	public function getPackageName()
    {
		return t('Formidable');
	}

	public function getPackageDescription()
    {
		return t("Create awesome forms with a few clicks!");
	}

	public function on_start()
    {
        $this->registerRoutes();
        $this->registerAssets();
        $this->registerTasks();

        Events::addListener('on_formidable_submit_sf', function ($event) {

            $data = $event->getArgument('formData');
            $endpoint = $event->getArgument('base_uri');

            $app    = Facade::getFacadeApplication();
            $logger = $app->make('log/factory')->createLogger('EnableSync');

            Log::addEntry("::: in event :::");
            Log::addEntry("formdata :: " . var_export($data, true) . PHP_EOL . " endpoint :: " . var_export($endpoint, true));

            try {
                $client = new \GuzzleHttp\Client([
                    'base_uri' => $endpoint,
                ]);

                $response = $client->request('POST', '', [
                    'json' => $data
                ]);

                $response_body = $response->getBody();

                if($response_body){
                    \Concrete\Core\Support\Facade\Log::addEntry('Success : ' . var_export($endpoint, true) .' response : ' . var_export($response_body, true) . ' Params:  ' . var_export($json_body, true));
                }else{
                    \Concrete\Core\Support\Facade\Log::addEntry('Failure : ' . var_export($endpoint, true) .' Params:  ' . var_export($json_body, true));
                }

            }catch (\Exception $exception) {
                $logger->error('Failure : ' . var_export($exception->getMessage(), true));
            }
        });
	}

	public function install()
    {
        // Run install
        $pkg = parent::install();

        // Do some install stuff of package
        $installer = new Installer($pkg);
        $installer->refreshEntities();
        $installer->install();

        $this->installContentFile('config/tasks.xml');

        $r = Request::getInstance();
        if ((int)$r->request->get('convertFromFormidableFull')) {
            $formidable = new FormidableFull();
            $formidable->convert();

            if ((int)$r->request->get('removeFormidableFull')) {
                $formidable->remove();
            }
        }
    }

    public function upgrade()
    {
        // Run upgrade
        parent::upgrade();

        // Do some upgrade stuff of package
        $installer = new Installer();
        $installer->refreshEntities();
        $installer->upgrade();
        $installer->clearCache();

        $this->installContentFile('config/tasks.xml');
    }

    public function uninstall()
    {
        // Do some uninstall stuff of package
        $installer = new Installer();
        $installer->uninstall();
        $installer->clearCache();

        //Run uninstall
        parent::uninstall();
    }

	private function registerRoutes()
    {
        $register = [
            '/formidable/dialog/dashboard/results/advanced_search' => '\Concrete\Package\Formidable\Controller\Element\Results\Search\AdvancedSearch::view',
            '/formidable/dialog/dashboard/results/advanced_search/add_field' => '\Concrete\Package\Formidable\Controller\Element\Results\Search\AdvancedSearch::addField',
            '/formidable/dialog/dashboard/results/advanced_search/submit' => '\Concrete\Package\Formidable\Controller\Element\Results\Search\AdvancedSearch::submit',
            '/formidable/dialog/dashboard/results/advanced_search/save_preset' => '\Concrete\Package\Formidable\Controller\Element\Results\Search\AdvancedSearch::savePreset',

            '/formidable/dialog/dashboard/results/advanced_search/preset/edit' => '\Concrete\Package\Formidable\Controller\Element\Results\Search\Preset\Edit::view',
            '/formidable/dialog/dashboard/results/advanced_search/preset/edit/edit_search_preset' => '\Concrete\Package\Formidable\Controller\Element\Results\Search\Preset\Edit::edit_search_preset',

            '/formidable/dialog/dashboard/results/advanced_search/preset/delete' => '\Concrete\Package\Formidable\Controller\Element\Results\Search\Preset\Delete::view',
            '/formidable/dialog/dashboard/results/advanced_search/preset/delete/remove_search_preset' => '\Concrete\Package\Formidable\Controller\Element\Results\Search\Preset\Delete::remove_search_preset',

            '/formidable/dialog/formidable' => '\Concrete\Package\Formidable\Controller\Dialog\Formidable::view',
        ];

        if (is_array($register) && count($register)) {
            foreach ($register as $path => $controller) {
                Route::register($path, $controller);
            }
        }
    }

    private function registerAssets()
    {
        $al = AssetList::getInstance();
        $al->register('javascript', 'formidable/dashboard/all', 'js/dashboard/all.js', ['minify' => true, 'combine' => true], $this->pkgHandle);
        $al->register('javascript', 'formidable/dashboard/form', 'js/dashboard/form.js', ['minify' => true, 'combine' => true], $this->pkgHandle);
        $al->register('javascript', 'formidable/dashboard/result', 'js/dashboard/result.js', ['minify' => true, 'combine' => true], $this->pkgHandle);
        $al->register('javascript', 'formidable/dashboard/template', 'js/dashboard/template.js', ['minify' => true, 'combine' => true], $this->pkgHandle);
        $al->register('css', 'formidable/dashboard/all', 'css/dashboard/all.css', ['minify' => true, 'combine' => true], $this->pkgHandle);

        $this->registerCaptcha();
    }

    private function registerTasks()
    {
        $manager = $this->app->make(TaskManager::class);
        $manager->extend('cleanup', function () {
            return new CleanupController();
        });
    }



    private function registerCaptcha()
    {
        $al = AssetList::getInstance();
        $config = $this->app->make('config');

        $script = "var formidable_captcha_site_key = '" . $config->get('captcha.recaptcha_v3.site_key') . "';";
        $al->register('javascript-inline', 'formidable/captcha_inline', $script, array('minify' => false, 'combine' => false));
        $al->register('javascript', 'formidable/captcha_render', 'js/captcha_template.js', array('minify' => false, 'combine' => false), $this->pkgHandle);
    }
}