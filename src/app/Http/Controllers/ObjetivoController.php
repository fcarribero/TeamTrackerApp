<?php
namespace App\Http\Controllers;
use App\Services\ObjetivoService;
use Illuminate\Http\Request;
class ObjetivoController extends Controller {
    protected $service;
    public function __construct(ObjetivoService $service) { $this->service = $service; }
    // Métodos de API eliminados. Implementar métodos para Blade si es necesario.
}
