    </div>
    <footer class="app-footer">© 2026 TraceStock · Control de inventario sin complicaciones</footer>
  </main>
</div>
<script>
(function () {
  var toggle = document.getElementById('navToggle');
  var sidebar = document.getElementById('sidebar');
  var overlay = document.getElementById('sidebarOverlay');
  if (!toggle || !sidebar || !overlay) return;

  function setOpen(open) {
    sidebar.classList.toggle('open', open);
    overlay.classList.toggle('open', open);
    overlay.hidden = !open;
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    toggle.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
    document.body.style.overflow = open ? 'hidden' : '';
  }

  toggle.addEventListener('click', function () {
    setOpen(!sidebar.classList.contains('open'));
  });
  overlay.addEventListener('click', function () { setOpen(false); });
  sidebar.querySelectorAll('a').forEach(function (link) {
    link.addEventListener('click', function () {
      if (window.matchMedia('(max-width: 1024px)').matches) setOpen(false);
    });
  });
  window.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') setOpen(false);
  });
})();
</script>
</body>
</html>
