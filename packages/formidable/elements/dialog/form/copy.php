<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = app('helper/form');
$token = app('helper/validation/token');
?>

<div style="display: none">    
    <div data-copy-form class="ccm-ui">
        <form data-copy-form action="<?= $view->action('copy', 'form'); ?>" method="post">
            <?php $token->output('copy_form'); ?>
            <?=$form->hidden('formID');?>

            <div class="form-group">
                <?=$form->label('formName', t('Name')); ?>    
                <?=$form->text('formName', '');?>
            </div>

            <?=$form->label('copyResults', t('Duplicate results'));?>
            <div class="form-group">
                <label>
                    <?= $form->checkbox('copyResults', 1) ?>
                    <?= t('Also copy the results from this form') ?>
                <label>
            </div>
            
        </form>
        <div class="dialog-buttons">
            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?= t('Cancel'); ?></button>
            <button onclick="$('form[data-copy-form]').submit()" class="btn btn-primary float-end"><?= t('Copy'); ?></button>
        </div>
    </div>
</div>