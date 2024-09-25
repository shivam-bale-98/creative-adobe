$(function() {

    $(function () {
        let searchResultsTable = new window.ConcreteSearchResultsTable($("#ccm-search-results-table"));
        searchResultsTable.setupBulkActions();
    });

    $('form[data-delete-result]').on('submit', function(e) {
        e.preventDefault();
        var target = $('div[data-delete-result]');
        FormidableHandleResponse($(this), target);
    });

    $('form[data-resend-result]').on('submit', function(e) {
        e.preventDefault();
        var target = $('div[data-resend-result]');
        FormidableHandleResponse($(this), target);
    });

});