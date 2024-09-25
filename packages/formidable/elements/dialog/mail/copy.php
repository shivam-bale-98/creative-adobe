<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = app('helper/form');
$token = app('helper/validation/token');
?>

<div style="display: none">    
    <div data-copy-mail class="ccm-ui">
        <form data-copy-mail action="<?= $view->action('copy', 'mail', $ff->getItemID()); ?>" method="post">
            <?php $token->output('copy_mail'); ?>
            <?=$form->hidden('mailID');?>

            <div class="form-group">
                <?=$form->label('mailName', t('Name')); ?>    
                <?=$form->text('mailName', '');?>
            </div>
            
        </form>
        <div class="dialog-buttons">
            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?= t('Cancel'); ?></button>
            <button onclick="$('form[data-copy-mail]').submit()" class="btn btn-primary float-end"><?= t('Copy'); ?></button>
        </div>
    </div>
</div>