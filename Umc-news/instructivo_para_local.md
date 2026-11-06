# UMC News

Periódico oficial de la Universidad Marítima del Caribe, construido con Laravel 12 y Blade. Incluye portada dinámica, noticias con SEO, comentarios moderados, publicación programada, historial de cambios y la sección secreta **Soplar al Viento**.

## Requisitos

- PHP 8.2 o superior con las extensiones habituales de Laravel (`pdo`, `mbstring`, `openssl`, `fileinfo`, `gd`).
- Composer 2.
- SQLite para desarrollo rápido, o MySQL/MariaDB para producción.(recomendable usar mysql)-(el usuario predeterminado admi es: admin@umcnews.com)
- No requiere Node.js ni Vite: los estilos y scripts se sirven directamente desde `public/`.

## Instalación local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

La configuración de ejemplo usa SQLite. Crea el archivo si no existe:

```bash
touch database/database.sqlite
```

Para usar MySQL, ajusta en `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=umc_news
DB_USERNAME=root
DB_PASSWORD=
```

El seeder del administrador exige una contraseña de al menos 12 caracteres:

```dotenv
ADMIN_PASSWORD=una-clave-segura-de-12-o-mas
```

Después ejecuta `php artisan db:seed`.

## Zona horaria

El proyecto trabaja en `America/Caracas`:

```dotenv
APP_TIMEZONE=America/Caracas
DB_TIMEZONE=-04:00
```

## Comandos útiles

```bash
php artisan migrate              # aplicar migraciones
php artisan migrate:fresh --seed # recrear la base con datos iniciales
php artisan test                 # ejecutar la suite de pruebas
vendor/bin/pint                  # formatear el código PHP
vendor/bin/pint --test           # comprobar formato sin modificar archivos
php artisan schedule:list        # ver tareas programadas
```

En producción, programa el scheduler de Laravel cada minuto:

```cron
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

Esto publica automáticamente las noticias en estado **Programado** cuando llega su fecha y hora.

## Funcionalidades principales

- **Portada dinámica**: las secciones se generan desde las categorías reales de la base de datos, con un máximo de 4 noticias por sección.
- **Categorías con slug**: URLs como `/categoria/campus`; los enlaces antiguos por ID redirigen con `301`.
- **Noticias**: borrador, publicado y programado; noticia principal con promoción automática si la actual se elimina o pasa a borrador.
- **SEO**: metadatos, Open Graph, Twitter Cards, JSON-LD `NewsArticle`, RSS en `/feed.xml` y sitemap en `/sitemap.xml`.
- **Comentarios**: solo usuarios registrados, validación de noticia publicada, honeypot, rate limit, moderación y paginación.
- **Roles**: `Administrador` y `Lector`. Los lectores pueden comentar y acceder a la sección secreta tras resolver el acertijo.
- **Panel de administración**: barra lateral con navegación, métricas de publicaciones, pendientes de moderación, publicaciones recientes y diseño adaptable a móvil.
- **Moderación visual**: comentarios, mensajes y auditoría comparten tarjetas, filtros y estados consistentes.
- **Soplar al Viento**: susurros anónimos con filtros, aliento y acceso protegido por autenticación más acertijo. Los administradores pueden usar **Limpiar todo** para borrar susurros, acertijos y claves de acceso.
- **Auditoría**: historial de creaciones, ediciones y eliminaciones de noticias.
- **Tiempo de lectura** estimado en cada noticia y en las tarjetas de portada/categoría.
- **Transiciones de página**: portada, categorías y noticias aparecen desde abajo; respetan la preferencia de movimiento reducido del sistema.

## Panel de administración

El panel vive en `/admin/noticias` y requiere rol `Administrador`:

- **Panel**: métricas de publicaciones, pendientes de comentarios y mensajes, y listado de noticias recientes.
- **Comentarios**: aprobación, rechazo y eliminación con filtros por estado.
- **Mensajes**: buzón de contacto con marcado de leído y respuesta por correo.
- **Historial**: auditoría de creaciones, ediciones y eliminaciones.
- **Acertijo**: edición del acertijo diario desde el propio panel.
- **Soplar al Viento**: acceso rápido y opción **Limpiar todo** para dejar la sección sin susurros, sin acertijo y sin clave de acceso.

## Estructura relevante

```text
app/
  Console/Commands/       Comando de publicación programada
  Http/Controllers/       Noticias, comentarios, contacto, feeds, sección secreta
  Models/                 Noticia, Categoria, Comentario, Usuario, auditoría, etc.
database/
  migrations/             Esquema y cambios incrementales
  seeders/                Administrador y categorías iniciales
public/
  css/                    Hojas de estilo por pantalla
  js/                     Scripts de la portada, noticias, categorías y panel
resources/views/          Vistas Blade
routes/web.php            Rutas públicas y del panel
routes/console.php        Tareas programadas
tests/Feature/            Pruebas de comportamiento
```

## Pruebas y CI

La suite usa Pest y SQLite en memoria:

```bash
php artisan test
```

El repositorio incluye un flujo de GitHub Actions en `.github/workflows/ci.yml` que ejecuta las pruebas y comprueba el formato con Pint en cada push y pull request.

## Notas de mantenimiento

- Los assets viven en `public/css` y `public/js`; no hay build de Vite/Tailwind.
- Para una categoría nueva no hace falta tocar código: se crea en la base de datos y aparece automáticamente en la portada, el menú y el footer.
- La sección **Campus** muestra el acertijo únicamente a usuarios autenticados.
- Si se ejecuta **Limpiar todo** en Soplar al Viento, la sección queda solo para administradores hasta que se cree un acertijo nuevo.
- Las noticias eliminadas conservan su rastro en la auditoría aunque la fila original ya no exista.
- Las transiciones están en `public/css/estilos.css` y `public/js/transiciones-pagina.js`; se desactivan con `prefers-reduced-motion`.
- El comando `noticias:publicar-programadas` debe ejecutarse cada minuto mediante el scheduler de Laravel.
