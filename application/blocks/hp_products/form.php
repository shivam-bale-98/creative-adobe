<?php defined("C5_EXECUTE") or die("Access Denied."); ?>

<?php $tabs = [
    ['form-basics-' . $identifier_getString, t('Basics'), true],
    ['form-page_items-' . $identifier_getString, t('Pages')]
];
echo Core::make('helper/concrete/ui')->tabs($tabs); ?>

<div class="tab-content">

<div role="tabpanel" class="tab-pane show active" id="form-basics-<?php echo $identifier_getString; ?>">
    <div class="form-group">
    <?php echo $form->label($view->field('title'), t("Title")); ?>
    <?php echo isset($btFieldsRequired) && in_array('title', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('title'), isset($title) ? $title : null, array (
  'maxlength' => 255,
)); ?>
</div><div class="form-group">
    <?php echo $form->label($view->field('subTitle'), t("Sub Title")); ?>
    <?php echo isset($btFieldsRequired) && in_array('subTitle', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('subTitle'), isset($subTitle) ? $subTitle : null, array (
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

    <?php $link_ContainerID = 'btHpProducts-link-container-' . $identifier_getString; ?>
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
			if (isset($link_File) && $link_File > 0) {
				$link_File_o = File::getByID($link_File);
				if (!is_object($link_File_o)) {
					unset($link_File_o);
				}
			} ?>
		    <?php echo $form->label($view->field('link_File'), t("File")); ?>
            <small class="required"><?php echo t('Required'); ?></small>
            <?php echo Core::make("helper/concrete/asset_library")->file('ccm-b-hp_products-link_File-' . $identifier_getString, $view->field('link_File'), t("Choose File"), isset($link_File_o) ? $link_File_o : null); ?>	
		</div>

		<div class="form-group hidden d-none" data-link-type="image">
			<?php
			if (isset($link_Image) && $link_Image > 0) {
				$link_Image_o = File::getByID($link_Image);
				if (!is_object($link_Image_o)) {
					unset($link_Image_o);
				}
			} ?>
			<?php echo $form->label($view->field('link_Image'), t("Image")); ?>
            <small class="required"><?php echo t('Required'); ?></small>
            <?php echo Core::make("helper/concrete/asset_library")->image('ccm-b-hp_products-link_Image-' . $identifier_getString, $view->field('link_Image'), t("Choose Image"), isset($link_Image_o) ? $link_Image_o : null); ?>
		</div>
		</div>
	</div>
</div>


<script type="text/javascript">
	Concrete.event.publish('btHpProducts.link.open', {id: '<?php echo $link_ContainerID; ?>'});
	$('#<?php echo $link_ContainerID; ?> .ft-smart-link-type').trigger('change');
</script>
</div>

<div role="tabpanel" class="tab-pane" id="form-page_items-<?php echo $identifier_getString; ?>">
    <script type="text/javascript">
    var CCM_EDITOR_SECURITY_TOKEN = "<?php echo Core::make('helper/validation/token')->generate('editor')?>";
</script>
<?php $repeatable_container_id = 'btHpProducts-page-container-' . $identifier_getString; ?>
    <div id="<?php echo $repeatable_container_id; ?>">
        <div class="sortable-items-wrapper">
            <a href="#" class="btn btn-primary add-entry">
                <?php echo t('Add Entry'); ?>
            </a>

            <div class="sortable-items" data-attr-content="<?php echo htmlspecialchars(
                json_encode(
                    [
                        'items' => $page_items,
                        'order' => array_keys($page_items),
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
                        <?php echo t('Pages') . ' ' . t("row") . ' <span>#{{id}}</span>'; ?>
                    </span>
                    <span class="sortable-item-title-generated"></span>
                </div>

                <div class="sortable-item-inner">            <div class="form-group">
    <label for="<?php echo $view->field('page'); ?>[{{id}}][pages]" class="control-label"><?php echo t("Pages"); ?></label>
    <?php echo isset($btFieldsRequired['page']) && in_array('pages', $btFieldsRequired['page']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <div id="page-pages-page-{{id}}" class="ft-link-page-pages-page-selector">
   <concrete-page-input page-id="{{ pages }}" 
                                                        input-name="<?php echo $view->field('page'); ?>[{{id}}][pages]" 
                                                        choose-text="<?php echo t('Choose Page') ?>" 
                                                        include-system-pages="false" 
                                                        ask-include-system-pages="false">
                                </concrete-page-input>
</div>
</div>

</div>

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
    Concrete.event.publish('btHpProducts.page.edit.open', {id: '<?php echo $repeatable_container_id; ?>'});
    $.each($('#<?php echo $repeatable_container_id; ?> input[type="text"].title-me'), function () {
        $(this).trigger('keyup');
    });
</script>
</div>

</div>