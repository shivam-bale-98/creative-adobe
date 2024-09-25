<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = app('helper/form');
$token = app('helper/validation/token');
?>

<div style="display: none">    
    <div data-delete-template class="ccm-ui">
        <form data-delete-template action="<?= $view->action('delete', 'template', $template->getItemID()); ?>" method="post">
            <?php $token->output('delete_template'); ?>
            <?=$form->hidden('templateID');?>
            <p><?=t('Delete this template? This cannot be undone.'); ?></p>               
        </form>
        <div class="dialog-buttons">
            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?= t('Cancel'); ?></button>
            <button onclick="$('form[data-delete-template]').submit()" class="btn btn-danger float-end"><?= t('Delete'); ?></button>
        </div>
    </div>
</div>