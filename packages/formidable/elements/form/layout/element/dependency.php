<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Symfony\Component\HttpFoundation\Session\Session;

$form = app('helper/form');
$token = app('helper/validation/token');
$session = app(Session::class);

$rules = [];

if (isset($element) && is_object($element)) {
    $rules = $element->getProperty('dependency', 'array');

    $default_action = 'show';
    $actions = $element->getTypeObject()->getDependencyActions();

    // any dependencies saved?
    $saved_dependencies = $session->get('savedElementDependency['.$ff->getItemID().']');
}

if (isset($mail) && is_object($mail)) {
    $rules = $mail->getDependencies();

    $default_action = 'send';
    $actions = ['send' => t('Send'), 'not_send' => t('Do not send')];

    // any dependencies saved?
    $saved_dependencies = $session->get('savedMailDependency['.$ff->getItemID().']');
}

// check if elements are still in rules...
foreach ($rules as $rk => $rv) {
    foreach ($rv['selector'] as $sk => $sv) {
        if (!isset($sv['element']) || (isset($sv['element']) && !$ff->getElementByID($sv['element']))) {
            // remove selector rule
            unset($rules[$rk]['selector'][$sk]);

            // see if any selector is still there
            if (!count($rules[$rk]['selector'])) {
                unset($rules[$rk]);
            }
        }
    }
}

$dependency = [
    'fields' => [],
    'conditions' => [],
    'values' => [],
];
$conditions = [];

$elements = $ff->getElements();
foreach ((array)$elements as $el) {

    if (isset($element) && is_object($element)) {
        if ((float)$element->getItemID() == $el->getItemID()) {
            continue;
        }
    }

    // are depedencies enabled?
    $deps = $el->getTypeObject()->isEditableOption('dependencies', 'array');
    if ($deps === false) {
        continue;
    }
    if (isset($deps['condition']) && !count($deps['condition'])) {
        continue;
    }

    // set fields for dependencies
    $dependency['fields'][$el->getItemID()] = $el->getName();

    // set conditions
    $dependency['conditions'][$el->getItemID()] = 'other';
    if ($el->getTypeObject()->isEditableOption('option', 'bool') !== false) {
        $dependency['conditions'][$el->getItemID()] = $el->getTypeObject()->getOptions();
    }

    $conditions[$el->getItemID()] = $el->getTypeObject()->getDependencyConditions();
}

// get available options if selected type has options
if (isset($element) && is_object($element) && $element->getTypeObject()->isEditableOption('option', 'bool')) {
    $dependency['values'] = ['' => t('Select value')] + $element->getTypeObject()->getOptions();
}

?>

