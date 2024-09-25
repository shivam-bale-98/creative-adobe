<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = app('helper/form');
$token = app('helper/validation/token');

$mails = $ff->getMails();
?>

<div style="display: none">    
    <div data-resend-result class="ccm-ui">
        <form data-resend-result action="<?= $view->action('resend', 'result', $ff->getItemID()); ?>" method="post">
            <?php $token->output('resend_result'); ?>
            <?=$form->hidden('resultID');?>
            <?php if (count($mails) == 1) { ?>
                <?=$form->hidden('mailID[]', $mails->first()->getItemID());?>
            <?php } ?>
            <p><?=t('Are you sure you want to resend this result(s)?'); ?></p>            

            <?php if (count($mails) > 1) { ?>
                <?=$form->label('mailIDs', t('Which mail do you want to resend?'));?>
                <?php foreach ($mails as $mail) { ?>
                    <div class="checkbox mb-2">
                        <label>
                            <?= $form->checkbox('mailID[]', $mail->getItemID(), 1) ?>
                            <?= t('%s to %s', $mail->getName(), $mail->getToDisplayPlain()) ?>
                        <label>
                    </div>
                <?php } ?>
            <?php } ?>

        </form>
        <div class="dialog-buttons">
            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?= t('Cancel'); ?></button>
            <button onclick="$('form[data-resend-result]').submit()" class="btn btn-success float-end"><?= t('Resend'); ?></button>
        </div>
    </div>
</div>