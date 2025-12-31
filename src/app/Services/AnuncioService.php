<?php

namespace App\Services;

use App\Models\Anuncio;
use Illuminate\Support\Facades\Auth;

class AnuncioService
{
    /**
     * Obtener el anuncio más reciente de un profesor específico.
     *
     * @param string $userId
     * @return Anuncio|null
     */
    public function getAnuncioByUserId($userId)
    {
        return Anuncio::where('userId', $userId)->latest()->first();
    }

    /**
     * Crear o actualizar el anuncio de un profesor.
     *
     * @param string $userId
     * @param array $data
     * @return Anuncio
     */
    public function updateOrCreateAnuncio($userId, array $data)
    {
        return Anuncio::updateOrCreate(
            ['userId' => $userId],
            [
                'contenido' => $data['contenido'],
                'activo' => $data['activo'] ?? true,
            ]
        );
    }

    /**
     * Alternar el estado activo/inactivo de un anuncio.
     *
     * @param Anuncio $anuncio
     * @return bool
     */
    public function toggleActivo(Anuncio $anuncio)
    {
        return $anuncio->update([
            'activo' => !$anuncio->activo
        ]);
    }

    /**
     * Obtener el último anuncio activo para mostrar a los alumnos.
     *
     * @param string|null $userId Profesor ID
     * @return Anuncio|null
     */
    public function getAnuncioActivo($userId = null)
    {
        $query = Anuncio::where('activo', true);
        if ($userId) {
            $query->where('userId', $userId);
        }
        return $query->latest()->first();
    }
}
