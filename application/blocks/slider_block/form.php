<?php defined("C5_EXECUTE") or die("Access Denied."); ?>

<?php $tabs = [
    ['form-basics-' . $identifier_getString, t('Basics'), true],
    ['form-filter_items-' . $identifier_getString, t('Filter')]
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
    <?php echo $form->label($view->field('itemCount'), t("Item Count")); ?>
    <?php echo isset($btFieldsRequired) && in_array('itemCount', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('itemCount'), isset($itemCount) ? $itemCount : null, array (
  'maxlength' => 255,
)); ?>
</div><div class="form-group">
    <?php echo $form->label($view->field('sortOrder'), t("Sort Order")); ?>
    <?php echo isset($btFieldsRequired) && in_array('sortOrder', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('sortOrder'), $sortOrder_options, isset($sortOrder) ? $sortOrder : null, []); ?>
</div><?php $link_ContainerID = 'btSliderBlock-link-container-' . $identifier_getString; ?>
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
		</div>
	</div>
</div>


<script type="text/javascript">
	Concrete.event.publish('btSliderBlock.link.open', {id: '<?php echo $link_ContainerID; ?>'});
	$('#<?php echo $link_ContainerID; ?> .ft-smart-link-type').trigger('change');
</script><div class="form-group">
    <?php echo $form->label($view->field('pageType'), t("Page Type")); ?>
    <?php echo isset($btFieldsRequired) && in_array('pageType', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->selectMultiple($view->field('pageType'), $pageType_options, array_keys($pageType), array (
  'class' => 'ft-multipleSelect-slider_block-pageType',
)); ?>
</div>

<script type="text/javascript">
    Concrete.event.publish('slider_block.pageType.multiple_select');
</script>
</div>

<div role="tabpanel" class="tab-pane" id="form-filter_items-<?php echo $identifier_getString; ?>">
    <script type="text/javascript">
    var CCM_EDITOR_SECURITY_TOKEN = "<?php echo Core::make('helper/validation/token')->generate('editor')?>";
</script>
<?php $repeatable_container_id = 'btSliderBlock-filter-container-' . $identifier_getString; ?>
    <div id="<?php echo $repeatable_container_id; ?>">
        <div class="sortable-items-wrapper">
            <a href="#" class="btn btn-primary add-entry">
                <?php echo t('Add Entry'); ?>
            </a>

            <div class="sortable-items" data-attr-content="<?php echo htmlspecialchars(
                json_encode(
                    [
                        'items' => $filter_items,
                        'order' => array_keys($filter_items),
                    ]
                )
            ); ?>" data-default-values="<?php echo $defaultValues; ?>" >
            </div>

            <a href="#" class="btn btn-primary add-entry add-entry-last">
                <?php echo t('Add Entry'); ?>
            </a>
        </div>

        <script class="repeatableTemplate" type="text/x-handlebars-template">
            <div class="sortable-item" data-id="{{id}}">
                <div class="sortable-item-title">
                    <span class="sortable-item-title-default">
                        <?php echo t('Filter') . ' ' . t("row") . ' <span>#{{id}}</span>'; ?>
                    </span>
                    <span class="sortable-item-title-generated"></span>
                </div>

                <div class="sortable-item-inner">            <div class="form-group">
    <label for="<?php echo $view->field('filter'); ?>[{{id}}][attribute]" class="control-label"><?php echo t("Attribute"); ?></label>
    <?php echo isset($btFieldsRequired['filter']) && in_array('attribute', $btFieldsRequired['filter']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php $filterAttribute_options = $filter['attribute_options']; ?>
                    <select name="<?php echo $view->field('filter'); ?>[{{id}}][attribute]" id="<?php echo $view->field('filter'); ?>[{{id}}][attribute]" class="form-control attribute">{{#select attribute}}<?php foreach ($filterAttribute_options as $k => $v) {
                        echo "<option value='" . $k . "'>" . $v . "</option>";
                     } ?>{{/select}}</select>
</div>            <div class="form-group">
    <label for="<?php echo $view->field('filter'); ?>[{{id}}][value]" class="control-label"><?php echo t("Value"); ?></label>
    <?php echo isset($btFieldsRequired['filter']) && in_array('value', $btFieldsRequired['filter']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php $filterValue_options = $filter['value_options']; ?>
                    <select name="<?php echo $view->field('filter'); ?>[{{id}}][value]" id="<?php echo $view->field('filter'); ?>[{{id}}][value]" class="form-control value">{{#select value}}{{{values}}}{{/select}}</select>
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
    Concrete.event.publish('btSliderBlock.filter.edit.open', {id: '<?php echo $repeatable_container_id; ?>'});
    $.each($('#<?php echo $repeatable_container_id; ?> input[type="text"].title-me'), function () {
        $(this).trigger('keyup');
    });
</script>
</div>

</div>