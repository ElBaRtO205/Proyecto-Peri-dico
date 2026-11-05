<?php

namespace App\Http\Controllers;

use App\Models\Acertijo;
use App\Models\Susurro;
use Illuminate\Http\Request;

class SoplarAlVientoController extends Controller
{
    private function normalizarTexto($texto)
    {
        $texto = mb_strtolower(trim((string) $texto), 'UTF-8');

        return str_replace(['á', 'é', 'í', 'ó', 'ú', 'ü'], ['a', 'e', 'i', 'o', 'u', 'u'], $texto);
    }

    /**
     * Solo administradores o lectores que resolvieron el acertijo activo
     * pueden entrar, publicar y dar aliento en esta sección.
     */
    private function tieneAcceso(Request $request): bool
    {
        $acertijoActivo = Acertijo::where('activo', true)
            ->orderByDesc('id_acertijo')
            ->first();

        return ($acertijoActivo && session('acertijo_resuelto_id') == $acertijoActivo->id_acertijo)
            || (bool) $request->user()?->isAdmin();
    }

    // 1. Verificar respuesta del acertijo desde la categoría Campus
    public function desbloquear(Request $request)
    {
        $acertijoActivo = Acertijo::where('activo', true)->orderByDesc('id_acertijo')->first();

        if (! $acertijoActivo) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Hoy el viento está en calma. No hay acertijo activo.',
            ], 404);
        }

        $respuestaUsuario = $this->normalizarTexto($request->input('clave', ''));
        $respuestaCorrecta = $this->normalizarTexto($acertijoActivo->respuesta);

        if ($respuestaUsuario === $respuestaCorrecta) {
            session(['acertijo_resuelto_id' => $acertijoActivo->id_acertijo]);

            return response()->json([
                'exito' => true,
                'mensaje' => 'El viento ha cambiado a tu favor. Regresa a la portada principal y busca el sello.',
            ]);
        }

        return response()->json([
            'exito' => false,
            'mensaje' => 'El viento se lleva tus palabras... Esa no es la respuesta.',
        ], 403);
    }

    // 2. Vista secreta "Soplar al viento"
    public function index(Request $request)
    {
        if (! $this->tieneAcceso($request)) {
            return redirect()->route('home');
        }

        $susurros = Susurro::orderByDesc('id_susurro')->paginate(12);
        $acertijoActivo = Acertijo::where('activo', true)->orderByDesc('id_acertijo')->first();

        return view('soplar-al-viento', compact('susurros', 'acertijoActivo'));
    }

    // 3. Publicar un susurro
    public function storeSusurro(Request $request)
    {
        abort_unless($this->tieneAcceso($request), 403);

        $request->validate([
            'apodo' => 'nullable|string|max:40',
            'contenido' => 'required|string|max:500',
            'categoria_viento' => 'required|in:Confesión,Rumor,Verso,Desahogo',
        ]);

        Susurro::create([
            'apodo' => $request->filled('apodo') ? $request->apodo : 'Brisa Anónima',
            'contenido' => $request->contenido,
            'categoria_viento' => $request->categoria_viento,
            'fecha_publicacion' => now(),
        ]);

        return back()->with('status', 'Tu susurro ha sido lanzado al viento.');
    }

    // 4. Dar aliento (Like)
    public function soplarSusurro(Request $request, $id)
    {
        abort_unless($this->tieneAcceso($request), 403);

        $susurro = Susurro::findOrFail($id);
        $llaveSesion = 'soplado_'.$id;

        if (! session()->has($llaveSesion)) {
            $susurro->increment('soplos');
            session([$llaveSesion => true]);
        }

        return response()->json(['soplos' => $susurro->soplos]);
    }

    // 5. Eliminar susurro (Solo Admin)
    public function destroySusurro($id)
    {
        Susurro::findOrFail($id)->delete();

        return back()->with('status', 'El viento disipó ese susurro.');
    }

    // 6. Limpiar por completo la sección (Solo Admin)
    public function limpiarSeccion(Request $request)
    {
        // Dejamos el muro sin susurros y la sección sin acertijo activo.
        Susurro::query()->delete();
        Acertijo::query()->delete();

        // Borramos la llave de acceso y los registros de "aliento" guardados
        // en la sesión para que todo vuelva a empezar desde cero.
        $request->session()->forget('acertijo_resuelto_id');

        foreach (array_keys($request->session()->all()) as $clave) {
            if (str_starts_with($clave, 'soplado_')) {
                $request->session()->forget($clave);
            }
        }

        return redirect()
            ->route('secreto.index')
            ->with('status', 'El viento se llevó todo: sin susurros, sin acertijo y sin clave.');
    }

    // 7. Guardar/Modificar acertijo desde el Panel de Administración
    public function actualizarAcertijo(Request $request)
    {
        $request->validate([
            'pregunta' => 'required|string',
            'respuesta' => 'required|string|max:100',
            'pista' => 'nullable|string|max:255',
        ]);

        Acertijo::where('activo', true)->update(['activo' => false]);

        Acertijo::create([
            'pregunta' => $request->pregunta,
            'respuesta' => $this->normalizarTexto($request->respuesta),
            'pista' => $request->pista,
            'activo' => true,
        ]);

        // Usa 'message' para que se muestre en la alerta de tu panel de administración
        return back()->with('message', 'Acertijo del día actualizado en "Rumores del Campus".');
    }
}
