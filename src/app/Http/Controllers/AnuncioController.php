<?php

namespace App\Http\Controllers;

use App\Models\Anuncio;
use App\Services\AnuncioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnuncioController extends Controller
{
    protected $anuncioService;

    public function __construct(AnuncioService $anuncioService)
    {
        $this->anuncioService = $anuncioService;
    }

    public function index()
    {
        $anuncio = $this->anuncioService->getAnuncioByUserId(Auth::id());
        return view('profesor.anuncio.index', compact('anuncio'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contenido' => 'required|string',
            'activo' => 'boolean',
        ]);

        $data['activo'] = $request->boolean('activo');

        $this->anuncioService->updateOrCreateAnuncio(Auth::id(), $data);

        return redirect()->back()->with('success', 'Anuncio actualizado correctamente');
    }

    public function toggle(Anuncio $anuncio)
    {
        if ($anuncio->userId !== Auth::id()) {
            return redirect()->back()->with('error', 'No tienes permiso para modificar este anuncio');
        }

        $this->anuncioService->toggleActivo($anuncio);

        return redirect()->back()->with('success', 'Estado del anuncio actualizado');
    }
}
