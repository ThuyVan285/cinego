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
      var old = el.textContent; el.textContent = 'Đã chép'; setTimeout(function () { el.textContent = old; }, 1200);
    });
  });

  // ----- Chọn ghế -----
  var sf = $('#seat-form');
  if (sf) {
    var max = parseInt(sf.dataset.max, 10), prices = JSON.parse(sf.dataset.prices);
    var input = $('#seats-input'), errBox = $('#seat-error');
    var sel = input.value ? input.value.split(',') : [];
    var totalEl = $('#sum-total'), combo = parseInt(totalEl.dataset.combo || '0', 10);
    var showErr = function (m) { errBox.textContent = m; errBox.hidden = !m; };
    var render = function () {
      var sum = 0;
      sel.forEach(function (c) { sum += prices[$('[data-code="' + c + '"]').dataset.type]; });
      input.value = sel.join(',');
      $('#sum-seats').innerHTML = sel.length ? sel.join(', ') : '<span class="muted">Chưa chọn</span>';
      $('#sum-count').textContent = sel.length;
      $('#sum-tickets').textContent = fmt(sum);
      totalEl.textContent = fmt(sum + combo);
      $$('.discount', sf).forEach(function (d) { d.hidden = true; });
    };
    $$('.seat', sf).forEach(function (b) {
      b.addEventListener('click', function () {
        var c = b.dataset.code;
        if (b.getAttribute('aria-disabled') === 'true') {
          showErr('Ghế ' + c + (b.classList.contains('is-booked') ? ' đã được đặt.' : ' không khả dụng.') + ' Vui lòng chọn ghế khác.');
          return;
        }
        var i = sel.indexOf(c);
        if (i >= 0) { sel.splice(i, 1); b.classList.remove('is-selected'); b.setAttribute('aria-pressed', 'false'); showErr(''); }
        else {
          if (sel.length >= max) { showErr('Mỗi lượt đặt tối đa ' + max + ' ghế.'); return; }
          sel.push(c); b.classList.add('is-selected'); b.setAttribute('aria-pressed', 'true'); showErr('');
        }
        render();
      });
    });
    sf.addEventListener('submit', function (e) {
      if (!sel.length) { e.preventDefault(); showErr('Vui lòng chọn ít nhất 1 ghế để tiếp tục.'); errBox.scrollIntoView({ block: 'center', behavior: 'smooth' }); }
    });
    if (sel.length) render();
  }

  // ----- Bắp nước -----
  var cf = $('#combo-form');
  if (cf) {
    var ticket = parseInt(cf.dataset.ticket, 10), tot = $('#sum-total');
    var recalc = function () {
      var sum = 0, lines = [];
      $$('.stepper', cf).forEach(function (s) {
        var q = parseInt($('input', s).value, 10) || 0;
        if (q > 0) { sum += q * parseInt(s.dataset.price, 10); lines.push('<li class="combo-line"><span>' + s.dataset.name + ' × ' + q + '</span><b>' + fmt(q * parseInt(s.dataset.price, 10)) + '</b></li>'); }
      });
      $$('.combo-line, .discount').forEach(function (l) { l.remove(); });
      $('.line-tickets').insertAdjacentHTML('afterend', lines.join(''));
      tot.textContent = fmt(ticket + sum);
    };
    $$('.stepper', cf).forEach(function (s) {
      var inp = $('input', s);
      $$('button', s).forEach(function (b) {
        b.addEventListener('click', function () {
          var v = (parseInt(inp.value, 10) || 0) + parseInt(b.dataset.d, 10);
          inp.value = Math.max(0, Math.min(10, v));
          recalc();
        });
      });
    });
    recalc();
  }

  // ----- Thanh toán mô phỏng -----
  var pf = $('#pay-form');
  if (pf) {
    var busy = false;
    pf.addEventListener('submit', function (e) {
      if (busy) return;
      e.preventDefault(); busy = true;
      $('#processing').hidden = false;
      setTimeout(function () { pf.submit(); }, 1400);
    });
  }

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
