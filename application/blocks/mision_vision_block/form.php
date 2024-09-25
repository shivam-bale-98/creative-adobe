<?php defined("C5_EXECUTE") or die("Access Denied."); ?>

<div class="form-group">
    <?php
    if (isset($leftImage) && $leftImage > 0) {
        $leftImage_o = File::getByID($leftImage);
        if (!is_object($leftImage_o)) {
            unset($leftImage_o);
        }
    } ?>
    <?php echo $form->label($view->field('leftImage'), t("Left Image")); ?>
    <?php echo isset($btFieldsRequired) && in_array('leftImage', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo Core::make("helper/concrete/asset_library")->image('ccm-b-mision_vision_block-leftImage-' . $identifier_getString, $view->field('leftImage'), t("Choose Image"), isset($leftImage_o) ? $leftImage_o : null); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('leftTitle'), t("Left Title")); ?>
    <?php echo isset($btFieldsRequired) && in_array('leftTitle', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('leftTitle'), isset($leftTitle) ? $leftTitle : null, array (
  'maxlength' => 255,
)); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('leftDesc'), t("Left Description")); ?>
    <?php echo isset($btFieldsRequired) && in_array('leftDesc', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->textarea($view->field('leftDesc'), isset($leftDesc) ? $leftDesc : null, array (
  'rows' => 5,
)); ?>
</div>

<div class="form-group">
    <?php
    if (isset($rightImage) && $rightImage > 0) {
        $rightImage_o = File::getByID($rightImage);
        if (!is_object($rightImage_o)) {
            unset($rightImage_o);
        }
    } ?>
    <?php echo $form->label($view->field('rightImage'), t("right image")); ?>
    <?php echo isset($btFieldsRequired) && in_array('rightImage', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo Core::make("helper/concrete/asset_library")->image('ccm-b-mision_vision_block-rightImage-' . $identifier_getString, $view->field('rightImage'), t("Choose Image"), isset($rightImage_o) ? $rightImage_o : null); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('rightTitle'), t("Right Title")); ?>
    <?php echo isset($btFieldsRequired) && in_array('rightTitle', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('rightTitle'), isset($rightTitle) ? $rightTitle : null, array (
  'maxlength' => 255,
)); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('rightDesc'), t("Right Description")); ?>
    <?php echo isset($btFieldsRequired) && in_array('rightDesc', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->textarea($view->field('rightDesc'), isset($rightDesc) ? $rightDesc : null, array (
  'rows' => 5,
)); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('hideBlock'), t("Hide Block")); ?>
    <?php echo isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('hideBlock'), (isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "no", 1 => "yes"], isset($hideBlock) ? $hideBlock : null, []); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('bgColor'), t("background color")); ?>
    <?php echo isset($btFieldsRequired) && in_array('bgColor', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('bgColor'), isset($bgColor) ? $bgColor : null, array (
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
    <?php echo $form->select($view->field('removePaddingBottom'), (isset($btFieldsRequired) && in_array('removePaddingBottom', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "no", 1 => "yes"], isset($removePaddingBottom) ? $removePaddingBottom : null, []); ?>
</div>