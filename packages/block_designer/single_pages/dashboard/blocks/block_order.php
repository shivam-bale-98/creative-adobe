<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<h3><?php echo t("Block order info"); ?></h3>

<div class="alert alert-info">
	<p>
		<?php echo t("Order your blocks for each block type set. The order in which you arrange blocks is in which order they will show up while dragging blocks on the front end."); ?>
	</p>

	<p>
		<?php echo t("To change the order of Block Type Sets, start by dragging the name of the Block Type Set to the position you would like."); ?>
	</p>
</div>

<?php
$i = 0;
if (!empty($blockTypeSets)) {
	?>
	<ul class="block-sets-sortable">
		<?php
		foreach ($blockTypeSets as $btsID => $blockTypeSet) {
			?>
			<li data-btsid="<?php echo $btsID; ?>">
				<span class="h3">
					<i class="fa fa-arrows"></i>
					<?php echo trim($blockTypeSet['name']) != '' ? $blockTypeSet['name'] : t("(No Title)"); ?>
				</span>

				<?php
				$li = [];
				$liTemplate = '<li id="btID_%s" data-btid="%s"><a href="#"><img src="%s" /> %s</a></li>';
				foreach ($blockTypeSet['blocks'] as $block) {
					$li[] = vsprintf($liTemplate, [$block['btID'], $block['btID'], $block['icon'], $block['name']]);
				} ?>

				<ul class="item-select-list block-types-sortable" data-btsid="<?php echo $btsID; ?>">
					<?php echo implode('', $li); ?>
				</ul>
			</li>
			<?php
			$i++;
		} ?>
	</ul>
	<?php
} ?>
