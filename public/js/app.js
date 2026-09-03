/**
 * Scripts do dashboard — jQuery
 * Finalizar serviço via AJAX e evitar clique duplo.
 */
$(function () {
    $('.services-table').on('click', '.btn-finish', function (event) {
        event.preventDefault();

        var $button = $(this);
        var id = $button.data('id');

        if (!id || $button.hasClass('is-loading')) {
            return;
        }

        if (!window.confirm('Deseja finalizar este serviço?')) {
            return;
        }

        $button.addClass('is-loading').text('...');

        $.ajax({
            url: BASE_URL + '?route=service/finish&id=' + encodeURIComponent(id),
            method: 'GET',
            dataType: 'json'
        }).done(function (response) {
            if (response.success) {
                window.location.reload();
                return;
            }

            window.alert(response.message || 'Não foi possível finalizar o serviço.');
            $button.removeClass('is-loading').text('Finalizar');
        }).fail(function () {
            window.alert('Erro de comunicação. Tente novamente.');
            $button.removeClass('is-loading').text('Finalizar');
        });
    });
});
