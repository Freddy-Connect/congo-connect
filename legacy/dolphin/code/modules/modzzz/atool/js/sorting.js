$('#sortid').sortable({
    axis: 'y',
    stop: function (event, ui) {
        var data = $(this).sortable('serialize');

        // POST to server using $.post or $.ajax
        $.ajax({
            data: data,
            type: 'POST',
            url: 'm/atool/'
        });
    }
});