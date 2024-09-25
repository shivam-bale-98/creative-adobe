Concrete.event.bind('talk_to_us.selectForm.stacks', function () {
    $(document).ready(function () {
        $('select[name="selectForm[]"]').select2_sortable();
    });
});