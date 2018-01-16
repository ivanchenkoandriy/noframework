;
(function () {

    function attachEvents() {
        $('#button-task-form').on('click', function () {
            $containerPreviewAdd.addClass('hidden');
            $containerFormAdd.removeClass('hidden');
        });
    }

    var $containerFormAdd = $('#container-form-add'),
            $containerPreviewAdd = $('#container-preview-add'),
            $buttonTaskPreview = $('#button-task-preview');

    $buttonTaskPreview.on('click', function () {
        $buttonTaskPreview.addClass('disabled');

        var form = $containerFormAdd.find('form')[0]
        data = new FormData(form);

        $.ajax({
            url: '/preview',
            dataType: 'json',
            type: "POST",
            enctype: 'multipart/form-data',
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (data, textStatus, jqXHR) {
                $containerFormAdd.addClass('hidden');
                if ('success' === data.status) {
                    $containerPreviewAdd.html(data.data.html);
                    attachEvents();
                }

                $containerPreviewAdd.removeClass('hidden');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            },
            complete: function (jqXHR, textStatus) {
                $buttonTaskPreview.removeClass('disabled');
            }
        });
    });
}());