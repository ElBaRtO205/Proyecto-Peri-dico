# 📰 UMC News — Qué es y cómo funciona

Resumen del proyecto: **qué hace**, **cómo está montado** y **cómo fluye la información**.

---

## 1. ¿Qué es?

**UMC News** es el **periódico digital de la Universidad Marítima del Caribe**. Es una *web app* hecha en **Laravel 12** donde:

- La **redacción (admin/lectores)** publica noticias con fotos, categorías y estado (borrador o publicado).
- Los **lectores** navegan la portada, leen noticias por categoría, comentan y se suscriben al feed RSS.
- Hay un **rincón secreto** ("Soplar al Viento") donde los estudiantes pueden dejar confesiones y rumores **anónimos**, desbloqueable con un acertijo diario.

**Stack:** PHP 8.2+ · Laravel 12 · MySQL (o SQLite) · Vite + Tailwind · Intervention Image (WebP) · Pest (tests).

---

## 2. ¿Cómo está organizado?

```
Umc-news/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # La lógica de cada pantalla/acción
│   │   │   ├── NoticiaController.php        # Portada, CRUD y lectura pública
│   │   │   ├── LoginController.php          # Login / registro / logout
│   │   │   ├── ComentarioController.php     # Comentarios de noticias
│   │   │   ├── ContactoController.php       # Formulario de contacto + bandeja admin
│   │   │   ├── SoplarAlVientoController.php # Rincón secreto (acertijo + susurros)
│   │   │   └── FeedController.php           # RSS y sitemap
│   │   ├── Middleware/EnsureUserIsAdmin.php # Bloquea a no-admins (403)
│   │   └── Requests/Post/                   # Validaciones al crear/editar noticias
│   ├── Models/                # 7 modelos (Noticia, Categoria, Usuario, etc.)
│   ├── Services/Post/PostService.php        # Opera las noticias (CRUD)
│   └── Providers/AppServiceProvider.php     # Categorías cacheadas (6 h)
├── database/
│   ├── migrations/            # Crea todas las tablas
│   └── seeders/               # Admin inicial + 4 categorías
├── resources/views/           # Plantillas Blade (HTML dinámico)
├── routes/web.php             # Todas las rutas (el "mapa" del sitio)
└── public/css, js, img/       # Estilos, scripts e imágenes
```

---

## 3. Módulos y funcionalidades

### 🏠 Portada (`/`)
- Muestra **una noticia principal** (la marcada como `es_principal`).
- Debajo, **secciones por categoría** con sus noticias más recientes:
  **Deportes** (2), **Cultura** (1), **Economía** (4), **Campus** (2).
- Todo se arma **desde la base de datos** (nada está escrito a mano en la plantilla).
- Incluye el **sello misterioso** que da acceso al rincón secreto.
- *Solo aparecen noticias con estado `publicado`* (los borradores son invisibles para el público).

### 📄 Lectura de noticias
- **`/noticia/{slug}`**: cada noticia tiene una **URL limpia y legible** con su *slug* generado del título.
  - Los enlaces viejos por número (`/noticia/5`) **redirigen con 301** a la URL nueva.
  - Cada página incluye **SEO completo**: meta description, canónica, Open Graph, Twitter Cards y **JSON-LD `NewsArticle`** (para que Google y las redes la entiendan).
  - Muestra **3 noticias relacionadas** y el **formulario de comentarios**.
- **`/categoria/{id}`**: lista las noticias de una categoría, paginadas (10 por página).

### 💬 Comentarios (`/noticia/{id}/comentario`)
- Cualquier visitante puede comentar (nombre + contenido), con *throttling* de 10 por minuto.

### 🔐 Autenticación y roles
- **`/login`**: iniciar sesión. Si eres **Administrador** te lleva al panel; si no, a la portada.
- **Registro**: crea usuarios con rol **Editor** (contraseña mínimo de 12 caracteres, confirmada).
- **`/logout`**: cierra sesión.
- **Dos roles:**
  - **Administrador**: acceso total (CRUD + herramientas admin).
  - **Editor**: puede crear y editar noticias, pero **no** toca las herramientas de administración.

### 🛠 Panel de administración (`/noticia`, protegido)
- **CRUD de noticias** (crear, ver, editar, borrar) usando `PostService`.
- Al **subir una foto**: se redimensiona a 1200 px, se convierte a **WebP** (ligero) y se guarda con nombre aleatorio en `storage/noticias/año/mes/`.
- **Solo puede haber una noticia principal** a la vez (si marcas una, se desmarca la anterior).
- Si algo falla al editar, se hace **rollback** y se borra la imagen recién subida (no deja basura).

