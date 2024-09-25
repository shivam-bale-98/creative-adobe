<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Forms\Rows\Row;

$form = app('helper/form');
$token = app('helper/validation/token');
?>

<div style="display: none">
    <div data-form-row="<?=(float)$row->getItemID();?>" class="ccm-ui">
        <form class="form-stacked" data-form-row="<?=(float)$row->getItemID();?>" action="<?=$view->action('row', $ff->getItemID()); ?>" method="post">
            <?php $token->output($row->getItemID()!=0?'update_row':'add_row'); ?>
            <?= $form->hidden('rowID', (float)$row->getItemID()); ?>

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

            <div class="row">
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <?=$form->label('rowCss', t('Additional CSS-class(es)')); ?>    
                        <?=$form->select('rowCss', $row->getCssOptions(), $row->getCss()) ?>
                    </div>  
                </div>
                <div class="col-12 col-md-9" data-row-css="1" >
                    <div class="form-group">
                        <?=$form->label('rowCssValue', t('Class(es)')); ?>
                        <?=$form->text('rowCssValue', $row->getCssValue());?>
                    </div>  
                </div>
            </div>
                    
        </form>
        <div class="dialog-buttons">
            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?=t('Cancel'); ?></button>
            <button onclick="$('form[data-form-row=<?=(float)$row->getItemID();?>]').submit()" class="btn btn-primary float-end"><?=t('Save'); ?></button>
        </div>
    </div>
</div>