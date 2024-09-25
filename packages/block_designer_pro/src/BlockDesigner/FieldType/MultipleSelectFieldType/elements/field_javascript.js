$(document).on('change', '.content-field[data-type="multiple_select"] select.view_output', function () {
    var contentField = $(this).parents('.content-field');
    switch ($(this).val()) {
        case '2':
        case '3':
            $(contentField).find('.multiple-select-html-open-close').slideDown('medium');
            break;
        default:
            $(contentField).find('.multiple-select-html-open-close').slideUp('medium');
            break;
    }
});

$(document).ready(function () {
    $('.content-field[data-type="multiple_select"]').find('select.view_output').trigger('change');
});