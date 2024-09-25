<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = app('helper/form');
$token = app('helper/validation/token');

?>

<div style="display: none">
    <div data-multiple-option class="ccm-ui">
        <form class="form-stacked" data-multiple-option action="" method="post">
            <?=$form->hidden('elementID');?>
        
            <div class="form-group">
                <?=$form->label('options', t('Options')); ?>    
                <?=$form->textarea('options', '', ['rows' => 10]) ?>
                <div class="help-block"><?=t('Add values in the textarea. New line for each new option.');?><br><?=t('Use semicolon (;) to use value;name options.');?></div>
            </div> 

            <div>
                <?=$form->label('clear', t('Clear before adding'));?>
                <div class="form-group">
                    <label>
                        <?= $form->checkbox('clear', 1) ?>
                        <?= t('Remove all existing options before adding these new ones') ?>
                    <label>
                </div> 
            </div>   
        </form>
        <div class="dialog-buttons">
            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?=t('Cancel'); ?></button>
            <button onclick="$('form[data-multiple-option]').submit()" class="btn btn-primary float-end"><?=t('Save'); ?></button>
        </div>
    </div>
</div>