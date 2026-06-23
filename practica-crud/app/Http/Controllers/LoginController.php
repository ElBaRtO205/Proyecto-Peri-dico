<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Metodo para procesar el inicio de sesión
    public function login(Request $request)
    {
        // Validar que el usuario llene los campos
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'], // Laravel por defecto busca la columna 'password' en inglés para Auth
        ]);

        // Intentar iniciar sesion
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Si es exitoso, redirige al panel administrativo del CRUD
            return redirect()->intended(route('noticia.index'));
        }

        // Si falla regresa al formulario con un error
        return back()->withErrors([
            'email' => 'Metiste algún dato mal chamo, vuelve a intentarlo.',
        ])->onlyInput('email');

     
        
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}