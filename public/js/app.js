/**
 * Scripts do dashboard — jQuery
 * Camada 6: finalizar serviço via AJAX (sem recarregar a página).
 */
$(function () {
    $('.services-table').on('click', '.btn-finish', function (event) {
        event.preventDefault();

        var $button = $(this);
        var id = $button.data('id');

        if (!id) {
            return;
        }

        if (!window.confirm('Deseja finalizar este serviço?')) {
            return;
        }

        $button.prop('disabled', true).text('...');

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
            $button.prop('disabled', false).text('Finalizar');
        }).fail(function () {
            window.alert('Erro de comunicação. Tente novamente.');
            $button.prop('disabled', false).text('Finalizar');
        });
    });
});
