// JS dùng chung mọi trang: ảnh dự phòng, menu mobile, banner, form, sao chép mã, giao diện sáng/tối
(function () {
  'use strict';
  var fmt = function (n) { return n.toLocaleString('vi-VN') + 'đ'; };
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  // Ảnh lỗi → ảnh dự phòng
  document.addEventListener('error', function (e) {
    var t = e.target;
    if (t.tagName === 'IMG' && t.dataset.fallback && t.src.indexOf(t.dataset.fallback) === -1) {
      t.src = t.dataset.fallback;
    }
  }, true);

  // Menu mobile
  var toggle = $('.nav-toggle');
  if (toggle) {
    toggle.addEventListener('click', function () {
      var open = document.body.classList.toggle('nav-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // Carousel banner
  var car = $('[data-carousel]');
  if (car) {
    var slides = $$('.hero-slide', car), dots = $$('.hero-dots button', car), idx = 0, timer;
    var go = function (i) {
      idx = (i + slides.length) % slides.length;
      slides.forEach(function (s, k) { s.classList.toggle('is-active', k === idx); s.setAttribute('aria-hidden', k === idx ? 'false' : 'true'); });
      dots.forEach(function (d, k) { d.classList.toggle('is-active', k === idx); });
    };
    var play = function () { clearInterval(timer); timer = setInterval(function () { go(idx + 1); }, 6000); };
    dots.forEach(function (d, k) { d.addEventListener('click', function () { go(k); play(); }); });
    car.addEventListener('mouseenter', function () { clearInterval(timer); });
    car.addEventListener('mouseleave', play);
    if (slides.length > 1) play();
  }

  // Form tự gửi khi đổi select; neo trang
  $$('form[data-autosubmit]').forEach(function (f) {
    $$('select', f).forEach(function (s) { s.addEventListener('change', function () { f.submit(); }); });
  });
  $$('form[data-anchor]').forEach(function (f) { f.action = f.getAttribute('action') + '#' + f.dataset.anchor; });

  // In vé, xác nhận, copy mã
  $$('[data-print]').forEach(function (b) { b.addEventListener('click', function () { window.print(); }); });
  $$('form[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (e) { if (!window.confirm(f.dataset.confirm)) e.preventDefault(); });
  });
  $$('[data-copy]').forEach(function (el) {
    el.title = 'Nhấn để sao chép';
    el.addEventListener('click', function () {
      if (navigator.clipboard) navigator.clipboard.writeText(el.dataset.copy);
      var old = el.textContent; el.textContent = 'Đã chép ✓'; setTimeout(function () { el.textContent = old; }, 1400);
    });
  });

  // Nút sao chép mã ưu đãi dạng button (.promo-code-btn)
  $$('.promo-code-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var code = btn.dataset.copy;
      if (!code) return;
      if (navigator.clipboard) navigator.clipboard.writeText(code);
      var label = btn.querySelector('.copy-label');
      var icon  = btn.querySelector('.icon-copy');
      if (label) { var oldLabel = label.textContent; label.textContent = 'Đã sao chép!'; setTimeout(function () { label.textContent = oldLabel; }, 1400); }
      btn.classList.add('copied');
      setTimeout(function () { btn.classList.remove('copied'); }, 1400);
    });
  });

  // ----- Đồng hồ giữ ghế / hạn thanh toán (phần tử có data-hold-until = mốc ms) -----
  $$('[data-hold-until]').forEach(function (el) {
    var until = parseInt(el.dataset.holdUntil, 10), out = el.querySelector('b'), t;
    var tick = function () {
      var left = Math.max(0, Math.round((until - Date.now()) / 1000));
      out.textContent = String(Math.floor(left / 60)).padStart(2, '0') + ':' + String(left % 60).padStart(2, '0');
      el.classList.toggle('is-urgent', left <= 60);
      if (left === 0) {
        clearInterval(t);
        el.classList.add('is-expired');
        el.innerHTML = el.dataset.expiredText || 'Đã hết thời gian giữ ghế. Hệ thống sẽ thử giữ lại ghế khi bạn tiếp tục; nếu ghế đã có người lấy, bạn cần chọn lại.';
      }
    };
    tick();
    t = setInterval(tick, 1000);
  });

  // ----- Trailer phát ngay trong trang (<dialog>) -----
  $$('[data-trailer]').forEach(function (btn) {
    var dlg = document.getElementById(btn.dataset.trailer);
    if (!dlg || typeof dlg.showModal !== 'function') return; // trình duyệt cũ: giữ link mở tab mới
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      var frame = dlg.querySelector('iframe');
      frame.src = frame.dataset.src;
      dlg.showModal();
    });
    dlg.addEventListener('close', function () { dlg.querySelector('iframe').src = 'about:blank'; });
    dlg.addEventListener('click', function (e) { if (e.target === dlg) dlg.close(); });
  });

  // ----- Theme toggle (Sáng / Tối) -----
  var themeBtn = $('#themeToggleBtn');
  var applyTheme = function(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    document.body.classList.toggle('theme-dark', theme === 'dark');
    document.body.classList.toggle('theme-light', theme === 'light');
    if (themeBtn) {
      themeBtn.textContent = theme === 'dark' ? '☀️' : '🌓';
      themeBtn.setAttribute('title', theme === 'dark' ? 'Chuyển sang giao diện sáng' : 'Chuyển sang giao diện tối');
    }
  };
  var savedTheme = localStorage.getItem('cinego_theme');
  if (savedTheme) {
    applyTheme(savedTheme);
  } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    applyTheme('dark');
  }
  if (themeBtn) {
    themeBtn.addEventListener('click', function () {
      var current = document.documentElement.getAttribute('data-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      var next = current === 'dark' ? 'light' : 'dark';
      applyTheme(next);
      localStorage.setItem('cinego_theme', next);
    });
  }
})();
