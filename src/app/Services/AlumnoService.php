<?php

namespace App\Services;

use App\Repositories\AlumnoRepository;
use Illuminate\Support\Str;

class AlumnoService
{
    protected $alumnoRepository;

    public function __construct(AlumnoRepository $alumnoRepository)
    {
        $this->alumnoRepository = $alumnoRepository;
    }

    public function getAllAlumnos()
    {
        return $this->alumnoRepository->getAllWithRelations();
    }

    public function getAlumnoById(string $id)
    {
        return $this->alumnoRepository->find($id);
    }

    public function getAlumnoWithDetails(string $id)
    {
        return $this->alumnoRepository->getByIdWithDetails($id);
    }

    public function createAlumno(array $data)
    {
        if (!isset($data['id'])) {
            $data['id'] = 'cl' . bin2hex(random_bytes(10)); // Simple CUID-like ID
        }
        $data['rol'] = 'alumno';
        if (!isset($data['email'])) {
            $data['email'] = 'alumno_' . $data['id'] . '@teamtracker.com';
        }
        if (!isset($data['password'])) {
            $data['password'] = bcrypt(Str::random(16));
        }
        return $this->alumnoRepository->create($data);
    }

    public function updateAlumno(string $id, array $data)
    {
        return $this->alumnoRepository->update($id, $data);
    }

    public function deleteAlumno(string $id)
    {
        return $this->alumnoRepository->delete($id);
    }
}
