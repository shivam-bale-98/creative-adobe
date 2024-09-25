<?php defined("C5_EXECUTE") or die("Access Denied.");
$ih = new \Application\Concrete\Helpers\ImageHelper();

$thumbnail1 = "";
$thumbnail2 = "";
$thumbnail3 = "";

if ($firstImage) {
	$thumbnail1 = $ih->getThumbnail($firstImage, 800, 1200);
	$low1 = $ih->getThumbnail($firstImage, 1, 1);
}

if ($secondImage) {
	$thumbnail2 = $ih->getThumbnail($secondImage, 800, 1200);
	$low2 = $ih->getThumbnail($secondImage, 1, 1);
}

if ($thirdImage) {
	$thumbnail3 = $ih->getThumbnail($thirdImage, 800, 1200);
	$low3 = $ih->getThumbnail($thirdImage, 1, 1);
}
?>








<? //php echo $removePaddingTop
?>
<section <?php if (isset($bgColor) && trim($bgColor) != "") { ?> style="background-color: <?php echo h($bgColor); ?>;" <?php } ?> class="<?php if (isset($hideBlock) && trim($hideBlock) == 1) { ?>hide-block<?php } ?> <?php if (isset($removePaddingTop) && trim($removePaddingTop) == 1) { ?>pt-0<?php } ?> <?php if (isset($removePaddingBottom) && trim($removePaddingBottom) == 1) { ?>pb-0<?php } ?> two-col-section relative xxl:px-[10rem] xl:px-[6rem] md:px-[4rem] px-[2rem] xl:py-[10rem] md:py-[8rem] py-[5rem] text-black" data-bg="#fff">

	<?php if (isset($subTitle) && trim($subTitle) != "") { ?>
		<h6 class="mb-[2rem] fade-text" data-duration="1">
			<?php echo h($subTitle); ?>
		</h6>
	<?php } ?>

	<div class="text-col flex flex-wrap justify-between ">
		<div class="title">


			<?php if (isset($title) && trim($title) != "") { ?>
				<div class="fade-text" data-duration="1" data-delay="0.2">
					<?php echo $title; ?>
				</div>
			<?php } ?>

		</div>
		<div class="desc md:mt-[0rem] mt-[2rem]">
			<?php if (isset($desc_1) && trim($desc_1) != "") { ?>
				<div class="fade-text prose-li:custom-li" data-duration="1" data-delay="0.1">
					<?php echo $desc_1; ?>
				</div>
			<?php } ?>
			<?php
			if (trim($link_URL) != "") { ?>
				<div class="fade-text" data-duration="0.6" data-delay="0.1">
					<?php
					$link_Attributes = [];
					$link_Attributes['href'] = $link_URL;
					if (in_array($link, ['url', 'relative_url'])) {
						$link_Attributes['target'] = '_blank';
					}
					$link_AttributesHtml = join(' ', array_map(function ($key) use ($link_Attributes) {
						return $key . '="' . $link_Attributes[$key] . '"';
					}, array_keys($link_Attributes)));
					echo sprintf('<a tabindex="0" role="link" aria-label="' . $link_Title . '" class="channeline-btn  channeline-btn--red channeline-btn--border  relative md:mt-[3rem] mt-[2rem] " %s>
				<span class="text z-2 relative">%s</span></a>', $link_AttributesHtml, $link_Title); ?>
				</div>
			<?php } ?>
		</div>
	</div>

	<?php if ($firstImage || $secondImage || $thirdImage) { ?>
		<div class="image-col flex md:mt-[10rem] mt-[3rem]">
			<?php if ($firstImage) { ?>
				<div class="img-wrap relative rounded-[2rem] overflow-hidden blurred-img" style="background-image: url('<?php echo $low1 ?>');">
					<img class="absolute inset-0 object-cover | lazy" loading="lazy" src="<?php echo $thumbnail1 ?>" alt="Channeline at work">
				</div>
			<?php } ?>

			<?php if ($secondImage) { ?>
				<div class="img-wrap relative rounded-[2rem] overflow-hidden blurred-img" style="background-image: url('<?php echo $low2 ?>');">
					<img class="absolute inset-0 object-cover | lazy" loading="lazy" src="<?php echo $thumbnail2 ?>" alt="Channeline's patented technology">
				</div>
			<?php } ?>

			<?php if ($thirdImage) { ?>
				<div class="img-wrap relative rounded-[2rem] overflow-hidden blurred-img" style="background-image: url('<?php echo $low3 ?>');">
					<img class="absolute inset-0 object-cover | lazy" loading="lazy" src="<?php echo $thumbnail3 ?>" alt="Beyond the ordinary">
				</div>
			<?php } ?>
		</div>
	<?php } ?>
</section>