-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 05-11-2026 a las 02:40:23
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `umc_news`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acertijos`
--

CREATE TABLE `acertijos` (
  `id_acertijo` bigint(20) UNSIGNED NOT NULL,
  `pregunta` text NOT NULL,
  `respuesta` varchar(255) NOT NULL,
  `pista` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autores`
--

CREATE TABLE `autores` (
  `id_autor` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `correo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `slug`, `descripcion`) VALUES
(1, 'Economia', 'economia', 'Economía, finanzas y becas: lo que afecta el bolsillo del estudiante.'),
(2, 'Deportes', 'deportes', 'Equipos, torneos y logros deportivos de la comunidad UMC.'),
(3, 'Cultura', 'cultura', 'Eventos culturales, cine, música y entretenimiento.'),
(4, 'Campus', 'campus', 'Vida universitaria, rumores y noticias de la comunidad.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `id_comentario` bigint(20) UNSIGNED NOT NULL,
  `id_noticia` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` bigint(20) UNSIGNED DEFAULT NULL,
  `autor` varchar(255) NOT NULL,
  `contenido` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `comentarios`
--

INSERT INTO `comentarios` (`id_comentario`, `id_noticia`, `id_usuario`, `autor`, `contenido`, `status`, `created_at`, `updated_at`) VALUES
(2, 1, NULL, 'alexalex', 'me gusta que se esten actualizando', 'aprobado', '2026-10-31 01:42:12', '2026-11-04 01:20:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes_contacto`
--

CREATE TABLE `mensajes_contacto` (
  `id_mensaje` bigint(20) UNSIGNED NOT NULL,
  `tipo` varchar(30) NOT NULL DEFAULT 'sugerencia',
  `nombre` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `asunto` varchar(150) DEFAULT NULL,
  `mensaje` text NOT NULL,
  `persona_recomendada` varchar(120) DEFAULT NULL,
  `enlace_persona` varchar(255) DEFAULT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mensajes_contacto`
--

INSERT INTO `mensajes_contacto` (`id_mensaje`, `tipo`, `nombre`, `email`, `asunto`, `mensaje`, `persona_recomendada`, `enlace_persona`, `leido`, `created_at`, `updated_at`) VALUES
(3, 'sugerencia', 'si', 'tumadre@gmail.com', 'vistas de noticias', 'son una mierda, cambienla porfa', NULL, NULL, 1, '2026-10-31 02:02:54', '2026-10-31 02:04:39'),
(4, 'podcast', 'alex', 'tumadre@gmail.com', NULL, 'tiene mucha historia, todos debemos sabemos sus cuentos', 'profesor de algebra', NULL, 1, '2026-10-31 02:08:03', '2026-10-31 02:08:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_06_14_214758_create_usuarios_table', 1),
(2, '2026_06_14_215912_create_categorias_table', 1),
(3, '2026_06_14_220055_create_autores_table', 1),
(4, '2026_06_14_220143_create_noticias_table', 1),
(5, '2026_07_21_230754_create_comentarios_table', 1),
(6, '2026_08_14_154823_create_sessions_table', 1),
(10, '2026_10_29_000000_change_default_usuarios_rol_to_editor', 2),
(11, '2026_10_30_161649_create_acertijos_table', 2),
(12, '2026_10_30_161707_create_susurros_table', 2),
(13, '2026_10_30_180000_create_mensajes_contacto_table', 3),
(14, '2026_10_30_190000_add_slug_to_noticias_table', 4),
(15, '2026_11_03_000000_change_usuarios_rol_editor_to_lector', 5),
(16, '2026_11_03_000100_add_moderation_to_comentarios_table', 5),
(17, '2026_11_03_000200_add_traceability_and_scheduling_to_noticias', 6),
(18, '2026_11_03_000300_add_slug_to_categorias_table', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticias`
--

CREATE TABLE `noticias` (
  `id_noticia` bigint(20) UNSIGNED NOT NULL,
  `id_categoria` bigint(20) UNSIGNED NOT NULL,
  `id_usuario_admin` bigint(20) UNSIGNED DEFAULT NULL,
  `autor` varchar(255) DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `contenido` text NOT NULL,
  `imagen_noticia` varchar(255) DEFAULT NULL,
  `fecha_publicacion` date NOT NULL DEFAULT curdate(),
  `programada_para` timestamp NULL DEFAULT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'borrador',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `noticias`
--

INSERT INTO `noticias` (`id_noticia`, `id_categoria`, `id_usuario_admin`, `autor`, `titulo`, `slug`, `contenido`, `imagen_noticia`, `fecha_publicacion`, `programada_para`, `es_principal`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, NULL, 'carlos mendoza', 'Modernización en los simuladores de navegación y maquetas navales', 'modernizacion-en-los-simuladores-de-navegacion-y-maquetas-navales', 'La universidad ha iniciado un proceso de renovación técnica en sus simuladores de maniobra y navegación. Con esta actualización, los estudiantes de Ciencias Náuticas podrán realizar prácticas con escenarios meteorológicos en tiempo real y nuevos modelos de buques de gran calado, fortaleciendo sus competencias operativas antes de sus pasantías en alta mar.', 'noticias/2026/09/cdb0e2e1-ffba-4a78-aeb3-9898b5d37ab1.webp', '2026-09-20', NULL, 1, 'publicado', '2026-09-20 04:00:00', '2026-09-20 04:00:00'),
(2, 1, NULL, 'mariana silvano', 'Jornada de mantenimiento preventivo y seguridad en instalaciones del campus', 'jornada-de-mantenimiento-preventivo-y-seguridad-en-instalaciones-del-campus', 'Durante la última semana se llevaron a cabo labores de optimización en la infraestructura de red, sistemas de videovigilancia e iluminación en los talleres y laboratorios de la institución. Estas mejoras buscan garantizar un entorno de aprendizaje seguro y eficiente para toda la  comunidad universitaria.', 'noticias/2026/09/09c9b6f2-c37b-4f99-a194-ef8f4d3d4501.webp', '2026-09-20', NULL, 0, 'publicado', '2026-09-20 04:00:00', '2026-11-05 00:00:02'),
(4, 3, NULL, 'elena rodriguez', 'Estudiantes presentan proyectos de innovación tecnológica e informática', 'estudiantes-presentan-proyectos-de-innovacion-tecnologica-e-informatica', 'En el marco de la feria de proyectos del semestre, alumnos de ingeniería expusieron soluciones de software orientadas al desarrollo web, gestión de bases de datos y seguridad de sistemas. Los docentes evaluadores destacaron el alto nivel técnico de las propuestas presentadas.', 'noticias/2026/09/894051ec-4500-4252-ad44-5a803986a3e0.webp', '2026-09-20', NULL, 0, 'publicado', '2026-09-20 04:00:00', '2026-09-20 04:00:00'),
(5, 1, NULL, 'andres paredes', 'Taller sobre gestión ambiental y preservación de ecosistemas marinos', 'taller-sobre-gestion-ambiental-y-preservacion-de-ecosistemas-marinos', 'e dictó una ponencia especial enfocada en el impacto de los desechos plásticos en las zonas costeras y las normativas internacionales para la protección del medio ambiente marino. La actividad contó con una alta participación de la comunidad estudiantil.', 'noticias/2026/09/7c199a29-700c-4add-8602-a0216f23c87c.webp', '2026-09-20', NULL, 0, 'publicado', '2026-09-20 04:00:00', '2026-09-20 04:00:00'),
(6, 4, NULL, 'umc', 'Actualización de cronograma para entrega de documentos y carnetización', 'actualizacion-de-cronograma-para-entrega-de-documentos-y-carnetizacion', 'Secretaría General informa a todos los alumnos regulares que el proceso de actualización de datos y emisión de carnets estudiantiles se mantendrá activo hasta el final de la presente mes. Es requisito indispensable presentar la planilla de inscripción vigente.', 'noticias/2026/09/912818fc-c4d2-47f4-94ff-9a12e6462131.webp', '2026-09-20', NULL, 0, 'publicado', '2026-09-20 04:00:00', '2026-09-20 04:00:00'),
(7, 2, NULL, 'yo', 'si', 'si', 'tal vez', NULL, '2026-10-30', NULL, 0, 'publicado', '2026-10-30 04:00:00', '2026-11-05 00:01:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticia_auditorias`
--

CREATE TABLE `noticia_auditorias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_noticia` bigint(20) UNSIGNED DEFAULT NULL,
  `id_usuario` bigint(20) UNSIGNED DEFAULT NULL,
  `accion` varchar(20) NOT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `cambios` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cambios`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `noticia_auditorias`
--

INSERT INTO `noticia_auditorias` (`id`, `id_noticia`, `id_usuario`, `accion`, `titulo`, `cambios`, `created_at`) VALUES
(1, 2, 1, 'actualizado', 'Jornada de mantenimiento preventivo y seguridad en instalaciones del campus', '{\"antes\":{\"status\":\"borrador\"},\"despues\":{\"status\":\"publicado\"}}', '2026-11-05 00:00:02'),
(2, 7, 1, 'actualizado', 'si', '{\"antes\":{\"id_categoria\":1},\"despues\":{\"id_categoria\":\"2\"}}', '2026-11-05 00:01:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('qM82qOwgGYM6GVaP8CLuVcmeOHAeg4jLTVZquTOA', 1, '::1', 'Mozilla/5.0 (X11; Linux x86_64; rv:159.0) Gecko/20100101 Firefox/159.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRlp1aUU3MlV0anVueWpwVzhJRDZTeWx1UTliMU5va0tmMWlsckUxSiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9wdWJsaWMvaW5kZXgucGhwIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1793840863);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `susurros`
--

CREATE TABLE `susurros` (
  `id_susurro` bigint(20) UNSIGNED NOT NULL,
  `apodo` varchar(40) NOT NULL DEFAULT 'Brisa Anónima',
  `contenido` text NOT NULL,
  `categoria_viento` varchar(30) NOT NULL DEFAULT 'Confesión',
  `soplos` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('Administrador','Lector') NOT NULL DEFAULT 'Lector',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_completo`, `usuario`, `email`, `contrasena`, `rol`, `fecha_creacion`) VALUES
(1, 'Administrador General', 'admin', 'admin@umcnews.com', '$2y$12$WtJhrPwD0oCpmCZEW3IPPuIQRpEt1H6jrGBN/iCA01dV8absNc03G', 'Administrador', '2026-08-14 18:31:21'),
(2, 'alezy', 'elalex', 'alexi@gmail.com', '$2y$12$4dkP/dnwmUqwM8PER0tEBuCSWxvHe2umn76nI6131gKNJAVKbZO3C', 'Lector', '2026-10-30 02:19:49');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acertijos`
--
ALTER TABLE `acertijos`
  ADD PRIMARY KEY (`id_acertijo`);

--
-- Indices de la tabla `autores`
--
ALTER TABLE `autores`
  ADD PRIMARY KEY (`id_autor`),
  ADD UNIQUE KEY `autores_correo_unique` (`correo`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `categorias_nombre_unique` (`nombre`),
  ADD UNIQUE KEY `categorias_slug_unique` (`slug`);

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id_comentario`),
  ADD KEY `comentarios_id_noticia_foreign` (`id_noticia`),
  ADD KEY `comentarios_id_usuario_foreign` (`id_usuario`),
  ADD KEY `comentarios_status_index` (`status`);

--
-- Indices de la tabla `mensajes_contacto`
--
ALTER TABLE `mensajes_contacto`
  ADD PRIMARY KEY (`id_mensaje`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id_noticia`),
  ADD UNIQUE KEY `noticias_slug_unique` (`slug`),
  ADD KEY `noticias_id_categoria_foreign` (`id_categoria`),
  ADD KEY `noticias_id_usuario_admin_foreign` (`id_usuario_admin`);

--
-- Indices de la tabla `noticia_auditorias`
--
ALTER TABLE `noticia_auditorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `noticia_auditorias_id_usuario_foreign` (`id_usuario`),
  ADD KEY `noticia_auditorias_id_noticia_created_at_index` (`id_noticia`,`created_at`),
  ADD KEY `noticia_auditorias_accion_index` (`accion`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `susurros`
--
ALTER TABLE `susurros`
  ADD PRIMARY KEY (`id_susurro`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuarios_usuario_unique` (`usuario`),
  ADD UNIQUE KEY `usuarios_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acertijos`
--
ALTER TABLE `acertijos`
  MODIFY `id_acertijo` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `autores`
--
ALTER TABLE `autores`
  MODIFY `id_autor` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id_comentario` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `mensajes_contacto`
--
ALTER TABLE `mensajes_contacto`
  MODIFY `id_mensaje` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id_noticia` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `noticia_auditorias`
--
ALTER TABLE `noticia_auditorias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `susurros`
--
ALTER TABLE `susurros`
  MODIFY `id_susurro` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_id_noticia_foreign` FOREIGN KEY (`id_noticia`) REFERENCES `noticias` (`id_noticia`) ON DELETE CASCADE,
  ADD CONSTRAINT `comentarios_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD CONSTRAINT `noticias_id_categoria_foreign` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON UPDATE CASCADE,
  ADD CONSTRAINT `noticias_id_usuario_admin_foreign` FOREIGN KEY (`id_usuario_admin`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `noticia_auditorias`
--
ALTER TABLE `noticia_auditorias`
  ADD CONSTRAINT `noticia_auditorias_id_noticia_foreign` FOREIGN KEY (`id_noticia`) REFERENCES `noticias` (`id_noticia`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `noticia_auditorias_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
