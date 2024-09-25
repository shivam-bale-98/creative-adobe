<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = app('helper/form');
$token = app('helper/validation/token');
?>

<div style="display: none">    
    <div data-clear-option class="ccm-ui">
        <form data-clear-option action="" method="post">
            <?=$form->hidden('elementID');?>
            <p><?=t('Are you sure you want to clear all the options This cannot be undone.'); ?></p>               
        </form>
        <div class="dialog-buttons">
            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?= t('Cancel'); ?></button>
            <button onclick="$('form[data-clear-option]').submit()" class="btn btn-danger float-end"><?= t('Clear'); ?></button>
        </div>
    </div>
</div>