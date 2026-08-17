<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;

class ModuleController extends Controller {

    public function placeholder(string $moduleName, string $title, string $icon = 'bi-box'): void {
        Auth::requirePermission('dashboard.view');

        $this->render('placeholder', [
            'pageTitle' => $title,
            'activeNav' => strtolower(str_replace(' ', '_', $moduleName)),
            'moduleName' => $moduleName,
            'moduleTitle' => $title,
            'icon' => $icon
        ]);
    }

    public function renderView(string $view, array $data = []): void {
        $this->render($view, $data);
    }
}
