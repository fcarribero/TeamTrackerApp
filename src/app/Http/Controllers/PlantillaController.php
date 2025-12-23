<?php
namespace App\Http\Controllers;
use App\Services\PlantillaService;
use Illuminate\Http\Request;
class PlantillaController extends Controller {
    protected $service;
    public function __construct(PlantillaService $service) { $this->service = $service; }

    public function index() {
        $plantillas = $this->service->getAll();
        return view('profesor.plantillas.index', compact('plantillas'));
    }

    public function create() {
        return view('profesor.plantillas.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'contenido' => 'required|array',
            'contenido.calentamiento' => 'nullable|string',
            'contenido.trabajo_principal' => 'nullable|string',
            'contenido.enfriamiento' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        $this->service->create($data);
        return redirect()->route('plantillas.index')->with('success', 'Plantilla creada correctamente');
    }

    public function edit($id) {
        $plantilla = $this->service->find($id);
        if (!$plantilla) return redirect()->route('plantillas.index')->with('error', 'Plantilla no encontrada');
        return view('profesor.plantillas.edit', compact('plantilla'));
    }

    public function update(Request $request, $id) {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'contenido' => 'required|array',
            'contenido.calentamiento' => 'nullable|string',
            'contenido.trabajo_principal' => 'nullable|string',
            'contenido.enfriamiento' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        $this->service->update($id, $data);
        return redirect()->route('plantillas.index')->with('success', 'Plantilla actualizada correctamente');
    }

    public function destroy($id) {
        $this->service->delete($id);
        return redirect()->route('plantillas.index')->with('success', 'Plantilla eliminada correctamente');
    }
}
