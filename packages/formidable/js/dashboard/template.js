$(function() {

    $('form[data-copy-template]').on('submit', function(e) {
        e.preventDefault();
        var target = $('div[data-copy-template]');
        FormidableHandleResponse($(this), target);
    });

    $('form[data-delete-template]').on('submit', function(e) {
        e.preventDefault();
        var target = $('div[data-delete-template]');
        FormidableHandleResponse($(this), target);
    });

});