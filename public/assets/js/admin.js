// Trang quản trị: xác nhận thao tác, ô chọn tự gửi, menu di động, giao diện sáng/tối, toast, xem trước poster.
(function () {
  'use strict';
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var body = document.body;

  // Form có data-confirm: hỏi trước khi gửi (xoá, huỷ đơn, khoá tài khoản...)
  $$('form[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      if (f.dataset.confirmed === '1') return;
      if (!window.confirm(f.dataset.confirm)) e.preventDefault();
    });
  });

  // <select data-autosubmit-select> → đổi là gửi form (trạng thái phim, vai trò, loại ghế, bộ lọc rạp...)
  $$('select[data-autosubmit-select]').forEach(function (s) {
    var initial = s.value;
    s.addEventListener('change', function () {
      var f = s.form;
      if (!f) return;
      if (f.dataset.confirm && !window.confirm(f.dataset.confirm)) { s.value = initial; return; }
      f.dataset.confirmed = '1';
      if (typeof f.requestSubmit === 'function') f.requestSubmit(); else f.submit();
    });
  });

  // Ảnh lỗi → ảnh dự phòng
  document.addEventListener('error', function (e) {
    var t = e.target;
    if (t.tagName === 'IMG' && t.dataset.fallback && t.src.indexOf(t.dataset.fallback) === -1) t.src = t.dataset.fallback;
  }, true);

  // Menu trượt trên màn hình nhỏ
  var toggle = document.querySelector('[data-nav-toggle]');
  var setNav = function (open) {
    body.classList.toggle('nav-open', open);
    if (toggle) toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  };
  if (toggle) toggle.addEventListener('click', function () { setNav(!body.classList.contains('nav-open')); });
  $$('[data-nav-close]').forEach(function (el) { el.addEventListener('click', function () { setNav(false); }); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setNav(false); });

  // Giao diện sáng / tối (dùng chung khoá lưu với trang khách)
  $$('[data-theme-toggle]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var cur = document.documentElement.getAttribute('data-theme')
        || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      var next = cur === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', next);
      try { localStorage.setItem('cinego_theme', next); } catch (e) { /* trình duyệt chặn lưu trữ */ }
    });
  });

  // Toast: tự ẩn sau 6 giây, hoặc bấm ×
  $$('.ad-toast').forEach(function (t, i) {
    var close = function () { t.classList.add('is-hiding'); setTimeout(function () { t.remove(); }, 260); };
    var btn = t.querySelector('[data-toast-close]');
    if (btn) btn.addEventListener('click', close);
    if (!t.classList.contains('ad-toast-error')) setTimeout(close, 6000 + i * 400);
  });

  // Xem trước poster khi chọn ảnh
  var input = document.querySelector('[data-poster-input]'), preview = document.querySelector('[data-poster-preview]');
  if (input && preview && window.URL) {
    input.addEventListener('change', function () {
      if (input.files && input.files[0]) preview.src = URL.createObjectURL(input.files[0]);
    });
  }
})();
