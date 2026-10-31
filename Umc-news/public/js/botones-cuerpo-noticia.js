  function pulse(btn){
    btn.style.transform = 'scale(0.85)';
    setTimeout(()=> btn.style.transform = '', 120);
  }

  function copyLink(btn){
    try{ navigator.clipboard && navigator.clipboard.writeText(window.location.href); }catch(e){}
    const label = btn.querySelector('.copy-label');
    if(label){
      const orig = label.textContent;
      label.textContent = '¡Copiado!';
      setTimeout(()=> label.textContent = orig, 1500);
    } else {
      btn.title = '¡Copiado!';
    }
  }