### 🌬 Soplar al Viento (`/soplar-al-viento`) — el rincón secreto
- Es un **muro de susurros anónimos** de la comunidad universitaria.
- **Para entrar** hay que:
  1. Resolver el **acertijo del día** (cuya respuesta aparece escondida en la categoría *Campus*), **o**
  2. Ser **Administrador**.
- Los susurros son de tipo **Confesión, Rumor, Verso o Desahogo**, con apodo opcional (si no, sale como *"Brisa Anónima"*).
- Cada susurro recibe **"soplos"** (como un *like*), y **una sola vez por sesión** para no inflar el contador.
- El **Administrador** puede borrar susurros y cambiar el acertijo del día desde el panel.

### 📬 Contacto (`/contacto`) — solo enlazado desde el pie de página
- Formulario con **3 tipos**: *sugerencia*, *contacto* o *podcast*.
- En modo **podcast** puedes **recomendar a una persona** (con su enlace).
- Los mensajes se guardan en la base de datos y el admin los ve en su **bandeja** (`/admin/mensajes`), pudiendo marcarlos como leídos o borrarlos.

### 📡 Feeds y SEO
- **`/feed.xml`**: feed **RSS** con las 20 noticias más recientes (los navegadores y lectores lo detectan solos).
- **`/sitemap.xml`**: **sitemap** con la portada, las categorías y todas las noticias publicadas (bueno para el posicionamiento en Google).

---

## 4. ¿Cómo fluye la información? (el camino de una noticia)

1. Un **Administrador** entra al panel y **crea una noticia** (título, categoría, contenido, foto y estado).
2. Al guardar, el sistema:
   - Genera automáticamente el **slug** (URL limpia) a partir del título.
   - **Procesa la foto** (redimensiona + WebP) y la sube al almacenamiento.
   - Si es la principal, **desmarca la anterior**.
3. Si la marca como **`publicado`**, la noticia aparece en la **portada**, en su **categoría**, en el **RSS** y en el **sitemap**. Si queda en **borrador**, solo la ve el equipo en el panel.
4. Los **lectores** la abren en `/noticia/{slug}`, **comentan** y comparten (el SEO ayuda a que se vea).
5. Los **mensajes del formulario** y los **susurros** quedan guardados para que el admin los gestione.

---

## 5. La base de datos (modelos y relaciones)

| Modelo | Tabla | Qué representa |
|--------|-------|----------------|
| `Noticia` | `noticias` | Los artículos del periódico |
| `Categoria` | `categorias` | Secciones (Economía, Deportes, Cultura, Campus) |
| `Comentario` | `comentarios` | Comentarios en las noticias |
| `Usuario` | `usuarios` | Redacción (Administrador / Editor) |
| `Acertijo` | `acertijos` | El acertijo diario que desbloquea el secreto |
| `Susurro` | `susurros` | Los mensajes anónimos de Soplar al Viento |
| `MensajeContacto` | `mensajes_contacto` | Mensajes del formulario de contacto |

**Relaciones principales:**
- Una **Noticia** *pertenece a* una **Categoria** y *tiene muchos* **Comentarios**.
- Una **Noticia** *puede tener* un **Usuario** (quien la publicó).

> Detalles técnicos: los modelos usan claves propias (`id_noticia`, `id_categoria`…) y `Noticia` genera su *slug* solo al guardar.

---

## 6. Seguridad implementada

- **Roles**: el middleware `EnsureUserIsAdmin` devuelve **403** si un Editor intenta entrar a herramientas de administración.
- **Throttling**: límite de intentos en login (5/min), comentarios (10/min), contacto (10/min) y publicar susurros (5/min).
- **Validación**: todos los formularios validan sus datos antes de guardar.
- **Transacciones**: al crear/editar noticias se usa `DB::transaction` para no dejar datos a medias.
- **Contraseñas**: se guardan con *hash* (nunca en texto plano).

---

## 7. Pruebas

El proyecto trae **33 pruebas automatizadas** (framework **Pest**) que comprueban la seguridad, el CRUD, el SEO, los feeds y la interfaz. Se ejecutan con:

```bash
php artisan test
```

---

## 8. Resumen en una frase

> **UMC News** es un periódico universitario donde la redacción publica noticias (con SEO, RSS y fotos optimizadas), los lectores leen y comentan, y los estudiantes tienen además un **muro anónimo secreto** desbloqueable con un acertijo diario.
