$(function () {
    $(".block-types-sortable").sortable({
        update: function (event, ui) {
            var parent = ui.item.parent();
            var order = [];
            $.each($(parent).find('> li'), function () {
                var btid = $(this).attr('data-btid');
                order.push(btid);
            });
            $.ajax({
                data: {
                    order: order,
                    btsID: $(parent).attr('data-btsid')
                },
                dataType: 'json',
                type: 'POST',
                url: CCM_DISPATCHER_FILENAME + '/dashboard/blocks/block_order/update'
            });
        }
    });

    $(".block-sets-sortable").sortable({
        update: function (event, ui) {
            var order = [];
            $.each($(ui.item.parent()).find('> li'), function () {
                var btsID = $(this).attr('data-btsid');
                if ($.trim(btsID) != '') {
                    order.push(btsID);
                }
            });
            $.ajax({
                data: {order: order},
                dataType: 'json',
                type: 'POST',
                url: CCM_DISPATCHER_FILENAME + '/dashboard/blocks/block_order/update_sets'
            });
        }
    });
});

$(document).on('click', '.block-types-sortable a', function (e) {
    e.preventDefault();
});
