<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Helpers\Editor;

$form = app('helper/form');
$token = app('helper/validation/token');

$editor = Editor::getTemplateEditor();

$token->output($template->getItemID()!=0?'update_template':'add_template')

?>
<style>
    .form-group .cke_contents {
        height: 300px !important;
    }
    .html-value { width: 100%;  border: 1px solid #eee; height: 245px;  }
</style>

<div class="row">
    <div class="col-12 col-md-6">
        <div class="form-group">
            <?= $form->label('templateName', t('Name')) ?>
            <?= $form->text('templateName', $template->getName()) ?>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="form-group">
            <?= $form->label('templateHandle', t('Handle')) ?>
            <?= $form->text('templateHandle', $template->getHandle()) ?>
        </div>
    </div>
</div>

<div class="form-group">
    <?= $form->label('templateContent', t('Content')) ?>
    <?= $editor->outputStandardEditor('templateContent', $template->getContent()); ?>
</div>

<div class="form-group">
    <?=$form->label('templateStyle', t('Additional CSS'));?>
    <div id="templateStyle" class="html-value"><?=base64_decode($template->getStyle())?></div>
    <?=$form->textarea('templateStyle', '', ['style' => 'display:none;', 'data-textarea' => 'templateStyle']);?>
    <div class="help-block"><?= t('You can use this to add addtional css-styles.'); ?></div>
</div>
<script>
    $(function() {

        $('[name="templateName"]').on('blur', function() {
            var handle = $('[name="templateHandle"]').val();
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
                    type: 'template',
                    current: <?=(int)$template->getItemID();?>
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
                    $('[name="templateHandle"]').val(data.success.handle);
                    jQuery.fn.dialog.hideLoader();
                }
            });
        });


        var templateStyle = ace.edit("templateStyle");
        templateStyle.setTheme("ace/theme/eclipse");
        templateStyle.getSession().setMode("ace/mode/css");
        templateStyle.setShowPrintMargin(false);
        $('[data-textarea="templateStyle"]').val(templateStyle.getValue());
        templateStyle.getSession().on('change', function() {
            $('[data-textarea="templateStyle"]').val(templateStyle.getValue());
        });

    });
</script>
