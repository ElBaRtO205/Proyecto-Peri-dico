(function () {
  'use strict';

  function initShare() {
    const rail = document.querySelector('.share-rail');
    if (!rail) return;

    const toggle = rail.querySelector('[data-share-toggle]');
    const menu = rail.querySelector('.share-menu');
    const copyButton = rail.querySelector('[data-copy-link]');
    const toast = document.getElementById('share-toast');

    let toastTimer = null;
    let copyResetTimer = null;

    /* ---------- Utilidades ---------- */

    function getArticleUrl() {
      const canonical = document.querySelector('link[rel="canonical"]');
      return canonical && canonical.href ? canonical.href : window.location.href;
    }

    function getArticleTitle() {
      const ogTitle = document.querySelector('meta[property="og:title"]');
      return ogTitle && ogTitle.content ? ogTitle.content : document.title;
    }

    function showToast(message) {
      if (!toast) return;
      toast.textContent = message;
      toast.classList.add('is-visible');
      clearTimeout(toastTimer);
      toastTimer = window.setTimeout(function () {
        toast.classList.remove('is-visible');
      }, 3200);
    }

    /*
     * Copia de texto con dos estrategias: Clipboard API cuando está
     * disponible (HTTPS o localhost) y el clásico textarea + execCommand
     * como respaldo para navegadores antiguos o contextos no seguros.
     */
    async function copyText(text) {
      if (navigator.clipboard && window.isSecureContext) {
        try {
          await navigator.clipboard.writeText(text);
          return true;
        } catch (error) {
          /* Continúa con el método de respaldo. */
        }
      }

      const textarea = document.createElement('textarea');
      textarea.value = text;
      textarea.setAttribute('readonly', '');
      textarea.style.position = 'fixed';
      textarea.style.top = '-9999px';
      textarea.style.left = '-9999px';
      document.body.appendChild(textarea);

      let copied = false;
      try {
        textarea.select();
        textarea.setSelectionRange(0, text.length);
        copied = document.execCommand('copy');
      } catch (error) {
        copied = false;
      }

      document.body.removeChild(textarea);
      return copied;
    }

    async function copyArticleLink() {
      const copied = await copyText(getArticleUrl());
      showToast(
        copied
          ? 'Enlace de la noticia copiado al portapapeles'
          : 'No se pudo copiar automáticamente. Copia el enlace de la barra del navegador.'
      );
      return copied;
    }

    function openExternal(url) {
      /* Un enlace temporal evita que el navegador bloquee la ventana emergente. */
      const link = document.createElement('a');
      link.href = url;
      link.target = '_blank';
      link.rel = 'noopener noreferrer';
      document.body.appendChild(link);
      link.click();
      link.remove();
    }

    /* ---------- Redes sociales ---------- */

    function shareOnWhatsApp() {
      const text = getArticleTitle() + ' ' + getArticleUrl();
      openExternal('https://api.whatsapp.com/send?text=' + encodeURIComponent(text));
    }

    function shareOnTelegram() {
      const url = getArticleUrl();
      const text = getArticleTitle();
      openExternal(
        'https://t.me/share/url?url=' + encodeURIComponent(url) +
        '&text=' + encodeURIComponent(text)
      );
    }

    async function shareOnInstagram() {
      const url = getArticleUrl();
      const title = getArticleTitle();

      /*
       * Instagram no permite compartir enlaces directamente por URL.
       * En móviles usamos la hoja nativa de compartir (donde aparece
       * Instagram) y, si no está disponible, copiamos el enlace y
       * abrimos Instagram para que se pegue en una historia o chat.
       */
      if (navigator.share) {
        try {
          await navigator.share({ title: title, text: title, url: url });
          return;
        } catch (error) {
          if (error && error.name === 'AbortError') return;
        }
      }

      openExternal('https://www.instagram.com/');
      const copied = await copyText(url);
      showToast(
        copied
          ? 'Enlace copiado. Pégalo en tu historia o chat de Instagram.'
          : 'Abre Instagram y pega el enlace de la noticia para compartirlo.'
      );
    }

    async function handleShareAction(action) {
      if (action === 'whatsapp') shareOnWhatsApp();
      else if (action === 'telegram') shareOnTelegram();
      else if (action === 'instagram') await shareOnInstagram();
      else if (action === 'copy') await copyArticleLink();
    }

    /* ---------- Menú de compartir ---------- */

    function openMenu() {
      menu.hidden = false;
      toggle.setAttribute('aria-expanded', 'true');
      const firstOption = menu.querySelector('button');
      if (firstOption) firstOption.focus();
    }

    function closeMenu(returnFocus) {
      if (menu.hidden) return;
      menu.hidden = true;
      toggle.setAttribute('aria-expanded', 'false');
      if (returnFocus) toggle.focus();
    }

    if (toggle && menu) {
      toggle.addEventListener('click', function (event) {
        event.stopPropagation();
        if (menu.hidden) openMenu();
        else closeMenu(true);
      });

      document.addEventListener('click', function (event) {
        if (!menu.hidden && !rail.contains(event.target)) closeMenu(false);
      });

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !menu.hidden) closeMenu(true);
      });

      menu.addEventListener('click', async function (event) {
        const option = event.target.closest('[data-share]');
        if (!option) return;

        const action = option.dataset.share;
        closeMenu(true);
        await handleShareAction(action);
      });
    }

    /* ---------- Barra de compartir en móviles ---------- */

    document.querySelectorAll('.mobile-share [data-share]').forEach(function (button) {
      button.addEventListener('click', function () {
        handleShareAction(button.dataset.share);
      });
    });

    /* ---------- Botón directo de copiar ---------- */

    if (copyButton) {
      copyButton.addEventListener('click', async function () {
        const copied = await copyArticleLink();
        if (!copied) return;

        copyButton.classList.add('is-copied');
        copyButton.title = '¡Enlace copiado!';
        clearTimeout(copyResetTimer);
        copyResetTimer = window.setTimeout(function () {
          copyButton.classList.remove('is-copied');
          copyButton.title = 'Copiar enlace';
        }, 1800);
      });
    }

  }

  /* ---------- Tamaño del texto de la noticia ---------- */

  function initArticleFontSize() {
    const article = document.querySelector('.article');
    const control = article && article.querySelector('.article-font-size');
    if (!article || !control) return;

    const levels = ['sm', 'normal', 'lg', 'xl'];
    let index = parseInt(localStorage.getItem('umc_article_font_size'), 10);

    if (!Number.isInteger(index) || index < 0 || index >= levels.length) {
      index = 1;
    }

    function applyFontSize(newIndex) {
      index = Math.max(0, Math.min(levels.length - 1, newIndex));

      if (levels[index] === 'normal') {
        article.removeAttribute('data-font-size');
      } else {
        article.setAttribute('data-font-size', levels[index]);
      }

      localStorage.setItem('umc_article_font_size', String(index));

      control.querySelectorAll('[data-font]').forEach(function (button) {
        button.classList.remove('is-active');
      });

      const activeAction = index === 1 ? 'reset' : index < 1 ? 'down' : 'up';
      const activeButton = control.querySelector('[data-font="' + activeAction + '"]');
      if (activeButton) activeButton.classList.add('is-active');
    }

    control.addEventListener('click', function (event) {
      const button = event.target.closest('[data-font]');
      if (!button) return;

      const action = button.dataset.font;
      if (action === 'up') applyFontSize(index + 1);
      else if (action === 'down') applyFontSize(index - 1);
      else applyFontSize(1);
    });

    applyFontSize(index);
  }

  function initArticlePage() {
    initShare();
    initArticleFontSize();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initArticlePage);
  } else {
    initArticlePage();
  }
})();
