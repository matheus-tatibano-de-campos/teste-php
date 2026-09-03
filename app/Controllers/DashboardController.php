<?php
/**
 * DashboardController — Tela inicial do sistema
 *
 * Exibe:
 * - Dados do usuário logado e data atual
 * - Valor total dos serviços do usuário
 * - Últimos serviços e serviços pendentes
 * - Tabela completa de serviços prestados
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Service;

class DashboardController extends Controller
{
    /** @var Service */
    private $serviceModel;

    public function __construct()
    {
        $this->serviceModel = new Service();
    }

    public function index(): void
    {
        $this->requireAuth();

        $userId = (int) $_SESSION['user']['id_user'];

        $this->render('dashboard/index', [
            'user'            => $_SESSION['user'],
            'flash'           => $this->getFlash(),
            'currentDate'     => date('d/m/Y'),
            'totalValue'      => $this->serviceModel->getTotalByUser($userId),
            'lastServices'    => $this->serviceModel->getLastByUser($userId),
            'pendingServices' => $this->serviceModel->getPendingByUser($userId),
            'services'        => $this->serviceModel->findAllWithUser(),
        ]);
    }
}
