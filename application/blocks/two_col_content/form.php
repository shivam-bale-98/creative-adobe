<?php defined("C5_EXECUTE") or die("Access Denied."); 
$link_File = $link_File ?? 0;
$link_Image = $link_Image ?? 0;
?>

<div class="form-group">
    <?php echo $form->label($view->field('subTitle'), t("Sub Title")); ?>
    <?php echo isset($btFieldsRequired) && in_array('subTitle', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('subTitle'), isset($subTitle) ? $subTitle : null, array (
  'maxlength' => 255,
)); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('title'), t("Title (&lt;h2&gt;)")); ?>
    <?php echo isset($btFieldsRequired) && in_array('title', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo Core::make('editor')->outputBlockEditModeEditor($view->field('title'), isset($title) ? $title : null); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('desc_1'), t("description (&lt;p&gt;)")); ?>
    <?php echo isset($btFieldsRequired) && in_array('desc_1', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo Core::make('editor')->outputBlockEditModeEditor($view->field('desc_1'), isset($desc_1) ? $desc_1 : null); ?>
</div>

<?php $link_ContainerID = 'btTwoColContent-link-container-' . $identifier_getString; ?>
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
            <?php echo Core::make("helper/concrete/asset_library")->file('ccm-b-two_col_content-link_File-' . $identifier_getString, $view->field('link_File'), t("Choose File"), isset($link_File_o) ? $link_File_o : null); ?>	
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
            <?php echo Core::make("helper/concrete/asset_library")->image('ccm-b-two_col_content-link_Image-' . $identifier_getString, $view->field('link_Image'), t("Choose Image"), isset($link_Image_o) ? $link_Image_o : null); ?>
		</div>
		</div>
	</div>
</div>


<script type="text/javascript">
	Concrete.event.publish('btTwoColContent.link.open', {id: '<?php echo $link_ContainerID; ?>'});
	$('#<?php echo $link_ContainerID; ?> .ft-smart-link-type').trigger('change');
</script>

<div class="form-group">
    <?php
    if (isset($firstImage) && $firstImage > 0) {
        $firstImage_o = File::getByID($firstImage);
        if (!is_object($firstImage_o)) {
            unset($firstImage_o);
        }
    } ?>
    <?php echo $form->label($view->field('firstImage'), t("First Image")); ?>
    <?php echo isset($btFieldsRequired) && in_array('firstImage', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo Core::make("helper/concrete/asset_library")->image('ccm-b-two_col_content-firstImage-' . $identifier_getString, $view->field('firstImage'), t("Choose Image"), isset($firstImage_o) ? $firstImage_o : null); ?>
</div>

<div class="form-group">
    <?php
    if (isset($secondImage) && $secondImage > 0) {
        $secondImage_o = File::getByID($secondImage);
        if (!is_object($secondImage_o)) {
            unset($secondImage_o);
        }
    } ?>
    <?php echo $form->label($view->field('secondImage'), t("Second Image")); ?>
    <?php echo isset($btFieldsRequired) && in_array('secondImage', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo Core::make("helper/concrete/asset_library")->image('ccm-b-two_col_content-secondImage-' . $identifier_getString, $view->field('secondImage'), t("Choose Image"), isset($secondImage_o) ? $secondImage_o : null); ?>
</div>

<div class="form-group">
    <?php
    if (isset($thirdImage) && $thirdImage > 0) {
        $thirdImage_o = File::getByID($thirdImage);
        if (!is_object($thirdImage_o)) {
            unset($thirdImage_o);
        }
    } ?>
    <?php echo $form->label($view->field('thirdImage'), t("Third Image")); ?>
    <?php echo isset($btFieldsRequired) && in_array('thirdImage', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo Core::make("helper/concrete/asset_library")->image('ccm-b-two_col_content-thirdImage-' . $identifier_getString, $view->field('thirdImage'), t("Choose Image"), isset($thirdImage_o) ? $thirdImage_o : null); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('hideBlock'), t("Hide Block")); ?>
    <?php echo isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('hideBlock'), (isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "No", 1 => "Yes"], isset($hideBlock) ? $hideBlock : null, []); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('bgColor'), t("backgroundColor") . ' <i class="fa fa-question-circle launch-tooltip" data-original-title="' . t("(romance: #F2F1EF, wheat: #ECE6E4, rustic-red: #1A0A0C)") . '"></i>'); ?>
    <?php echo isset($btFieldsRequired) && in_array('bgColor', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('bgColor'), isset($bgColor) ? $bgColor : null, array (
  'maxlength' => 255,
)); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('removePaddingTop'), t("Remove Padding Top")); ?>
    <?php echo isset($btFieldsRequired) && in_array('removePaddingTop', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('removePaddingTop'), (isset($btFieldsRequired) && in_array('removePaddingTop', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "no", 1 => "yes"], isset($removePaddingTop) ? $removePaddingTop : null, []); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('removePaddingBottom'), t("remove padding bottom")); ?>
    <?php echo isset($btFieldsRequired) && in_array('removePaddingBottom', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('removePaddingBottom'), (isset($btFieldsRequired) && in_array('removePaddingBottom', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "no", 1 => "yes"], isset($removePaddingBottom) ? $removePaddingBottom : null, []); ?>
</div>