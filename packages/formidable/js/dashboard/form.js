$(function() {

    $('form[data-copy-form]').on('submit', function(e) {
        e.preventDefault();
        var target = $('div[data-copy-form]');
        FormidableHandleResponse($(this), target);
    });

    $('form[data-delete-form]').on('submit', function(e) {
        e.preventDefault();
        var target = $('div[data-delete-form]');
        FormidableHandleResponse($(this), target);
    });

    $('form[data-copy-mail]').on('submit', function(e) {
        e.preventDefault();
        var target = $('div[data-copy-mail]');
        FormidableHandleResponse($(this), target);
    });

    $('form[data-delete-mail]').on('submit', function(e) {
        e.preventDefault();
        var target = $('div[data-delete-mail]');
        FormidableHandleResponse($(this), target);
    });

    $('input[name=copyColumns]').on('change', function() {
        var parent = $(this).closest('form');
        $('div[data-row-elements]', parent).hide();
        if ($(this).is(':checked')) {
            $('div[data-row-elements]', parent).show();
        }
    }).trigger('change');

    $('form[data-copy-row]').on('submit', function(e) {
        e.preventDefault();
        var target = $('div[data-copy-row]');
        FormidableHandleResponse($(this), target);
    });

    $('form[data-delete-row]').on('submit', function(e) {
        e.preventDefault();
        var target = $('div[data-delete-row]');
        FormidableHandleResponse($(this), target);
    });

    /* sorting rows */
    $('.form-rows').sortable({
        handle: '.form-row-header',
        connectWith: '.form-rows',
        stop: function(ev, ui) {
            FormidableCheckRows();
            FormidableSaveSorting();
        }
    });

    $('form[data-copy-column]').on('submit', function(e) {
        e.preventDefault();
        var target = $('div[data-copy-column]');
        FormidableHandleResponse($(this), target);
    });

    $('form[data-delete-column]').on('submit', function(e) {
        e.preventDefault();
        var target = $('div[data-delete-column]');
        FormidableHandleResponse($(this), target);
    });

    /* sort columns */
    $('.form-columns').sortable({
        handle: '.form-column-header',
        connectWith: '.form-columns',
        stop: function(ev, ui) {
            FormidableCheckColumns();
            FormidableSaveSorting();
        }
    });

    $('form[data-copy-element]').on('submit', function(e) {
        e.preventDefault();
        var target = $('div[data-copy-element]');
        FormidableHandleResponse($(this), target);
    });

    $('form[data-delete-element]').on('submit', function(e) {
        e.preventDefault();
        var target = $('div[data-delete-element]');
        FormidableHandleResponse($(this), target);
    });

    /* sorting element */
    $('.form-elements').sortable({
        handle: '.form-element-header',
        connectWith: '.form-elements',
        stop: function(ev, ui) {
            FormidableCheckElements();
            FormidableSaveSorting();
        }
    });

    $('[data-onchange]').off('change').on('change', function() {
        var parent = $(this).closest('form');
        $('[data-target="'+$(this).attr('data-onchange')+'"]', parent).addClass('hide');
        $('[data-target="'+$(this).attr('data-onchange')+'"][data-value="'+$(this).val()+'"]', parent).removeClass('hide').find(':input:not([id^="cke"])').attr('disabled', false);
        FormidableDisableElements(parent);
    }).trigger('change');

    $('select[name="elementType"]').off('change').on('change', function() {
        var value = $(this).val();
        var parent = $(this).closest('form');
        $('[data-available-for], [data-available-for-other]', parent).addClass('hide');
        $('[data-available-for*="|'+value+'|"]', parent).removeClass('hide').find(':input').attr('disabled', false);

        var action = $('[data-option-dependencies-actions]', parent);
        if ($('[data-available-for*="|'+value+'|"]', action).length <= 0) {
            $('[data-available-for-other]', action).removeClass('hide');
        }

        FormidableDisableTab(parent);
        FormidableDisableElements(parent);

    }).trigger('change');

    $('select[name="rowType"]').on('change', function() {
        var parent = $(this).closest('form');
        $('div[data-row-type]', parent).hide();
        $('div[data-row-type="'+$(this).val()+'"]', parent).show();
    }).trigger('change');

    $('[data-option-rows]').sortable({
        handle: '[data-row-move]',
        connectWith: '[data-option-rows]',
    });

    /*
    // disabled for now
    // it's no longer a checkbox/radio, but it can be set with the "defaults".
    $('input[id="option_multiple"]').on('click', function() {
        var target = $(this).closest('form[data-form-element]');
        if ($(this).is(':checked')) {
            $('[data-option-rows] input[type="radio"]').each(function(i, el) {
                var l = $(el).parent('div');
                $(el).detach().attr('type', 'checkbox').appendTo(l);
            });
        }
        else {
            $('[data-option-rows] input[type="checkbox"]').each(function(i, el) {
                var l = $(el).parent('div');
                $(el).detach().attr('type', 'radio').appendTo(l);
            });
        }
    });
    */

    var triggerTabList = [].slice.call(document.querySelectorAll('#elementTab a'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()
        });
    });

    $('[data-remove-option]').on('click', function() {
        $(this).closest('div.option').remove();
    });

    $('[data-add-option]').on('click', function() {
        var count = $(this).attr('data-row-count');
        var row = $('[data-template-option]').eq(0).clone();
        row.css('display', 'block').find(':input').attr('disabled', false);
        row = row.html().replace(/_tmp/g, count);
        $('[data-option-rows]').append(row);
        $(this).attr('data-row-count', count-1);

        $('[data-remove-option]').off('click').on('click', function() {
            $(this).closest('div.option').remove();
        });
    });
});

