<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Http\Request;
use Concrete\Package\Formidable\Src\Formidable\Formidable as Formidable;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Form\Service\Widget\FileFolderSelector;
use Concrete\Core\File\Set\Set as FileSet;

$form = app('helper/form');
$number = app('helper/number');
$token = app('helper/validation/token');
$page_selector = app('helper/form/page_selector');
$group_selector = app('helper/form/group_selector');
$folder_selector = new FileFolderSelector();
$editor = app('editor');
$request = app(Request::class);

$country = app('helper/lists/countries');
$countries = $country->getCountries();
foreach (array_keys($countries) as $countryID) {
    if (empty($countryID) || empty($countries[$countryID])) {
        unset($countries[$countryID]);
    }
}

// first get all types
// basiclly we want to force this sorting...
$types = [];
foreach (Formidable::getElementGroupName() as $handle => $name) {
    $types[$name] = [];
}

$available = [];
$additional = [];
$fields = [];

$elements = Formidable::getElementTypes();
$options = Element::getElementEditableOptions();

foreach ($elements as $handle => $type) {

    // insert into group
    $groupName = Formidable::getElementGroupName($type->getGroupHandle());
    $types[$groupName][$handle] = $type;

    if (!isset($fields[$handle])) {
        $fields[$handle] = [];
    }

    foreach (array_keys($options) as $option) {
        // fist set to false
        $additional[$option][$handle] = $options[$option];

        // then check for real
        $editable = $type->isEditableOption($option);
        if ($editable !== false) {
            $available[$option][] = $handle;
            if (is_array($editable)) {
                $additional[$option][$handle] = $editable;
            }
        }
    }
}

$elements = $ff->getElements();
foreach ((array)$elements as $el) {
    if (isset($element) && is_object($element)) {
        if ((float)$element->getItemID() == $el->getItemID()) {
            continue;
        }
    }
    $fields[$el->getType()][$el->getItemID()] = $el->getName();
}

?>

<?php $token->output($element->getItemID()!=0?'update_element':'add_element'); ?>
<?=$form->hidden('columnID', $column->getItemID()); ?>
<?=$form->hidden('elementID', (float)$element->getItemID()); ?>

