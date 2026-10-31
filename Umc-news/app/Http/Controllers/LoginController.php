<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

            if ($request->user()?->isAdmin()) {
                return redirect()->intended(route('admin.noticias.index'));
            }

            return redirect()->route('home');
        }

        // Si falla regresa al formulario con un error
        return back()->withErrors([
            'email' => 'Metiste algún dato mal chamo, vuelve a intentarlo.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_completo' => ['required', 'string', 'max:150'],
            'usuario' => ['required', 'string', 'alpha_dash', 'max:50', 'unique:usuarios,usuario'],
            'email' => ['required', 'email', 'max:100', 'unique:usuarios,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('login', ['panel' => 'registro'])
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $usuario = Usuario::create([
            'nombre_completo' => $data['nombre_completo'],
            'usuario' => $data['usuario'],
            'email' => $data['email'],
            'contrasena' => Hash::make($data['password']),
            'rol' => 'Editor',
        ]);

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('home')->with('status', 'Tu cuenta fue creada correctamente.');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}