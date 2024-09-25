<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = app('helper/form');
$token = app('helper/validation/token');
?>

<div style="display: none">    
    <div data-copy-element class="ccm-ui">
        <form data-copy-element action="<?= $view->action('copy', 'element', $ff->getItemID()); ?>" method="post">
            <?php $token->output('copy_element'); ?>
            <?=$form->hidden('elementID');?>

            <div class="form-group">
                <?=$form->label('elementName', t('Name')); ?>    
                <?=$form->text('elementName', '');?>
            </div>
            
        </form>
        <div class="dialog-buttons">
            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?= t('Cancel'); ?></button>
            <button onclick="$('form[data-copy-element]').submit()" class="btn btn-primary float-end"><?= t('Copy'); ?></button>
        </div>
    </div>
</div>