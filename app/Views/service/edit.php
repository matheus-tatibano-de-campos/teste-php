<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Serviço — Sistema de Controle de Serviços</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/style.css">
</head>
<body class="auth-page">

    <div class="auth-box">
        <h1 class="auth-title">Editar Serviço</h1>

        <?php if (!empty($error)): ?>
            <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>?route=service/update" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="id" value="<?= (int) $service['id_service'] ?>">

            <input
                type="text"
                name="description"
                placeholder="descrição"
                maxlength="45"
                value="<?= htmlspecialchars($service['description']) ?>"
                required
            >

            <input
                type="text"
                name="price"
                placeholder="preço"
                value="<?= htmlspecialchars(number_format((float) $service['price'], 2, ',', '')) ?>"
                required
            >

            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>

        <p class="auth-footer">
            <a href="<?= BASE_URL ?>?route=dashboard" class="auth-link">Voltar ao dashboard</a>
        </p>
    </div>

</body>
</html>
