# 📋 Resumen de hoy — UMC News

## 🗓️ Semana 1 — Bugs y limpieza
- Herramientas admin (acertijo, susurros, buzón) ahora **solo Administrador**; Editor recibe 403
- Eliminadas rutas duplicadas/inseguras en Soplar al Viento
- Modelo **`Noticia`** con scope `publicados()` + relación muerta `autor()` eliminada
- Portada **dinámica** desde la BD + placeholder para 4 imágenes rotas
- Footer dinámico, categorías cacheadas 6h, layout admin corregido (doble body, fecha, noindex)
- Código muerto borrado (PostController, User, Autor, admin.js, app/View, archivos basura)
- `.env`: nombre, locale `es`, queue `sync`

## 🗓️ Semana 2 — SEO y feeds
- URLs limpias con **slug** (+ redirección 301 desde IDs viejos)
- **SEO**: meta description, canónica, Open Graph, Twitter Cards, **JSON-LD NewsArticle**
- **RSS** `/feed.xml` + **Sitemap** `/sitemap.xml`

## 🐛 Bugs corregidos
- Icono de portada enorme/sin efectos (favicon vacío + falta CSS del sello)
- **TypeError** al editar noticia (slug vs `int $id`)
- "Crear noticia" daba **404** (orden de rutas)
- Botones "Soplar al Viento" y "Mensajes" ilegibles → texto blanco
- Añadido **"Volver al panel de admin"** en Soplar al Viento
- **Logo/título clicable** → inicio

## 📁 Renombrado
- `practica-crud` → **`Umc-news`** (movido, sin copiar) + symlink storage relativo

## 🔧 GitHub
- Restaurado el proyecto (push hecho desde la carpeta equivocada) + eliminado el `.git` duplicado

## 🧪 Tests
- De 13 → **33 tests** (107 aserciones), todos en verde
