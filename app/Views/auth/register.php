<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário — Sistema de Controle de Serviços</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/style.css">
</head>
<body class="auth-page">

    <div class="auth-box">
        <h1 class="auth-title">Cadastrar Novo Usuário</h1>

        <?php if (!empty($error)): ?>
            <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>?route=auth/register" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <input
                type="email"
                name="email"
                placeholder="email@email.com"
                value="<?= htmlspecialchars($email ?? '') ?>"
                required
            >

            <input
                type="password"
                name="password"
                placeholder="*****************"
                required
            >

            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>

        <p class="auth-footer">
            <a href="<?= BASE_URL ?>?route=auth/login" class="auth-link">Voltar ao login</a>
        </p>
    </div>

</body>
</html>
