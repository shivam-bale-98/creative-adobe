<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = app('helper/form');
$token = app('helper/validation/token');
?>

<div style="display: none">    
    <div data-delete-row class="ccm-ui">
        <form data-delete-row action="<?= $view->action('delete', 'row', $ff->getItemID()); ?>" method="post">
            <?php $token->output('delete_row'); ?>
            <?=$form->hidden('rowID');?>
            <p><?=t('Delete this row? This cannot be undone.'); ?></p>
            <?=$form->label('forceDelete', t('Delete children'));?>
            <div class="form-group">
                <label class="text-danger">
                    <?= $form->checkbox('forceDelete', 1) ?>
                    <?= t('Force delete, even if there are still columns and/or elements in this row.') ?>
                <label>
            </div>
        </form>
        <div class="dialog-buttons">
            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?= t('Cancel'); ?></button>
            <button onclick="$('form[data-delete-row]').submit()" class="btn btn-danger float-end"><?= t('Delete'); ?></button>
        </div>
    </div>
</div>