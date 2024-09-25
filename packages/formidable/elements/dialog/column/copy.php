<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = app('helper/form');
$token = app('helper/validation/token');
?>

<div style="display: none">    
    <div data-copy-column class="ccm-ui">
        <form data-copy-column action="<?= $view->action('copy', 'column', $ff->getItemID()); ?>" method="post">
            <?php $token->output('copy_column'); ?>
            <?=$form->hidden('columnID');?>

            <div class="form-group">
                <?=$form->label('columnName', t('Name')); ?>    
                <?=$form->text('columnName', '');?>
            </div>
            
            <?=$form->label('copyElements', t('Duplicate elements'));?>
            <div class="form-group">
                <label>
                    <?= $form->checkbox('copyElements', 1) ?>
                    <?= t('Also copy the elements in this column') ?>
                <label>
            </div>
            
        </form>
        <div class="dialog-buttons">
            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?= t('Cancel'); ?></button>
            <button onclick="$('form[data-copy-column]').submit()" class="btn btn-primary float-end"><?= t('Copy'); ?></button>
        </div>
    </div>
</div>