<?php if (!count($dependency['fields'])) { ?>

    <div class="help-block mt-0 text-center">
        <?=t('No (other) fields available for setting up dependencies'); ?>
    </div>

<?php } else { ?>

    <div data-no-dependency class="help-block mt-0 text-center">
        <?=t('You have not created any dependency rules'); ?>
    </div>

    <div data-option-dependencies <?=isset($available['dependencies'])?'data-available-for="|'.implode('|', (array)$available['dependencies']).'|"':'';?>>

        <?php $tmp = 1; ?>
        <?php foreach ($rules as $rk => $rv) { ?>
            <div class="dependency mb-3">

                <div class="or">
                    <h4><?=t('or');?></h4>
                </div>

                <div class="card" data-rule-id="<?=$rk;?>">
                    <div class="card-header form-column-header">
                        <?=t('Rule #%s', $rk)?>
                        <div class="btn-group float-end">
                            <a href="#" class="btn btn-secondary btn-sm text-danger" data-remove-dependency data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
                                <i class="fa-fw fa fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div data-option-dependencies-actions class="actions" data-current-rule="<?=$rk;?>" data-action-count="-1">
                            <?php foreach ($rv['action'] as $ak => $av) { ?>
                                <div class="row mb-3 action">
                                    <div class="col">
                                        <div class="input-group">
                                            <span class="input-group-text"><?=t('and');?></span>
                                            <?=$form->select('dependency['.$rk.'][action]['.$ak.'][0]', $actions, isset($av[0])?$av[0]:$default_action, ['data-dependency-action-value' => 1]);?>
                                        </div>
                                    </div>
                                    <?php if (isset($element) && is_object($element)) { ?>
                                        <div class="col">
                                            <div data-dependency-action-value-for="value">
                                                <div data-available-for="|<?=implode('|', (array)$available['option']);?>|">
                                                    <?=$form->select('dependency['.$rk.'][action]['.$ak.'][1]', $dependency['values'], isset($av[1])?$av[1]:'');?>
                                                </div>
                                                <div data-available-for-other>
                                                    <?=$form->text('dependency['.$rk.'][action]['.$ak.'][1]', isset($av[1])?$av[1]:'', ['placeholder' => t('Value...')]);?>
                                                </div>
                                            </div>
                                            <div data-dependency-action-value-for="class">
                                                <?=$form->text('dependency['.$rk.'][action]['.$ak.'][1]', isset($av[1])?$av[1]:'', ['placeholder' => t('Class...')]);?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="col-auto">
                                        <div class="btn-group">
                                            <a href="#" class="btn btn-secondary btn-sq" data-add-action data-bs-toggle="tooltip" data-bs-title="<?=t('Add');?>">
                                                <i class="fa-fw fa fa-plus"></i>
                                            </a>
                                            <a href="#" class="btn btn-secondary btn-sq text-danger" data-remove-row data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
                                                <i class="fa-fw fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php }  ?>
                        </div>
                        <div data-option-dependencies-selector class="selector" data-current-rule="<?=$rk;?>" data-selector-count="-1">
                            <?php foreach ($rv['selector'] as $sk => $sv) { ?>
                                <div class="row element">
                                    <div class="col field">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text first"><?=t('if');?></span>
                                            <span class="input-group-text"><?=t('and if');?></span>
                                            <?=$form->select('dependency['.$rk.'][selector]['.$sk.'][element]', $dependency['fields'], $sv['element'], ['data-selector' => 1]);?>
                                        </div>
                                    </div>
                                    <div class="col-auto btns">
                                        <div class="btn-group">
                                            <a href="#" class="btn btn-secondary btn-sq" data-add-selector data-bs-toggle="tooltip" data-bs-title="<?=t('Add');?>">
                                                <i class="fa-fw fa fa-plus"></i>
                                            </a>
                                            <a href="#" class="btn btn-secondary btn-sq text-danger" data-remove-row data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
                                                <i class="fa-fw fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div data-option-dependencies-conditions class="conditions" data-current-selector="<?=$sk;?>" data-condition-count="-1">
                                            <?php foreach ($sv['condition'] as $ck => $cv) { ?>
                                                <div class="row condition">
                                                    <div class="col">
                                                        <?php foreach ((array)$dependency['conditions'] as $handle => $options) { ?>
                                                            <?php if (is_array($options)) { ?>
                                                                <div class="input-group mb-3" data-dependency-condition-for="<?=$handle;?>">
                                                                    <span class="input-group-text first"><?=t('has');?></span>
                                                                    <span class="input-group-text"><?=t('and has');?></span>
                                                                    <?=$form->select('dependency['.$rk.'][selector]['.$sk.'][condition]['.$ck.'][value]', ['any' => t('any option'), 'non' => t('non option')] + $options, isset($cv['value'])?$cv['value']:'');?>
                                                                    <span class="input-group-text"><?=t('selected/checked');?></span>
                                                                </div>
                                                            <?php } else { ?>
                                                                <div class="row mb-3" data-dependency-condition-for="<?=$handle;?>">
                                                                    <div class="col">
                                                                        <div class="input-group">
                                                                            <span class="input-group-text"><?=t('and');?></span>
                                                                            <?=$form->select('dependency['.$rk.'][selector]['.$sk.'][condition]['.$ck.'][condition]', (array)$conditions[$handle], isset($cv['condition'])?$cv['condition']:'', ['data-dependency-condition-value' => true]);?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col" data-dependency-condition-value-for="value">
                                                                        <?=$form->text('dependency['.$rk.'][selector]['.$sk.'][condition]['.$ck.'][value]', isset($cv['value'])?$cv['value']:'', ['placeholder' => t('Value...')]);?>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="btn-group">
                                                            <a href="#" class="btn btn-secondary btn-sq" data-add-condition data-bs-toggle="tooltip" data-bs-title="<?=t('Add');?>">
                                                                <i class="fa-fw fa fa-plus"></i>
                                                            </a>
                                                            <a href="#" class="btn btn-secondary btn-sq text-danger" data-remove-row data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
                                                                <i class="fa-fw fa fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php }  ?>
    </div>

    <div data-template-dependency class="hide" style="display:none">
        <div class="dependency mb-3">

            <div class="or">
                <h4><?=t('or');?></h4>
            </div>

            <div class="card" data-rule-id="_tmp">
                <div class="card-header form-column-header">
                    <?=t('Rule #%s', '_tmp')?>
                    <div class="btn-group float-end">
                        <a href="#" class="btn btn-secondary btn-sm text-danger" data-remove-dependency data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
                            <i class="fa-fw fa fa-trash"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div data-option-dependencies-actions class="actions" data-current-rule="_tmp" data-action-count="-2">
                        <!-- here the actions -->
                    </div>
                    <div data-option-dependencies-selector class="selector" data-current-rule="_tmp" data-selector-count="-2">
                        <!-- here the selectors -->
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div data-template-dependency-action class="hide" style="display:none">

        <div class="row mb-3 action">
            <div class="col">
                <div class="input-group">
                    <span class="input-group-text"><?=t('and');?></span>
                    <?=$form->select('dependency[_tmp][action][_tmp_action][0]', $actions, $default_action, ['disabled' => 'disabled', 'data-dependency-action-value' => 1, 'data-action-type' => 1]);?>
                </div>
            </div>
            <div class="col">
                <div data-dependency-action-value-for="value">
                    <div <?=isset($available['dependencies'])?'data-available-for="|'.implode('|', (array)$available['option']).'|"':'';?>>
                        <?=$form->select('dependency[_tmp][action][_tmp_action][1]', $dependency['values'], ['disabled' => 'disabled']);?>
                    </div>
                    <div data-available-for-other>
                        <?=$form->text('dependency[_tmp][action][_tmp_action][1]', '', ['disabled' => 'disabled', 'data-action-value' => 1, 'placeholder' => t('Value...')]);?>
                    </div>
                </div>
                <div data-dependency-action-value-for="class">
                    <?=$form->text('dependency[_tmp][action][_tmp_action][1]', '', ['disabled' => 'disabled', 'data-action-value' => 1, 'placeholder' => t('Class...')]);?>
                </div>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <a href="#" class="btn btn-secondary btn-sq" data-add-action data-bs-toggle="tooltip" data-bs-title="<?=t('Add');?>">
                        <i class="fa-fw fa fa-plus"></i>
                    </a>
                    <a href="#" class="btn btn-secondary btn-sq text-danger" data-remove-row data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
                        <i class="fa-fw fa fa-trash"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div data-template-dependency-selector class="hide" style="display:none">

        <div class="row element">
            <div class="col field">
                <div class="input-group mb-3">
                    <span class="input-group-text first"><?=t('if');?></span>
                    <span class="input-group-text"><?=t('and if');?></span>
                    <?=$form->select('dependency[_tmp][selector][_tmp_selector][element]', $dependency['fields'], '', ['disabled' => 'disabled', 'data-selector' => 1]);?>
                </div>
            </div>
            <div class="col-auto btns">
                <div class="btn-group">
                    <a href="#" class="btn btn-secondary btn-sq" data-add-selector data-bs-toggle="tooltip" data-bs-title="<?=t('Add');?>">
                        <i class="fa-fw fa fa-plus"></i>
                    </a>
                    <a href="#" class="btn btn-secondary btn-sq text-danger" data-remove-row data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
                        <i class="fa-fw fa fa-trash"></i>
                    </a>
                </div>
            </div>

            <div class="col-12">
                <div data-option-dependencies-conditions class="conditions" data-current-selector="_tmp_selector" data-condition-count="-2">
                    <!-- here the conditions -->
                </div>
            </div>
        </div>
    </div>

    <div data-template-dependency-condition class="hide" style="display:none">

        <div class="row condition">
            <div class="col">
                <?php foreach ((array)$dependency['conditions'] as $handle => $options) { ?>
                    <?php if (is_array($options)) { ?>
                        <div class="input-group mb-3" data-dependency-condition-for="<?=$handle;?>">
                            <span class="input-group-text first"><?=t('has');?></span>
                            <span class="input-group-text"><?=t('and has');?></span>
                            <?=$form->select('dependency[_tmp][selector][_tmp_selector][condition][_tmp_condition][value]', ['any' => t('any option'), 'non' => t('non option')] + $options, 'element', ['disabled' => 'disabled', 'data-condition-type' => 1]);?>
                            <span class="input-group-text"><?=t('selected/checked');?></span>
                        </div>
                    <?php } else { ?>
                        <div class="row mb-3" data-dependency-condition-for="<?=$handle;?>">
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text"><?=t('and');?></span>
                                    <?=$form->select('dependency[_tmp][selector][_tmp_selector][condition][_tmp_condition][condition]', (array)$conditions[$handle], '', ['disabled' => 'disabled', 'data-dependency-condition-value' => true, 'data-condition-type' => 1]);?>
                                </div>
                            </div>
                            <div class="col" data-dependency-condition-value-for="value">
                                <?=$form->text('dependency[_tmp][selector][_tmp_selector][condition][_tmp_condition][value]', '', ['disabled' => 'disabled', 'data-condition-value' => 1, 'placeholder' => t('Value...')]);?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <a href="#" class="btn btn-secondary btn-sq" data-add-condition data-bs-toggle="tooltip" data-bs-title="<?=t('Add');?>">
                        <i class="fa-fw fa fa-plus"></i>
                    </a>
                    <a href="#" class="btn btn-secondary btn-sq text-danger" data-remove-row data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
                        <i class="fa-fw fa fa-trash"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <a class="btn btn-sm btn-primary" data-add-rule data-rule-count="-1">
                <?=t('Add rule');?>
            </a>
            <a class="btn btn-sm btn-outline-dark <?=!isset($saved_dependencies)?'hide':'';?>" data-paste-rule data-bs-toggle="tooltip" data-bs-title="<?=t('Paste dependency rules');?>">
                <?=t('Paste');?>
            </a>
            <a class="btn btn-sm btn-outline-dark" data-copy-rule data-bs-toggle="tooltip" data-bs-title="<?=t('Copy dependency rules');?>">
                <?=t('Copy');?>
            </a>
        </div>
    </div>


    <script>
        $(function() {

            $('[data-add-rule]').off('click').on('click', function() {
                var row = $('[data-template-dependency]').eq(0).clone();
                var _tmp = $(this).attr('data-rule-count')*1;
                var _tmp_action = -1; // aways -1
                var _tmp_selector = -1; // aways -1
                var _tmp_condition = -1; // aways -1

                var action = $('[data-template-dependency-action]').eq(0).clone();
                var selector = $('[data-template-dependency-selector]').eq(0).clone();
                var condition = $('[data-template-dependency-condition]').eq(0).clone();

                $('[data-option-dependencies-actions]', row).append(action.html());
                $('[data-option-dependencies-conditions]', selector).append(condition.html());
                $('[data-option-dependencies-selector]', row).append(selector.html());

                row.css('display', 'block').find(':input').attr('disabled', false);
                row = row.html().replace(/_tmp_action/g, _tmp_action).replace(/_tmp_selector/g, _tmp_selector).replace(/_tmp_condition/g, _tmp_condition).replace(/_tmp/g, _tmp);

                $('[data-option-dependencies]').append(row);

                $(this).attr('data-rule-count', _tmp - 1);

                FormidableDependencyElements();
            });

            $('[data-copy-rule]').off('click').on('click', function() {

                $('[data-option-dependencies]').wrap('<form class="hide"></form>');
                var row = $('[data-option-dependencies]').closest('form');

                <?php
                    $action = $view->action('dependency', 'element', $ff->getItemID(), 'save');
                    if (isset($mail) && is_object($mail)) {
                        $action = $view->action('dependency', 'mail', $ff->getItemID(), 'save');
                    }
                ?>
                $.ajax({
                    url: '<?=$action;?>',
                    method: 'POST',
                    dataType: 'json',
                    data: row.serialize()+'&ccm_token=<?=$token->generate('dependency');?>',
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
                        ConcreteAlert.notify({
                            title: '<?=t('Success');?>',
                            message: data.success
                        });
                        $('[data-paste-rule]').removeClass('hide');
                    },
                    complete: function() {
                        $('[data-option-dependencies]').unwrap();
                        jQuery.fn.dialog.hideLoader();
                    }
                });
            });

            $('[data-paste-rule]').off('click').on('click', function() {
                <?php
                    $action = $view->action('dependency', 'element', $ff->getItemID(), 'paste');
                    if (isset($mail) && is_object($mail)) {
                        $action = $view->action('dependency', 'mail', $ff->getItemID(), 'paste');
                    }
                ?>
                console.log('<?=$action;?>');
                $.ajax({
                    url: '<?=$action;?>',
                    method: 'POST',
                    dataType: 'json',
                    data: 'ccm_token=<?=$token->generate('dependency');?>',
                    beforeSend: function() {
                        jQuery.fn.dialog.showLoader();
                        FormidableShowLoader();
                    },
                    error: function(response) {
                        var data = response.responseJSON;
                        ConcreteAlert.error({
                            title: '<?=t('Error');?>',
                            message: data.error
                        });
                    },
                    success: function(data) {
                        $.each(data.success.dependencies, function(d, dependency) {
                            var dependency_row = $('[data-template-dependency]').eq(0).clone();
                            $.each(dependency.action, function(a, action) {
                                var action_row = $('[data-template-dependency-action]').eq(0).clone();
                                // set fields
                                $('[data-action-type] option[value="'+action[0]+'"]', action_row).attr('selected', 'selected');
                                $('[data-action-value]', action_row).attr('value', action[1]);
                                action_row = action_row.html().replace(/_tmp_action/g, a);
                                $('[data-option-dependencies-actions]', dependency_row).append(action_row);

                            });
                            $.each(dependency.selector, function(s, selector) {
                                var selector_row = $('[data-template-dependency-selector]').eq(0).clone();
                                // set fields
                                var option = $('[data-selector] option[value="'+selector.element+'"]', selector_row);
                                if (option.length > 0) {
                                    option.attr('selected', 'selected');
                                    $.each(selector.condition, function(c, condition) {
                                        var condition_row = $('[data-template-dependency-condition]').eq(0).clone();
                                        // set fields
                                        if (condition.condition) {
                                            var option = $('[data-condition-type] option[value="'+condition.condition+'"]', condition_row);
                                            if (option.length > 0) {
                                                option.attr('selected', 'selected');
                                            }
                                            else {
                                                $('[data-condition-type]', condition_row).prepend($('<option>').attr('selected', 'selected').text('<?=t('(option removed or not available)');?>'));
                                            }
                                            $('[data-condition-value]', condition_row).attr('value', condition.value);
                                        }
                                        else {
                                            var option = $('[data-condition-type] option[value="'+condition.value+'"]', condition_row);
                                            if (option.length > 0) {
                                                option.attr('selected', 'selected');
                                            }
                                            else {
                                                $('[data-condition-type]', condition_row).prepend($('<option>').attr('selected', 'selected').text('<?=t('(option removed or not available)');?>'));
                                            }
                                        }
                                        condition_row = condition_row.html().replace(/_tmp_condition/g, c);
                                        $('[data-option-dependencies-conditions]', selector_row).append(condition_row);
                                    });
                                }
                                else {
                                    var option = $('<option>').attr({'selected': 'selected', 'disabled': 'disabled'}).text('<?=t('(element removed or not available)');?>');
                                    $('[data-selector]', selector_row).prepend(option);
                                    var condition_row = $('[data-template-dependency-condition]').eq(0).clone();
                                    condition_row = condition_row.html().replace(/_tmp_condition/g, -1);
                                    $('[data-option-dependencies-conditions]', selector_row).append(condition_row).addClass('hide');

                                }
                                selector_row = selector_row.html().replace(/_tmp_selector/g, s);
                                $('[data-option-dependencies-selector]', dependency_row).append(selector_row);
                            });
                            dependency_row.css('display', 'block').find(':input').attr('disabled', false);
                            dependency_row = dependency_row.html().replace(/_tmp/g, d);
                            $('[data-option-dependencies]').append(dependency_row);
                            $(this).attr('data-rule-count', d);
                        });

                        FormidableDependencyElements();

                        ConcreteAlert.notify({
                            title: '<?=t('Success');?>',
                            message: data.success.message
                        });
                    },
                    complete: function() {
                        jQuery.fn.dialog.hideLoader();
                        FormidableHideLoader();
                    }
                })
            });

        });
    </script>

<?php }