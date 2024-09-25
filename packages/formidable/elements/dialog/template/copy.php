<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = app('helper/form');
$token = app('helper/validation/token');
?>

<div style="display: none">    
    <div data-copy-template class="ccm-ui">
        <form data-copy-template action="<?= $view->action('copy', 'template', $template->getItemID()); ?>" method="post">
            <?php $token->output('copy_template'); ?>
            <?=$form->hidden('templateID');?>

            <div class="form-group">
                <?=$form->label('templateName', t('Name')); ?>    
                <?=$form->text('templateName', '');?>
            </div>
            
        </form>
        <div class="dialog-buttons">
            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?= t('Cancel'); ?></button>
            <button onclick="$('form[data-copy-template]').submit()" class="btn btn-primary float-end"><?= t('Copy'); ?></button>
        </div>
    </div>
</div>