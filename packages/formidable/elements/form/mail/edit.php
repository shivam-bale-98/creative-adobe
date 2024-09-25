<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Http\Request;
use Concrete\Package\Formidable\Src\Formidable\Mails\Mail;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;
use Concrete\Package\Formidable\Src\Formidable\Templates\TemplateList;
use Concrete\Package\Formidable\Src\Formidable\Helpers\Editor;
use Concrete\Core\View\View;
use Symfony\Component\HttpFoundation\Session\Session;

$form = app('helper/form');
$datetime = app('helper/form/date_time');
$token = app('helper/validation/token');
$selector = app('helper/form/page_selector');
$files = app('helper/concrete/asset_library');
$request = app(Request::class);
$session = app(Session::class);

$editor = Editor::getFullEditor();

$templates = TemplateList::getOptionList();

$selectable = [];
$elements = $ff->getElementsByType(['email']);
foreach ($elements as $el) {
    $selectable[$el->getItemID()] = $el->getName();
}

$downloadable = [];
$elements = $ff->getElementsByType(['file']);
foreach ($elements as $el) {
    $downloadable[$el->getItemID()] = $el->getName();
}

$mailto = $mail?$mail->getToEmail():[];
if (!count($mailto)) {
    $mailto = [''];
}

$mailMessage = $mail?$mail->getMessage():'';

$attachment = $mail?$mail->getAttachmentFilesObject():[];
// make sure there are 5!
for ($i=0; $i<=4; $i++) {
    if (!isset($attachment[$i])) {
        $attachment[$i] = '';
    }
}

if ($request->isPost()) {
    $mailto = (array)$request->post('mailToEmailAddresses');
    $attachment = (array)$request->post('mailAttachmentFiles');
    $mailMessage = $request->post('mailMessage');
}
?>

