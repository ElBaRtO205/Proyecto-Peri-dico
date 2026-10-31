<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>UMC News</title>
    <link>{{ url('/') }}</link>
    <description>Periódico oficial de la Universidad Marítima del Caribe. Noticias de deportes, cultura, académico y rumores del campus.</description>
    <language>es-ve</language>
    <lastBuildDate>{{ now()->toRfc7231String() }}</lastBuildDate>
    <atom:link href="{{ url('/feed.xml') }}" rel="self" type="application/rss+xml"/>

    @foreach ($noticias as $noticia)
      @php
        $enlace = route('noticia.show', $noticia);
        $descripcion = Str::limit(strip_tags($noticia->contenido), 300);
      @endphp
      <item>
        <title>{{ $noticia->titulo }}</title>
        <link>{{ $enlace }}</link>
        <guid isPermaLink="true">{{ $enlace }}</guid>
        <category>{{ $noticia->categoria->nombre ?? 'General' }}</category>
        <description>{{ $descripcion }}</description>
        <pubDate>{{ \Carbon\Carbon::parse($noticia->fecha_publicacion)->toRfc7231String() }}</pubDate>
      </item>
    @endforeach
  </channel>
</rss>
