<?php
namespace App\Services;
use App\Repositories\ObjetivoRepository;
class ObjetivoService {
    protected $repository;
    public function __construct(ObjetivoRepository $repository) { $this->repository = $repository; }
    public function getAll() { return $this->repository->getAllWithRelations(); }
    public function find(string $id) { return $this->repository->find($id); }
    public function create(array $data) {
        if (!isset($data['id'])) $data['id'] = 'ob' . bin2hex(random_bytes(10));
        return $this->repository->create($data);
    }
    public function update(string $id, array $data) { return $this->repository->update($id, $data); }
    public function delete(string $id) { return $this->repository->delete($id); }
}
