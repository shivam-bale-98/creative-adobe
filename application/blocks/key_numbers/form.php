<?php defined("C5_EXECUTE") or die("Access Denied."); ?>

<?php $tabs = [
    ['form-basics-' . $identifier_getString, t('Basics'), true],
    ['form-keyNumbersLeft_items-' . $identifier_getString, t('key numbers left')],
    ['form-keyNumbersRight_items-' . $identifier_getString, t('key numbers right')]
];
echo Core::make('helper/concrete/ui')->tabs($tabs); ?>

<div class="tab-content">

<div role="tabpanel" class="tab-pane show active" id="form-basics-<?php echo $identifier_getString; ?>">
    <div class="form-group">
    <?php echo $form->label($view->field('hideBlock'), t("Hide Block")); ?>
    <?php echo isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('hideBlock'), (isset($btFieldsRequired) && in_array('hideBlock', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "no", 1 => "yes"], isset($hideBlock) ? $hideBlock : null, []); ?>
</div><div class="form-group">
    <?php echo $form->label($view->field('bgColor'), t("backgroundColor(romance: #F2F1EF, rustic-red: #1A0A0C, wheat: #ECE6E4)")); ?>
    <?php echo isset($btFieldsRequired) && in_array('bgColor', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->text($view->field('bgColor'), isset($bgColor) ? $bgColor : null, array (
  'maxlength' => 255,
)); ?>
</div><div class="form-group">
    <?php echo $form->label($view->field('borders'), t("select borders")); ?>
    <?php echo isset($btFieldsRequired) && in_array('borders', $btFieldsRequired) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <?php echo $form->select($view->field('borders'), (isset($btFieldsRequired) && in_array('borders', $btFieldsRequired) ? [] : ["" => "--" . t("Select") . "--"]) + [0 => "borderBottom", 1 => "borderTop"], isset($borders) ? $borders : null, []); ?>
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

<div role="tabpanel" class="tab-pane" id="form-keyNumbersLeft_items-<?php echo $identifier_getString; ?>">
    <script type="text/javascript">
    var CCM_EDITOR_SECURITY_TOKEN = "<?php echo Core::make('helper/validation/token')->generate('editor')?>";
</script>
<?php $repeatable_container_id = 'btKeyNumbers-keyNumbersLeft-container-' . $identifier_getString; ?>
    <div id="<?php echo $repeatable_container_id; ?>">
        <div class="sortable-items-wrapper">
            <a href="#" class="btn btn-primary add-entry">
                <?php echo t('Add Entry'); ?>
            </a>

            <div class="sortable-items" data-attr-content="<?php echo htmlspecialchars(
                json_encode(
                    [
                        'items' => $keyNumbersLeft_items,
                        'order' => array_keys($keyNumbersLeft_items),
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
                        <?php echo t('key numbers left') . ' ' . t("row") . ' <span>#{{id}}</span>'; ?>
                    </span>
                    <span class="sortable-item-title-generated"></span>
                </div>

                <div class="sortable-item-inner">            <div class="form-group">
    <label for="<?php echo $view->field('keyNumbersLeft'); ?>[{{id}}][leftValues]" class="control-label"><?php echo t("left values"); ?></label>
    <?php echo isset($btFieldsRequired['keyNumbersLeft']) && in_array('leftValues', $btFieldsRequired['keyNumbersLeft']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <input name="<?php echo $view->field('keyNumbersLeft'); ?>[{{id}}][leftValues]" id="<?php echo $view->field('keyNumbersLeft'); ?>[{{id}}][leftValues]" class="form-control" type="text" value="{{ leftValues }}" maxlength="255" />
</div>            <div class="form-group">
    <label for="<?php echo $view->field('keyNumbersLeft'); ?>[{{id}}][leftDesc]" class="control-label"><?php echo t("left desc"); ?></label>
    <?php echo isset($btFieldsRequired['keyNumbersLeft']) && in_array('leftDesc', $btFieldsRequired['keyNumbersLeft']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <input name="<?php echo $view->field('keyNumbersLeft'); ?>[{{id}}][leftDesc]" id="<?php echo $view->field('keyNumbersLeft'); ?>[{{id}}][leftDesc]" class="form-control" type="text" value="{{ leftDesc }}" maxlength="255" />
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
    Concrete.event.publish('btKeyNumbers.keyNumbersLeft.edit.open', {id: '<?php echo $repeatable_container_id; ?>'});
    $.each($('#<?php echo $repeatable_container_id; ?> input[type="text"].title-me'), function () {
        $(this).trigger('keyup');
    });
</script>
</div>

<div role="tabpanel" class="tab-pane" id="form-keyNumbersRight_items-<?php echo $identifier_getString; ?>">
    <?php $repeatable_container_id = 'btKeyNumbers-keyNumbersRight-container-' . $identifier_getString; ?>
    <div id="<?php echo $repeatable_container_id; ?>">
        <div class="sortable-items-wrapper">
            <a href="#" class="btn btn-primary add-entry">
                <?php echo t('Add Entry'); ?>
            </a>

            <div class="sortable-items" data-attr-content="<?php echo htmlspecialchars(
                json_encode(
                    [
                        'items' => $keyNumbersRight_items,
                        'order' => array_keys($keyNumbersRight_items),
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
                        <?php echo t('key numbers right') . ' ' . t("row") . ' <span>#{{id}}</span>'; ?>
                    </span>
                    <span class="sortable-item-title-generated"></span>
                </div>

                <div class="sortable-item-inner">            <div class="form-group">
    <label for="<?php echo $view->field('keyNumbersRight'); ?>[{{id}}][rightValues]" class="control-label"><?php echo t("right value"); ?></label>
    <?php echo isset($btFieldsRequired['keyNumbersRight']) && in_array('rightValues', $btFieldsRequired['keyNumbersRight']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <input name="<?php echo $view->field('keyNumbersRight'); ?>[{{id}}][rightValues]" id="<?php echo $view->field('keyNumbersRight'); ?>[{{id}}][rightValues]" class="form-control" type="text" value="{{ rightValues }}" maxlength="255" />
</div>            <div class="form-group">
    <label for="<?php echo $view->field('keyNumbersRight'); ?>[{{id}}][rightDesc]" class="control-label"><?php echo t("right desc"); ?></label>
    <?php echo isset($btFieldsRequired['keyNumbersRight']) && in_array('rightDesc', $btFieldsRequired['keyNumbersRight']) ? '<small class="required">' . t('Required') . '</small>' : null; ?>
    <input name="<?php echo $view->field('keyNumbersRight'); ?>[{{id}}][rightDesc]" id="<?php echo $view->field('keyNumbersRight'); ?>[{{id}}][rightDesc]" class="form-control" type="text" value="{{ rightDesc }}" maxlength="255" />
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
    Concrete.event.publish('btKeyNumbers.keyNumbersRight.edit.open', {id: '<?php echo $repeatable_container_id; ?>'});
    $.each($('#<?php echo $repeatable_container_id; ?> input[type="text"].title-me'), function () {
        $(this).trigger('keyup');
    });
</script>
</div>

</div>