<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('css/login.css') }}">

        
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap">
    <title>UMC News - Inicio de sesión y registro</title>
</head>

<body>

    <div class="autenticacion" id="autenticacion">
        <!-- Panel de registro -->
        <div class="autenticacion__panel autenticacion__panel--registro">
            <form class="autenticacion__formulario">
                <h1 class="autenticacion__titulo">Crea una cuenta</h1>

                <span class="autenticacion__subtexto">usa tu correo para registrarte</span>

                <!-- Input con icono de usuario -->
                <div class="autenticacion__input-grupo">
                    <i class="fas fa-user autenticacion__input-icono"></i>
                    <input class="autenticacion__campo" type="text" placeholder="Nombre completo" required>
                </div>

                <!-- Input con icono de correo -->
                <div class="autenticacion__input-grupo">
                    <i class="fas fa-envelope autenticacion__input-icono"></i>
                    <input class="autenticacion__campo" type="email" placeholder="Correo electrónico" required>
                </div>

                <!-- Contraseña con botón para mostrar/ocultar -->
                <div class="autenticacion__input-grupo autenticacion__input-grupo--password">
                    <i class="fas fa-lock autenticacion__input-icono"></i>
                    <input class="autenticacion__campo" type="password" id="password-registro" placeholder="Contraseña" required>
                    <button type="button" class="autenticacion__toggle-password"
                        onclick="togglePassword('password-registro', this)" aria-label="Mostrar contraseña">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <button class="autenticacion__boton" type="submit">
                    <i class="fas fa-user-plus"></i> Registrarse
                </button>
                
                <!-- Este enlace solo se ve en móvil -->
                <p class="autenticacion__cambio-movil">
                    ¿Ya tienes cuenta?
                    <a href="#" class="autenticacion__enlace-cambio" id="movil-login">Inicia sesión aquí</a>
                </p>
            </form>
        </div>

        <!-- Panel de inicio de sesión -->
        <div class="autenticacion__panel autenticacion__panel--inicio">
            
            <form class="autenticacion__formulario" action="{{ url('/login') }}" method="POST">
                @csrf
                
                <h1 class="autenticacion__titulo">Iniciar sesión</h1>

                <span class="autenticacion__subtexto">Coloca tu correo y contraseña</span>

                @if ($errors->any())
                    <div style="color: #dc3545; font-size: 14px; margin-bottom: 10px; text-align: center; font-weight: bold;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="autenticacion__input-grupo">
                    <i class="fas fa-envelope autenticacion__input-icono"></i>
                    <input class="autenticacion__campo" type="email" name="email" value="{{ old('email') }}" placeholder="Correo electrónico" required>
                </div>

                <div class="autenticacion__input-grupo autenticacion__input-grupo--password">
                    <i class="fas fa-lock autenticacion__input-icono"></i>
                    <input class="autenticacion__campo" type="password" name="password" id="password-login" placeholder="Contraseña" required>
                    <button type="button" class="autenticacion__toggle-password"
                        onclick="togglePassword('password-login', this)" aria-label="Mostrar contraseña">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <a class="autenticacion__enlace" href="#">¿Olvidaste tu contraseña?</a>

                <button class="autenticacion__boton" type="submit">
                    <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                </button>
                
                <p class="autenticacion__cambio-movil">
                    ¿No tienes cuenta?
                    <a href="#" class="autenticacion__enlace-cambio" id="movil-register">Regístrate aquí</a>
                </p>
            </form>
        </div>

        <!-- Panel que se desliza para cambiar entre login y registro -->
        <div class="autenticacion__intercambiador">
            <div class="autenticacion__fondo">
                <!-- Mensaje izquierdo (se ve cuando estás en registro) -->
                <div class="autenticacion__mensaje autenticacion__mensaje--izquierda">
                    <div class="autenticacion__logo">
                        <img src="{{ asset('img/umc_news_logo.svg') }}" alt="Logotipo UMC News" class="login__logo">
                    </div>
                    <h1 class="autenticacion__titulo">¡Bienvenido de vuelta!</h1>
                    <p class="autenticacion__texto">Ingresa tus datos personales y accede a UMC News</p>
                    <button class="autenticacion__boton autenticacion__boton--secundario" id="login" type="button">
                        <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                    </button>
                </div>
                
                <!-- Mensaje derecho (se ve cuando estás en login) -->
                <div class="autenticacion__mensaje autenticacion__mensaje--derecha">
                    <div class="autenticacion__logo">
                        <img src="{{ asset('img/umc_news_logo.svg') }}" alt="Logotipo UMC News" class="login__logo">
                    </div>
                    <h1 class="autenticacion__titulo">¡Hola y bienvenido!</h1>
                    <p class="autenticacion__texto">Regístrate y forma parte de la comunidad UMC News</p>
                    <button class="autenticacion__boton autenticacion__boton--secundario" id="register" type="button">
                        <i class="fas fa-user-plus"></i> Registrarse
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/login.js') }}" defer></script> 
</body>

</html>