<div class="row">
    <div class="col-12 col-lg-2">
        <a href="<?=URL::to('/dashboard/formidable/forms/layout', $ff->getItemID()); ?>" class="btn btn-sm btn-back mb-3">
            <svg><use xlink:href="#icon-arrow-left"></use></svg> <?=t('Back to layout');?>
        </a>
        <ul class="nav nav-pills flex-row flex-lg-column bg-light mb-3" id="elementTab">
            <li class="nav-item flex-fill">
                <a class="nav-link active" href="#basic" data-bs-toggle="tab">
                    <?=t('Basic');?>
                </a>
            </li>
            <li class="nav-item flex-fill" data-available-for="|<?=@implode('|', (array)$available['option']);?>|">
                <a class="nav-link" href="#option" data-bs-toggle="tab">
                    <?=t('Options');?>
                </a>
            </li>
            <li class="nav-item flex-fill">
                <a class="nav-link" href="#input" data-bs-toggle="tab">
                    <?=t('Input');?>
                </a>
            </li>
            <li class="nav-item flex-fill">
                <a class="nav-link" href="#appearance" data-bs-toggle="tab">
                    <?=t('Appearance');?>
                </a>
            </li>

            <li class="nav-item flex-fill" data-available-for="|<?=@implode('|', (array)$available['dependencies']);?>|">
                <a class="nav-link" href="#dependencies" data-bs-toggle="tab">
                    <?=t('Dependencies');?>
                </a>
            </li>

            <?php if (isset($error_reporting)) { ?>
                <li class="nav-item flex-fill" data-available-for="|<?=@implode('|', (array)$available['errors']);?>|">
                    <a class="nav-link" href="#errors" data-bs-toggle="tab">
                        <?=t('Error Reporting');?>
                    </a>
                </li>
            <?php } ?>

        </ul>
    </div>
    <div class="col-12 col-lg-10">

        <?php /* TAB CONTENT */ ?>

        <div class="tab-content">

            <div class="tab-pane active" id="basic">

                <div class="form-group">
                    <?=$form->label('elementType', t('Type')); ?>
                    <?=$form->select('elementType', array_filter($types), $element->getType());?>
                </div>

                <div class="row" data-available-for="|<?=@implode('|', (array)$available['name']);?>|">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <?=$form->label('elementName', t('Name')); ?>
                            <?=$form->text('elementName', $element->getName());?>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <?=$form->label('elementHandle', t('Handle')); ?>
                            <?=$form->text('elementHandle', $element->getHandle());?>
                        </div>
                    </div>
                </div>

                <div data-available-for="|<?=@implode('|', (array)$available['required']);?>|">
                    <?=$form->label('elementRequired', t('Required'));?>
                    <div class="form-group">
                        <label>
                            <?=$form->checkbox('elementRequired', 1, $element->isRequired()) ?>
                            <?=t('Required on form') ?>
                        </label>
                    </div>
                </div>

                <div data-available-for="|<?=@implode('|', (array)$available['wysiwyg']);?>|">
                    <div class="form-group">
                        <?=$form->label('wysiwyg', t('Message')) ?>
                        <?=$editor->outputStandardEditor('wysiwyg', $element->getProperty('wysiwyg')); ?>
                    </div>
                </div>

                <div data-available-for="|<?=@implode('|', (array)$available['code']);?>|">
                    <div class="form-group">
                        <?=$form->label('code', t('Code')) ?>
                        <div class="form-group">
                            <div id="code" class="html-value"><?=htmlspecialchars($element->getProperty('code'), ENT_QUOTES,APP_CHARSET) ?></div>
                            <?=$form->textarea('code', '', ['style' => 'display:none;', 'data-textarea' => 'code']);?>
                        </div>
                    </div>
                </div>

            </div>

            <?php /* NEXT TAB */ ?>

            <div class="tab-pane" id="input">

                <div class="row" data-available-for="|<?=@implode('|', (array)$available['default']);?>|">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <?=$form->label('default', t('Default value')); ?>
                            <?=$form->select('default', [0 => t('Disable'), 1 => t('Enable')], $element->getProperty('default'), ['data-onchange' => 'default']);?>
                        </div>
                    </div>
                    <div class="col-12 col-md-9" data-target="default" data-value="1">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <?=$form->label('default_type', t('Type')); ?>
                                    <?=$form->select('default_type', $options['default']['types'], $element->getProperty('default_type'), ['data-onchange' => 'default_type']);?>
                                </div>
                            </div>
                            <div class="col-12 col-md-8" data-target="default_type" data-value="value">
                                <div class="form-group">
                                    <?=$form->label('default_value', t('Value')); ?>
                                    <?=$form->text('default_value', $element->getProperty('default_value'));?>
                                </div>
                            </div>
                            <div class="col-12 col-md-8" data-target="default_type" data-value="request">
                                <div class="form-group">
                                    <?=$form->label('default_request', t('Parameter')); ?>
                                    <?=$form->text('default_request', $element->getProperty('default_request'));?>
                                </div>
                            </div>
                            <div class="col-12 col-md-8" data-target="default_type" data-value="page">
                                <div class="form-group">
                                    <?=$form->label('default_page', t('Select value')); ?>
                                    <?=$form->select('default_page', $options['default']['pages'], $element->getProperty('default_page'));?>
                                </div>
                            </div>
                            <div class="col-12 col-md-8" data-target="default_type" data-value="member">
                                <div class="form-group">
                                    <?=$form->label('default_member', t('Select value')); ?>
                                    <?=$form->select('default_member', $options['default']['members'], $element->getProperty('default_member'));?>
                                </div>
                            </div>
                        </div>
                        <?php if (isset($options['default']['help'])) { ?>
                            <div class="row">
                                <div class="col-12">
                                    <div class="help-block">
                                        <?=@implode('<br />', (array)$options['default']['help']);?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="row" data-available-for="|<?=@implode('|', (array)$available['placeholder']);?>|">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <?=$form->label('placeholder', t('Placeholder')); ?>
                            <?=$form->select('placeholder', [0 => t('Disable'), 1 => t('Enable')], $element->getProperty('placeholder'), ['data-onchange' => 'placeholder']);?>
                        </div>
                    </div>
                    <div class="col-12 col-md-9" data-target="placeholder" data-value="1">
                        <div class="form-group">
                            <?=$form->label('placeholder_value', t('Value')); ?>
                            <?=$form->text('placeholder_value', $element->getProperty('placeholder_value'));?>
                            <?php foreach ((array)$additional['placeholder'] as $handle => $types) { ?>
                                <?php if (isset($types['help'])) { ?>
                                    <div data-available-for="|<?=$handle;?>|">
                                        <div class="help-block">
                                            <?=@implode('<br />', (array)$types['help']);?>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="row" data-available-for="|<?=@implode('|', (array)$available['mask']);?>|">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <?=$form->label('mask', t('Mask input')); ?>
                            <?=$form->select('mask', [0 => t('Disable'), 1 => t('Enable')], $element->getProperty('mask'), ['data-onchange' => 'mask']);?>
                        </div>
                    </div>
                    <div class="col-12 col-md-9" data-target="mask" data-value="1">
                        <div class="form-group">
                            <?=$form->label('mask_value', t('Format')); ?>
                            <?=$form->text('mask_value', $element->getProperty('mask_value'));?>
                            <?php foreach ((array)$additional['mask'] as $handle => $types) { ?>
                                <?php if (isset($types['help'])) { ?>
                                    <div data-available-for="|<?=$handle;?>|">
                                        <div class="help-block">
                                            <?=@implode('<br />', (array)$types['help']);?>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="row" data-available-for="|<?=@implode('|', (array)$available['range']);?>|">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <?=$form->label('range', t('Range / Limit')); ?>
                            <?=$form->select('range', [0 => t('Disable'), 1 => t('Enable')], $element->getProperty('range'), ['data-onchange' => 'range']);?>
                        </div>
                    </div>
                    <div class="col-12 col-md-9" data-target="range" data-value="1">
                        <div class="row">
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <?=$form->label('range_min', t('Minimal')); ?>
                                    <?php
                                        $args = ['min' => 0, 'step' => 1];
                                        if (in_array($element->getType(), ['number'])) {
                                            $args = ['step' => 'any'];
                                        }
                                        echo $form->number('range_min', $element->getProperty('range_min'), $args);
                                    ?>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <?=$form->label('range_max', t('Maximal')); ?>
                                    <?php
                                        $args = ['min' => 1, 'step' => 1];
                                        if (in_array($element->getType(), ['number'])) {
                                            $args = ['step' => 'any'];
                                        }
                                        echo $form->number('range_max', $element->getProperty('range_max'), $args);
                                    ?>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <?php foreach ((array)$additional['range'] as $handle => $types) { ?>
                                    <?php if (isset($types['types'])) { ?>
                                        <div data-available-for="|<?=$handle;?>|">
                                            <div class="form-group">
                                                <?=$form->label('range_type', t('Type')); ?>
                                                <?=$form->select('range_type', $types['types'], $element->getProperty('range_type'));?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (isset($types['step'])) { ?>
                                        <div data-available-for="|<?=$handle;?>|">
                                            <div class="form-group">
                                                <?=$form->label('range_step', t('Steps')); ?>
                                                <?=$form->number('range_step', $element->getProperty('range_step'), ['step' => 'any']);?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        <?php foreach ((array)$additional['range'] as $handle => $types) { ?>
                            <?php if (isset($types['help'])) { ?>
                                <div class="row">
                                    <div class="col-12">
                                        <div data-available-for="|<?=$handle;?>|">
                                            <div class="help-block">
                                                <?=@implode('<br />', (array)$types['help']);?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

                <div class="row" data-available-for="|<?=@implode('|', (array)$available['confirm']);?>|">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <?=$form->label('confirm', t('Confirm / Match')); ?>
                            <?=$form->select('confirm', [0 => t('Disable'), 1 => t('Enable')], $element->getProperty('confirm'), ['data-onchange' => 'confirm']);?>
                        </div>
                    </div>
                    <div class="col-12 col-md-9" data-target="confirm" data-value="1">
                        <div class="form-group">
                            <?=$form->label('confirm_value', t('Field')); ?>
                            <?php foreach($fields as $handle => $flds) { ?>
                                <div data-available-for="|<?=$handle?>|">
                                    <?php if (count($fields[$handle])) { ?>
                                        <?=$form->select('confirm_value', $fields[$handle], $element->getProperty('confirm_value'));?>
                                    <?php } else { ?>
                                        <div class="help-block mt-0"><?=t('No other fields of the same type created');?></div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?php foreach ((array)$additional['confirm'] as $handle => $types) { ?>
                            <?php if (isset($types['confirm'])) { ?>
                                <div data-available-for="|<?=$handle;?>|">
                                    <div class="help-block">
                                    <?=@implode('<br />', (array)$types['confirm']);?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

                <div class="row" data-available-for="|<?=@implode('|', (array)$available['format']);?>|">
                    <div class="form-group">
                        <?=$form->label('format', t('Format')); ?>
                        <?php foreach ((array)$additional['format'] as $handle => $types) { ?>
                            <?php if (isset($types['types'])) { ?>
                                <div data-available-for="|<?=$handle;?>|">
                                    <div class="form-group">
                                        <?=$form->select('format', $types['types'], $element->getProperty('format'), ['data-onchange' => 'format']);?>
                                    </div>
                                    <div data-target="format" data-value="custom">
                                        <div class="form-group">
                                            <?=$form->text('format_other', $element->getProperty('format_other'), ['placeholder' => isset($additional['format'][$handle]['placeholder'])?$additional['format'][$handle]['placeholder']:'']);?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <?php foreach ((array)$additional['format'] as $handle => $types) { ?>
                            <?php if (isset($types['help'])) { ?>
                                <div data-available-for="|<?=$handle;?>|">
                                    <div class="help-block">
                                        <?=@implode('<br />', (array)$types['help']);?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

                <div class="row" data-available-for="|<?=@implode('|', (array)$available['country']);?>|">
                    <div class="col-12 col-md-6">

                        <div class="form-group">
                            <?=$form->label('', t('Available countries'));?>
                            <?=$form->select('country_custom', [0 => t('All'), 1 => t('Custom')], $element->getProperty('country_custom'), ['data-onchange' => 'country_custom']);?>
                        </div>
                        <div data-target="country_custom" data-value="1">
                            <?php
                                $selected = $element->getProperty('country_available');
                                if (is_object($selected)) {
                                    $selected = $selected->getValue();
                                }
                            ?>
                            <?=$form->selectMultiple('country_available', $countries, $selected, ['size' => 7]) ?>
                            <div class="help-block">
                                <?=t('Select the countries subscribers can choose from');?>
                            </div>
                        </div>

                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <?=$form->label('', t('Default country'));?>
                            <?=$form->select('country_default', $countries, $element->getProperty('country_default'));?>
                        </div>

                        <div class="form-group">
                            <label>
                                <?=$form->checkbox('country_ip', 1, $element->getProperty('country_ip')==1) ?>
                                <?=t('Suggest the Country from the user IP address') ?>
                            </label>
                        </div>

                        <?=$form->label('', t('Options for state/province/county'));?>
                        <div class="form-group">
                            <label>
                                <?=$form->checkbox('country_clearonchange', 1, $element->getProperty('country_clearonchange')==1) ?>
                                <?=t('Clear state/province/county field when country is changed') ?>
                            </label>
                        </div>

                        <div class="form-group">
                            <label>
                                <?=$form->checkbox('country_hideunused', 1, $element->getProperty('country_hideunused')==1) ?>
                                <?=t('Hide state/province/county field when country has no available options') ?>
                            </label>
                        </div>

                    </div>
                </div>

                <div class="row" data-available-for="|<?=@implode('|', (array)$available['extensions']);?>|">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <?=$form->label('extensions', t('Force extension(s)')); ?>
                            <?=$form->select('extensions', [0 => t('Disable'), 1 => t('Enable')], $element->getProperty('extensions'), ['data-onchange' => 'extensions']);?>
                        </div>
                    </div>
                    <div class="col-12 col-md-9" data-target="extensions" data-value="1">
                        <div class="form-group">
                            <?=$form->label('extensions_value', t('Extensions')); ?>
                            <?=$form->text('extensions_value', !empty($element->getProperty('extensions_value'))?$element->getProperty('extensions_value'):'jpg, gif, jpeg, png, tiff, docx, doc, xls, xlsx, csv, pdf', ['placeholder' => 'jpg, gif, jpeg, ... ']);?>
                            <?php foreach ((array)$additional['extensions'] as $handle => $types) { ?>
                                <?php if (isset($types['help'])) { ?>
                                    <div data-available-for="|<?=$handle;?>|">
                                        <div class="help-block">
                                            <?=@implode('<br />', (array)$types['help']);?>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="row" data-available-for="|<?=@implode('|', (array)$available['filesize']);?>|">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <?=$form->label('filesize', t('Uploaded filesize')); ?>
                            <?=$form->select('filesize', [0 => t('Disable'), 1 => t('Enable')], $element->getProperty('filesize'), ['data-onchange' => 'filesize']);?>
                        </div>
                    </div>
                    <div class="col-12 col-md-9" data-target="filesize" data-value="1">
                        <?php $max_filesize = $number->formatSize($number->getBytes(ini_get('upload_max_filesize')), 'MB'); ?>
                        <div class="form-group">
                            <?=$form->label('filesize_value', t('Max filesize')); ?>
                            <div class="input-group">
                                <?=$form->number('filesize_value', !empty($element->getProperty('filesize_value'))?$element->getProperty('filesize_value'):'5', ['min' => 0.1, 'max' => (int)$max_filesize, 'step' => 0.1, 'placeholder' => '5']);?>
                                <div class="input-group-append">
                                    <div class="input-group-text"><?=t('MB');?></div>
                                </div>
                            </div>
                            <div class="help-block">
                                <?=t('Set the maximum filesize for each uploaded file. (not larger then %s)', $max_filesize);?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" data-available-for="|<?=@implode('|', (array)$available['advanced']);?>|">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <?=$form->label('advanced', t('Advanced settings')); ?>
                            <?=$form->select('advanced', [0 => t('Disable'), 1 => t('Enable')], $element->getProperty('advanced'), ['data-onchange' => 'advanced']);?>
                        </div>
                    </div>
                    <div class="col-12 col-md-9" data-target="advanced" data-value="1">
                        <div class="form-group">
                            <?=$form->label('advanced_value', t('Format')); ?>
                            <?=$form->text('advanced_value', $element->getProperty('advanced_value'));?>
                            <?php foreach ((array)$additional['advanced'] as $handle => $types) { ?>
                                <?php if (isset($types['help'])) { ?>
                                    <div data-available-for="|<?=$handle;?>|">
                                        <div class="help-block">
                                            <?=@implode('<br />', (array)$types['help']);?>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="row" data-available-for="|<?=@implode('|', (array)$available['folder']);?>|">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <?=$form->label('folder', t('Upload files in folder')); ?>
                            <?=$form->select('folder', [0 => t('Disable'), 1 => t('Enable')], $element->getProperty('folder'), ['data-onchange' => 'folder']);?>
                        </div>
                    </div>
                    <div class="col-12 col-md-9" data-target="folder" data-value="1">
                        <div class="form-group">
                            <?=$form->label('folder_value', t('Select folder')); ?>
                            <?=$folder_selector->selectFileFolder('folder_value', $element->getProperty('folder_value', 'int')); ?>
                            <?php foreach ((array)$additional['folder'] as $handle => $types) { ?>
                                <?php if (isset($types['help'])) { ?>
                                    <div data-available-for="|<?=$handle;?>|">
                                        <div class="help-block">
                                            <?=@implode('<br />', (array)$types['help']);?>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="row" data-available-for="|<?=@implode('|', (array)$available['fileset']);?>|">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <?=$form->label('fileset', t('Assign files to fileset')); ?>
                            <?=$form->select('fileset', [0 => t('Disable'), 1 => t('Enable')], $element->getProperty('fileset'), ['data-onchange' => 'fileset']);?>
                        </div>
                    </div>
                    <div class="col-12 col-md-9" data-target="fileset" data-value="1">
                        <div class="form-group">
                            <?=$form->label('fileset_value', t('Select fileset')); ?>
                            <?php
                                $sets = FileSet::getMySets();
                                if (count($sets)) {
                                    foreach ((array)$sets as $set) { ?>
                                        <div class="form-group">
                                            <label>
                                                <?=$form->checkbox('fileset_value[]', $set->getFileSetID(), in_array($set->getFileSetID(), $element->getProperty('fileset_value', 'array'))) ?>
                                                <?=$set->getFileSetName() ?>
                                            </label>
                                        </div>
                                <?php } ?>
                            <?php } else { ?>
                                <div class="help-block mt-0"><?=t('No filesets created');?></div>
                            <?php } ?>
                            <?php foreach ((array)$additional['fileset'] as $handle => $types) { ?>
                                <?php if (isset($types['help'])) { ?>
                                    <div data-available-for="|<?=$handle;?>|">
                                        <div class="help-block">
                                            <?=@implode('<br />', (array)$types['help']);?>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php /* NEXT TAB */ ?>

            <div class="tab-pane" id="appearance">

                <div class="row" data-available-for="|<?=@implode('|', (array)$available['view']);?>|">
                    <div class="form-group">
                        <?=$form->label('view', t('Template (view)')); ?>
                        <?php foreach ((array)$additional['view'] as $handle => $types) { ?>
                            <?php if (isset($types['types'])) { ?>
                                <div data-available-for="|<?=$handle;?>|">
                                    <div class="form-group">
                                        <?=$form->select('view', $types['types'], $element->getProperty('view'));?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <?php foreach ((array)$additional['view'] as $handle => $types) { ?>
                            <?php if (isset($types['help'])) { ?>
                                <div data-available-for="|<?=$handle;?>|">
                                    <div class="help-block">
                                        <?=@implode('<br />', (array)$types['help']);?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

                <div data-available-for="|<?=@implode('|', (array)$available['no_label']);?>|">
                    <?=$form->label('no_label', t('Hide label'));?>
                    <div class="form-group">
                        <label>
                            <?=$form->checkbox('no_label', 1, $element->getProperty('no_label')==1) ?>
                            <?=t('Hide label on form') ?>
                        </label>
                    </div>
                </div>

                <div data-available-for="|<?=@implode('|', (array)$available['labels_vs_placeholder']);?>|">
                    <div class="form-group">
                        <?=$form->label('labels_vs_placeholder', t('Labels vs placeholder')); ?>
                        <div>
                            <label>
                                <?=$form->checkbox('labels_vs_placeholder', 1, $element->getProperty('labels_vs_placeholder')==1) ?>
                                <?=t('Prepend labels, instead of placeholders in the fields') ?>
                            </label>
                        </div>
                        <div class="help-block">
                            <?=t('In case you want to use labels instead of the default placeholder.');?>
                        </div>
                    </div>
                </div>

                <div class="row" data-available-for="|<?=@implode('|', (array)$available['help']);?>|">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <?=$form->label('help', t('Help / Description')); ?>
                            <?=$form->select('help', [0 => t('Disable'), 1 => t('Enable')], $element->getProperty('help'), ['data-onchange' => 'help']);?>
                        </div>
                    </div>
                    <div class="col-12 col-md-9" data-target="help" data-value="1">
                        <div class="form-group">
                            <?=$form->label('help_value', t('Text')); ?>
                            <?=$form->textarea('help_value', $element->getProperty('help_value'));?>
                        </div>
                        <?php foreach ((array)$additional['help'] as $handle => $types) { ?>
                            <?php if (isset($types['help'])) { ?>
                                <div data-available-for="|<?=$handle;?>|">
                                    <div class="help-block">
                                        <?=@implode('<br />', (array)$types['help']);?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

                <div class="row" data-available-for="|<?=@implode('|', (array)$available['css']);?>|">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <?=$form->label('css', t('Additional CSS-class(es)')); ?>
                            <?=$form->select('css', [0 => t('Disable'), 1 => t('Enable')], $element->getProperty('css'), ['data-onchange' => 'css']);?>
                        </div>
                    </div>
                    <div class="col-12 col-md-9" data-target="css" data-value="1">
                        <div class="form-group">
                            <?=$form->label('css_value', t('Class(es)')); ?>
                            <?=$form->text('css_value', $element->getProperty('css_value'));?>
                            <?php foreach ((array)$additional['css'] as $handle => $types) { ?>
                                <?php if (isset($types['help'])) { ?>
                                    <div data-available-for="|<?=$handle;?>|">
                                        <div class="help-block">
                                            <?=@implode('<br />', (array)$types['help']);?>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            </div>

            <?php /* NEXT TAB */ ?>

            <div class="tab-pane" id="option">

                <div class="form-group">
                    <?=$form->label('option_type', t('Options').' / '.t('Selectables')); ?>
                    <?php foreach ((array)$additional['option'] as $handle => $options) { ?>
                        <?php if (isset($options['types'])) { ?>
                            <div data-available-for="|<?=$handle;?>|">
                                <?=$form->select('option_type', $options['types'], $element->getProperty('option_type'), ['data-onchange' => 'option_type']);?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>

                <div data-target="option_type" data-value="manual">

                    <?php
                        $opts = [];
                        if ($request->isPost()) {
                            $opts = $request->post('option_value');
                        }
                        else {
                            $opt = $element->getProperty('option_value');
                            if (is_object($opt)) {
                                $opts = $opt->getValue();
                            }
                        }
                        if (!count((array)$opts)) {
                            $opts = ['0' => []];
                        }
                    ?>

                    <div class="row option">
                        <?php /*
                        <div class="col-auto mt-2 pl-3">
                            <?=$form->radio('dummy', 0, ['disabled' => 'disabled', 'style' => 'visibility: hidden;']);?>
                        </div>
                        */ ?>
                        <div class="col-5">
                            <?=$form->label('dummy', t('Value')); ?>
                        </div>
                        <div class="col">
                            <?=$form->label('dummy', t('Name')); ?>
                        </div>
                    </div>

                    <div data-option-rows>
                        <?php foreach ($opts as $ko => $option) { ?>
                            <div class="row option mb-2">
                                <?php /*
                                <div class="col-auto mt-2 pl-3">
                                    <?php
                                        $type = 'radio';
                                        if ($element->getProperty('option_multiple') == 1) {
                                            $type = 'checkbox';
                                        }
                                        echo $form->{$type}('option_value['.$ko.'][default]', 1, isset($option['default'])?(float)$option['default']==1:false);
                                    ?>
                                </div>
                                */ ?>
                                <div class="col-5">
                                    <?=$form->text('option_value['.$ko.'][value]', isset($option['value'])?$option['value']:'', ['data-option-value' => '']);?>
                                </div>
                                <div class="col">
                                    <?=$form->text('option_value['.$ko.'][name]', isset($option['name'])?$option['name']:'', ['data-option-name' => '']);?>
                                </div>
                                <div class="col-auto">
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-secondary btn-sq" data-row-move data-bs-toggle="tooltip" data-bs-title="<?=t('Move');?>">
                                            <i class="fa-fw fa fa-arrows-alt-v"></i>
                                        </a>
                                        <a href="#" class="btn btn-secondary btn-sq text-danger" data-remove-option data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
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
                                <a class="btn btn-sm btn-primary" data-add-option data-row-count="-1">
                                    <?=t('Add option');?>
                                </a>
                                <a class="btn btn-sm btn-secondary" data-multiple-option="<?=$element->getItemID();?>">
                                    <?=t('Add multiple');?>
                                </a>
                                <a class="btn btn-sm btn-danger" data-clear-option="<?=$element->getItemID();?>">
                                    <?=t('Clear all');?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div data-template-option class="hide" style="display:none">
                        <div class="row option mb-2">
                            <?php /*
                            <div class="col-auto mt-2 pl-3">
                                <?=$form->radio('option_value[_tmp][default]', 1, ['disabled' => 'disabled']);?>
                            </div>
                            */ ?>
                            <div class="col-5">
                                <?=$form->text('option_value[_tmp][value]', '', ['disabled' => 'disabled', 'data-option-value' => '']);?>
                            </div>
                            <div class="col">
                                <?=$form->text('option_value[_tmp][name]', '', ['disabled' => 'disabled', 'data-option-name' => '']);?>
                            </div>
                            <div class="col-auto">
                                <div class="btn-group">
                                    <a href="#" class="btn btn-secondary btn-sq" data-row-move data-bs-toggle="tooltip" data-bs-title="<?=t('Move');?>">
                                        <i class="fa-fw fa fa-arrows-alt-v"></i>
                                    </a>
                                    <a href="#" class="btn btn-secondary btn-sq text-danger" data-remove-option data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
                                        <i class="fa-fw fa fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div data-target="option_type" data-value="pages">

                    <?php
                        $types = [];
                        $page_types = $element->getProperty('option_page_type');
                        if (is_object($page_types)) {
                            $types = $page_types->getValue();
                        }
                    ?>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <?=$form->label('option_page_type', t('Page Type')); ?>
                                <?php foreach ((array)$additional['option'] as $handle => $options) { ?>
                                    <?php if (isset($options['pages']['types'])) { ?>
                                        <div data-available-for="|<?=$handle;?>|">
                                            <div class="form-group">
                                                <?php foreach($options['pages']['types'] as $ptID => $ptName) { ?>
                                                    <label class="d-block">
                                                        <?=$form->checkbox('option_page_type[]', $ptID, in_array($ptID, (array)$types)) ?>
                                                        <?=$ptName ?>
                                                    </label>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="form-group mt-2">
                                <?=$form->label('option_page_location', t('Location')); ?>
                                <?=$form->select('option_page_location', [0 => t('Everywhere'), 'beneath' => t('Beneath page')], $element->getProperty('option_page_location'), ['data-onchange' => 'option_page_location']);?>
                            </div>
                            <div class="form-group"  data-target="option_page_location" data-value="beneath">
                                <?=$form->label('option_page_location_value', t('Select page')); ?>
                                <?=$page_selector->selectPage('option_page_location_value', $element->getProperty('option_page_location_value')) ?>
                            </div>
                        </div>

                        <div class="col-12 col-md-8">
                            <div class="form-group">
                                <?=$form->label('option_page_name', t('Option Name')); ?>
                                <?=$form->select('option_page_name', isset($options['pages']['properties'])?$options['pages']['properties']:[], $element->getProperty('option_page_name'));?>
                                <div class="help-block"><?=t('Will be shown as selectable value');?></div>
                            </div>
                            <div class="form-group">
                                <?=$form->label('option_member_name', t('Options'));?>
                                <label class="d-block">
                                    <?=$form->checkbox('option_page_aliasses', 1, $element->getProperty('option_page_aliasses')==1) ?>
                                    <?=t('Display page aliasses') ?>
                                </label>
                                <label class="d-block">
                                    <?=$form->checkbox('option_page_featured', 1, $element->getProperty('option_page_featured')==1) ?>
                                    <?=t('Show featured pages only') ?>
                                </label>
                                <label class="d-block mb-2">
                                    <?=$form->checkbox('option_page_empty', 1, $element->getProperty('option_page_empty')==1) ?>
                                    <?=t('Only display pages where "%s" is not empty', t('Option Name')) ?>
                                </label>
                            </div>

                            <div class="form-group">
                                <?=$form->label('option_page_order', t('Sort order')); ?>
                                <?=$form->select('option_page_order', ['display_asc' => t('Sitemap order'), 'display_desc' => t('Reverse sitemap order'), 'chrono_desc' => t('Most recent first'), 'chrono_asc' => t('Earliest first'), 'alpha_asc' => t('Alphabetical order'), 'alpha_desc' => t('Reverse alphabetical order'), 'modified_desc' => t('Most recently modified first'), 'random' => t('Random')], $element->getProperty('option_page_order'));?>
                            </div>
                        </div>

                    </div>

                    <?php if (isset($options['pages']['help'])) { ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="help-block">
                                    <?=@implode('<br />', (array)$options['pages']['help']);?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>


                <div data-target="option_type" data-value="members">

                    <?php
                        $groups = [];
                        $member_groups = $element->getProperty('option_member_group');
                        if (is_object($member_groups)) {
                            $groups = $member_groups->getValue();
                        }
                    ?>

                    <div class="row">
                        <div class="col-12 col-md-5">
                            <div class="form-group">
                                <?=$form->label('option_member_group', t('Groups')); ?>
                                <?=$group_selector->selectGroupWithTree('option_member_group'); ?>
                                <?php /*
                                // DISABLED FOR NOW. WE USE THE CORE SELECTOR FOR GROUPS
                                <?php foreach ((array)$additional['option'] as $handle => $options) { ?>
                                    <?php if (isset($options['members']['groups'])) { ?>
                                        <div data-available-for="|<?=$handle;?>|">
                                            <div class="form-group">
                                                <?php foreach($options['members']['groups'] as $gID => $gName) { ?>
                                                    <label class="d-block mb-2">
                                                        <?=$form->checkbox('option_member_group[]', $gID, in_array($ptID, (array)$groups)) ?>
                                                        <?=$gName ?>
                                                    </label>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                */ ?>
                            </div>
                        </div>
                        <div class="col-12 col-md-7">
                            <div class="form-group">
                                <?=$form->label('option_member_name', t('Option Name')); ?>
                                <?=$form->select('option_member_name', isset($options['members']['properties'])?$options['members']['properties']:[], $element->getProperty('option_member_name'));?>
                                <div class="help-block"><?=t('Will be shown as selectable value');?></div>
                            </div>

                            <?=$form->label('option_member_empty', t('Options'));?>
                            <div class="form-group">
                                <label class="d-block">
                                    <?=$form->checkbox('option_member_active', 1, $element->getProperty('option_member_active')==1) ?>
                                    <?=t('Show active members only') ?>
                                </label>
                                <label class="d-block">
                                    <?=$form->checkbox('option_member_valid', 1, $element->getProperty('option_member_valid')==1) ?>
                                    <?=t('Show valid members only') ?>
                                </label>
                                <label class="d-block mb-2">
                                    <?=$form->checkbox('option_member_empty', 1, $element->getProperty('option_member_empty')==1) ?>
                                    <?=t('Only display users where "%s" is not empty', t('Option Name')) ?>
                                </label>
                            </div>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-12 col-md-7">
                            <div class="form-group">
                                <?=$form->label('option_member_sort_by', t('Sort By')); ?>
                                <?=$form->select('option_member_sort_by', isset($options['members']['properties'])?$options['members']['properties']:[], $element->getProperty('option_member_sort_by'));?>
                            </div>
                        </div>
                        <div class="col-12 col-md-5">
                            <div class="form-group">
                                <?=$form->label('option_member_sort_order', t('Sort order')); ?>
                                <?=$form->select('option_member_sort_order', ['asc' => t('Ascending'), 'desc' => 'Descending'], $element->getProperty('option_member_sort_order'));?>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($options['pages']['help'])) { ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="help-block">
                                    <?=@implode('<br />', (array)$options['pages']['help']);?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <?php foreach ((array)$additional['option'] as $handle => $options) { ?>
                    <?php if (isset($options['help'])) { ?>
                        <div data-available-for="|<?=$handle;?>|">
                            <div class="help-block">
                                <?=@implode('<br />', (array)$options['help']);?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>

                <?php foreach ((array)$additional['option'] as $handle => $options) { ?>
                    <?php if (isset($options['other'])) { ?>
                        <div data-available-for="|<?=$handle;?>|" class="mt-3">
                            <div class="row">
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <?=$form->label('option_other', t('"Other"-option')); ?>
                                        <?=$form->select('option_other', [0 => t('Disable'), 1 => t('Enable')], $element->getProperty('option_other'), ['data-onchange' => 'option_other']);?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-9" data-target="option_other" data-value="1">
                                    <div class="row">
                                        <?php if (isset($options['other']['types'])) { ?>
                                            <div class="col-12 col-md-5">
                                                <div class="form-group">
                                                    <?=$form->label('option_other_type', t('Element Type')); ?>
                                                    <?=$form->select('option_other_type', $options['other']['types'], $element->getProperty('option_other_type'));?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-12 col-md-7">
                                            <div class="form-group">
                                                <?=$form->label('option_other_value', t('Name')); ?>
                                                <?=$form->text('option_other_value', $element->getProperty('option_other_value'));?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (isset($options['other']['help'])) { ?>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="help-block">
                                                    <?=@implode('<br />', (array)$options['other']['help']);?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>


                <?php foreach ((array)$additional['option'] as $handle => $options) { ?>
                    <?php if (isset($options['multiple'])) { ?>
                        <div data-available-for="|<?=$handle;?>|">
                            <?=$form->label('option_multiple', t('Allow multiple'));?>
                            <div class="form-group">
                                <label>
                                    <?=$form->checkbox('option_multiple', 1, $element->getProperty('option_multiple')==1) ?>
                                    <?=t('Allow multiple selected options') ?>
                                </label>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>

            </div>


            <?php /* NEXT TAB */ ?>

            <div class="tab-pane" id="dependencies">
                <?php View::element('form/layout/element/dependency', ['ff' => $ff, 'element' => $element?$element:'', 'available' => $available], 'formidable'); ?>
            </div>

            <?php if (isset($error_reporting)) { ?>


                <?php /* NEXT TAB */ ?>

                <div class="tab-pane" id="errors">

                </div>

            <?php } ?>

        </div>
    </div>
</div>

<style>
    .html-value { width: 100%;  border: 1px solid #eee; height: 245px;  }
</style>

<script>

    $(function() {

        $('[name="elementName"]').on('blur', function() {
            var handle = $('[name="elementHandle"]').val();
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
                    type: 'element',
                    form: <?=(int)$ff->getItemID();?>,
                    current: <?=(int)$element->getItemID();?>
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
                    $('[name="elementHandle"]').val(data.success.handle);
                    jQuery.fn.dialog.hideLoader();
                }
            });
        });

        $('[data-multiple-option]').on('click', function() {
            var itemID = $(this).attr('data-multiple-option');
            jQuery.fn.dialog.open({
                element: 'div[data-multiple-option]',
                modal: true,
                width: 600,
                title: '<?=t('Add multiple options at once'); ?>',
                height: 500,
                onOpen: function() {
                    $('form[data-multiple-option] input[id="elementID"]').val(itemID);
                }
            });
        });
        $('form[data-multiple-option]').on('submit', function(e) {
            e.preventDefault();
            var clear = $('input[id="clear"]', $(this)).is(':checked')?1:0;
            if (clear) {
                $('[data-option-rows]').empty();
            }
            else {
                // check if first row is empty
                var rows = $('[data-option-rows] div.row.option');
                if (rows.length == 1) {
                    if ($('[data-option-value]', rows[0]).val() == '' && $('[data-option-name]', rows[0]).val() == '') {
                        $('[data-option-rows]').empty();
                    }
                }
            }
            $.each($('textarea[id="options"]', $(this)).val().split(/\n/), function(i, value) {
                if (value.length > 0) {
                    var name = value;
                    if (value.indexOf(';') > 0) {
                        value = value.split(";", 2);
                        if (value.length == 1) {
                            name = value[0];
                        }
                        else if (value.length == 2) {
                            name = value[1];
                            value = value[0];
                        }
                    }
                    $('[data-add-option]').trigger('click');
                    var last = $('[data-option-rows] div.row.option:last-child');

                    $('[data-option-value]', last).val(value);
                    $('[data-option-name]', last).val(name);
                }
            });
            ConcreteAlert.notify({
                title: '<?=t('Success');?>',
                message: '<?=t('New options successfully added!');?>'
            });
            $(this).trigger('reset');
            jQuery.fn.dialog.hideLoader();
            jQuery.fn.dialog.closeTop();
        });

        $('[data-clear-option]').on('click', function() {
            var itemID = $(this).attr('data-clear-option');
            jQuery.fn.dialog.open({
                element: 'div[data-clear-option]',
                modal: true,
                width: 400,
                title: '<?=t('Remove all options'); ?>',
                height: 250,
                onOpen: function() {
                    $('form[data-clear-option] input[id="elementID"]').val(itemID);
                }
            });
        });

        $('form[data-clear-option]').on('submit', function(e) {
            e.preventDefault();
            $('[data-option-rows]').empty();
            $('[data-add-option]').trigger('click');
            ConcreteAlert.notify({
                title: '<?=t('Success');?>',
                message: '<?=t('All options are successfully removed!');?>'
            });
            jQuery.fn.dialog.hideLoader();
            jQuery.fn.dialog.closeTop();
        });

        var code = ace.edit("code");
        code.setTheme("ace/theme/eclipse");
        code.setShowPrintMargin(false);
        code.getSession().setMode("ace/mode/html");
        $('[data-textarea="code"]').val(code.getValue());
        code.getSession().on('change', function() {
            $('[data-textarea="code"]').val(code.getValue());
        });

        $('a[href="#dependencies"]').on('click', function() {

            $('[id="option"]').wrap('<form class="hide"></form>');
            var row = $('[id="option"]').closest('form');

            $.ajax({
                url: '<?=$view->action('options');?>',
                method: 'POST',
                dataType: 'json',
                data: row.serialize()+'&type='+$('[name="elementType"]').val()+'&ccm_token=<?=$token->generate('generate_options');?>',
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

                    var options = data.success.options;

                    $.each($('[data-dependency-action-value-for="value"] select'), function(i, select) {
                        select = $(select);
                        var selected = select.val();
                        // clear all
                        select.find('option').remove();
                        // now set new ones
                        for (var o = 0; o < options.length; o++) {
                            var option = $('<option>').attr('value', options[o].value).text(options[o].name);
                            if (options[o].value == selected) {
                                option.attr('selected', 'selected');
                            }
                            select.append(option);
                        }
                    });

                    var actions = data.success.actions;

                    $.each($('[data-dependency-action-value]'), function(i, select) {
                        select = $(select);
                        var selected = select.val();
                        // clear all
                        select.find('option').remove();
                        // now set new ones
                        for (var o = 0; o < actions.length; o++) {
                            var option = $('<option>').attr('value', actions[o].value).text(actions[o].name);
                            if (actions[o].value == selected) {
                                option.attr('selected', 'selected');
                            }
                            select.append(option);
                        }
                    });
                },
                complete: function() {
                    $('[id="option"]').unwrap();

                    jQuery.fn.dialog.hideLoader();
                }
            })
        });

        FormidableDependencyElements();
        FormidableCheckElements();

    });

</script>