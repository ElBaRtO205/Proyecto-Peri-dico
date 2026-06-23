/*
    Lógica del login y registro
    - Toggle para mostrar/ocultar contraseña
    - Cambio entre paneles de login y registro
    - En móvil se usan enlaces para cambiar
*/

// Mostrar/ocultar contraseña
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
        button.setAttribute('aria-label', 'Ocultar contraseña');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
        button.setAttribute('aria-label', 'Mostrar contraseña');
    }
}

// Cambio entre login y registro
const container = document.getElementById('autenticacion');
const btnRegister = document.getElementById('register');
const btnLogin = document.getElementById('login');

// Botones del intercambiador (desktop)
if (btnRegister) {
    btnRegister.addEventListener('click', () => {
        container.classList.add('autenticacion--activo');
    });
}

if (btnLogin) {
    btnLogin.addEventListener('click', () => {
        container.classList.remove('autenticacion--activo');
    });
}

// Enlaces de cambio en móvil
const movilLogin = document.getElementById('movil-login');
const movilRegister = document.getElementById('movil-register');

if (movilLogin) {
    movilLogin.addEventListener('click', (e) => {
        e.preventDefault();
        container.classList.remove('autenticacion--activo');
    });
}

if (movilRegister) {
    movilRegister.addEventListener('click', (e) => {
        e.preventDefault();
        container.classList.add('autenticacion--activo');
    });
}

/* Prevenir envío de formularios (por ahora es demo)
document.querySelectorAll('.autenticacion__formulario').forEach(form => {
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Formulario enviado (simulación)');
    });
});
*/