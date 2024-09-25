<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = app('helper/form');
$token = app('helper/validation/token');
?>

<div style="display: none">    
    <div data-delete-mail class="ccm-ui">
        <form data-delete-mail action="<?= $view->action('delete', 'mail', $ff->getItemID()); ?>" method="post">
            <?php $token->output('delete_mail'); ?>
            <?=$form->hidden('mailID');?>
            <p><?=t('Delete this mail? This cannot be undone.'); ?></p>               
        </form>
        <div class="dialog-buttons">
            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?= t('Cancel'); ?></button>
            <button onclick="$('form[data-delete-mail]').submit()" class="btn btn-danger float-end"><?= t('Delete'); ?></button>
        </div>
    </div>
</div>