<?php defined("C5_EXECUTE") or die("Access Denied."); ?>

<div class="form-group">
    <?php echo $form->label($view->field('title'), t("Title")); ?>
    <?php echo isset($btFieldsRequired) && in_array('title', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('title'), isset($title) ? $title : null, array (
  'maxlength' => 255,
)); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('hideBlock'), t("hide Block")); ?>
    <?php echo isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('hideBlock'), (isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "no", 1 => "yes"], isset($hideBlock) ? $hideBlock : null, []); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('bgColor'), t("Background Color")); ?>
    <?php echo isset($btFieldsRequired) && in_array('bgColor', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('bgColor'), isset($bgColor) ? $bgColor : null, array (
        'maxlength' => 255,
    )); ?>
</div>
<div class="form-group">
    <?php echo $form->label($view->field('paddingTop'), t("Padding Top")); ?>
    <?php echo isset($btFieldsRequired) && in_array('paddingTop', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('paddingTop'), isset($paddingTop) ? $paddingTop : null, array (
        'maxlength' => 255,
    )); ?>
</div>
<div class="form-group">
    <?php echo $form->label($view->field(''), t("Padding Bottom")); ?>
    <?php echo isset($btFieldsRequired) && in_array('paddingBottom', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('paddingBottom'), isset($paddingBottom) ? $paddingBottom : null, array (
        'maxlength' => 255,
    )); ?>
</div>