$(function () {

    if ($('#javascript-ajax-button').length !== 0) {
        $('#javascript-ajax-button').on('click', function () {
            $.ajax("/songs/ajaxGetStats").done(function (result) {
                $('#javascript-ajax-result-box').html(result)
            }).fail(function () {
            }).always(function () {
            })
        })
    }

    if ($('#age-cal').length > 0) {
        $('#age-cal').on('click', function(e){
            e.preventDefault();

            $.ajax({
                method: "post",
                url: "/age/cal",
                data: $('#age-form').serialize(),
                dataType: 'json'
            }).done(function (result) {
                $('.age-result').html(result.msg)
            }).fail(function () {
            }).always(function () {
            })

            return false;
        });

    }
})
