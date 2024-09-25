<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php use Concrete\Core\View\View; ?>

<div class="formidableLoader"></div>

<?php if ($this->controller->getAction() == 'props') { ?>

    <form method="post" action="<?=$view->action('props', $template?$template->getItemID():0); ?>">
        <?php View::element('template/edit', ['template' => $template], 'formidable'); ?>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?= URL::to('/dashboard/formidable/templates'); ?>" class="btn btn-secondary float-start"><?= t('Cancel'); ?></a>
                <button class="float-end btn btn-primary" type="submit"><?= t('Save'); ?></button>
            </div>
        </div>
    </form>

<?php } else { ?>

    <?php if (count($templates) > 0) {  ?>

        <div class="table-responsive">
            <table class="table table-hover ccm-search-results-table">
                <thead>
                    <tr>
                        <th><?= t('Name'); ?></th>
                        <th><?= t('Used'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates as $template) { ?>
                        <tr>
                            <td class="template-name">
                                <?=$template->getName(); ?>
                                <span class="small text-muted">(<?=$template->getHandle();?>)</span>
                            </td>
                            <td class="template-used">
                                <?=$template->getTotalMails(); ?>
                            </td>
                            <td class="template-tasks text-end">
                                <?php View::element('template/menu', ['template' => $template], 'formidable'); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

	<?php } else { ?>

		<div class="help-block mt-0 text-center">
            <?= t('You have not created any templates yet.'); ?>
            <br><br><a href="<?=$view->action('props'); ?>" class="btn btn-secondary"><?=t('Add new'); ?></a>
        </div>

    <?php } ?>

<?php } ?>

<?php
    View::element('dialog/template/copy', ['template' => $template], 'formidable');
    View::element('dialog/template/delete', ['template' => $template], 'formidable');
?>

<script>
    $(function() {

        /* copy template */
        $('a[data-copy-template]').on('click', function() {
            var itemID = $(this).attr('data-copy-template');
            jQuery.fn.dialog.open({
                element: 'div[data-copy-template]',
                modal: true,
                width: 400,
                title: '<?=t('Duplicate template'); ?>',
                height: 400,
                onOpen: function() {
                    $('form[data-copy-template] input[id="templateID"]').val(itemID);
                }
            });
        });

        /* delete template */
        $('a[data-delete-template]').on('click', function() {
            var itemID = $(this).attr('data-delete-template');
            jQuery.fn.dialog.open({
                element: 'div[data-delete-template]',
                modal: true,
                width: 400,
                title: '<?=t('Delete template'); ?>',
                height: 250,
                onOpen: function() {
                    $('form[data-delete-template] input[id="templateID"]').val(itemID);
                }
            });
        });
    });
</script>

<?php
    // show flash messages
    if (isset($flash)) {
        echo $flash;
    }