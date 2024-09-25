$(function() {

    $('[data-bs-toggle="tooltip"]').tooltip();
    $('[data-bs-toggle="tab"]').tab();

});

function FormidableHandleResponse(form, target)
{
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        dataType: 'json',
        data: form.serialize(),
        beforeSend: function() {
            jQuery.fn.dialog.showLoader();
            FormidableDialogClear(target);
        },
        error: function(response) {
            var data = response.responseJSON;
            var error = data.error;
            if ($.isArray(error)) {
                error = error.join('<br />');
            }
            FormidableDialogError(target, error);
        },
        success: function(data) {
            if (data.redirect) {
                window.location.href = data.redirect;
            }
            jQuery.fn.dialog.closeTop();
        },
        complete: function() {
            jQuery.fn.dialog.hideLoader();
        }
    });
}

function FormidableDisableElements(dialog)
{
    $('.hide').find(':input:not([id^="cke"])').attr('disabled', true);

}

function FormidableDisableTab(dialog)
{
    var tab = $('.tab-pane', dialog).each(function() {
        $(this).addClass('tab-hide');
        $('.nav-link[href="#'+$(this).attr('id')+'"]').closest('.nav-link').addClass('tab-hide');
        if ($('> [data-available-for]', $(this)).length <= 0 || $('> [data-available-for]:not(.hide)', $(this)).length > 0) {
            $(this).removeClass('tab-hide');
            $('.nav-link[href="#'+$(this).attr('id')+'"]').closest('.nav-link').removeClass('tab-hide');
        }
    });
}

function FormidableDialogError(dialog, error)
{
    dialog.prepend($('<div>').addClass('alert alert-danger').html(error));
}

function FormidableDialogClear(dialog)
{
    $('div.alert', dialog).remove();
}

function FormidableShowLoader()
{
    $('.formidableLoader').fadeOut(50);
}

function FormidableHideLoader()
{
    $('.formidableLoader').fadeOut(200);
}

window.onload = function(){
    FormidableHideLoader();
}
