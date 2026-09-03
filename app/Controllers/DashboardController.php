<?php
/**
 * DashboardController — Tela inicial do sistema
 *
 * Exibe:
 * - Dados do usuário logado e data atual
 * - Valor total dos serviços do usuário
 * - Últimos serviços e serviços pendentes
 * - Tabela de serviços com filtros (nome, período, status, usuário)
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

        $userId  = (int) $_SESSION['user']['id_user'];
        $filters = $this->readFilters();

        $this->render('dashboard/index', [
            'user'            => $_SESSION['user'],
            'flash'           => $this->getFlash(),
            'currentDate'     => date('d/m/Y'),
            'totalValue'      => $this->serviceModel->getTotalByUser($userId),
            'lastServices'    => $this->serviceModel->getLastByUser($userId),
            'pendingServices' => $this->serviceModel->getPendingByUser($userId),
            'services'        => $this->serviceModel->findAllWithUser($filters),
            'filters'         => $filters,
        ]);
    }

    /**
     * Lê e sanitiza os filtros da query string.
     *
     * @return array
     */
    private function readFilters()
    {
        $status = trim($_GET['status'] ?? '');

        if ($status !== 'Pendente' && $status !== 'Finalizado') {
            $status = '';
        }

        return [
            'description' => trim($_GET['description'] ?? ''),
            'date_from'   => $this->sanitizeDate($_GET['date_from'] ?? ''),
            'date_to'     => $this->sanitizeDate($_GET['date_to'] ?? ''),
            'status'      => $status,
            'user_name'   => trim($_GET['user_name'] ?? ''),
        ];
    }

    /**
     * Aceita apenas datas no formato YYYY-MM-DD (input type="date").
     *
     * @param string $value
     * @return string
     */
    private function sanitizeDate($value)
    {
        $value = trim($value);

        if ($value === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return '';
        }

        return $value;
    }
}
