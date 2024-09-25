<?php namespace Concrete\Package\BlockDesigner;

use Package;
use Page;
use SinglePage;
use Exception;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends Package
{
	protected $pkgHandle = 'block_designer';
	protected $appVersionRequired = '9.0.1';
	protected $pkgVersion = '4.1.3';
	protected $pkgAutoloaderRegistries = [
		'src' => 'RamonLeenders\\BlockDesigner',
	];

	public function getPackageName()
	{
		return t("Block Designer");
	}

	public function getPackageDescription()
	{
		return t("Design your own content blocks within a few clicks!");
	}

	public function install()
	{
		$package = parent::install();
		$this->installUpgrade($package);
	}

	public function uninstall(){
		if($bdp = Package::getByHandle('block_designer_pro')){
			if($bdp->isPackageInstalled()){
				throw new Exception(t("You can not uninstall Block Designer while Block Designer Pro is still installed. Please uninstall Block Designer Pro first if you'd like to continue."));
			}
		}

		parent::uninstall();
	}

	public function upgrade()
	{
		$this->installUpgrade($this);

		parent::upgrade();
	}

	protected function installSinglePages($package)
	{
		$singlePages = [
			['path' => '/dashboard/blocks/block_order'],
			['path' => sprintf('/dashboard/blocks/%s', $this->pkgHandle)],
			['path' => sprintf('/dashboard/blocks/%s/block_config', $this->pkgHandle)],
			['path' => sprintf('/dashboard/blocks/%s/settings', $this->pkgHandle)],
		];
		foreach ($singlePages as $singlePage) {
			$singlePageObject = Page::getByPath($singlePage['path']);
			// Check if it exists, if not, add it
			if ($singlePageObject->isError() || (!is_object($singlePageObject))) {
				$sp = SinglePage::add($singlePage['path'], $package);
				unset($singlePage['path']);
				if (!empty($singlePage)) {
					// And make sure we update the page with the remaining values
					$sp->update($singlePage);
				}
			}
		}
	}

	protected function installUpgrade($package)
	{
		$this->installSinglePages($package);
	}

	public function getToolbarDesignerPresets()
	{
		return [
			'/dashboard/blocks/block_designer' => [
				'title'             => t('Block Designer'),
				'controller_handle' => 'item_default',
				'pkg_handle'        => 'toolbar_designer',
				'cPath'             => '/dashboard/blocks/block_designer',
				'icon'              => 'fa-square',
			],
		];
	}
}
