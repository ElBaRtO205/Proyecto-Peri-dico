<?php

namespace App\Http\Controllers;

use App\Models\MensajeContacto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactoController extends Controller
{
    public function index(Request $request)
    {
        $tipo = $request->query('tipo', 'sugerencia');

        if (! in_array($tipo, ['sugerencia', 'contacto', 'podcast'], true)) {
            $tipo = 'sugerencia';
        }

        return view('contacto.index', compact('tipo'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo' => ['required', Rule::in(['sugerencia', 'contacto', 'podcast'])],
            'nombre' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'asunto' => ['nullable', 'string', 'max:150'],
            'mensaje' => ['required', 'string', 'max:2000'],
            'persona_recomendada' => ['nullable', 'required_if:tipo,podcast', 'string', 'max:120'],
            'enlace_persona' => ['nullable', 'string', 'max:255'],
        ], [
            'persona_recomendada.required_if' => 'Indica el nombre de la persona que quieres recomendar para el podcast.',
        ]);

        if ($data['tipo'] !== 'podcast') {
            $data['persona_recomendada'] = null;
            $data['enlace_persona'] = null;
        }

        MensajeContacto::create($data);

        return redirect()
            ->route('contacto.index', ['tipo' => $data['tipo']])
            ->with('exito', '¡Gracias! Tu mensaje fue enviado a los administradores.');
    }

    public function adminIndex()
    {
        $mensajes = MensajeContacto::latest()->paginate(15);
        $noLeidos = MensajeContacto::where('leido', false)->count();

        return view('admin.mensajes.index', compact('mensajes', 'noLeidos'));
    }

    public function toggleLeido(MensajeContacto $mensaje)
    {
        $mensaje->update(['leido' => ! $mensaje->leido]);

        return back()->with('message', 'Estado del mensaje actualizado.');
    }

    public function destroy(MensajeContacto $mensaje)
    {
        $mensaje->delete();

        return back()->with('message', 'Mensaje eliminado correctamente.');
    }
}