function FormidableCheckElements()
{
    $('.form-elements > .form-element-empty').hide();
    $('.form-elements').each(function() {
        if ($('.form-element:not(.form-element-empty)', $(this)).length <= 0) {
            $('.form-element-empty', $(this)).show();
        }
    });
}

function FormidableCheckRows()
{
    $('.form-rows > .form-row-empty').hide();
    $('.form-rows').each(function() {
        if ($('.form-row:not(.form-row-empty)', $(this)).length <= 0) {
            $('.form-row-empty', $(this)).show();
        }
    });
}
function FormidableCheckColumns()
{
    $('.form-columns > .form-column-empty').hide();
    $('.form-columns').each(function() {
        if ($('.form-column:not(.form-column-empty)', $(this)).length <= 0) {
            $('.form-column-empty', $(this)).show();
        }
    });
}


function FormidableDependencyElements()
{
    $('.dependency [data-bs-toggle="tooltip"]').tooltip();

    $('[data-remove-dependency]').off('click').on('click', function() {
        $(this).closest('div.dependency').remove();
        // hack to remove tooltip also
        $('.tooltip').remove();
        FormidableDependencyElementsDisable();
    });

    $('[data-remove-row]').off('click').on('click', function() {
        $(this).closest('div.row').remove();
        // hack to remove tooltip also
        $('.tooltip').remove();
        FormidableDependencyElementsDisable();
    });

    $('[data-dependency-action-value]').off('change').on('change', function() {
        var parent = $(this).closest('div.row');
        $('[data-dependency-action-value-for]', parent).addClass('hide').find(':input').attr('disabled', true);
        if ($.inArray($(this).val(), ['value']) > -1) {
            $('[data-dependency-action-value-for="value"]', parent).removeClass('hide'); //.find(':input').attr('disabled', false);
            var type = $('select[name="elementType"]').val();
            if ($('[data-available-for*="|'+type+'|"]', parent).length > 0) {
                $('[data-available-for*="|'+type+'|"]', parent).removeClass('hide').find(':input').attr('disabled', false);
            } else {
                $('[data-available-for-other]', parent).removeClass('hide').find(':input').attr('disabled', false);
            }
        }
        if ($.inArray($(this).val(), ['class']) > -1) {
            $('[data-dependency-action-value-for="class"]', parent).removeClass('hide').find(':input').attr('disabled', false);
        }

        var col = $('[data-dependency-action-value-for]', parent).closest('div.col');
        if (col.find('div[data-dependency-action-value-for]:not(.hide)').length > 0) {
            col.show();
        } else {
            col.hide();
        }
    }).trigger('change');

    $('[data-selector]').off('change').on('change', function() {
        var parent = $(this).closest('div.row');
        $('[data-option-dependencies-conditions]', parent).addClass('hide');
        if ($(this).val() != null) {
            $('[data-option-dependencies-conditions]', parent).removeClass('hide');
            $('[data-dependency-condition-for]', parent).addClass('hide').find(':input').attr('disabled', true);
            $('[data-dependency-condition-for="'+$(this).val()+'"]', parent).removeClass('hide').find(':input').attr('disabled', false);
        }
    }).trigger('change');

    $('[data-dependency-condition-value]').off('change').on('change', function() {
        var parent = $(this).closest('div.row');
        $('[data-dependency-condition-value-for]', parent).addClass('hide').find(':input').attr('disabled', true);
        if (!parent.hasClass('hide')) {
            if ($.inArray($(this).val(), ['empty', 'not_empty']) <= -1) {
                $('[data-dependency-condition-value-for="value"]', parent).removeClass('hide').find(':input').attr('disabled', false);
            }
        }
    }).trigger('change');

    $('[data-add-action]').off('click').on('click', function() {
        var row = $(this).closest('div.row').eq(0).clone();
        $(':input', row).val('');
        $('select option:first', row).prop('selected', true);
        $(this).closest('div.row').after(row);
    });

    $('[data-add-action]').off('click').on('click', function() {
        var row = $(this).closest('div.actions');
        var _tmp = row.attr('data-current-rule')*1;
        var _tmp_action = row.attr('data-action-count')*1;
        var action = $('[data-template-dependency-action]').eq(0).clone();
        action.css('display', 'block').find(':input').attr('disabled', false);
        action = action.html().replace(/_tmp_action/g, _tmp_action).replace(/_tmp/g, _tmp);
        row.append(action).attr('data-action-count', _tmp_action - 1);
        FormidableDependencyElements();
    });

    $('[data-add-selector]').off('click').on('click', function() {
        var row = $(this).closest('div.selector');
        var _tmp = row.attr('data-current-rule')*1;
        var _tmp_selector = row.attr('data-selector-count')*1;
        var selector = $('[data-template-dependency-selector]').eq(0).clone();
        var condition = $('[data-template-dependency-condition]').eq(0).clone();
        var _tmp_condition = -1; // aways -1
        $('[data-option-dependencies-conditions]', selector).append(condition.html());
        selector.css('display', 'block').find(':input').attr('disabled', false);
        selector = selector.html().replace(/_tmp_selector/g, _tmp_selector).replace(/_tmp_condition/g, _tmp_condition).replace(/_tmp/g, _tmp);
        row.append(selector).attr('data-selector-count', _tmp_selector - 1);
        FormidableDependencyElements();
    });

    $('[data-add-condition]').off('click').on('click', function() {
        var row = $(this).closest('div.conditions');
        var _tmp = row.closest('div.selector').attr('data-current-rule')*1;
        var _tmp_selector = row.attr('data-current-selector')*1;
        var _tmp_condition = row.attr('data-condition-count')*1;
        var condition = $('[data-template-dependency-condition]').eq(0).clone();
        condition.css('display', 'block').find(':input').attr('disabled', false);
        condition = condition.html().replace(/_tmp_selector/g, _tmp_selector).replace(/_tmp_condition/g, _tmp_condition).replace(/_tmp/g, _tmp);
        row.append(condition).attr('data-condition-count', _tmp_condition - 1);
        FormidableDependencyElements();
    });

    FormidableDependencyElementsDisable();
}

