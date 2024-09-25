<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Forms\Columns\Column;
use Concrete\Core\Support\Facade\Url;

$form = app('helper/form');
$token = app('helper/validation/token');
?>

<?php $token->output($column->getItemID()!=0?'update_column':'add_column'); ?>
<?= $form->hidden('rowID', (float)$row->getItemID()); ?>
<?= $form->hidden('columnID', (float)$column->getItemID()); ?>


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
                            <?=$form->label('columnName', t('Name')); ?>
                            <?=$form->text('columnName', $column->getName());?>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <?=$form->label('columnHandle', t('Handle')); ?>
                            <?=$form->text('columnHandle', $column->getHandle());?>
                        </div>
                    </div>
                </div>

                <?php
                // DISABLED FOR NOW
                // We just allow div's no others...
                echo $form->hidden('columnType', Column::COLUMN_TYPE_COLUMN);
                /*
                <div class="form-group">
                    <?=$form->label('columnType', t('Type')); ?>
                    <?=$form->select('columnType', Column::getTypes(), $column->getType()) ?>
                </div>
                */
                ?>

                <div class="form-group">
                    <?=$form->label('columnWidth', t('Width')); ?>
                    <?=$form->select('columnWidth', Column::getWidths(), $column->getWidth()) ?>
                    <div class="help-block"><?=t('Rows are divided by 12 columns (default <a href="%s" target="_blank">Bootstrap</a>).', 'https://getbootstrap.com/docs/5.1/layout/grid/');?></div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <?=$form->label('columnCss', t('Additional CSS-class(es)')); ?>
                            <?=$form->select('columnCss', $column->getCssOptions(), $column->getCss(), ['data-onchange' => 'css']) ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-9" data-target="css" data-value="1">
                        <div class="form-group">
                            <?=$form->label('columnCssValue', t('Class(es)')); ?>
                            <?=$form->text('columnCssValue', $column->getCssValue());?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('[name="columnName"]').on('blur', function() {
            var handle = $('[name="columnHandle"]').val();
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
                    type: 'column',
                    form: <?=(int)$ff->getItemID();?>,
                    current: <?=(int)$column->getItemID();?>
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
                    $('[name="columnHandle"]').val(data.success.handle);
                    jQuery.fn.dialog.hideLoader();
                }
            });
        });
    });
</script>