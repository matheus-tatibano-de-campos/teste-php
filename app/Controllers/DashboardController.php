<?php
/**
 * DashboardController — Placeholder temporário
 *
 * Será expandido na Camada 4 com tabela de serviços, filtros e totais.
 */

namespace App\Controllers;

use App\Core\Controller;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $this->render('dashboard/index', [
            'user'  => $_SESSION['user'],
            'flash' => $this->getFlash(),
        ]);
    }
}
