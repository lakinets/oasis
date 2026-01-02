/* ---------- 30. Собственный «PJAX-заменитель» без прыжков ---------- */
(function(){
  document.addEventListener('click', function (e) {
    const a = e.target.closest('a[href]:not([href^="#"]):not([target])');
    if (!a || a.hostname !== location.hostname) return;
    e.preventDefault();

    fetch(a.href, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
      .then(r => r.ok ? r.text() : Promise.reject(r))
      .then(html => {
          const start = html.indexOf('<main');
          const end   = html.indexOf('</main>', start) + 7;
          if (start === -1 || end === 6) throw 'no <main>';
          document.querySelector('main').outerHTML = html.slice(start, end);
          history.pushState(null, null, a.href);
          document.title = (/<title>(.+?)<\/title>/.exec(html) || [,document.title])[1];
      })
      .catch(() => location.href = a.href);   // fallback
  });
  window.addEventListener('popstate', () => location.reload()); // «назад» — перезагрузка
})();