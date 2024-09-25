<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = app('helper/form');
$token = app('helper/validation/token');
?>

<div style="display: none">    
    <div data-delete-element class="ccm-ui">
        <form data-delete-element action="<?= $view->action('delete', 'element', $ff->getItemID()); ?>" method="post">
            <?php $token->output('delete_element'); ?>
            <?=$form->hidden('elementID');?>
            <p><?=t('Delete this element? This cannot be undone.'); ?></p>               
        </form>
        <div class="dialog-buttons">
            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?= t('Cancel'); ?></button>
            <button onclick="$('form[data-delete-element]').submit()" class="btn btn-danger float-end"><?= t('Delete'); ?></button>
        </div>
    </div>
</div>