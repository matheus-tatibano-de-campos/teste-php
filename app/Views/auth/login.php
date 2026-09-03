<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistema de Controle de Serviços</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/style.css">
</head>
<body class="auth-page">

    <div class="auth-box">
        <h1 class="auth-title">Sistema de Controle de Serviços</h1>

        <?php if (!empty($flash)): ?>
            <p class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </p>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>?route=auth/login" class="auth-form">
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

            <div class="auth-actions">
                <button type="submit" class="btn btn-primary">Entrar</button>
                <a href="<?= BASE_URL ?>?route=auth/register" class="auth-link">Cadastrar usuário</a>
            </div>
        </form>
    </div>

</body>
</html>
