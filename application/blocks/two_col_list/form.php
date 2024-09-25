<?php defined("C5_EXECUTE") or die("Access Denied."); ?>

<?php $tabs = [
    ['form-basics-' . $identifier_getString, t('Basics'), true],
    ['form-list_items-' . $identifier_getString, t('list')]
];
echo Core::make('helper/concrete/ui')->tabs($tabs); ?>

<div class="tab-content">

<div role="tabpanel" class="tab-pane show active" id="form-basics-<?php echo $identifier_getString; ?>">
    <div class="form-group">
    <?php echo $form->label($view->field('bgColor'), t("background color")); ?>
    <?php echo isset($btFieldsRequired) && in_array('bgColor', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('bgColor'), isset($bgColor) ? $bgColor : null, array (
  'maxlength' => 255,
)); ?>
</div><div class="form-group">
    <?php echo $form->label($view->field('hideBlock'), t("Hide Block")); ?>
    <?php echo isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('hideBlock'), (isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => t("No"), 1 => t("Yes")], isset($hideBlock) ? $hideBlock : null, []); ?>
</div><div class="form-group">
    <?php echo $form->label($view->field('removePaddingTop'), t("remove padding top")); ?>
    <?php echo isset($btFieldsRequired) && in_array('removePaddingTop', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('removePaddingTop'), (isset($btFieldsRequired) && in_array('removePaddingTop', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "no ", 1 => "yes"], isset($removePaddingTop) ? $removePaddingTop : null, []); ?>
</div><div class="form-group">
    <?php echo $form->label($view->field('removePaddingBottom'), t("remove padding bottom")); ?>
    <?php echo isset($btFieldsRequired) && in_array('removePaddingBottom', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('removePaddingBottom'), (isset($btFieldsRequired) && in_array('removePaddingBottom', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "no", 1 => "yes"], isset($removePaddingBottom) ? $removePaddingBottom : null, []); ?>
</div>
</div>

<div role="tabpanel" class="tab-pane" id="form-list_items-<?php echo $identifier_getString; ?>">
    <script type="text/javascript">
    var CCM_EDITOR_SECURITY_TOKEN = "<?php echo Core::make('helper/validation/token')->generate('editor')?>";
</script>
<?php $repeatable_container_id = 'btTwoColList-list-container-' . $identifier_getString; ?>
    <div id="<?php echo $repeatable_container_id; ?>">
        <div class="sortable-items-wrapper">
            <a href="#" class="btn btn-primary add-entry">
                <?php echo t('Add Entry'); ?>
            </a>

            <div class="sortable-items" data-attr-content="<?php echo htmlspecialchars(
                json_encode(
                    [
                        'items' => $list_items,
                        'order' => array_keys($list_items),
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
                        <?php echo t('list') . ' ' . t("row") . ' <span>#{{id}}</span>'; ?>
                    </span>
                    <span class="sortable-item-title-generated"></span>
                </div>

                <div class="sortable-item-inner">            <div class="form-group">
    <label for="<?php echo $view->field('list'); ?>[{{id}}][title]" class="control-label"><?php echo t("title"); ?></label>
    <?php echo isset($btFieldsRequired['list']) && in_array('title', $btFieldsRequired['list']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <input name="<?php echo $view->field('list'); ?>[{{id}}][title]" id="<?php echo $view->field('list'); ?>[{{id}}][title]" class="form-control" type="text" value="{{ title }}" maxlength="255" />
</div>            <div class="form-group">
    <label for="<?php echo $view->field('list'); ?>[{{id}}][desc_1]" class="control-label"><?php echo t("desc"); ?></label>
    <?php echo isset($btFieldsRequired['list']) && in_array('desc_1', $btFieldsRequired['list']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <textarea name="<?php echo $view->field('list'); ?>[{{id}}][desc_1]" id="<?php echo $view->field('list'); ?>[{{id}}][desc_1]" class="form-control" rows="5">{{ desc_1 }}</textarea>
</div>            <?php $link_ContainerID = 'btTwoColList-link-container-' . $identifier_getString; ?>
<div class="ft-smart-link" id="<?php echo $link_ContainerID; ?>">
	<div class="form-group">
		<label for="<?php echo $view->field('list'); ?>[{{id}}][link]" class="control-label"><?php echo t("link"); ?></label>
	    <?php echo isset($btFieldsRequired['list']) && in_array('link', $btFieldsRequired['list']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
	    <?php $listLink_options = $link_Options; ?>
                    <select name="<?php echo $view->field('list'); ?>[{{id}}][link]" id="<?php echo $view->field('list'); ?>[{{id}}][link]" class="form-control ft-smart-link-type">{{#select link}}<?php foreach ($listLink_options as $k => $v) {
                        echo "<option value='" . $k . "'>" . $v . "</option>";
                     } ?>{{/select}}</select>
	</div>
	
	<div class="form-group">
		<div class="ft-smart-link-options hidden d-none" style="padding-left: 10px;">
			<div class="form-group">
				<label for="<?php echo $view->field('list'); ?>[{{id}}][link_Title]" class="control-label"><?php echo t("Title"); ?></label>
			    <input name="<?php echo $view->field('list'); ?>[{{id}}][link_Title]" id="<?php echo $view->field('list'); ?>[{{id}}][link_Title]" class="form-control" type="text" value="{{ link_Title }}" />		
			</div>
			
			<div class="form-group hidden d-none" data-link-type="page">
			<label for="<?php echo $view->field('list'); ?>[{{id}}][link_Page]" class="control-label"><?php echo t("Page"); ?></label>
            <small class="required"><?php echo t('Required'); ?></small>
            <div id="list-link_Page-page-{{id}}" class="ft-smart-link-list-link-page-selector">
   <concrete-page-input page-id="{{ link_Page }}" 
                                                        input-name="<?php echo $view->field('list'); ?>[{{id}}][link_Page]" 
                                                        choose-text="<?php echo t('Choose Page') ?>" 
                                                        include-system-pages="false" 
                                                        ask-include-system-pages="false">
                                </concrete-page-input>
</div>
		</div>

		<div class="form-group hidden d-none" data-link-type="url">
			<label for="<?php echo $view->field('list'); ?>[{{id}}][link_URL]" class="control-label"><?php echo t("URL"); ?></label>
            <small class="required"><?php echo t('Required'); ?></small>
            <input name="<?php echo $view->field('list'); ?>[{{id}}][link_URL]" id="<?php echo $view->field('list'); ?>[{{id}}][link_URL]" class="form-control" type="text" value="{{ link_URL }}" />
		</div>

		<div class="form-group hidden d-none" data-link-type="relative_url">
			<label for="<?php echo $view->field('list'); ?>[{{id}}][link_Relative_URL]" class="control-label"><?php echo t("URL"); ?></label>
            <small class="required"><?php echo t('Required'); ?></small>
            <input name="<?php echo $view->field('list'); ?>[{{id}}][link_Relative_URL]" id="<?php echo $view->field('list'); ?>[{{id}}][link_Relative_URL]" class="form-control" type="text" value="{{ link_Relative_URL }}" />
		</div>

		<div class="form-group hidden d-none" data-link-type="file">
		    <label for="<?php echo $view->field('list'); ?>[{{id}}][link_File]" class="control-label"><?php echo t("File"); ?></label>
            <small class="required"><?php echo t('Required'); ?></small>
            <div id="list-link_File-file-{{id}}" class="ft-smart-link-list-link-file-selector">
<concrete-file-input file-id="{{ link_File }}"
                                                     choose-text="<?php echo t('Choose File'); ?>"
                                                     input-name="<?php echo $view->field('list'); ?>[{{id}}][link_File]">
                                </concrete-file-input>
</div>	
		</div>

		<div class="form-group hidden d-none" data-link-type="image">
			<label for="<?php echo $view->field('list'); ?>[{{id}}][link_Image]" class="control-label"><?php echo t("Image"); ?></label>
            <small class="required"><?php echo t('Required'); ?></small>
            <div id="list-link_Image-image-{{id}}" class="ft-smart-link-list-link-image-selector">
<concrete-file-input file-id="{{ link_Image }}"
                                                     choose-text="<?php echo t('Choose Image'); ?>"
                                                     input-name="<?php echo $view->field('list'); ?>[{{id}}][link_Image]">
                                </concrete-file-input>
</div>
		</div>
		</div>
	</div>
</div>
            <div class="form-group">
    <label for="<?php echo $view->field('list'); ?>[{{id}}][image]" class="control-label"><?php echo t("Image"); ?></label>
    <?php echo isset($btFieldsRequired['list']) && in_array('image', $btFieldsRequired['list']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <div id="list-image-image-{{id}}" class="ccm-file-selector ft-image-list-image-file-selector">
<concrete-file-input file-id="{{ image }}"
                                                     choose-text="<?php echo t('Choose Image'); ?>"
                                                     input-name="<?php echo $view->field('list'); ?>[{{id}}][image]">
                                </concrete-file-input>
</div>
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
    Concrete.event.publish('btTwoColList.list.edit.open', {id: '<?php echo $repeatable_container_id; ?>'});
    $.each($('#<?php echo $repeatable_container_id; ?> input[type="text"].title-me'), function () {
        $(this).trigger('keyup');
    });
</script>
</div>

</div>