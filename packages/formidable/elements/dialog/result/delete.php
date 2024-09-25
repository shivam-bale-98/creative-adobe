<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = app('helper/form');
$token = app('helper/validation/token');
?>

<div style="display: none">    
    <div data-delete-result class="ccm-ui">
        <form data-delete-result action="<?= $view->action('delete', 'result', $ff->getItemID()); ?>" method="post">
            <?php $token->output('delete_result'); ?>
            <?=$form->hidden('resultID');?>
            <p><?=t('Delete this result(s)? This cannot be undone.'); ?></p>               
        </form>
        <div class="dialog-buttons">
            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?= t('Cancel'); ?></button>
            <button onclick="$('form[data-delete-result]').submit()" class="btn btn-danger float-end"><?= t('Delete'); ?></button>
        </div>
    </div>
</div>