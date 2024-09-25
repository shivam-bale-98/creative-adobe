Concrete.event.bind('related_listing.pageType.multiple_select', function () {
    $(document).ready(function () {
        $('.ft-multipleSelect-related_listing-pageType').select2();
    });
});Concrete.event.bind('related_listing.attributes.multiple_select', function () {
    $(document).ready(function () {
        $('.ft-multipleSelect-related_listing-attributes').select2();
    });
});Concrete.event.bind('btRelatedListing.link.open', function (options, settings) {
    var container = $('#' + settings.id);

    $(container).on('change', '.ft-smart-link-type', function () {
        var me = this;
        var value = $(me).val();
        var ftSmartLink = $(me).parents('.ft-smart-link');
        var ftSmartLinkOptions = $(ftSmartLink).find('.ft-smart-link-options');
        var ftSmartLinkOptionsShow = false;
        if($(ftSmartLinkOptions).hasClass('hidden')){
            $(ftSmartLinkOptions).removeClass('hidden d-none').hide();
        }
        $.each($(ftSmartLinkOptions).find('[data-link-type]'), function () {
            if ($(this).hasClass('hidden')) {
                $(this).removeClass('hidden d-none').hide();
            }
            var linkType = $(this).attr('data-link-type');
            if (linkType == value) {
                $(this).slideDown();
                ftSmartLinkOptionsShow = true;
            }
            else {
                $(this).slideUp();
            }
        });
        if(ftSmartLinkOptionsShow){
            $(ftSmartLinkOptions).slideDown();
        }
        else {
            $(ftSmartLinkOptions).slideUp();
        }
    });
});