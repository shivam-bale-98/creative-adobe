<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = app('helper/form');
$token = app('helper/validation/token');
?>

<div style="display: none">    
    <div data-delete-column class="ccm-ui">
        <form data-delete-column action="<?= $view->action('delete', 'column', $ff->getItemID()); ?>" method="post">
            <?php $token->output('delete_column'); ?>
            <?=$form->hidden('columnID');?>
            <p><?=t('Delete this column? This cannot be undone.'); ?></p>
            <?=$form->label('forceDelete', t('Delete children'));?>
            <div class="form-group">
                <label class="text-danger">
                    <?= $form->checkbox('forceDelete', 1) ?>
                    <?= t('Force delete, even if there are still elements in this column.') ?>
                <label>
            </div>
        </form>
        <div class="dialog-buttons">
            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?= t('Cancel'); ?></button>
            <button onclick="$('form[data-delete-column]').submit()" class="btn btn-danger float-end"><?= t('Delete'); ?></button>
        </div>
    </div>
</div>