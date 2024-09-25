<?php defined("C5_EXECUTE") or die("Access Denied."); ?>

<?php $tabs = [
    ['form-basics-' . $identifier_getString, t('Basics'), true],
    ['form-testimonials_items-' . $identifier_getString, t('Testimonials')]
];
echo Core::make('helper/concrete/ui')->tabs($tabs); ?>

<div class="tab-content">

<div role="tabpanel" class="tab-pane show active" id="form-basics-<?php echo $identifier_getString; ?>">
    <div class="form-group">
    <?php echo $form->label($view->field('bgColor'), t("background color (rustic red : #1A0A0C, wheat: #ECE6E4; romance : #F2F1EF, berry-red: #80151A)")); ?>
    <?php echo isset($btFieldsRequired) && in_array('bgColor', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('bgColor'), isset($bgColor) ? $bgColor : null, array (
  'maxlength' => 255,
)); ?>
</div><div class="form-group">
    <?php echo $form->label($view->field('hideBlock'), t("Hide Block")); ?>
    <?php echo isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('hideBlock'), (isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => t("No"), 1 => t("Yes")], isset($hideBlock) ? $hideBlock : null, []); ?>
</div><div class="form-group">
    <?php echo $form->label($view->field('subTitle'), t("sub title")); ?>
    <?php echo isset($btFieldsRequired) && in_array('subTitle', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('subTitle'), isset($subTitle) ? $subTitle : null, array (
  'maxlength' => 255,
)); ?>
</div><div class="form-group">
    <?php echo $form->label($view->field('title'), t("title")); ?>
    <?php echo isset($btFieldsRequired) && in_array('title', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo Core::make('editor')->outputBlockEditModeEditor($view->field('title'), isset($title) ? $title : null); ?>
</div><div class="form-group">
    <?php echo $form->label($view->field('removePaddingTop'), t("remove padding top ")); ?>
    <?php echo isset($btFieldsRequired) && in_array('removePaddingTop', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('removePaddingTop'), (isset($btFieldsRequired) && in_array('removePaddingTop', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "no ", 1 => "yes"], isset($removePaddingTop) ? $removePaddingTop : null, []); ?>
</div><div class="form-group">
    <?php echo $form->label($view->field('removePaddingBottom'), t("remove padding bottom")); ?>
    <?php echo isset($btFieldsRequired) && in_array('removePaddingBottom', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('removePaddingBottom'), (isset($btFieldsRequired) && in_array('removePaddingBottom', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => t("No"), 1 => t("Yes")], isset($removePaddingBottom) ? $removePaddingBottom : null, []); ?>
</div>
</div>

<div role="tabpanel" class="tab-pane" id="form-testimonials_items-<?php echo $identifier_getString; ?>">
    <script type="text/javascript">
    var CCM_EDITOR_SECURITY_TOKEN = "<?php echo Core::make('helper/validation/token')->generate('editor')?>";
</script>
            <?php
	$core_editor = Core::make('editor');
	if (method_exists($core_editor, 'outputStandardEditorInitJSFunction')) {
		/* @var $core_editor \Concrete\Core\Editor\CkeditorEditor */
		?>
		<script type="text/javascript">var blockDesignerEditor = <?php echo $core_editor->outputStandardEditorInitJSFunction(); ?>;</script>
	<?php
	} else {
	/* @var $core_editor \Concrete\Core\Editor\RedactorEditor */
	if(method_exists($core_editor, 'requireEditorAssets')){
		$core_editor->requireEditorAssets();
	} ?>
		<script type="text/javascript">
			var blockDesignerEditor = function (identifier) {$(identifier).redactor(<?php echo json_encode(array('plugins' => ['concrete5magic'] + $core_editor->getPluginManager()->getSelectedPlugins(), 'minHeight' => 300,'concrete5' => array('filemanager' => $core_editor->allowFileManager(), 'sitemap' => $core_editor->allowSitemap()))); ?>).on('remove', function () {$(this).redactor('core.destroy');});};
		</script>
		<?php
	} ?><?php $repeatable_container_id = 'btTestimonialSlider-testimonials-container-' . $identifier_getString; ?>
    <div id="<?php echo $repeatable_container_id; ?>">
        <div class="sortable-items-wrapper">
            <a href="#" class="btn btn-primary add-entry">
                <?php echo t('Add Entry'); ?>
            </a>

            <div class="sortable-items" data-attr-content="<?php echo htmlspecialchars(
                json_encode(
                    [
                        'items' => $testimonials_items,
                        'order' => array_keys($testimonials_items),
                    ]
                )
            ); ?>">
            </div>

            <a href="#" class="btn btn-primary add-entry add-entry-last">
                <?php echo t('Add Entry'); ?>
            </a>
        </div>

        <script class="repeatableTemplate" type="text/x-handlebars-template">
            <div class="sortable-item" data-id="{{id}}">
                <div class="sortable-item-title">
                    <span class="sortable-item-title-default">
                        <?php echo t('Testimonials') . ' ' . t("row") . ' <span>#{{id}}</span>'; ?>
                    </span>
                    <span class="sortable-item-title-generated"></span>
                </div>

                <div class="sortable-item-inner">            <div class="form-group">
    <label for="<?php echo $view->field('testimonials'); ?>[{{id}}][redLogo]" class="control-label"><?php echo t("red logo (svg: 280X300 max resolution)"); ?></label>
    <?php echo isset($btFieldsRequired['testimonials']) && in_array('redLogo', $btFieldsRequired['testimonials']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <div id="testimonials-redLogo-image-{{id}}" class="ccm-file-selector ft-image-testimonials-redLogo-file-selector">
<concrete-file-input file-id="{{ redLogo }}"
                                                     choose-text="<?php echo t('Choose Image'); ?>"
                                                     input-name="<?php echo $view->field('testimonials'); ?>[{{id}}][redLogo]">
                                </concrete-file-input>
</div>
</div>            <div class="form-group">
    <label for="<?php echo $view->field('testimonials'); ?>[{{id}}][whiteLogo]" class="control-label"><?php echo t("white logo (svg: 280X300 max resolution)"); ?></label>
    <?php echo isset($btFieldsRequired['testimonials']) && in_array('whiteLogo', $btFieldsRequired['testimonials']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <div id="testimonials-whiteLogo-image-{{id}}" class="ccm-file-selector ft-image-testimonials-whiteLogo-file-selector">
<concrete-file-input file-id="{{ whiteLogo }}"
                                                     choose-text="<?php echo t('Choose Image'); ?>"
                                                     input-name="<?php echo $view->field('testimonials'); ?>[{{id}}][whiteLogo]">
                                </concrete-file-input>
</div>
</div>            <div class="form-group">
    <label for="<?php echo $view->field('testimonials'); ?>[{{id}}][description_1]" class="control-label"><?php echo t("desc"); ?></label>
    <?php echo isset($btFieldsRequired['testimonials']) && in_array('description_1', $btFieldsRequired['testimonials']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <textarea name="<?php echo $view->field('testimonials'); ?>[{{id}}][description_1]" id="<?php echo $view->field('testimonials'); ?>[{{id}}][description_1]" class="ft-testimonials-description_1">{{ description_1 }}</textarea>
</div>            <div class="form-group">
    <label for="<?php echo $view->field('testimonials'); ?>[{{id}}][authorName]" class="control-label"><?php echo t("Author Name"); ?></label>
    <?php echo isset($btFieldsRequired['testimonials']) && in_array('authorName', $btFieldsRequired['testimonials']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <input name="<?php echo $view->field('testimonials'); ?>[{{id}}][authorName]" id="<?php echo $view->field('testimonials'); ?>[{{id}}][authorName]" class="form-control" type="text" value="{{ authorName }}" maxlength="255" />
</div>            <div class="form-group">
    <label for="<?php echo $view->field('testimonials'); ?>[{{id}}][authorDesignation]" class="control-label"><?php echo t("Author Designation"); ?></label>
    <?php echo isset($btFieldsRequired['testimonials']) && in_array('authorDesignation', $btFieldsRequired['testimonials']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <input name="<?php echo $view->field('testimonials'); ?>[{{id}}][authorDesignation]" id="<?php echo $view->field('testimonials'); ?>[{{id}}][authorDesignation]" class="form-control" type="text" value="{{ authorDesignation }}" maxlength="255" />
</div></div>

                <span class="sortable-item-collapse-toggle"></span>

                <a href="#" class="sortable-item-delete" data-attr-confirm-text="<?php echo t('Are you sure'); ?>">
                    <i class="fa fa-times"></i>
                </a>

                <div class="sortable-item-handle">
                    <i class="fa fa-sort"></i>
                </div>
            </div>
        </script>
    </div>

<script type="text/javascript">
    Concrete.event.publish('btTestimonialSlider.testimonials.edit.open', {id: '<?php echo $repeatable_container_id; ?>'});
    $.each($('#<?php echo $repeatable_container_id; ?> input[type="text"].title-me'), function () {
        $(this).trigger('keyup');
    });
</script>
</div>

</div>