<?php defined("C5_EXECUTE") or die("Access Denied."); ?>

<div class="form-group">
    <?php echo $form->label($view->field('text'), t("Text")); ?>
    <?php echo isset($btFieldsRequired) && in_array('text', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('text'), isset($text) ? $text : null, array (
  'maxlength' => 255,
)); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('name'), t("Name")); ?>
    <?php echo isset($btFieldsRequired) && in_array('name', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('name'), isset($name) ? $name : null, array (
  'maxlength' => 255,
)); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('designation'), t("Designation")); ?>
    <?php echo isset($btFieldsRequired) && in_array('designation', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('designation'), isset($designation) ? $designation : null, array (
  'maxlength' => 255,
)); ?>
</div>