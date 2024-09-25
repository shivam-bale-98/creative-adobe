<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Forms\Rows\Row;
use Concrete\Core\Support\Facade\Url;

$form = app('helper/form');
$token = app('helper/validation/token');
?>

<?php $token->output($row->getItemID()!=0?'update_row':'add_row'); ?>
<?= $form->hidden('rowID', (float)$row->getItemID()); ?>

<div class="row">
    <div class="col-12 col-lg-2">
        <a href="<?= URL::to('/dashboard/formidable/forms/layout', $ff->getItemID()); ?>" class="btn btn-sm btn-back mb-3">
            <svg><use xlink:href="#icon-arrow-left"></use></svg> <?=t('Back to layout');?>
        </a>
        <ul class="nav nav-pills flex-row flex-lg-column bg-light mb-3" id="elementTab">
            <li class="nav-item flex-fill">
                <a class="nav-link active" href="#basic" data-bs-toggle="tab">
                    <?=t('Basic');?>
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
                            <?=$form->label('rowName', t('Name')); ?>
                            <?=$form->text('rowName', $row->getName());?>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <?=$form->label('rowHandle', t('Handle')); ?>
                            <?=$form->text('rowHandle', $row->getHandle());?>
                        </div>
                    </div>
                </div>

                <?php
                // DISABLED FOR NOW
                // We just allow div's no steps...
                echo $form->hidden('rowType', Row::ROW_TYPE_ROW);
                /*
                <div class="form-group">
                    <?=$form->label('rowType', t('Type')); ?>
                    <?=$form->select('rowType', $row->getTypes(), $row->getType()) ?>
                </div>

                <div data-row-type="<?=Row::ROW_TYPE_STEP;?>" style="<?=$row->getHandle()!=Row::ROW_TYPE_STEP?'display:none':'';?>">

                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <?=$form->label('rowButton', t('Custom buttons')); ?>
                                <?=$form->select('rowButton', $row->getButtons(), $row->getButton(), ['data-onchange' => 'buttons']) ?>
                            </div>
                        </div>
                        <div class="col-12 col-md-8">

                            <div data-target="buttons" data-value="1">

                                <?=$form->label('rowButtonPrevious', t('Previous-button')); ?>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <?=$form->label('rowButtonPreviousName', t('Name')); ?>
                                            <?=$form->text('rowButtonPreviousName', $row->getButtonPreviousName());?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <?=$form->label('rowButtonPreviousLabel', t('Handle')); ?>
                                            <?=$form->text('rowButtonPreviousLabel', $row->getButtonPreviousHandle());?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <?=$form->label('rowButtonPreviousCss', t('Additional CSS-class(es)')); ?>
                                            <?=$form->select('rowButtonPreviousCss', $row->getCssOptions(), $row->getButtonPreviousCss(), ['data-onchange' => 'prev-button']) ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-8" data-target="prev-button" data-value="1">
                                        <div class="form-group">
                                            <?=$form->label('rowButtonPreviousCssValue', t('Class(es)')); ?>
                                            <?=$form->text('rowButtonPreviousCssValue', $row->getButtonPreviousCssValue());?>
                                        </div>
                                    </div>
                                </div>

                                <?=$form->label('rowButtonNext', t('Next-button')); ?>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <?=$form->label('rowButtonNextName', t('Name')); ?>
                                            <?=$form->text('rowButtonNextName', $row->getButtonNextName());?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <?=$form->label('rowButtonNextLabel', t('Handle')); ?>
                                            <?=$form->text('rowButtonNextLabel', $row->getButtonNextHandle());?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <?=$form->label('rowButtonNextCss', t('Additional CSS-class(es)')); ?>
                                            <?=$form->select('rowButtonNextCss', $row->getCssOptions(), $row->getButtonNextCss(), ['data-onchange' => 'next-button']) ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-8" data-target="next-button" data-value="1">
                                        <div class="form-group">
                                            <?=$form->label('rowButtonNextCssValue', t('Class(es)')); ?>
                                            <?=$form->text('rowButtonNextCssValue', $row->getButtonNextCssValue());?>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                */
                ?>

                <div class="row">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <?=$form->label('rowCss', t('Additional CSS-class(es)')); ?>
                            <?=$form->select('rowCss', $row->getCssOptions(), $row->getCss(), ['data-onchange' => 'row-css']) ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-9" data-target="row-css" data-value="1">
                        <div class="form-group">
                            <?=$form->label('rowCssValue', t('Class(es)')); ?>
                            <?=$form->text('rowCssValue', $row->getCssValue());?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('[name="rowName"]').on('blur', function() {
            var handle = $('[name="rowHandle"]').val();
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
                    type: 'row',
                    form: <?=(int)$ff->getItemID();?>,
                    current: <?=(int)$row->getItemID();?>
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
                    $('[name="rowHandle"]').val(data.success.handle);
                    jQuery.fn.dialog.hideLoader();
                }
            });
        });
    });
</script>

