<?php
namespace App\Services;
use App\Repositories\EntrenamientoRepository;
class EntrenamientoService {
    protected $repository;
    protected $chatGPTService;

    public function __construct(EntrenamientoRepository $repository, ChatGPTService $chatGPTService) {
        $this->repository = $repository;
        $this->chatGPTService = $chatGPTService;
    }

    public function getAll() { return $this->repository->getAllWithRelations(); }
    public function find(string $id) { return $this->repository->find($id); }

    public function create(array $data) {
        if (!isset($data['id'])) $data['id'] = 'en' . bin2hex(random_bytes(10));

        if (isset($data['contenidoPersonalizado'])) {
            $metrics = $this->chatGPTService->estimateTrainingMetrics($data['contenidoPersonalizado']);
            if ($metrics) {
                $data['distanciaTotal'] = $metrics['distancia'];
                $data['tiempoTotal'] = $metrics['tiempo'];
            }
        }

        return $this->repository->create($data);
    }

    public function update(string $id, array $data) {
        if (isset($data['contenidoPersonalizado'])) {
            // Solo estimar si no se han proporcionado distancia o tiempo manualmente
            if (!isset($data['distanciaTotal']) && !isset($data['tiempoTotal'])) {
                $metrics = $this->chatGPTService->estimateTrainingMetrics($data['contenidoPersonalizado']);
                if ($metrics) {
                    $data['distanciaTotal'] = $metrics['distancia'];
                    $data['tiempoTotal'] = $metrics['tiempo'];
                }
            }
        }
        return $this->repository->update($id, $data);
    }
    public function delete(string $id) { return $this->repository->delete($id); }
    public function getForAlumno(string $alumnoId) { return $this->repository->getForAlumno($alumnoId); }
    public function getWithResultados(string $id) { return $this->repository->getWithResultados($id); }
}
