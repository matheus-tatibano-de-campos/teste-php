<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Sistema de Controle de Serviços</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/style.css">
</head>
<body class="dashboard-page">

    <aside class="sidebar">
        <p class="sidebar-user">Logado como:</p>
        <p class="sidebar-name"><?= htmlspecialchars($user['name']) ?></p>
        <nav class="sidebar-nav">
            <a href="<?= BASE_URL ?>?route=service/create">Cadastrar Serviço</a>
            <a href="<?= BASE_URL ?>?route=auth/logout">Sair</a>
        </nav>
    </aside>

    <main class="main-content">
        <h1>DASHBOARD</h1>

        <?php if (!empty($flash)): ?>
            <p class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </p>
        <?php endif; ?>

        <p class="dashboard-placeholder">
            Bem-vindo, <strong><?= htmlspecialchars($user['name']) ?></strong>.
            A listagem de serviços será implementada na próxima camada.
        </p>
    </main>

</body>
</html>