function FormidableDependencyElementsDisable()
{
    $('[data-option-dependencies] .dependency').each(function(i, dependency) {
        var dependency = $(dependency);
        $('.row.action [data-remove-row]', dependency).prop('disabled', true).addClass('disabled text-muted');
        if ($('.row.action', dependency).length > 1) {
            $('.row.action [data-remove-row]', dependency).prop('disabled', false).removeClass('disabled text-muted');
        }
        $('.row.element .btns [data-remove-row]', dependency).prop('disabled', true).addClass('disabled text-muted');
        if ($('.row.element', dependency).length > 1) {
            $('.row.element .btns [data-remove-row]', dependency).prop('disabled', false).removeClass('disabled text-muted');
        }
        $('.row.element', dependency).each(function(i, element) {
            var element = $(element);
            $('.conditions [data-remove-row]', element).prop('disabled', true).addClass('disabled text-muted');
            if ($('.conditions > .row', element).length > 1) {
                $('.conditions [data-remove-row]', element).prop('disabled', false).removeClass('disabled text-muted');
            }
        });
    });

    $('[data-no-dependency]').show();
    $('[data-copy-rule]').hide();
    if ($('[data-option-dependencies] .dependency').length > 0) {
        $('[data-copy-rule]').show();
        $('[data-no-dependency]').hide();
    }
}