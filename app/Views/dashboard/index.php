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
            <a class="nav-logout" href="<?= BASE_URL ?>?route=auth/logout">Sair</a>
        </nav>
    </aside>

    <main class="main-content">
        <h1>DASHBOARD</h1>

        <p class="dashboard-meta">
            Data atual: <strong><?= htmlspecialchars($currentDate) ?></strong>
        </p>

        <?php if (!empty($flash)): ?>
            <p class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </p>
        <?php endif; ?>

        <!-- Valor total destacado -->
        <div class="total-box">
            <span class="total-label">Valor total dos seus serviços:</span>
            <span class="total-value">
                R$ <?= number_format($totalValue, 2, ',', '.') ?>
            </span>
        </div>

        <!-- Listas: Últimos serviços | Serviços pendentes -->
        <div class="lists-row">
            <div class="list-block">
                <h2>Ultimos Serviços</h2>
                <?php if (empty($lastServices)): ?>
                    <p class="empty-msg">Nenhum serviço cadastrado.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($lastServices as $item): ?>
                            <li>
                                <?= (int) $item['id_service'] ?>
                                - <?= htmlspecialchars($item['description']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="list-block">
                <h2>Serviços Pendentes</h2>
                <?php if (empty($pendingServices)): ?>
                    <p class="empty-msg">Nenhum serviço pendente.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($pendingServices as $item): ?>
                            <li>
                                <?= (int) $item['id_service'] ?>
                                - <?= htmlspecialchars($item['description']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filtros da tabela (GET) -->
        <form method="GET" action="<?= BASE_URL ?>" class="filter-form">
            <input type="hidden" name="route" value="dashboard">

            <input
                type="text"
                name="description"
                placeholder="Nome"
                value="<?= htmlspecialchars($filters['description']) ?>"
            >

            <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from']) ?>">
            <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to']) ?>">

            <select name="status">
                <option value="">Status</option>
                <option value="Pendente"<?= $filters['status'] === 'Pendente' ? ' selected' : '' ?>>Pendente</option>
                <option value="Finalizado"<?= $filters['status'] === 'Finalizado' ? ' selected' : '' ?>>Finalizado</option>
            </select>

            <input
                type="text"
                name="user_name"
                placeholder="Usuário"
                value="<?= htmlspecialchars($filters['user_name']) ?>"
            >

            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a class="auth-link" href="<?= BASE_URL ?>?route=dashboard">Limpar</a>
        </form>

        <div class="table-wrap">
            <table class="services-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>DESCRIÇÃO</th>
                        <th>VALOR</th>
                        <th>STATUS</th>
                        <th>USUÁRIO</th>
                        <th>AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($services)): ?>
                        <tr>
                            <td colspan="6" class="empty-msg">Nenhum serviço encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($services as $service): ?>
                            <tr>
                                <td><?= (int) $service['id_service'] ?></td>
                                <td><?= htmlspecialchars($service['description']) ?></td>
                                <td>R$ <?= number_format((float) $service['price'], 2, ',', '.') ?></td>
                                <td><?= htmlspecialchars(strtoupper($service['status'])) ?></td>
                                <td><?= htmlspecialchars($service['user_name']) ?></td>
                                <td class="actions">
                                    <a
                                        class="btn btn-small"
                                        href="<?= BASE_URL ?>?route=service/edit&amp;id=<?= (int) $service['id_service'] ?>"
                                    >Alterar</a>

                                    <a
                                        class="btn btn-small"
                                        href="<?= BASE_URL ?>?route=service/delete&amp;id=<?= (int) $service['id_service'] ?>"
                                        onclick="return confirm('Deseja excluir este serviço?');"
                                    >Excluir</a>

                                    <?php if ($service['finished_at'] === null): ?>
                                        <a
                                            class="btn btn-small btn-finish"
                                            href="<?= BASE_URL ?>?route=service/finish&amp;id=<?= (int) $service['id_service'] ?>"
                                            data-id="<?= (int) $service['id_service'] ?>"
                                        >Finalizar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        var BASE_URL = <?= json_encode(BASE_URL) ?>;
    </script>
    <script src="<?= BASE_URL ?>public/js/app.js"></script>
</body>
</html>
