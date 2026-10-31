<x-app-layout>
    <x-slot:title>Contacto y sugerencias | UMC News</x-slot:title>

    <link rel="stylesheet" href="{{ asset('css/contacto.css') }}">

    @php
        $tipoSugerencia = $tipo === 'podcast' ? 'sugerencia' : $tipo;
        $esPodcast = old('tipo') === 'podcast';
    @endphp

    <main class="contacto-page">
        <header class="contacto-hero">
            <span class="contacto-hero__eyebrow">
                <i class="fas fa-paper-plane"></i> Participa en UMC News
            </span>
            <h1 class="contacto-hero__titulo">Contacto y buzón de sugerencias</h1>
            <p class="contacto-hero__texto">
                ¿Tienes una idea para una noticia, una carta para la redacción o conoces a alguien
                que debería estar en nuestro podcast? Escríbenos por aquí.
            </p>

            <div class="contacto-hero__correo">
                <span class="contacto-hero__correo-icono"><i class="fas fa-envelope"></i></span>
                <div>
                    <span class="contacto-hero__correo-etiqueta">Correo de los administradores</span>
                    <a href="mailto:{{ config('contacto.email') }}">{{ config('contacto.email') }}</a>
                </div>
            </div>
        </header>

        @if(session('exito'))
            <div class="contacto-alerta contacto-alerta--exito" role="status">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('exito') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="contacto-alerta contacto-alerta--error" role="alert">
                <i class="fas fa-circle-exclamation"></i>
                <div>
                    <strong>Revisa los datos del formulario:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="contacto-grid">
            <div class="contacto-main">
                {{-- Buzón de sugerencias y contacto general --}}
                <section class="contacto-card" id="sugerencias">
                    <div class="contacto-card__cabecera">
                        <span class="contacto-card__icono contacto-card__icono--dorado">
                            <i class="fas fa-lightbulb"></i>
                        </span>
                        <div>
                            <h2 class="contacto-card__titulo">Buzón de sugerencias</h2>
                            <p class="contacto-card__descripcion">
                                Envíanos ideas para nuevas noticias, temas que deberíamos cubrir o un mensaje directo para los administradores.
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('contacto.store') }}" method="POST" class="contacto-form">
                        @csrf

                        <div class="contacto-form__fila">
                            <label class="contacto-form__campo">
                                <span>Tu nombre</span>
                                <input type="text" name="nombre" value="{{ $esPodcast ? '' : old('nombre') }}"
                                       placeholder="Nombre y apellido" required>
                            </label>

                            <label class="contacto-form__campo">
                                <span>Tu correo</span>
                                <input type="email" name="email" value="{{ $esPodcast ? '' : old('email') }}"
                                       placeholder="correo@ejemplo.com" required>
                            </label>
                        </div>

                        <div class="contacto-form__fila">
                            <label class="contacto-form__campo">
                                <span>Motivo</span>
                                <select name="tipo">
                                    <option value="sugerencia" @selected(old('tipo', $tipoSugerencia) === 'sugerencia')>Sugerencia de noticia</option>
                                    <option value="contacto" @selected(old('tipo', $tipoSugerencia) === 'contacto')>Contacto general</option>
                                </select>
                            </label>

                            <label class="contacto-form__campo">
                                <span>Asunto <small>(opcional)</small></span>
                                <input type="text" name="asunto" value="{{ $esPodcast ? '' : old('asunto') }}"
                                       placeholder="Ej. Idea para la sección de Cultura">
                            </label>
                        </div>

                        <label class="contacto-form__campo">
                            <span>Tu mensaje</span>
                            <textarea name="mensaje" rows="6" placeholder="Cuéntanos tu idea o sugerencia..." required>{{ $esPodcast ? '' : old('mensaje') }}</textarea>
                        </label>

                        <button type="submit" class="contacto-form__boton">
                            <i class="fas fa-paper-plane"></i> Enviar mensaje
                        </button>
                    </form>
                </section>

                {{-- Recomendaciones para el podcast --}}
                <section class="contacto-card contacto-card--podcast" id="podcast">
                    <div class="contacto-card__cabecera">
                        <span class="contacto-card__icono contacto-card__icono--podcast">
                            <i class="fas fa-microphone-lines"></i>
                        </span>
                        <div>
                            <h2 class="contacto-card__titulo">Recomendar para el podcast</h2>
                            <p class="contacto-card__descripcion">
                                ¿Conoces a un estudiante, profesor o invitado que debería participar en el podcast de UMC News? Cuéntanos quién es y por qué.
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('contacto.store') }}" method="POST" class="contacto-form">
                        @csrf
                        <input type="hidden" name="tipo" value="podcast">

                        <div class="contacto-form__fila">
                            <label class="contacto-form__campo">
                                <span>Tu nombre</span>
                                <input type="text" name="nombre" value="{{ $esPodcast ? old('nombre') : '' }}"
                                       placeholder="Nombre y apellido" required>
                            </label>

                            <label class="contacto-form__campo">
                                <span>Tu correo</span>
                                <input type="email" name="email" value="{{ $esPodcast ? old('email') : '' }}"
                                       placeholder="correo@ejemplo.com" required>
                            </label>
                        </div>

                        <label class="contacto-form__campo">
                            <span>Persona recomendada</span>
                            <input type="text" name="persona_recomendada"
                                   value="{{ $esPodcast ? old('persona_recomendada') : '' }}"
                                   placeholder="Nombre de la persona que quieres recomendar" required>
                        </label>

                        <label class="contacto-form__campo">
                            <span>Enlace o red social <small>(opcional)</small></span>
                            <input type="text" name="enlace_persona"
                                   value="{{ $esPodcast ? old('enlace_persona') : '' }}"
                                   placeholder="Ej. @usuario en Instagram o un enlace a su perfil">
                        </label>

                        <label class="contacto-form__campo">
                            <span>¿Por qué la recomiendas?</span>
                            <textarea name="mensaje" rows="5"
                                      placeholder="Cuéntanos qué tema podría abordar y por qué sería una buena entrevista..." required>{{ $esPodcast ? old('mensaje') : '' }}</textarea>
                        </label>

                        <button type="submit" class="contacto-form__boton contacto-form__boton--podcast">
                            <i class="fas fa-microphone-lines"></i> Enviar recomendación
                        </button>
                    </form>
                </section>
            </div>

            <aside class="contacto-sidebar">
                <div class="contacto-info">
                    <h2 class="contacto-info__titulo"><i class="fas fa-envelope-open-text"></i> Escríbenos directo</h2>
                    <p class="contacto-info__texto">
                        Si prefieres usar tu correo personal, puedes escribirnos directamente a:
                    </p>
                    <a class="contacto-info__correo" href="mailto:{{ config('contacto.email') }}">
                        <i class="fas fa-envelope"></i>
                        <span>{{ config('contacto.email') }}</span>
                    </a>
                </div>

                <div class="contacto-info">
                    <h2 class="contacto-info__titulo"><i class="fas fa-list-check"></i> ¿Qué puedes enviar?</h2>
                    <ul class="contacto-info__lista">
                        <li><i class="fas fa-check"></i> Ideas para nuevas noticias.</li>
                        <li><i class="fas fa-check"></i> Temas que deberíamos investigar.</li>
                        <li><i class="fas fa-check"></i> Cartas o mensajes para la redacción.</li>
                        <li><i class="fas fa-check"></i> Recomendaciones para el podcast.</li>
                    </ul>
                </div>

                <div class="contacto-info contacto-info--podcast">
                    <h2 class="contacto-info__titulo"><i class="fas fa-podcast"></i> Podcast UMC</h2>
                    <p class="contacto-info__texto">
                        Buscamos voces de la comunidad universitaria para nuestras entrevistas.
                    </p>
                    <a class="contacto-info__enlace" href="#podcast">
                        Recomendar a alguien <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </aside>
        </div>
    </main>
</x-app-layout>
