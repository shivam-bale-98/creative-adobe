<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Package\Formidable\Src\Formidable\Helpers\Converter;

$form = app('helper/form');
$token = app('helper/validation/token');

$id = isset($tag)?'-'.$tag:uniqid();
?>
<style>
    div[data-formidable-data].only-form .noshow { display: none; }
</style>

<div style="display:none">
    <div data-formidable-data<?=isset($tag)?'-'.$tag:'';?> class="ccm-ui">

        <ul class="nav nav-pills bg-light mb-3" id="elementTab">
            <li class="nav-item flex-fill">
                <a class="nav-link active" href="#form<?=$id;?>" data-bs-toggle="tab">
                    <?=t('Form');?>
                </a>
            </li>
            <li class="nav-item flex-fill noshow">
                <a class="nav-link" href="#result<?=$id;?>" data-bs-toggle="tab">
                    <?=t('Result');?>
                </a>
            </li>
            <li class="nav-item flex-fill noshow">
                <a class="nav-link" href="#elements<?=$id;?>" data-bs-toggle="tab">
                    <?=t('Elements');?>
                </a>
            </li>
            <li class="nav-item flex-fill">
                <a class="nav-link" href="#page<?=$id;?>" data-bs-toggle="tab">
                    <?=t('Page');?>
                </a>
            </li>
            <li class="nav-item flex-fill">
                <a class="nav-link" href="#user<?=$id;?>" data-bs-toggle="tab">
                    <?=t('User');?>
                </a>
            </li>
        </ul>

        <?php /* TAB CONTENT */ ?>

        <div class="tab-content mt-2">

            <div class="tab-pane noshow" id="elements<?=$id;?>">
                <?php
                    $combined = ['label' => '', 'value' => '','both' => ''];
                    $elements = $ff?$ff->getElements():[];
                    foreach ($elements as $element) {
                        $combined['label'] .= '{%element_label|'.$element->getHandle().'%}<br />';
                        $combined['value'] .= '{%element_value|'.$element->getHandle().'%}<br />';
                        $combined['both'] .= '{%element_label|'.$element->getHandle().'%}: {%element_value|'.$element->getHandle().'%}<br />';
                    }
                ?>
                <div class="table-responsive">
                    <table class="table table-hover ccm-search-results-table">
                        <thead>
                            <tr>
                                <th><?=t('Name'); ?></th>
                                <th><?=t('Type'); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?=t('All elements'); ?></td>
                                <td><?=t('(combined)');?></td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="{%element_data%}">
                                            <?=t('Both');?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><?=t('All elements'); ?></td>
                                <td><?=t('(seperate tags)');?></td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="<?=$combined['label'];?>">
                                            <?=t('Label');?>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="<?=$combined['value'];?>">
                                            <?=t('Value');?>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="<?=$combined['both'];?>">
                                            <?=t('Both');?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php foreach ($elements as $element) { ?>
                                <tr>
                                    <td><?=$element->getName() ?></td>
                                    <td><?=$element->getTypeObject()->getName()?></td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="#" class="btn btn-sm btn-secondary" data-insert="{%element_label|<?=$element->getHandle()?>%}">
                                                <?=t('Label');?>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-secondary" data-insert="{%element_value|<?=$element->getHandle()?>%}">
                                                <?=t('Value');?>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-secondary" data-insert="{%element_label|<?=$element->getHandle()?>%}: {%element_value|<?=$element->getHandle()?>%}">
                                                <?=t('Both');?>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane active" id="form<?=$id;?>">
                <?php
                    $combined = ['label' => '', 'value' => '','both' => ''];
                    $rows = Converter::getFormTags();
                    foreach ($rows as $row) {
                        $combined['label'] .= '{%form_label|'.$row['handle'].'%}<br />';
                        $combined['value'] .= '{%form_value|'.$row['handle'].'%}<br />';
                        $combined['both'] .= '{%form_label|'.$row['handle'].'%}: {%form_value|'.$row['handle'].'%}<br />';
                    }
                ?>
                <div class="table-responsive">
                    <table class="table table-hover ccm-search-results-table">
                        <thead>
                            <tr>
                                <th><?=t('Name'); ?></th>
                                <th><?=t('Type'); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?=t('All properties'); ?></td>
                                <td><?=t('(combined)');?></td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="{%form_data%}">
                                            <?=t('Both');?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><?=t('All properties'); ?></td>
                                <td><?=t('(seperate tags)');?></td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="<?=$combined['label'];?>">
                                            <?=t('Label');?>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="<?=$combined['value'];?>">
                                            <?=t('Value');?>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="<?=$combined['both'];?>">
                                            <?=t('Both');?>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <?php foreach ($rows as $row) { ?>
                                <tr>
                                    <td><?=$row['label']?></td>
                                    <td><?=$row['type']?></td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="#" class="btn btn-sm btn-secondary" data-insert="{%form_label|<?=$row['handle']?>%}">
                                                <?=t('Label');?>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-secondary" data-insert="{%form_value|<?=$row['handle']?>%}">
                                                <?=t('Value');?>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-secondary" data-insert="{%form_label|<?=$row['handle']?>%}: {%form_value|<?=$row['handle']?>%}">
                                                <?=t('Both');?>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane noshow" id="result<?=$id;?>">

                <?php
                    $combined = ['label' => '', 'value' => '','both' => ''];
                    $rows = Converter::getResultTags();
                    foreach ($rows as $row) {
                        $combined['label'] .= '{%result_label|'.$row['handle'].'%}<br />';
                        $combined['value'] .= '{%result_value|'.$row['handle'].'%}<br />';
                        $combined['both'] .= '{%result_label|'.$row['handle'].'%}: {%result_value|'.$row['handle'].'%}<br />';
                    }
                ?>
                <div class="table-responsive">
                    <table class="table table-hover ccm-search-results-table">
                        <thead>
                            <tr>
                                <th><?=t('Name'); ?></th>
                                <th><?=t('Type'); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?=t('All properties'); ?></td>
                                <td><?=t('(combined)');?></td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="{%result_data%}">
                                            <?=t('Both');?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><?=t('All properties'); ?></td>
                                <td><?=t('(seperate tags)');?></td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="<?=$combined['label'];?>">
                                            <?=t('Label');?>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="<?=$combined['value'];?>">
                                            <?=t('Value');?>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="<?=$combined['both'];?>">
                                            <?=t('Both');?>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <?php foreach ($rows as $row) { ?>
                                <tr>
                                    <td><?=$row['label']?></td>
                                    <td><?=$row['type']?></td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="#" class="btn btn-sm btn-secondary" data-insert="{%result_label|<?=$row['handle']?>%}">
                                                <?=t('Label');?>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-secondary" data-insert="{%result_value|<?=$row['handle']?>%}">
                                                <?=t('Value');?>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-secondary" data-insert="{%result_label|<?=$row['handle']?>%}: {%result_value|<?=$row['handle']?>%}">
                                                <?=t('Both');?>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane" id="page<?=$id;?>">

                <?php
                    $combined = ['label' => '', 'value' => '','both' => ''];
                    $rows = Converter::getPageTags();
                    foreach ($rows as $row) {
                        $combined['label'] .= '{%page_label|'.$row['handle'].'%}<br />';
                        $combined['value'] .= '{%page_value|'.$row['handle'].'%}<br />';
                        $combined['both'] .= '{%page_label|'.$row['handle'].'%}: {%page_value|'.$row['handle'].'%}<br />';
                    }
                ?>
                <div class="table-responsive">
                    <table class="table table-hover ccm-search-results-table">
                        <thead>
                            <tr>
                                <th><?=t('Name'); ?></th>
                                <th><?=t('Type'); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?=t('All properties'); ?></td>
                                <td><?=t('(combined)');?></td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="{%page_data%}">
                                            <?=t('Both');?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><?=t('All properties'); ?></td>
                                <td><?=t('(seperate tags)');?></td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="<?=$combined['label'];?>">
                                            <?=t('Label');?>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="<?=$combined['value'];?>">
                                            <?=t('Value');?>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="<?=$combined['both'];?>">
                                            <?=t('Both');?>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <?php foreach ($rows as $row) { ?>
                                <tr>
                                    <td><?=$row['label']?></td>
                                    <td><?=$row['type']?></td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="#" class="btn btn-sm btn-secondary" data-insert="{%page_label|<?=$row['handle']?>%}">
                                                <?=t('Label');?>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-secondary" data-insert="{%page_value|<?=$row['handle']?>%}">
                                                <?=t('Value');?>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-secondary" data-insert="{%page_label|<?=$row['handle']?>%}: {%page_value|<?=$row['handle']?>%}">
                                                <?=t('Both');?>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane" id="user<?=$id;?>">

                <?php
                    $combined = ['label' => '', 'value' => '','both' => ''];
                    $rows = Converter::getUserTags();
                    foreach ($rows as $row) {
                        $combined['label'] .= '{%user_label|'.$row['handle'].'%}<br />';
                        $combined['value'] .= '{%user_value|'.$row['handle'].'%}<br />';
                        $combined['both'] .= '{%user_label|'.$row['handle'].'%}: {%user_value|'.$row['handle'].'%}<br />';
                    }
                ?>
                <div class="table-responsive">
                    <table class="table table-hover ccm-search-results-table">
                        <thead>
                            <tr>
                                <th><?=t('Name'); ?></th>
                                <th><?=t('Type'); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?=t('All properties'); ?></td>
                                <td><?=t('(combined)');?></td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="{%user_data%}">
                                            <?=t('Both');?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><?=t('All properties'); ?></td>
                                <td><?=t('(seperate tags)');?></td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="<?=$combined['label'];?>">
                                            <?=t('Label');?>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="<?=$combined['value'];?>">
                                            <?=t('Value');?>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-secondary" data-insert="<?=$combined['both'];?>">
                                            <?=t('Both');?>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <?php foreach ($rows as $row) { ?>
                                <tr>
                                    <td><?=$row['label']?></td>
                                    <td><?=$row['type']?></td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="#" class="btn btn-sm btn-secondary" data-insert="{%user_label|<?=$row['handle']?>%}">
                                                <?=t('Label');?>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-secondary" data-insert="{%user_value|<?=$row['handle']?>%}">
                                                <?=t('Value');?>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-secondary" data-insert="{%user_label|<?=$row['handle']?>%}: {%user_value|<?=$row['handle']?>%}">
                                                <?=t('Both');?>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>