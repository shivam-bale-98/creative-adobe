<?php defined("C5_EXECUTE") or die("Access Denied."); 
$link_File = $link_File ?? 0;
$link_Image = $link_Image ?? 0;
?>

<div class="form-group">
    <?php
    if (isset($desktopImage) && $desktopImage > 0) {
        $desktopImage_o = File::getByID($desktopImage);
        if (!is_object($desktopImage_o)) {
            unset($desktopImage_o);
        }
    } ?>
    <?php echo $form->label($view->field('desktopImage'), t("desktop image (2000X1616)")); ?>
    <?php echo isset($btFieldsRequired) && in_array('desktopImage', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo Core::make("helper/concrete/asset_library")->image('ccm-b-technology-desktopImage-' . $identifier_getString, $view->field('desktopImage'), t("Choose Image"), isset($desktopImage_o) ? $desktopImage_o : null); ?>
</div>

<div class="form-group">
    <?php
    if (isset($mobileImage) && $mobileImage > 0) {
        $mobileImage_o = File::getByID($mobileImage);
        if (!is_object($mobileImage_o)) {
            unset($mobileImage_o);
        }
    } ?>
    <?php echo $form->label($view->field('mobileImage'), t("Mobile Image(800X1400)")); ?>
    <?php echo isset($btFieldsRequired) && in_array('mobileImage', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo Core::make("helper/concrete/asset_library")->image('ccm-b-technology-mobileImage-' . $identifier_getString, $view->field('mobileImage'), t("Choose Image"), isset($mobileImage_o) ? $mobileImage_o : null); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('title'), t("Title")); ?>
    <?php echo isset($btFieldsRequired) && in_array('title', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('title'), isset($title) ? $title : null, array (
  'maxlength' => 255,
)); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('desc_1'), t("description")); ?>
    <?php echo isset($btFieldsRequired) && in_array('desc_1', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->textarea($view->field('desc_1'), isset($desc_1) ? $desc_1 : null, array (
  'rows' => 5,
)); ?>
</div>

<?php $link_ContainerID = 'btTechnology-link-container-' . $identifier_getString; ?>
<div class="ft-smart-link" id="<?php echo $link_ContainerID; ?>">
	<div class="form-group">
		<?php echo $form->label($view->field('link'), t("Link")); ?>
	    <?php echo isset($btFieldsRequired) && in_array('link', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
	    <?php echo $form->select($view->field('link'), $link_Options, isset($link) ? $link : null, array (
  'class' => 'form-control ft-smart-link-type',
)); ?>
	</div>
	
	<div class="form-group">
		<div class="ft-smart-link-options hidden d-none" style="padding-left: 10px;">
			<div class="form-group">
				<?php echo $form->label($view->field('link_Title'), t("Title")); ?>
			    <?php echo $form->text($view->field('link_Title'), isset($link_Title) ? $link_Title : null, []); ?>		
			</div>
			
			<div class="form-group hidden d-none" data-link-type="page">
			<?php echo $form->label($view->field('link_Page'), t("Page")); ?>
            <small class="required"><?php echo t('Required'); ?></small>
            <?php echo Core::make("helper/form/page_selector")->selectPage($view->field('link_Page'), isset($link_Page) ? $link_Page : null); ?>
		</div>

		<div class="form-group hidden d-none" data-link-type="url">
			<?php echo $form->label($view->field('link_URL'), t("URL")); ?>
            <small class="required"><?php echo t('Required'); ?></small>
            <?php echo $form->text($view->field('link_URL'), isset($link_URL) ? $link_URL : null, []); ?>
		</div>

		<div class="form-group hidden d-none" data-link-type="relative_url">
			<?php echo $form->label($view->field('link_Relative_URL'), t("URL")); ?>
            <small class="required"><?php echo t('Required'); ?></small>
            <?php echo $form->text($view->field('link_Relative_URL'), isset($link_Relative_URL) ? $link_Relative_URL : null, []); ?>
		</div>

		<div class="form-group hidden d-none" data-link-type="file">
			<?php
			if ($link_File > 0) {
				$link_File_o = File::getByID($link_File);
				if (!is_object($link_File_o)) {
					unset($link_File_o);
				}
			} ?>
		    <?php echo $form->label($view->field('link_File'), t("File")); ?>
            <small class="required"><?php echo t('Required'); ?></small>
            <?php echo Core::make("helper/concrete/asset_library")->file('ccm-b-technology-link_File-' . $identifier_getString, $view->field('link_File'), t("Choose File"), isset($link_File_o) ? $link_File_o : null); ?>	
		</div>

		<div class="form-group hidden d-none" data-link-type="image">
			<?php
			if ($link_Image > 0) {
				$link_Image_o = File::getByID($link_Image);
				if (!is_object($link_Image_o)) {
					unset($link_Image_o);
				}
			} ?>
			<?php echo $form->label($view->field('link_Image'), t("Image")); ?>
            <small class="required"><?php echo t('Required'); ?></small>
            <?php echo Core::make("helper/concrete/asset_library")->image('ccm-b-technology-link_Image-' . $identifier_getString, $view->field('link_Image'), t("Choose Image"), isset($link_Image_o) ? $link_Image_o : null); ?>
		</div>
		</div>
	</div>
</div>


<script type="text/javascript">
	Concrete.event.publish('btTechnology.link.open', {id: '<?php echo $link_ContainerID; ?>'});
	$('#<?php echo $link_ContainerID; ?> .ft-smart-link-type').trigger('change');
</script>

<div class="form-group">
    <?php
    if (isset($svg) && $svg > 0) {
        $svg_o = File::getByID($svg);
        if (!is_object($svg_o)) {
            unset($svg_o);
        }
    } ?>
    <?php echo $form->label($view->field('svg'), t("svg (330X290)")); ?>
    <?php echo isset($btFieldsRequired) && in_array('svg', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo Core::make("helper/concrete/asset_library")->image('ccm-b-technology-svg-' . $identifier_getString, $view->field('svg'), t("Choose Image"), isset($svg_o) ? $svg_o : null); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('vimeoLink'), t(" video link (vimeo sharer link)")); ?>
    <?php echo isset($btFieldsRequired) && in_array('vimeoLink', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('vimeoLink'), isset($vimeoLink) ? $vimeoLink : null, array (
  'maxlength' => 255,
)); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('removePaddingTop'), t("remove padding top")); ?>
    <?php echo isset($btFieldsRequired) && in_array('removePaddingTop', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('removePaddingTop'), (isset($btFieldsRequired) && in_array('removePaddingTop', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "no", 1 => "yes"], isset($removePaddingTop) ? $removePaddingTop : null, []); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('removePaddingBottom'), t("remove padding bottom")); ?>
    <?php echo isset($btFieldsRequired) && in_array('removePaddingBottom', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('removePaddingBottom'), (isset($btFieldsRequired) && in_array('removePaddingBottom', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "no ", 1 => "yes"], isset($removePaddingBottom) ? $removePaddingBottom : null, []); ?>
</div>