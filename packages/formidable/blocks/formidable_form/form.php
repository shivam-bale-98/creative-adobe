<?php defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Package\Formidable\Src\Formidable\Helpers\Editor;

$form = app('helper/form');
$selector = app('helper/form/page_selector');
//$editor = app('editor');

$editor = Editor::getFullEditor();
?>

<?php if (!isset($forms)) { ?>

    <div class="help-block mt-0">
        <?=t('No forms created yet!');?>
    </div>

<?php } else { ?>

    <style type="text/css">
        .html-value { width: 100%;  border: 1px solid #eee; height: 245px;  }
        .hide, .row.hide { display: none; }
        .form-group .cke_contents { height: 300px !important; display: block; width: 100%; }
        .ui-dialog.fadeIn { opacity: 1; filter: alpha(opacity=1); -webkit-animation: none !important; animation: none !important; }
    </style>

    <ul class="nav nav-tabs nav-fill" id="formidableTab">
        <li class="nav-item">
            <a class="nav-link active" href="#basic" data-bs-toggle="tab">
                <?=t('Form');?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#handling" data-bs-toggle="tab">
                <?=t('Handling');?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#errors" data-bs-toggle="tab">
                <?=t('Errors');?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#success" data-bs-toggle="tab">
                <?=t('Success');?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#data" data-bs-toggle="tab">
                <?=t('Data');?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#advanced" data-bs-toggle="tab">
                <?=t('Advanced');?>
            </a>
        </li>
    </ul>

    <div class="tab-content mt-4">

        <div class="tab-pane active" id="basic">

            <h6><?= t('Form properties'); ?></h6>

            <div class="form-group">
                <?=$form->label('formID', t('Select the form you want to show'));?>
                <?=$form->select('formID', ['' => t('Please select')] + $forms, (int)$formID);?>
            </div>

            <h6 class="pt-3"><?= t('Advanced settings for this block'); ?></h6>

            <div class="form-group">
                <?=$form->label('options[formAdditionalClass]', t('Additional class'));?>
                <?=$form->text('options[formAdditionalClass]', isset($options['formAdditionalClass'])?$options['formAdditionalClass']:'');?>
                <div class="help-block"><?= t('If you need to add specific classes to the block'); ?></div>
            </div>

        </div>

        <div class="tab-pane" id="handling">

            <h6><?= t('When submitted'); ?></h6>

            <div class="form-group">
                <?=$form->label('options[scrollToTop]', t('Do you want to scroll to the top of the form?'));?>
                <?=$form->select('options[scrollToTop]', ['no' => t('No, stay on the current position'), 'yes' => t('Yes, scroll to the top of the form')], isset($options['scrollToTop'])?$options['scrollToTop']:'yes', ['data-onchange' => 'scrollToTop']);?>
            </div>

            <div class="row" data-target="scrollToTop" data-value="yes">
                <div class="col-6">
                    <div class="form-group">
                        <?=$form->label('options[scrollOffset]', t('Offset'));?>
                        <div class="input-group">
                            <?=$form->number('options[scrollOffset]', isset($options['scrollOffset'])?(int)$options['scrollOffset']:0);?>
                            <span class="input-group-text">px</span>
                        </div>
                        <div class="help-block"><?= t('Scroll to top +/- additional pixels'); ?></div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <?=$form->label('options[scrollTime]', t('Speed'));?>
                        <div class="input-group">
                            <?=$form->number('options[scrollTime]', isset($options['scrollTime'])?(int)$options['scrollTime']:200);?>
                            <span class="input-group-text">ms</span>
                        </div>
                        <div class="help-block"><?= t('Scroll speed in miliseconds'); ?></div>
                    </div>
                </div>
            </div>

        </div>


        <div class="tab-pane" id="errors">

            <h6><?= t('Displaying errors'); ?></h6>

            <div class="form-group">
                <?=$form->label('options[errors]', t('Where do you want to show the errors?'));?>
                <?=$form->select('options[errors]', ['element' => t('Beneath element'), 'form' => t('On top of form')], isset($options['errors'])?$options['errors']:'element');?>
            </div>

            <div class="form-group">
                <?=$form->label('options[errorsAdditionalClass]', t('Additional class'));?>
                <?=$form->text('options[errorsAdditionalClass]', isset($options['errorsAdditionalClass'])?$options['errorsAdditionalClass']:'');?>
                <div class="help-block"><?= t('If you need to add specific classes to the error message(s)'); ?></div>
            </div>

        </div>


        <div class="tab-pane" id="success">

            <h6><?= t('When successfully submitted'); ?></h6>

            <div class="form-group">
                <?= $form->label('options[success]', t('On successful submission')) ?>
                <?= $form->select('options[success]', ['content' => t('Show message on page'), 'redirect' => t('Redirect to page')], isset($options['success'])?$options['success']:'content', ['data-onchange' => 'success']) ?>
                <div class="help-block"><?=t('What should happen after a successful submission of this form?');?></div>
            </div>

            <div data-target="success" data-value="content">

                <div class="form-group">
                    <?= $form->label('options[successContent]', t('Message')) ?>
                    <?=$editor->outputBlockEditModeEditor('options[successContent]', isset($options['successContent'])?$options['successContent']:''); ?>
                </div>

                <div class="form-group">
                    <?=$form->label('options[successHideForm]', t('Do you want to hide the form after a successful submission?'));?>
                    <?=$form->select('options[successHideForm]', [0 => t('No, just clear the form'), 1 => t('Yes, hide the form')], isset($options['successHideForm'])?(int)$options['successHideForm']:1);?>
                </div>

                <div class="form-group">
                    <?=$form->label('options[successAdditionalClass]', t('Additional class'));?>
                    <?=$form->text('options[successAdditionalClass]', isset($options['successAdditionalClass'])?$options['successAdditionalClass']:'');?>
                    <div class="help-block"><?= t('If you need to add specific classes to the success message(s)'); ?></div>
                </div>

            </div>

            <div class="form-group" data-target="success" data-value="redirect">
                <?= $form->label('options[successPage]', t('Select page')) ?>
                <?= $selector->selectPage('options[successPage]', isset($options['successPage'])?(int)$options['successPage']:'') ?>
            </div>

        </div>

        <div class="tab-pane" id="data">

            <h6><?= t('Saving data'); ?></h6>

            <div class="form-group">
                <?=$form->label('options[saveEmptyData]', t('Do you want to save empty data?'));?>
                <?=$form->select('options[saveEmptyData]', [0 => t('No, keep results clean!'), 1 => t('Yes, also save empty fields')], isset($options['saveEmptyData'])?(int)$options['saveEmptyData']:0);?>
                <div class="help-block"><?= t('Field that aren\'t filled by the submitter will resolve in empty data. In some cases you might want to save if for later. By default empty data is not stored in the results.'); ?></div>
            </div>

        </div>


        <div class="tab-pane" id="advanced">

            <h6><?= t('Callback options (jquery / javascript)'); ?></h6>

            <div class="form-group">
                <?=$form->label('options[onloadCallback]', t('Callback on onload (jquery / javascript)'));?>
                <div id="onloadCallback" class="html-value"><?=htmlspecialchars(isset($options['onloadCallback'])?$options['onloadCallback']:'function() { }', ENT_QUOTES,APP_CHARSET) ?></div>
                <?=$form->textarea('options[onloadCallback]', '', ['style' => 'display:none;', 'data-textarea' => 'onloadCallback']);?>
                <div class="help-block"><?= t('You can use this to create a callback on onload'); ?></div>
            </div>
            <script>
                $(function() {
                    var onloadCallback = ace.edit("onloadCallback");
                    onloadCallback.setTheme("ace/theme/eclipse");
                    onloadCallback.getSession().setMode("ace/mode/javascript");
                    onloadCallback.setShowPrintMargin(false);
                    $('[data-textarea="onloadCallback"]').val(onloadCallback.getValue());
                    onloadCallback.getSession().on('change', function() {
                        $('[data-textarea="onloadCallback"]').val(onloadCallback.getValue());
                    });
                })
            </script>

            <div class="form-group">
                <?=$form->label('options[errorsCallback]', t('Callback on errors (jquery / javascript)'));?>
                <div id="errorsCallback" class="html-value"><?=htmlspecialchars(isset($options['errorsCallback'])?$options['errorsCallback']:'function() { }', ENT_QUOTES,APP_CHARSET) ?></div>
                <?=$form->textarea('options[errorsCallback]', '', ['style' => 'display:none;', 'data-textarea' => 'errorsCallback']);?>
                <div class="help-block"><?= t('You can use this to create a callback on errors'); ?></div>
            </div>
            <script>
                $(function() {
                    var errorsCallback = ace.edit("errorsCallback");
                    errorsCallback.setTheme("ace/theme/eclipse");
                    errorsCallback.getSession().setMode("ace/mode/javascript");
                    errorsCallback.setShowPrintMargin(false);
                    $('[data-textarea="errorsCallback"]').val(errorsCallback.getValue());
                    errorsCallback.getSession().on('change', function() {
                        $('[data-textarea="errorsCallback"]').val(errorsCallback.getValue());
                    });
                })
            </script>

            <div class="form-group">
                <?=$form->label('options[successCallback]', t('Callback on success (jquery / javascript)'));?>
                <div id="successCallback" class="html-value"><?=htmlspecialchars(isset($options['successCallback'])?$options['successCallback']:'function() { }', ENT_QUOTES,APP_CHARSET) ?></div>
                <?=$form->textarea('options[successCallback]', '', ['style' => 'display:none;', 'data-textarea' => 'successCallback']);?>
                <div class="help-block"><?= t('You can use this to create a callback on success'); ?></div>
            </div>
            <script>
                $(function() {
                    var successCallback = ace.edit("successCallback");
                    successCallback.setTheme("ace/theme/eclipse");
                    successCallback.getSession().setMode("ace/mode/javascript");
                    successCallback.setShowPrintMargin(false);
                    $('[data-textarea="successCallback"]').val(successCallback.getValue());
                    successCallback.getSession().on('change', function() {
                        $('[data-textarea="successCallback"]').val(successCallback.getValue());
                    });
                })
            </script>
        </div>
    </div>

    <div class="formidable-data-holder"></div>

    <script>
        $(function() {
            $('[data-onchange]').off('change').on('change', function() {
                var parent = $(this).closest('form');
                $('[data-target="'+$(this).attr('data-onchange')+'"]', parent).addClass('hide');
                $('[data-target="'+$(this).attr('data-onchange')+'"][data-value="'+$(this).val()+'"]', parent).removeClass('hide');
            }).trigger('change');

            $('select[id="formID"]').off('change').on('change', function() {
                var formID = $(this).val();
                $.ajax({
                    url: '<?=$view->action('data');?>',
                    method: 'POST',
                    dataType: 'json',
                    data: {formID: formID},
                    beforeSend: function() {
                        jQuery.fn.dialog.showLoader();
                    },
                    error: function () {
                        $('div.formidable-data-holder').html('');
                    },
                    success: function(data) {
                        $('div.formidable-data-holder').html(data.data);
                    }
                });
            }).trigger('change');
        });
    </script>

<?php }