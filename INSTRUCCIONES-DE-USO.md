# 🧭 Instrucciones de uso — UMC News

Guía paso a paso para **montar el proyecto en local** y que te funcione tal cual. Está pensada para compañeros que lo descarguen de GitHub.

> **Ojo:** el código vive dentro de la carpeta **`Umc-news/`**. Todo lo que sigue se hace **dentro de esa carpeta**.

---

## 1. Requisitos (versiones)

| Software | Versión mínima | Para qué sirve |
|----------|----------------|----------------|
| **PHP** | 8.2 o superior | Ejecutar Laravel |
| **Composer** | 2.x | Instalar las dependencias de PHP |
| **Node.js** | 18 o superior (probado con 22) | Compilar los assets (CSS/JS) |
| **npm** | 9 o superior | Lo mismo, viene con Node |
| **MySQL** o **MariaDB** | 5.7+ / 10.4+ | Base de datos *(o usa SQLite, más fácil, ver paso 4)* |

💡 **Atajo para instalar todo (Windows):** instala [XAMPP](https://www.apachefriends.org/) (trae PHP + MySQL) y [Node.js LTS](https://nodejs.org/). Para Composer, usa su [instalador oficial](https://getcomposer.org/download/).

---

## 2. Descargar el proyecto

```bash
git clone https://github.com/ElBaRtO205/Proyecto-Peri-dico.git
cd Proyecto-Peri-dico/Umc-news
```

*(Si lo descargas como ZIP desde GitHub, descomprímelo y entra a la carpeta `Umc-news`.)*

---

## 3. Instalar las dependencias

```bash
composer install
npm install
```

---

## 4. Configurar la base de datos

Tienes dos opciones. **La (a) es la más fácil si solo quieres probar.**

### a) SQLite (sin instalar nada extra) — recomendada para empezar
1. En el archivo `.env` (ver paso 5) deja `DB_CONNECTION=sqlite`.
2. Crea el archivo de la base de datos:
   ```bash
   touch database/database.sqlite
   ```

### b) MySQL / MariaDB (como funciona en el servidor original)
1. Arranca MySQL (en XAMPP, pulsa **Start** en el módulo MySQL).
2. Crea la base de datos vacía. Con phpMyAdmin o por consola:
   ```sql
   CREATE DATABASE umc_news CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. En el `.env` (paso 5) ajusta estos valores con **tu** usuario y contraseña:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=umc_news
   DB_USERNAME=root
   DB_PASSWORD=tu_contraseña
   ```

---

## 5. Crear y editar el archivo `.env`

El archivo `.env` **no se sube a GitHub** (es privado). Cópialo desde la plantilla:

```bash
cp .env.example .env        # en Windows: copy .env.example .env
```

Ábrelo y revisa dos cosas:

1. **La base de datos** (paso 4). Si usas SQLite deja la línea `DB_CONNECTION=sqlite`; si usas MySQL, descomenta y completa el bloque `DB_*`.
2. **`ADMIN_PASSWORD`**: ponle una contraseña de **mínimo 12 caracteres**. Se usará para crear el usuario administrador en el paso 7. Ejemplo:
   ```
   ADMIN_PASSWORD=MiClaveSegura123
   ```

Ahora genera la clave de la aplicación:

```bash
php artisan key:generate
```

---

## 6. Montar las tablas (migraciones)

Esto crea todas las tablas (usuarios, noticias con su `slug`, categorías, comentarios, acertijos, susurros, mensajes de contacto, sesiones…):

```bash
php artisan migrate
```

> ⚠️ **No uses el archivo `umc_news.sql`** de la raíz. Es un volcado **antiguo** (de antes de añadir los *slugs* y de borrar la tabla `autores`), así que no sirve para la versión actual. Las migraciones (este paso) ya crean todo lo necesario.

---

## 7. Cargar los datos iniciales (usuarios y categorías)

```bash
php artisan db:seed
```

Esto crea:
- **Usuario administrador:** `admin@umcnews.com` — contraseña: la que pusiste en `ADMIN_PASSWORD` (paso 5).
- **4 categorías:** Economía, Deportes, Cultura, Campus.

> Las **noticias NO vienen precargadas**: se crean desde el panel de administración (paso 10). Así cada quien publica las suyas.

---

## 8. Enlazar el almacenamiento de imágenes

Las fotos subidas desde el panel se guardan en `storage/app/public` y se sirven desde `public/storage`:

```bash
php artisan storage:link
```

---

## 9. Compilar los assets y arrancar

```bash
npm run build          # compila el CSS/JS para producción
php artisan serve      # arranca el servidor web
```

El proyecto queda en: **http://127.0.0.1:8000** (o la URL que te indique la consola).

> 🔁 **Cada vez que quieras arrancarlo**, solo repite: `php artisan serve`. Los pasos 1–8 solo se hacen **una vez** al montarlo.

---

## 10. Entrar al panel de administración

1. Ve a **http://127.0.0.1:8000/login** (o pulsa "Iniciar sesión" en la página).
2. Entra con:
   - **Email:** `admin@umcnews.com`
   - **Contraseña:** la que definiste en `ADMIN_PASSWORD`.
3. Ya dentro puedes **crear noticias**, gestionar categorías, el acertijo diario, ver los mensajes del formulario de contacto, etc.

> 💡 Las noticias se publican desde el panel; en cuanto marcas una como **"publicado"** aparece en la portada y en el feed RSS.

---

## 11. Ejecutar las pruebas (opcional)

El proyecto trae una suite de tests (33 pruebas) que comprueba que todo funciona:

```bash
php artisan test
```

Deben salir **todas en verde**.

---

## 🔧 Resumen rápido (copiar y pegar)

```bash
cd Umc-news
composer install
npm install
cp .env.example .env
#   → edita .env: elige SQLite o MySQL y pon ADMIN_PASSWORD (mín. 12 caracteres)
php artisan key:generate
touch database/database.sqlite      # solo si usas SQLite
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run build
php artisan serve
```

Luego entra a `http://127.0.0.1:8000/login` con `admin@umcnews.com` y tu `ADMIN_PASSWORD`.

---

## ❗ Problemas frecuentes

- **`Action Not Allowed` / falta `APP_KEY`:** no ejecutaste `php artisan key:generate` (paso 5).
- **Error al hacer `migrate`:** revisa que la BD exista y que las credenciales del `.env` sean correctas (paso 4).
- **El seeder falla con "Define ADMIN_PASSWORD":** falta la línea `ADMIN_PASSWORD=...` en el `.env`, o tiene menos de 12 caracteres (pasos 5 y 7).
- **Las imágenes no se ven:** falta `php artisan storage:link` (paso 8).
- **`storage/framework` o `bootstrap/cache` con error de permisos:** en Linux/macOS dale permisos: `chmod -R 775 storage bootstrap/cache`.
- **Puerto 8000 ocupado:** usa otro: `php artisan serve --port=8080`.
