<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = app('helper/form');
$token = app('helper/validation/token');
?>

<div style="display: none">    
    <div data-delete-form class="ccm-ui">
        <form data-delete-form action="<?= $view->action('delete', 'form'); ?>" method="post">
            <?php $token->output('delete_form'); ?>
            <?=$form->hidden('formID');?>
            <p><?=t('Delete this form? This cannot be undone.'); ?></p>
            <div class="help-block"><?=t('Results for this form will also be deleted!');?></div>
        </form>
        <div class="dialog-buttons">
            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?= t('Cancel'); ?></button>
            <button onclick="$('form[data-delete-form]').submit()" class="btn btn-danger float-end"><?= t('Delete'); ?></button>
        </div>
    </div>
</div>