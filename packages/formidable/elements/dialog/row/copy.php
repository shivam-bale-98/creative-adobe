<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = app('helper/form');
$token = app('helper/validation/token');
?>

<div style="display: none">    
    <div data-copy-row class="ccm-ui">
        <form data-copy-row action="<?= $view->action('copy', 'row', $ff->getItemID()); ?>" method="post">
            <?php $token->output('copy_row'); ?>
            <?=$form->hidden('rowID');?>

            <div class="form-group">
                <?=$form->label('rowName', t('Name')); ?>    
                <?=$form->text('rowName', '');?>
            </div>

            <?=$form->label('copyColumns', t('Duplicate colums'));?>
            <div class="form-group">
                <label>
                    <?= $form->checkbox('copyColumns', 1) ?>
                    <?= t('Also copy the columns in this row') ?>
                <label>
            </div>

            <div data-row-elements style="display:none">
                <?=$form->label('copyElements', t('Duplicate elements'));?>
                <div class="form-group">
                    <label>
                        <?= $form->checkbox('copyElements', 1) ?>
                        <?= t('Also copy the elements in this row') ?>
                    <label>
                </div>
            </div>
        </form>
        <div class="dialog-buttons">
            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?= t('Cancel'); ?></button>
            <button onclick="$('form[data-copy-row]').submit()" class="btn btn-primary float-end"><?= t('Copy'); ?></button>
        </div>
    </div>
</div>