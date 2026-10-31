<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>{{ url('/') }}</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>

  @foreach ($categorias as $categoria)
    <url>
      <loc>{{ route('noticia.categoria', $categoria->id_categoria) }}</loc>
      <changefreq>weekly</changefreq>
      <priority>0.7</priority>
    </url>
  @endforeach

  @foreach ($noticias as $noticia)
    <url>
      <loc>{{ route('noticia.show', $noticia->slug) }}</loc>
      <lastmod>{{ \Carbon\Carbon::parse($noticia->fecha_publicacion)->format('Y-m-d') }}</lastmod>
      <priority>0.8</priority>
    </url>
  @endforeach
</urlset>
