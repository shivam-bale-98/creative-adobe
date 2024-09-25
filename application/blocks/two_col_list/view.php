<?php defined("C5_EXECUTE") or die("Access Denied.");
$ih = new \Application\Concrete\Helpers\ImageHelper();

$thumbnail = "";


?>




















<section <?php if (isset($bgColor) && trim($bgColor) != "") { ?>style="background-color:  <?php echo h($bgColor); ?>;" <?php } ?> class="<?php if (isset($hideBlock) && trim($hideBlock) == 1) { ?>hide-block<?php } ?> <?php if (isset($removePaddingTop) && trim($removePaddingTop) == 1) { ?>pt-0<?php } ?> <?php if (isset($removePaddingBottom) && trim($removePaddingBottom) == 1) { ?>pb-0<?php } ?> two-col-list relative xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] text-rustic-red">

	<?php if (!empty($list_items)) { ?>
		<?php
		$counter = 1;
		foreach ($list_items as $list_item_key => $list_item) { ?>
			<?php if ($counter % 2 == 0) { ?>
				<div class="wrp flex flex-wrap justify-between items-center">


					<?php if ($list_item["image"]) {
						$thumbnail = $ih->getThumbnail($list_item["image"], 800, 1200);
					?>
						<div class="image left">
							<div class="img-wrap relative blurred-img rounded-[2rem] overflow-hidden" style="background-image: url('<?php echo $thumbnail ?>');">
								<img class="absolute inset-0 | lazy" src="<?php echo $thumbnail ?>" alt="<?php echo h($list_item["title"]); ?>">
							</div>
						</div>
					<?php } ?>


					<div class="content right xl:mr-[9rem]">
						<?php if (isset($list_item["title"]) && trim($list_item["title"]) != "") { ?>

							<h3 class="xl:mb-[4rem] mb-[2rem]"><?php echo h($list_item["title"]); ?></h3>
						<?php } ?>


						<?php if (isset($list_item["desc_1"]) && trim($list_item["desc_1"]) != "") { ?>

							<p><?php echo h($list_item["desc_1"]); ?></p>
						<?php } ?>



						<?php if (trim($list_item["link_URL"]) != "") { ?>
							<?php
							$list_itemlink_Attributes = [];
							$list_itemlink_Attributes['href'] = $list_item["link_URL"];
							if (in_array($list_item["link"], ['file', 'url', 'relative_url'])) {
								$list_itemlink_Attributes['target'] = '_blank';
							}
							$list_item["link_AttributesHtml"] = join(' ', array_map(function ($key) use ($list_itemlink_Attributes) {
								return $key . '="' . $list_itemlink_Attributes[$key] . '"';
							}, array_keys($list_itemlink_Attributes)));
							echo sprintf('<a class="channeline-btn channeline-btn--arrow channeline-btn--border channeline-btn--red relative xl:mt-[4rem] mt-[2rem] " %s>
		                               <span class="text z-2 relative">%s</span>
		                               <div class="shape absolute">
		                               <i class="icon-right_button_arrow absolute z-2"></i>
			                           <span class="bg absolute inset-0 size-full"></span>
		                               </div>
									   </a>', $list_item["link_AttributesHtml"], $list_item["link_Title"]); ?>
						<?php } ?>
					</div>




				</div>
			<?php } else { ?>
				<div class="wrp flex flex-wrap justify-between items-center">

					<div class="content left">
						<?php if (isset($list_item["title"]) && trim($list_item["title"]) != "") { ?>

							<h3 class="xl:mb-[4rem] mb-[2rem]"><?php echo h($list_item["title"]); ?></h3>
						<?php } ?>


						<?php if (isset($list_item["desc_1"]) && trim($list_item["desc_1"]) != "") { ?>

							<p><?php echo h($list_item["desc_1"]); ?></p>
						<?php } ?>



						<?php if (trim($list_item["link_URL"]) != "") { ?>
							<?php
							$list_itemlink_Attributes = [];
							$list_itemlink_Attributes['href'] = $list_item["link_URL"];
							if (in_array($list_item["link"], ['file', 'url', 'relative_url'])) {
								$list_itemlink_Attributes['target'] = '_blank';
							}
							$list_item["link_AttributesHtml"] = join(' ', array_map(function ($key) use ($list_itemlink_Attributes) {
								return $key . '="' . $list_itemlink_Attributes[$key] . '"';
							}, array_keys($list_itemlink_Attributes)));
							echo sprintf('<a  class="channeline-btn channeline-btn--arrow channeline-btn--border channeline-btn--red relative xl:mt-[4rem] mt-[2rem] " %s>
		                               <span class="text z-2 relative">%s</span>
		                               <div class="shape absolute">
		                               <i class="icon-right_button_arrow absolute z-2"></i>
			                           <span class="bg absolute inset-0 size-full"></span>
		                               </div>
									   </a>', $list_item["link_AttributesHtml"], $list_item["link_Title"]); ?>
						<?php } ?>
					</div>



					<?php if ($list_item["image"]) {
						$thumbnail = $ih->getThumbnail($list_item["image"], 800, 1200);
					?>
						<div class="image right">
							<div class="img-wrap relative blurred-img rounded-[2rem] overflow-hidden" style="background-image: url('<?php echo $thumbnail ?>');">
								<img class="absolute inset-0 object-cover | lazy" loading="lazy" src="<?php echo $thumbnail ?>" alt="<?php echo h($list_item["title"]); ?>">
							</div>
						</div>
					<?php } ?>





				</div>
				<?php ?>

				<?php $counter++ ?>
			<?php } ?>
		<?php } ?>
	<?php } ?>

</section>