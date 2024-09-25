<?php defined("C5_EXECUTE") or die("Access Denied."); ?>

<div class="form-group">
    <?php echo $form->label($view->field('hideBlock'), t("Hide  Block")); ?>
    <?php echo isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('hideBlock'), (isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "no", 1 => "yes"], isset($hideBlock) ? $hideBlock : null, []); ?>
</div>

<div class="form-group">
    <?php
    if (isset($Dimage) && $Dimage > 0) {
        $Dimage_o = File::getByID($Dimage);
        if (!is_object($Dimage_o)) {
            unset($Dimage_o);
        }
    } ?>
    <?php echo $form->label($view->field('Dimage'), t("Desktop Image (2000X2000)")); ?>
    <?php echo isset($btFieldsRequired) && in_array('Dimage', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo Core::make("helper/concrete/asset_library")->image('ccm-b-home_banner-Dimage-' . $identifier_getString, $view->field('Dimage'), t("Choose Image"), isset($Dimage_o) ? $Dimage_o : null); ?>
</div>

<div class="form-group">
    <?php
    if (isset($Mimage) && $Mimage > 0) {
        $Mimage_o = File::getByID($Mimage);
        if (!is_object($Mimage_o)) {
            unset($Mimage_o);
        }
    } ?>
    <?php echo $form->label($view->field('Mimage'), t("Mobile Image")); ?>
    <?php echo isset($btFieldsRequired) && in_array('Mimage', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo Core::make("helper/concrete/asset_library")->image('ccm-b-home_banner-Mimage-' . $identifier_getString, $view->field('Mimage'), t("Choose Image"), isset($Mimage_o) ? $Mimage_o : null); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('videoURL'), t("Video Link(Distribution Link)")); ?>
    <?php echo isset($btFieldsRequired) && in_array('videoURL', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('videoURL'), isset($videoURL) ? $videoURL : null, array (
  'maxlength' => 255,
)); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('subTitle'), t("Sub Title")); ?>
    <?php echo isset($btFieldsRequired) && in_array('subTitle', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('subTitle'), isset($subTitle) ? $subTitle : null, array (
  'maxlength' => 255,
)); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('title'), t("Title(use &lt;heading1&gt;)")); ?>
    <?php echo isset($btFieldsRequired) && in_array('title', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo Core::make('editor')->outputBlockEditModeEditor($view->field('title'), isset($title) ? $title : null); ?>
</div>