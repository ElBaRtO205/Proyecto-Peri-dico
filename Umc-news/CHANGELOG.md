# Changelog — UMC News

Resumen de los cambios realizados en la última sesión de trabajo.

---

## 🗓️ Semana 1 — Arreglo de bugs y limpieza

### Seguridad
- [ ] Las rutas de herramientas admin (acertijo, borrar susurros, buzón de mensajes) ahora exigen rol Administrador. Un **Editor recibe 403**.
- [ ] Eliminado el grupo de rutas `Route::middleware(['auth'])` que permitía a cualquier usuario autenticado modificar el acertijo.
- [ ] Eliminadas rutas duplicadas por *closures* en `/soplar-al-viento` que devolvían HTML crudo y tenían la respuesta del acertijo hardcodeada.
- [ ] Añadidos `whereNumber`, *throttles* y validación a las rutas públicas.

### Modelo y controlador
- [ ] Clase `noticia` renombrada a `Noticia`; añadido el scope `publicados()` para unificar el filtro de noticias visibles (`status = 'publicado'`).
- [ ] Eliminada la relación muerta `autor()` (el campo `autor` es un texto plano); ahora se muestra el autor real con *fallback* a "Redacción UMC".
- [ ] Portada dinámica: las secciones se generan desde la base de datos en lugar de 4 bloques duplicados hardcodeados.
- [ ] Categorías cacheadas en memoria (6 h) en `AppServiceProvider`.

### Vistas
- [ ] Creado `public/img/placeholder-noticia.svg` para reemplazar 4 rutas de imagen rotas.
- [ ] Footer: enlaces de secciones generados dinámicamente desde las categorías reales de la BD.
- [ ] Layout admin: corregido doble `<body>`, eliminado `<link>` vacío y JS muerto, fecha dinámica en español y añadido `noindex`.
- [ ] Layout público: fuentes movidas dentro de `<head>`.

### Limpieza de código muerto
- [ ] Eliminados: `PostController.php`, `post.php`, `User.php`, `UserFactory.php`, `Autor.php` (+ su migración), `noticia/show.blade.php`, `admin.js`, directorio `app/View/` y 2 archivos basura en la raíz.
- [ ] `.env` / `.env.example`: `APP_NAME="UMC News"`, `APP_LOCALE=es`, `APP_FAKER_LOCALE=es_VE`, `QUEUE_CONNECTION=sync`.

---

## 🗓️ Semana 2 — URLs limpias, SEO y feeds

### URLs con slug
- [ ] Nueva migración que añade la columna `slug` (única) a `noticias` y genera los slugs existentes.
- [ ] El modelo genera automáticamente el slug desde el título (evitando duplicados) y las rutas públicas lo usan como identificador.
- [ ] Ruta pública `/noticia/{slug}`; los enlaces antiguos por ID (`/noticia/5`) redirigen con **301** a la URL limpia.

### SEO
- [ ] Meta description, URL canónica y `<title>` con el titular de la noticia.
- [ ] **Open Graph** (og:title, og:description, og:image, og:url, og:type) y **Twitter Cards**.
- [ ] Datos estructurados **JSON-LD `NewsArticle`** (titular, fechas, autor, publicador, logo).
- [ ] `@stack('head')` en el layout para inyectar meta por página.

### Feeds
- [ ] **RSS** en `/feed.xml` (20 noticias más recientes) con autodiscovery en el `<head>`.
- [ ] **Sitemap** en `/sitemap.xml` (portada + categorías + noticias publicadas).

---

## 🐛 Correcciones reportadas

### Gravedad alta
- [ ] **Icono de la portada enorme y sin efectos**: `favicon.ico` estaba vacío y no existía CSS para el sello. Añadido todo el estilo del sello (tamaño fijo 64px, latido, hover) y enlace directo al SVG.
- [ ] **TypeError al editar una noticia**: `getRouteKeyName()` devolvía `slug`, pero el controlador esperaba `int $id`. Los enlaces del panel ahora pasan el ID numérico.

### Resto
- [ ] **Formulario "Crear noticia" devolvía 404**: la ruta `/noticia/{slug}` se registraba antes que el resource admin. Reordenada.
- [ ] **Botones "Ver Soplar al Viento" y "Mensajes" ilegibles**: añadida la clase `.panel-admin__btn-nueva--claro` (texto blanco) para fondos oscuros.
- [ ] Añadido el botón **"Volver al panel de admin"** en la página *Soplar al Viento* (solo visible para administradores).
- [ ] El **logo y el nombre de la cabecera** ahora son un enlace clicable al inicio.

---

## 📁 Renombrado del proyecto

- [ ] Carpeta **`practica-crud` → `Umc-news`** (movida, no duplicada).
- [ ] Recreado el symlink `public/storage` como **relativo** (antes apuntaba por ruta absoluta y se habría roto).
- [ ] `package-lock.json`: `name` actualizado a `umc-news`.

---

## 🧪 Tests

- [ ] De **13** a **33 tests** (107 aserciones), todos en verde.
- [ ] Nuevos: autorización de herramientas admin, SEO/feeds, y correcciones de UI.
