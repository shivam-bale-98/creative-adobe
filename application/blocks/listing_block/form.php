<?php defined("C5_EXECUTE") or die("Access Denied."); ?>

<?php $tabs = [
    ['form-basics-' . $identifier_getString, t('Basics'), true],
    ['form-filters_items-' . $identifier_getString, t('Filters')],
    ['form-advance-' . $identifier_getString, t('Advance')],
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
    <?php echo $form->label($view->field('searchPlaceHolderText'), t("Search Place Holder Text")); ?>
    <?php echo isset($btFieldsRequired) && in_array('searchPlaceHolderText', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('searchPlaceHolderText'), isset($searchPlaceHolderText) ? $searchPlaceHolderText : null, array (
  'maxlength' => 255,
)); ?>
</div>

<div class="form-group">
    <?php echo $form->label($view->field('loadMoreText'), t("Load More Text")); ?>
    <?php echo isset($btFieldsRequired) && in_array('loadMoreText', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('loadMoreText'), isset($loadMoreText) ? $loadMoreText : null, array (
  'maxlength' => 255,
)); ?>
</div><?php $button_ContainerID = 'btListingBlock-button-container-' . $identifier_getString; ?>
<div class="ft-smart-link" id="<?php echo $button_ContainerID; ?>">
	<div class="form-group">
		<?php echo $form->label($view->field('button'), t("Button")); ?>
	    <?php echo isset($btFieldsRequired) && in_array('button', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
	    <?php echo $form->select($view->field('button'), $button_Options, isset($button) ? $button : null, array (
  'class' => 'form-control ft-smart-link-type',
)); ?>
	</div>
	
	<div class="form-group">
		<div class="ft-smart-link-options hidden d-none" style="padding-left: 10px;">
			<div class="form-group">
				<?php echo $form->label($view->field('button_Title'), t("Title")); ?>
			    <?php echo $form->text($view->field('button_Title'), isset($button_Title) ? $button_Title : null, []); ?>		
			</div>
			
			<div class="form-group hidden d-none" data-link-type="page">
			<?php echo $form->label($view->field('button_Page'), t("Page")); ?>
            <small class="required"><?php echo t('Required'); ?></small>
            <?php echo Core::make("helper/form/page_selector")->selectPage($view->field('button_Page'), isset($button_Page) ? $button_Page : null); ?>
		</div>

		<div class="form-group hidden d-none" data-link-type="url">
			<?php echo $form->label($view->field('button_URL'), t("URL")); ?>
            <small class="required"><?php echo t('Required'); ?></small>
            <?php echo $form->text($view->field('button_URL'), isset($button_URL) ? $button_URL : null, []); ?>
		</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	Concrete.event.publish('btListingBlock.button.open', {id: '<?php echo $button_ContainerID; ?>'});
	$('#<?php echo $button_ContainerID; ?> .ft-smart-link-type').trigger('change');
</script>

</div>

<div role="tabpanel" class="tab-pane" id="form-filters_items-<?php echo $identifier_getString; ?>">
    <script type="text/javascript">
    var CCM_EDITOR_SECURITY_TOKEN = "<?php echo Core::make('helper/validation/token')->generate('editor')?>";
</script>
<?php $repeatable_container_id = 'btListingBlock-filters-container-' . $identifier_getString; ?>
    <div id="<?php echo $repeatable_container_id; ?>">
        <div class="sortable-items-wrapper">
            <a href="#" class="btn btn-primary add-entry">
                <?php echo t('Add Entry'); ?>
            </a>

            <div class="sortable-items" data-attr-content="<?php echo htmlspecialchars(
                json_encode(
                    [
                        'items' => $filters_items,
                        'order' => array_keys($filters_items),
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
                        <?php echo t('Filters') . ' ' . t("row") . ' <span>#{{id}}</span>'; ?>
                    </span>
                    <span class="sortable-item-title-generated"></span>
                </div>

                <div class="sortable-item-inner">            <div class="form-group">
    <label for="<?php echo $view->field('filters'); ?>[{{id}}][filterTitle]" class="control-label"><?php echo t("Filter Title"); ?></label>
    <?php echo isset($btFieldsRequired['filters']) && in_array('filterTitle', $btFieldsRequired['filters']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <input name="<?php echo $view->field('filters'); ?>[{{id}}][filterTitle]" id="<?php echo $view->field('filters'); ?>[{{id}}][filterTitle]" class="form-control" type="text" value="{{ filterTitle }}" maxlength="255" />
</div>            <div class="form-group">
    <label for="<?php echo $view->field('filters'); ?>[{{id}}][filterAttribute]" class="control-label"><?php echo t("Filter Attribute"); ?></label>
    <?php echo isset($btFieldsRequired['filters']) && in_array('filterAttribute', $btFieldsRequired['filters']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php $filtersFilterAttribute_options = $filters['filterAttribute_options']; ?>
                    <select name="<?php echo $view->field('filters'); ?>[{{id}}][filterAttribute]" id="<?php echo $view->field('filters'); ?>[{{id}}][filterAttribute]" class="form-control">{{#select filterAttribute}}<?php foreach ($filtersFilterAttribute_options as $k => $v) {
                        echo "<option value='" . $k . "'>" . $v . "</option>";
                     } ?>{{/select}}</select>
</div>            <div class="form-group">
    <label for="<?php echo $view->field('filters'); ?>[{{id}}][allowMultiple]" class="control-label"><?php echo t("Allow Multiple"); ?></label>
    <?php echo isset($btFieldsRequired) && in_array('allowMultiple', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <br>
    <input type="checkbox" id="<?php echo $view->field('filters'); ?>[{{id}}][allowMultiple]" name="<?php echo $view->field('filters'); ?>[{{id}}][allowMultiple]" value="1" {{#if (isMultiple allowMultiple)}} checked {{/if}} />
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
    Concrete.event.publish('btListingBlock.filters.edit.open', {id: '<?php echo $repeatable_container_id; ?>'});
    $.each($('#<?php echo $repeatable_container_id; ?> input[type="text"].title-me'), function () {
        $(this).trigger('keyup');
    });
</script>
</div>

<div role="tabpanel" class="tab-pane show" id="form-advance-<?php echo $identifier_getString; ?>">
    <div class="form-group">
        <?php echo $form->label($view->field('enableSearch'), t("Enable Search")); ?>
        <?php echo isset($btFieldsRequired) && in_array('enableSearch', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
        <br>
        <?php echo $form->checkbox($view->field('enableSearch'), "1", isset($enableSearch) && $enableSearch == "1" ? true : false) ?>
    </div>

    <div class="form-group">
        <?php echo $form->label($view->field('enableSort'), t("Enable Sort")); ?>
        <?php echo isset($btFieldsRequired) && in_array('enableSort', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
        <br>
        <?php echo $form->checkbox($view->field('enableSort'), "1", isset($enableSort) && $enableSort == "1" ? true : false) ?>
    </div>

    <div class="form-group">
        <?php echo $form->label($view->field('enablePagination'), t("Enable Pagination")); ?>
        <?php echo isset($btFieldsRequired) && in_array('enablePagination', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
        <br>
        <?php echo $form->checkbox($view->field('enablePagination'), "1", isset($enablePagination) && $enablePagination == "1" ? true : false) ?>
    </div>

    <div class="form-group">
        <?php echo $form->label($view->field('paginationStyle'), t("Pagination Style")); ?>
        <?php echo isset($btFieldsRequired) && in_array('paginationStyle', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
        <?php echo $form->select($view->field('paginationStyle'), $paginationStyle_options, isset($paginationStyle) ? $paginationStyle : null, []); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label($view->field('itemCount'), t("Item Count")); ?>
        <?php echo isset($btFieldsRequired) && in_array('itemCount', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
        <?php echo $form->text($view->field('itemCount'), isset($itemCount) ? $itemCount : null, array (
            'maxlength' => 255,
        )); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label($view->field('pageTypes'), t("Page Types")); ?>
        <?php echo isset($btFieldsRequired) && in_array('pageTypes', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
        <?php echo $form->selectMultiple($view->field('pageTypes'), $pageTypes_options, array_keys($pageTypes), array (
            'class' => 'ft-multipleSelect-listing_block-pageTypes',
        )); ?>
    </div>

    <script type="text/javascript">
        Concrete.event.publish('listing_block.pageTypes.multiple_select');
    </script>

    <div class="form-group">
        <?php echo $form->label($view->field('sortOptions'), t("Sort Options")); ?>
        <?php echo isset($btFieldsRequired) && in_array('sortOptions', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
        <?php echo $form->selectMultiple($view->field('sortOptions'), $sortOptions_options, array_keys($sortOptions), array (
            'class' => 'ft-multipleSelect-listing_block-sortOptions',
        )); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label($view->field('sortOrder'), t("Sort Order")); ?>
        <?php echo isset($btFieldsRequired) && in_array('sortOrder', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
        <?php echo $form->select($view->field('sortOrder'), $sortOptions_options, isset($sortOrder) ? $sortOrder : null, []); ?>
    </div>

    <script type="text/javascript">
        Concrete.event.publish('listing_block.sortOptions.multiple_select');
    </script>
</div>

</div>

<?php echo $form->hidden($view->field('isAdd'), isset($isAdd) ? true : false); ?>