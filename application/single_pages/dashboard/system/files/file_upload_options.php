<?php
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Form\Service\Form;

$app = \Concrete\Core\Support\Facade\Facade::getFacadeApplication();
$token  = new Token();
$form   = new Form($app);
?>
<form method="POST" action="<?= $view->action('/save') ?>">
    <?= $token->output('file_upload_options') ?>

    <div class="form-group">
        <?= $form->label('file_size_limit', t('File Size Limit'), ['class' => 'launch-tooltip control-label', 'title' => t('The size entered will be the max size limit for uploading files in file manager')]) ?>
        <?= $form->number('file_size_limit', $file_size_limit, ['required' => 'required', 'min' => '0', 'max' => '100']) ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-right btn btn-primary" type="submit"><?= t('Save') ?></button>
        </div>
    </div>
</form>
