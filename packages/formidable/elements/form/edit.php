<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Forms\Form;
use Concrete\Package\Formidable\Src\Formidable\Helpers\Editor;

$form = app('helper/form');
$datetime = app('helper/form/date_time');
$token = app('helper/validation/token');
$selector = app('helper/form/page_selector');
//$editor = app('editor');

$editor = Editor::getSimpleEditor();

$token->output($ff->getItemID()!=0?'update_form':'add_form')
?>

<style>
    .form-group .cke_contents {
        height: 300px !important;
    }
    .hide {
        display:none;
    }
    a.nav-link.active {
        color: white !important;
    }
    .btn-back svg {
        height: 20px;
        transition: fill .1s ease-in-out;
        width: 20px;
    }
    .btn-sq {
        padding: 10px;
    }
</style>

<div class="row">
    <div class="col-12 col-lg-2">
        <a href="<?= URL::to('/dashboard/formidable/forms'); ?>" class="btn btn-sm btn-back mb-3">
            <svg><use xlink:href="#icon-arrow-left"></use></svg> <?=t('Back to forms');?>
        </a>
        <ul class="nav nav-pills flex-row flex-lg-column bg-light mb-3" id="elementTab">
            <li class="nav-item flex-fill">
                <a class="nav-link active" href="#basic" data-bs-toggle="tab">
                    <?=t('Basic');?>
                </a>
            </li>
            <li class="nav-item flex-fill">
                <a class="nav-link" href="#limits" data-bs-toggle="tab">
                    <?=t('Limits');?>
                </a>
            </li>
            <li class="nav-item flex-fill">
                <a class="nav-link" href="#scheduling" data-bs-toggle="tab">
                    <?=t('Scheduling');?>
                </a>
            </li>
            <li class="nav-item flex-fill">
                <a class="nav-link" href="#privacy" data-bs-toggle="tab">
                    <?=t('Privacy');?>
                </a>
            </li>
        </ul>
    </div>
    <div class="col-12 col-lg-10">

        <?php /* TAB CONTENT */ ?>

        <div class="tab-content">

            <div class="tab-pane active" id="basic">

                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <?= $form->label('formName', t('Name')) ?>
                            <?= $form->text('formName', $ff->getName()) ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <?= $form->label('formHandle', t('Handle')) ?>
                            <?= $form->text('formHandle', $ff->getHandle()) ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <?= $form->label('formEnabled', t('Enabled')) ?>
                    <?= $form->select('formEnabled', Form::getEnabledOptions(), $ff->getEnabled()) ?>
                    <div class="help-block"><?=t('Enable this form overall within C5 installation');?></div>
                </div>

            </div>

            <div class="tab-pane" id="limits">

                <div class="form-group">
                    <?= $form->label('formLimit', t('Limits')) ?>
                    <?= $form->select('formLimit', Form::getLimitOptions(), $ff->getLimit(), ['data-onchange' => 'limits']) ?>
                    <div class="help-block"><?=t('Limit the submissions based on a diversity of options');?></div>
                </div>

                <div data-target="limits" data-value="1">

                    <div class="row">
                        <div class="col-5 col-md-6">
                            <?= $form->label('formLimitValue', t('Value')) ?>
                            <?= $form->number('formLimitValue', $ff->getLimitValue(), ['step' => 1, 'min' => 0, 'max' => 9999]); ?>
                        </div>
                        <div class="col-7 col-md-6">
                            <?= $form->label('formLimitType', t('Based on')) ?>
                            <?= $form->select('formLimitType', Form::getLimitTypes(), $ff->getLimitType()) ?>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <?= $form->label('formLimitMessage', t('On limit reached')) ?>
                        <?= $form->select('formLimitMessage', Form::getLimitMessages(), $ff->getLimitMessage(), ['data-onchange' => 'limitsmessage']) ?>
                        <div class="help-block"><?=t('What should happen when the limit is reached for this form?');?></div>
                    </div>

                    <div class="form-group" data-target="limitsmessage" data-value="content">
                        <?= $form->label('formLimitMessageContent', t('Message')) ?>
                        <?= $editor->outputStandardEditor('formLimitMessageContent', $ff->getLimitMessageContent()); ?>
                    </div>

                    <div class="form-group" data-target="limitsmessage" data-value="redirect">
                        <?= $form->label('formLimitMessagePage', t('Select page')) ?>
                        <?= $selector->selectPage('formLimitMessagePage', (int)$ff->getLimitMessagePage()) ?>
                    </div>

                </div>

            </div>

            <div class="tab-pane" id="scheduling">

                <div class="form-group">
                    <?= $form->label('formSchedule', t('Schedule')) ?>
                    <?= $form->select('formSchedule', Form::getScheduleOptions(), $ff->getSchedule(), ['data-onchange' => 'schedule']) ?>
                    <div class="help-block"><?=t('Schedule the submissions based on a diversity of options');?></div>
                </div>

                <div data-target="schedule" data-value="1">
                    <div class="row">
                        <div class="col-5 col-md-6">
                            <?= $form->label('formScheduleDateFrom', t('Date from')) ?>
                            <?= $datetime->datetime('formScheduleDateFrom', $ff->getScheduleDateFrom()?$ff->getScheduleDateFrom():''); ?>
                        </div>
                        <div class="col-7 col-md-6">
                            <?= $form->label('formScheduleDateTo', t('Date to')) ?>
                            <?= $datetime->datetime('formScheduleDateTo', $ff->getScheduleDateTo()?$ff->getScheduleDateTo():'') ?>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <?= $form->label('formScheduleMessage', t('When outside schedule')) ?>
                        <?= $form->select('formScheduleMessage', Form::getScheduleMessages(), $ff->getScheduleMessage(), ['data-onchange' => 'schedulemessage']) ?>
                        <div class="help-block"><?=t('What should happen when the visitor is outside the schedule of this form?');?></div>
                    </div>

                    <div class="form-group" data-target="schedulemessage" data-value="content">
                        <?= $form->label('formScheduleMessageContent', t('Message')) ?>
                        <div data-disable="false">
                            <?= $editor->outputStandardEditor('formScheduleMessageContent', $ff->getScheduleMessageContent()); ?>
                        </div>
                    </div>

                    <div class="form-group" data-target="schedulemessage" data-value="redirect">
                        <?= $form->label('formScheduleMessagePage', t('Select page')) ?>
                        <?= $selector->selectPage('formScheduleMessagePage', (int)$ff->getScheduleMessagePage()) ?>
                    </div>
                 </div>

            </div>

            <div class="tab-pane" id="privacy">

                <div class="form-group">
                    <?= $form->label('formPrivacy', t('Privacy')) ?>
                    <?= $form->select('formPrivacy', Form::getPrivacyOptions(), $ff->getPrivacy(), ['data-onchange' => 'privacy']) ?>
                    <div class="help-block"><?=t('Setup privacy settings for this form');?></div>
                </div>

                <div data-target="privacy" data-value="1">

                    <div class="mb-2">
                        <?= $form->label('formPrivacy', t('Additional options')) ?>
                    </div>

                    <div class="form-group">
                        <label>
                            <?= $form->checkbox('formPrivacyIP', 1, $ff->getPrivacyIP()) ?>
                            <?= t('Don\'t save the submitters IP-address. (Note: This could effect the limit-settings of your form!)') ?>
                        <label>
                    </div>

                    <div class="form-group">
                        <label>
                            <?= $form->checkbox('formPrivacyBrowser', 1, $ff->getPrivacyBrowser()) ?>
                            <?= t('Don\'t save the submitters browser information') ?>
                        <label>
                    </div>

                    <div class="form-group">
                        <label>
                            <?= $form->checkbox('formPrivacyLog', 1, $ff->getPrivacyLog()) ?>
                            <?= t('Don\'t log the submission in the reports / logs') ?>
                        <label>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <?= $form->label('formPrivacyRemove', t('Remove submissions')) ?>
                                <?= $form->select('formPrivacyRemove', Form::getPrivacyRemoveOptions(), $ff->getPrivacyRemove(), ['data-onchange' => 'privacyremove']) ?>
                                <div class="help-block"><?=t('Remove form submissions automatically after a certain period.');?></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-8">
                            <div data-target="privacyremove" data-value="1">
                                <div class="row">
                                    <div class="col-5 col-md-6">
                                        <?= $form->label('formPrivacyRemoveValue', t('Value')) ?>
                                        <?= $form->number('formPrivacyRemoveValue', $ff->getPrivacyRemoveValue(), ['step' => 1, 'min' => 0, 'max' => 9999]); ?>
                                    </div>
                                    <div class="col-7 col-md-6">
                                        <?= $form->label('formPrivacyRemoveType', t('Type')) ?>
                                        <?= $form->select('formPrivacyRemoveType', Form::getPrivacyRemoveTypes(), $ff->getPrivacyRemoveType()) ?>
                                    </div>
                                    <div class="col-12">
                                        <div class="help-block"><?=t('After which period should the results be removed? (e.g. 30 days)');?></div>
                                    </div>
                                </div>

                                <div class="mt-2">
                                    <?= $form->label('formPrivacyRemove', t('Additional options')) ?>
                                </div>

                                <div class="form-group">
                                    <label>
                                        <?= $form->checkbox('formPrivacyRemoveFiles', 1, $ff->getPrivacyRemoveFiles()) ?>
                                        <?= t('Remove submitted files from the file manager') ?>
                                    <label>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('[name="formName"]').on('blur', function() {
            var handle = $('[name="formHandle"]').val();
            if (handle.length > 0) {
                return;
            }
            $.ajax({
                url: '<?=$view->action('handle');?>',
                method: 'POST',
                dataType: 'json',
                data: {
                    ccm_token: '<?=$token->generate('generate_handle');?>',
                    name: $(this).val(),
                    type: 'form',
                    current: <?=(int)$ff->getItemID();?>
                },
                beforeSend: function() {
                    jQuery.fn.dialog.showLoader();
                },
                error: function(response) {
                    var data = response.responseJSON;
                    ConcreteAlert.error({
                        title: '<?=t('Error');?>',
                        message: data.error
                    });
                },
                success: function(data) {
                    $('[name="formHandle"]').val(data.success.handle);
                    jQuery.fn.dialog.hideLoader();
                }
            });
        });
    });

</script>