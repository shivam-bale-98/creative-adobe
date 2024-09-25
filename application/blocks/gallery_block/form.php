<?php defined("C5_EXECUTE") or die("Access Denied."); ?>

<?php $tabs = [
    ['form-basics-' . $identifier_getString, t('Basics'), true],
    ['form-gallery_items-' . $identifier_getString, t('Gallery')]
];
echo Core::make('helper/concrete/ui')->tabs($tabs); ?>

<div class="tab-content">

<div role="tabpanel" class="tab-pane show active" id="form-basics-<?php echo $identifier_getString; ?>">
    <div class="form-group">
    <?php echo $form->label($view->field('title'), t("Title")); ?>
    <?php echo isset($btFieldsRequired) && in_array('title', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo Core::make('editor')->outputBlockEditModeEditor($view->field('title'), isset($title) ? $title : null); ?>
</div><div class="form-group">
    <?php echo $form->label($view->field('hideBlock'), t("Hide Block")); ?>
    <?php echo isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('hideBlock'), (isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "No", 1 => "Yes"], isset($hideBlock) ? $hideBlock : null, []); ?>
</div><div class="form-group">
    <?php echo $form->label($view->field('removePaddingTop'), t("remove padding top")); ?>
    <?php echo isset($btFieldsRequired) && in_array('removePaddingTop', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('removePaddingTop'), (isset($btFieldsRequired) && in_array('removePaddingTop', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "no", 1 => "yes"], isset($removePaddingTop) ? $removePaddingTop : null, []); ?>
</div><div class="form-group">
    <?php echo $form->label($view->field('removePaddingBottom'), t("remove padding bottom")); ?>
    <?php echo isset($btFieldsRequired) && in_array('removePaddingBottom', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('removePaddingBottom'), (isset($btFieldsRequired) && in_array('removePaddingBottom', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "no", 1 => "yes"], isset($removePaddingBottom) ? $removePaddingBottom : null, []); ?>
</div>
</div>

<div role="tabpanel" class="tab-pane" id="form-gallery_items-<?php echo $identifier_getString; ?>">
    <script type="text/javascript">
    var CCM_EDITOR_SECURITY_TOKEN = "<?php echo Core::make('helper/validation/token')->generate('editor')?>";
</script>
<?php $repeatable_container_id = 'btGalleryBlock-gallery-container-' . $identifier_getString; ?>
    <div id="<?php echo $repeatable_container_id; ?>">
        <div class="sortable-items-wrapper">
            <a href="#" class="btn btn-primary add-entry">
                <?php echo t('Add Entry'); ?>
            </a>

            <div class="sortable-items" data-attr-content="<?php echo htmlspecialchars(
                json_encode(
                    [
                        'items' => $gallery_items,
                        'order' => array_keys($gallery_items),
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
                        <?php echo t('Gallery') . ' ' . t("row") . ' <span>#{{id}}</span>'; ?>
                    </span>
                    <span class="sortable-item-title-generated"></span>
                </div>

                <div class="sortable-item-inner">            <div class="form-group">
    <label for="<?php echo $view->field('gallery'); ?>[{{id}}][image]" class="control-label"><?php echo t("Image"); ?></label>
    <?php echo isset($btFieldsRequired['gallery']) && in_array('image', $btFieldsRequired['gallery']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <div id="gallery-image-image-{{id}}" class="ccm-file-selector ft-image-gallery-image-file-selector">
<concrete-file-input file-id="{{ image }}"
                                                     choose-text="<?php echo t('Choose Image'); ?>"
                                                     input-name="<?php echo $view->field('gallery'); ?>[{{id}}][image]">
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
    Concrete.event.publish('btGalleryBlock.gallery.edit.open', {id: '<?php echo $repeatable_container_id; ?>'});
    $.each($('#<?php echo $repeatable_container_id; ?> input[type="text"].title-me'), function () {
        $(this).trigger('keyup');
    });
</script>
</div>

</div>