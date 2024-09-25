<?php defined("C5_EXECUTE") or die("Access Denied."); ?>

<div class="form-group">
    <?php echo $form->label($view->field('title'), t("Title")); ?>
    <?php echo isset($btFieldsRequired) && in_array('title', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('title'), isset($title) ? $title : null, array (
  'maxlength' => 255,
)); ?>
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
    <?php echo $form->label($view->field('paddingBottom'), t("Padding Bottom")); ?>
    <?php echo isset($btFieldsRequired) && in_array('paddingBottom', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('paddingBottom'), isset($paddingBottom) ? $paddingBottom : null, array (
        'maxlength' => 255,
    )); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('filesets'), t("File Sets")); ?>
    <?php echo isset($btFieldsRequired) && in_array('filesets', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->selectMultipleCheckbox($view->field('filesets'), $filesets_options, isset($filesets) ? $filesets : null, array('class' =>  'file_sets' ));
    print $form->hidden('filesets', isset($filesets) ? $filesets : null, array('class' => 'span4  file_sets_value')); ?>
</div>

<script>
    $(".file_sets").on('click', function () {
        var txtTextObj = $('.file_sets_value');
        var selected   = [];

        $('.file_sets :checked').each(function () {
            selected.push($(this).val());
        });

        txtTextObj.val(selected.join(','));
    });
</script>
