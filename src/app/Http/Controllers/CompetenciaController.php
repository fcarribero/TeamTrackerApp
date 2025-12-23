<?php
namespace App\Http\Controllers;
use App\Services\CompetenciaService;
use Illuminate\Http\Request;
class CompetenciaController extends Controller {
    protected $service;
    public function __construct(CompetenciaService $service) { $this->service = $service; }
    // Métodos de API eliminados. Implementar métodos para Blade si es necesario.
}
