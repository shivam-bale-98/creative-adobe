<?php namespace Concrete\Package\BlockDesigner\Controller\SinglePage\Dashboard\Blocks;

defined('C5_EXECUTE') or die("Access Denied.");

use AssetList;
use BlockTypeList;
use Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Core;
use Database;

class BlockOrder extends DashboardPageController
{
	public $helpers = ['form'];
	public $packageHandle = 'block_designer';

	private function getBlockTypeSets()
	{
		$db = Database::connection();
		$blockTypeSets = $db->fetchAll('SELECT * FROM BlockTypeSets ORDER BY btsDisplayOrder');
		$options = [];
		foreach ($blockTypeSets as $blockTypeSet) {
			$blockTypeSet['name'] = t($blockTypeSet['btsName']);
			$blockTypeSet['blocks'] = [];
			$options[$blockTypeSet['btsID']] = $blockTypeSet;
		}
		return $options;
	}

	private function getBlockTypes($btsID = false)
	{
		$db = Database::connection();
		$whereValues = ['0'];
		if ($this->getBlockTypeSortingOld()) {
			if (!$btsID) {
				$queryString = 'SELECT *, bt.btID FROM BlockTypes bt LEFT JOIN BlockTypeSetBlockTypes btsbt ON btsbt.btID = bt.btID WHERE btIsInternal = ? ORDER BY btDisplayOrder';
			} else {
				$whereValues[] = $btsID;
				$queryString = 'SELECT *, bt.btID FROM BlockTypes bt LEFT JOIN BlockTypeSetBlockTypes btsbt ON btsbt.btID = bt.btID WHERE btIsInternal = ? AND btsbt.btsID = ? ORDER BY btDisplayOrder';
			}
		} else {
			if (!$btsID) {
				$queryString = 'SELECT *, bt.btID FROM BlockTypes bt LEFT JOIN BlockTypeSetBlockTypes btsbt ON btsbt.btID = bt.btID WHERE btIsInternal = ? ORDER BY displayOrder';
			} else {
				if ($btsID == 'other') {
					$queryString = 'SELECT *, bt.btID FROM BlockTypes bt LEFT JOIN BlockTypeSetBlockTypes btsbt ON btsbt.btID = bt.btID WHERE btIsInternal = ? AND btsbt.btsID IS NULL ORDER BY btDisplayOrder';
				} else {
					$whereValues[] = $btsID;
					$queryString = 'SELECT *, bt.btID FROM BlockTypes bt LEFT JOIN BlockTypeSetBlockTypes btsbt ON btsbt.btID = bt.btID WHERE btIsInternal = ? AND btsbt.btsID = ? ORDER BY displayOrder';
				}
			}
		}
		$blockTypes = $db->fetchAll($queryString, $whereValues);
		$options = [];
		foreach ($blockTypes as $blockType) {
			$blockType['name'] = t($blockType['btName']);
			$options[$blockType['btID']] = $blockType;
		}
		return $options;
	}

	public function view()
	{
		$al = AssetList::getInstance();
		$al->register('css', 'block-designer-order-view', 'css/block_order.view.css', [], $this->packageHandle);
		$al->register('javascript', 'block-designer-order-view', 'js/block_order.view.js', [], $this->packageHandle);
		$this->requireAsset('css', 'block-designer-order-view');
		$this->requireAsset('javascript', 'block-designer-order-view');
		$blockTypeSets = $this->getBlockTypeSets();
		$blockTypeSets['other'] = [
			'btsID'  => 'other',
			'name'   => t("Other"),
			'blocks' => [],
		];
		if ($blockTypes = ($this->getBlockTypes())) {
			$btl = new BlockTypeList();
			$btInstalledArray = $btl->get();
			$ci = Core::make('helper/concrete/urls');
			foreach ($btInstalledArray as $k => $_bt) {
				$btIcon = $ci->getBlockTypeIconURL($_bt);
				$btID = $_bt->getBlockTypeID();
				if (isset($blockTypes[$btID])) {
					$blockTypes[$btID]['icon'] = $btIcon;
				}
			}
			foreach ($blockTypeSets as &$blockTypeSet) {
				$blockTypeSetBlockTypes = $this->getBlockTypes($blockTypeSet['btsID']);
				foreach ($blockTypeSetBlockTypes as $blockType) {
					$blockTypeSet['blocks'][] = $blockTypes[$blockType['btID']];
				}
			}
		}
		$this->set('blockTypeSets', $blockTypeSets);
	}

	public function update()
	{
		if (isset($_POST['btsID'], $_POST['order']) && is_array($_POST['order']) && !empty($_POST['order'])) {
			$db = Database::connection();
			$blockTypes = $this->getBlockTypes($_POST['btsID']);
			$i = 0;
			foreach ($_POST['order'] as $v) {
				if (isset($blockTypes[$v])) {
					// both the old sorting AND the "Other" BlockTypeSet (no entry in BlockTypeSets table) use this specific database table instead
					if ($_POST['btsID'] == 'other' || $this->getBlockTypeSortingOld()) {
						$db->executeQuery('UPDATE BlockTypes SET btDisplayOrder = ? WHERE btID = ?', [$i, $v]);
					} else {
						$db->executeQuery('UPDATE BlockTypeSetBlockTypes SET displayOrder = ? WHERE btID = ? AND btsID = ?', [$i, $v, $_POST['btsID']]);
					}
					$i++;
				}
			}
		}
		exit;
	}

	public function update_sets()
	{
		if (isset($_POST['order']) && is_array($_POST['order']) && !empty($_POST['order'])) {
			$db = Database::connection();
			$blockTypeSets = $this->getBlockTypeSets();
			$i = 0;
			foreach ($_POST['order'] as $v) {
				if (isset($blockTypeSets[$v])) {
					$db->executeQuery('UPDATE BlockTypeSets SET btsDisplayOrder = ? WHERE btsID = ?', [$i, $v]);
					$i++;
				}
			}
		}
		exit;
	}

	private function getBlockTypeSortingOld()
	{
		return version_compare(Config::get('concrete.version'), '5.7.4', '<') ? true : false;
	}
}
