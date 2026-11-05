<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Noticia;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    /**
     * Publica un comentario de un usuario registrado.
     * El comentario queda pendiente hasta que un administrador lo apruebe.
     */
    public function store(Request $request, int $id)
    {
        // Solo se puede comentar una noticia que exista y esté publicada.
        $noticia = Noticia::publicados()->findOrFail($id);

        $request->validate([
            'contenido' => ['required', 'string', 'max:1000'],
            // Honeypot: los bots suelen rellenar todos los campos ocultos.
            'sitio_web' => ['nullable', 'prohibited'],
        ]);

        $usuario = $request->user();

        Comentario::create([
            'id_noticia' => $noticia->id_noticia,
            'id_usuario' => $usuario?->id_usuario,
            'autor' => $usuario?->nombre_completo ?? 'Lector UMC',
            'contenido' => $request->string('contenido')->trim()->toString(),
            'status' => Comentario::STATUS_PENDIENTE,
        ]);

        return back()->with(
            'exito',
            '¡Gracias por participar! Tu comentario será revisado antes de publicarse.'
        );
    }

    /**
     * Bandeja de moderación de comentarios para administradores.
     */
    public function adminIndex(Request $request)
    {
        $status = $request->query('status', Comentario::STATUS_PENDIENTE);

        if (! in_array($status, ['pendiente', 'aprobado', 'rechazado', 'todos'], true)) {
            $status = Comentario::STATUS_PENDIENTE;
        }

        $comentarios = Comentario::query()
            ->with(['noticia', 'usuario'])
            ->when($status !== 'todos', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $conteo = [
            'pendientes' => Comentario::pendientes()->count(),
            'aprobados' => Comentario::where('status', Comentario::STATUS_APROBADO)->count(),
            'rechazados' => Comentario::where('status', Comentario::STATUS_RECHAZADO)->count(),
            'todos' => Comentario::count(),
        ];

        return view('admin.comentarios.index', compact('comentarios', 'status', 'conteo'));
    }

    public function aprobar(Comentario $comentario)
    {
        $comentario->update(['status' => Comentario::STATUS_APROBADO]);

        return back()->with('message', 'Comentario aprobado y publicado.');
    }

    public function rechazar(Comentario $comentario)
    {
        $comentario->update(['status' => Comentario::STATUS_RECHAZADO]);

        return back()->with('message', 'Comentario rechazado.');
    }

    public function destroy(Comentario $comentario)
    {
        $comentario->delete();

        return back()->with('message', 'Comentario eliminado correctamente.');
    }
}