<?= $token->output($mail->getItemID()!=0?'update_mail':'add_mail') ?>
<?= $form->hidden('mailID', (float)$mail->getItemID()); ?>

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
        <a href="<?= URL::to('/dashboard/formidable/forms/mails', $ff->getItemID()); ?>" class="btn btn-sm btn-back mb-3">
            <svg><use xlink:href="#icon-arrow-left"></use></svg> <?=t('Back to mails');?>
        </a>
        <ul class="nav nav-pills flex-row flex-lg-column bg-light mb-3" id="elementTab">
            <li class="nav-item flex-fill">
                <a class="nav-link active" href="#basic" data-bs-toggle="tab">
                    <?=t('Basic');?>
                </a>
            </li>
            <li class="nav-item flex-fill">
                <a class="nav-link" href="#from" data-bs-toggle="tab">
                    <?=t('From');?>
                </a>
            </li>
            <li class="nav-item flex-fill">
                <a class="nav-link" href="#to" data-bs-toggle="tab">
                    <?=t('To');?>
                </a>
            </li>
            <li class="nav-item flex-fill">
                <a class="nav-link" href="#message" data-bs-toggle="tab">
                    <?=t('Message');?>
                </a>
            </li>
            <li class="nav-item flex-fill">
                <a class="nav-link" href="#attachment" data-bs-toggle="tab">
                    <?=t('Attachments');?>
                </a>
            </li>
            <li class="nav-item flex-fill">
                <a class="nav-link" href="#dependencies" data-bs-toggle="tab">
                    <?=t('Dependencies');?>
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
                            <?= $form->label('mailName', t('Name')) ?>
                            <?= $form->text('mailName', $mail?$mail->getName():'') ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <?= $form->label('mailHandle', t('Handle')) ?>
                            <?= $form->text('mailHandle', $mail?$mail->getHandle():'') ?>
                        </div>
                    </div>
                </div>

            </div>

            <div class="tab-pane" id="from">

                <div class="row">
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <?= $form->label('mailFrom', t('From')) ?>
                            <?= $form->select('mailFrom', Mail::getFroms(), $mail?$mail->getFrom():'custom') ?>
                            <div class="help-block"><?=t('From which address should this mail be send from?');?></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-8">

                        <div class="row" data-mail-from="custom" style="<?=$mail->getFrom()==0?'display:none':'';?>">
                            <div class="col-6 col-md-6">
                                <?= $form->label('mailFromEmail', t('Email Address')) ?>
                                <?= $form->email('mailFromEmail', $mail->getFromEmail()) ?>
                            </div>
                            <div class="col-6 col-md-6">
                                <?= $form->label('mailFromName', t('Name')) ?>
                                <?= $form->text('mailFromName', $mail->getFromName()); ?>
                            </div>
                        </div>

                        <div data-mail-from="element" style="<?=$mail->getFrom()==0?'display:none':'';?>">
                            <div class="form-group">
                                <?= $form->label('mailFromElement', t('Element')) ?>
                                <?php if (count($selectable)) { ?>
                                    <?= $form->select('mailFromElement', $selectable, $mail?$mail->getFromElement():'') ?>
                                    <div class="help-block"><?=t('Select the element the "from"-details will be loaded from');?></div>
                                <?php } else { ?>
                                    <div class="help-block mt-0"><?=t('No fields in form to use for the "from"-details');?></div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <?= $form->label('mailReplyTo', t('Reply To')) ?>
                            <?= $form->select('mailReplyTo', Mail::getReplyTos(), $mail?$mail->getReplyTo():'from') ?>
                            <div class="help-block"><?=t('Set the "reply-to"-details for the mail');?></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-8">

                        <div class="row" data-mail-reply="from">

                        </div>

                        <div class="row" data-mail-reply="custom" style="<?=$mail->getReplyTo()==0?'display:none':'';?>">
                            <div class="col-6 col-md-6">
                                <?= $form->label('mailReplyToEmail', t('Email Address')) ?>
                                <?= $form->email('mailReplyToEmail', $mail->getReplyToEmail()) ?>
                            </div>
                            <div class="col-6 col-md-6">
                                <?= $form->label('mailReplyToName', t('Name')) ?>
                                <?= $form->text('mailReplyToName', $mail->getReplyToName()); ?>
                            </div>
                        </div>

                        <div data-mail-reply="element" style="<?=$mail->getReplyTo()==0?'display:none':'';?>">
                            <div class="form-group">
                                <?= $form->label('mailReplyToElement', t('Element')) ?>
                                <?php if (count($selectable)) { ?>
                                    <?= $form->select('mailReplyToElement', $selectable, $mail?$mail->getReplyToElement():'') ?>
                                    <div class="help-block"><?=t('Select the element the "reply-to"-details will be loaded from');?></div>
                                <?php } else { ?>
                                    <div class="help-block mt-0"><?=t('No fields in form to use for the "reply-to"-details');?></div>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>

            </div>


            <div class="tab-pane" id="to">

                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <?= $form->label('mailToEmailAddresses', t('To (custom email addresses)')) ?>
                            <div data-mailto-rows>
                                <?php foreach ((array)$mailto as $to) { ?>
                                    <div class="row mailto mb-2">
                                        <div class="col">
                                            <?=$form->email('mailToEmailAddresses[]', $to, ['placeholder' => t('Email Address')]);?>
                                        </div>
                                        <div class="col-auto">
                                            <div class="btn-group">
                                                <a href="#" class="btn btn-secondary btn-sq text-danger" data-remove-mailto data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
                                                    <i class="fa-fw fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="btn-group float-end mt-2">
                                        <a class="btn btn-sm btn-primary" data-add-mailto data-row-count="-1">
                                            <?=t('Add Email Address');?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div data-template-mailto class="hide" style="display:none">
                                <div class="row mailto mb-2">
                                    <div class="col">
                                        <?=$form->email('mailToEmailAddresses[_tmp]', '', ['disabled' => 'disabled', 'placeholder' => t('Email Address')]);?>
                                    </div>
                                    <div class="col-auto">
                                        <div class="btn-group">
                                            <a href="#" class="btn btn-secondary btn-sq text-danger" data-remove-mailto data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
                                                <i class="fa-fw fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <?= $form->label('mailTo', t('To (available elements in the form)')) ?>
                            <?php if (!count($selectable)) { ?>
                                <div class="help-block mt-0"><?=t('No elements in the form available to send mail to');?></div>
                            <?php } else { ?>
                                <?php foreach ($selectable as $elementID => $name) { ?>
                                    <div class="form-group mt-2">
                                        <label>
                                            <?= $form->checkbox('mailTo[]', $elementID, $mail&&in_array($elementID, $mail->getTo())?$elementID:'') ?>
                                            <?= $name ?>
                                        <label>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <?= $form->label('mailToUseCC', t('Use CC')) ?>
                    <div>
                        <label>
                            <?= $form->checkbox('mailToUseCC', 1, $mail?$mail->getUseCC():'') ?>
                            <?= t('Use CC instead of BCC when you send to multple Email Addresses') ?>
                        <label>
                    </div>
                </div>

            </div>


            <div class="tab-pane" id="message">

                <div class="form-group">
                    <?= $form->label('mailSubject', t('Subject')) ?>
                    <div class="input-group">
                        <?= $form->text('mailSubject', $mail->getSubject()) ?>
                        <a class="input-group-text" href="javascript:;" data-bs-toggle="tooltip" data-bs-title="<?=t('Add dynamic data');?>" data-dynamic-data>
                            <i class="fa-fw fa fa-plus"></i>
                        </a>
                    </div>
                </div>

                <div class="form-group">
                    <?= $form->label('mailTemplate', t('Template')) ?>
                    <?php if (!count($templates)) { ?>
                        <div class="help-block mt-0"><?=t('No templates created');?></div>
                    <?php } else { ?>
                        <?= $form->select('mailTemplate', ['' => t('Don\'t use a template')] + $templates, $mail&&$mail->getTemplate()?$mail->getTemplate()->getItemID():'') ?>
                        <div class="help-block"><?=t('Select the template for this mail');?></div>
                    <?php } ?>
                </div>

                <div class="form-group">
                    <?= $form->label('mailMessage', t('Message')) ?>
                    <?= $editor->outputStandardEditor('mailMessage', $mailMessage); ?>
                </div>

                <div class="form-group">
                    <?= $form->label('mailSkipEmpty', t('Skip empty values')) ?>
                    <div>
                        <label>
                            <?= $form->checkbox('mailSkipEmpty', 1, $mail?$mail->getSkipEmpty():'') ?>
                            <?= t('Empty values in the dynamic data in the message will be removed before sending the message') ?>
                        <label>
                    </div>
                </div>

                <div class="form-group">
                    <?= $form->label('mailSkipLayout', t('Skip layout elements')) ?>
                    <div>
                        <label>
                            <?= $form->checkbox('mailSkipLayout', 1, $mail?$mail->getSkipLayout():'') ?>
                            <?= t('Layout elements in the dynamic data in the message will be removed before sending the message') ?>
                        <label>
                    </div>
                </div>

            </div>

            <div class="tab-pane" id="attachment">

                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <?= $form->label('mailAttachmentFiles', t('Attachment (files from filemanager)')) ?>
                            <div data-attachment-rows>
                                <?php foreach ((array)$attachment as $key => $f) { ?>
                                    <div class="row attachment mb-2 <?=!is_object($f)&&$key>0?'hide':'';?>">
                                        <div class="col">
                                            <?=$files->file('mailAttachmentFiles['.$key.']', 'mailAttachmentFiles['.$key.']', t('Choose File'), is_object($f)?$f:''); ?>
                                        </div>
                                        <div class="col-auto">
                                            <div class="btn-group">
                                                <a href="#" class="btn btn-secondary btn-sq text-danger" data-remove-attachment data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
                                                    <i class="fa-fw fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="btn-group float-end mt-2">
                                        <a class="btn btn-sm btn-primary" data-add-attachment>
                                            <?=t('Add attachment');?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <?= $form->label('mailAttachments', t('Attachment (available elements in the form)')) ?>
                            <?php if (!count($downloadable)) { ?>
                                <div class="help-block mt-0"><?=t('No elements in the form available to use as attachment');?></div>
                            <?php } else { ?>
                                <?php foreach ($downloadable as $elementID => $name) { ?>
                                    <div class="form-group mt-2">
                                        <label>
                                            <?= $form->checkbox('mailAttachments[]', $elementID, $mail&&in_array($elementID, $mail->getAttachments())?$elementID:'') ?>
                                            <?= $name ?>
                                        <label>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            </div>

            <div class="tab-pane" id="dependencies">
                <?php View::element('form/layout/element/dependency', ['ff' => $ff, 'mail' => $mail?$mail:''], 'formidable'); ?>
            </div>

        </div>
    </div>
</div>

<?php View::element('dialog/data', ['ff' => $ff?$ff:null, 'tag' => 'subject'], 'formidable'); ?>

<script type="text/javascript">

    $(function() {

        $('[name="mailName"]').on('blur', function() {
            var handle = $('[name="mailHandle"]').val();
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
                    type: 'mail',
                    form: <?=(int)$ff->getItemID();?>,
                    current: <?=(int)$mail->getItemID();?>
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
                    $('[name="mailHandle"]').val(data.success.handle);
                    jQuery.fn.dialog.hideLoader();
                }
            });
        });

        $('[data-bs-toggle="tooltip"]').tooltip();

        $('a[data-dynamic-data]').on('click', function() {
            var subject = $(this).prev('input');
            jQuery.fn.dialog.open({
                element: 'div[data-formidable-data-subject]',
                modal: true,
                width: 750,
                title: '<?=t('Select "Formidable Data" - Tag');?>',
                height: 600,
                onOpen: function() {
                    $('div[data-formidable-data-subject] [data-insert]').off('click').on('click', function() {
                        var code = $(this).data('insert');
                        if (subject.val().length > 0) {
                            subject.val(subject.val() + ' ');
                        }
                        subject.val(subject.val() + code);
                        jQuery.fn.dialog.closeTop();
                    });
                }
            });
        });

        $('select[name=mailFrom]').on('change', function() {
            $('div[data-mail-from]').hide();
            $('div[data-mail-from="'+$(this).val()+'"]').show();
        }).trigger('change');

        $('select[name=mailReplyTo]').on('change', function() {
            $('div[data-mail-reply]').hide();
            $('div[data-mail-reply="'+$(this).val()+'"]').show();
        }).trigger('change');

        $('[data-remove-mailto]').on('click', function() {
            $(this).closest('div.row.mailto').remove();
        });

        $('[data-add-mailto]').on('click', function() {
            var count = $(this).attr('data-row-count');
            var row = $('[data-template-mailto]').eq(0).clone();
            row.css('display', 'block').find(':input').attr('disabled', false);
            row = row.html().replace(/_tmp/g, count);
            $('[data-mailto-rows]').append(row);
            $(this).attr('data-row-count', count-1);

            $('[data-remove-mailto]').off('click').on('click', function() {
                $(this).closest('div.row.mailto').remove();
            });
        });

        $('[data-remove-attachment]').on('click', function() {
            $(this).closest('div.row.attachment').find('.ccm-item-selector-reset').trigger('click');
            $(this).closest('div.row.attachment').addClass('hide');
        });

        $('[data-add-attachment]').on('click', function() {
            $('[data-attachment-rows] .attachment.hide:first').removeClass('hide');
        });

        FormidableDependencyElements();
        FormidableCheckElements();
    });

</script>
