<?php defined("C5_EXECUTE") or die("Access Denied."); ?>

<?php $tabs = [
    ['form-basics-' . $identifier_getString, t('Basics'), true],
    ['form-bannerItems_items-' . $identifier_getString, t('banner Items')]
];
echo Core::make('helper/concrete/ui')->tabs($tabs); ?>

<div class="tab-content">

<div role="tabpanel" class="tab-pane show active" id="form-basics-<?php echo $identifier_getString; ?>">
    <div class="form-group">
    <?php echo $form->label($view->field('hideBlock'), t("Hide  Block")); ?>
    <?php echo isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('hideBlock'), (isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "no", 1 => "yes"], isset($hideBlock) ? $hideBlock : null, []); ?>
</div>
</div>

<div role="tabpanel" class="tab-pane" id="form-bannerItems_items-<?php echo $identifier_getString; ?>">
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
	} ?><?php $repeatable_container_id = 'btHomeBannerSlider-bannerItems-container-' . $identifier_getString; ?>
    <div id="<?php echo $repeatable_container_id; ?>">
        <div class="sortable-items-wrapper">
            <a href="#" class="btn btn-primary add-entry">
                <?php echo t('Add Entry'); ?>
            </a>

            <div class="sortable-items" data-attr-content="<?php echo htmlspecialchars(
                json_encode(
                    [
                        'items' => $bannerItems_items,
                        'order' => array_keys($bannerItems_items),
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
                        <?php echo t('banner Items') . ' ' . t("row") . ' <span>#{{id}}</span>'; ?>
                    </span>
                    <span class="sortable-item-title-generated"></span>
                </div>

                <div class="sortable-item-inner">            <div class="form-group">
    <label for="<?php echo $view->field('bannerItems'); ?>[{{id}}][Dimage]" class="control-label"><?php echo t("Desktop Image (2000X2000)"); ?></label>
    <?php echo isset($btFieldsRequired['bannerItems']) && in_array('Dimage', $btFieldsRequired['bannerItems']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <div id="bannerItems-Dimage-image-{{id}}" class="ccm-file-selector ft-image-bannerItems-Dimage-file-selector">
<concrete-file-input file-id="{{ Dimage }}"
                                                     choose-text="<?php echo t('Choose Image'); ?>"
                                                     input-name="<?php echo $view->field('bannerItems'); ?>[{{id}}][Dimage]">
                                </concrete-file-input>
</div>
</div>            <div class="form-group">
    <label for="<?php echo $view->field('bannerItems'); ?>[{{id}}][Mimage]" class="control-label"><?php echo t("Mobile Image"); ?></label>
    <?php echo isset($btFieldsRequired['bannerItems']) && in_array('Mimage', $btFieldsRequired['bannerItems']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <div id="bannerItems-Mimage-image-{{id}}" class="ccm-file-selector ft-image-bannerItems-Mimage-file-selector">
<concrete-file-input file-id="{{ Mimage }}"
                                                     choose-text="<?php echo t('Choose Image'); ?>"
                                                     input-name="<?php echo $view->field('bannerItems'); ?>[{{id}}][Mimage]">
                                </concrete-file-input>
</div>
</div>            <div class="form-group">
    <label for="<?php echo $view->field('bannerItems'); ?>[{{id}}][videoURL]" class="control-label"><?php echo t("Video Link(Distribution Link)"); ?></label>
    <?php echo isset($btFieldsRequired['bannerItems']) && in_array('videoURL', $btFieldsRequired['bannerItems']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <input name="<?php echo $view->field('bannerItems'); ?>[{{id}}][videoURL]" id="<?php echo $view->field('bannerItems'); ?>[{{id}}][videoURL]" class="form-control" type="text" value="{{ videoURL }}" maxlength="255" />
</div>            <div class="form-group">
    <label for="<?php echo $view->field('bannerItems'); ?>[{{id}}][subTitle]" class="control-label"><?php echo t("Sub Title"); ?></label>
    <?php echo isset($btFieldsRequired['bannerItems']) && in_array('subTitle', $btFieldsRequired['bannerItems']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <input name="<?php echo $view->field('bannerItems'); ?>[{{id}}][subTitle]" id="<?php echo $view->field('bannerItems'); ?>[{{id}}][subTitle]" class="form-control" type="text" value="{{ subTitle }}" maxlength="255" />
</div>            <div class="form-group">
    <label for="<?php echo $view->field('bannerItems'); ?>[{{id}}][title]" class="control-label"><?php echo t("Title(use &lt;heading1&gt;)"); ?></label>
    <?php echo isset($btFieldsRequired['bannerItems']) && in_array('title', $btFieldsRequired['bannerItems']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <textarea name="<?php echo $view->field('bannerItems'); ?>[{{id}}][title]" id="<?php echo $view->field('bannerItems'); ?>[{{id}}][title]" class="ft-bannerItems-title">{{ title }}</textarea>
</div>            <div class="form-group">
    <label for="<?php echo $view->field('bannerItems'); ?>[{{id}}][ctaLink]" class="control-label"><?php echo t("cta Link"); ?></label>
    <?php echo isset($btFieldsRequired['bannerItems']) && in_array('ctaLink', $btFieldsRequired['bannerItems']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <div id="bannerItems-ctaLink-page-{{id}}" class="ft-link-bannerItems-ctaLink-page-selector">
   <concrete-page-input page-id="{{ ctaLink }}" 
                                                        input-name="<?php echo $view->field('bannerItems'); ?>[{{id}}][ctaLink]" 
                                                        choose-text="<?php echo t('Choose Page') ?>" 
                                                        include-system-pages="false" 
                                                        ask-include-system-pages="false">
                                </concrete-page-input>
</div>
</div>

<div class="form-group">
    <label for="<?php echo $view->field('bannerItems'); ?>[{{id}}][ctaLink_text]" class="control-label"><?php echo t("cta Link") . " " . t("Text"); ?></label>
    <input name="<?php echo $view->field('bannerItems'); ?>[{{id}}][ctaLink_text]" id="<?php echo $view->field('bannerItems'); ?>[{{id}}][ctaLink_text]" class="form-control" type="text" value="{{ ctaLink_text }}" />
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
    Concrete.event.publish('btHomeBannerSlider.bannerItems.edit.open', {id: '<?php echo $repeatable_container_id; ?>'});
    $.each($('#<?php echo $repeatable_container_id; ?> input[type="text"].title-me'), function () {
        $(this).trigger('keyup');
    });
</script>
</div>

</div>