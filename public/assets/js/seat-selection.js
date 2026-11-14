// Sơ đồ ghế: chọn/bỏ ghế, giới hạn số ghế, cập nhật tạm tính, tự làm mới tình trạng ghế (trang /booking/seats)
(function () {
  'use strict';
  var fmt = function (n) { return n.toLocaleString('vi-VN') + 'đ'; };
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  var sf = $('#seat-form');
  if (!sf) return;

  var max = parseInt(sf.dataset.max, 10), prices = JSON.parse(sf.dataset.prices);
  var input = $('#seats-input'), errBox = $('#seat-error');
  var sel = input.value ? input.value.split(',') : [];
  var totalEl = $('#sum-total'), combo = parseInt(totalEl.dataset.combo || '0', 10);
  var LABEL = { booked: ' đã được đặt.', held: ' đang được người khác giữ.', blocked: ' đang tạm ngưng sử dụng.' };
  var showErr = function (m) { errBox.textContent = m; errBox.hidden = !m; };
  var seatEl = function (c) { return $('.seat[data-code="' + c + '"]', sf); };

  var render = function () {
    var sum = 0;
    sel.forEach(function (c) { var el = seatEl(c); if (el) sum += prices[el.dataset.type]; });
    input.value = sel.join(',');
    $('#sum-seats').innerHTML = sel.length ? sel.join(', ') : '<span class="muted">Chưa chọn</span>';
    $('#sum-count').textContent = sel.length;
    $('#sum-tickets').textContent = fmt(sum);
    totalEl.textContent = fmt(sum + combo);
    $$('.discount', sf).forEach(function (d) { d.hidden = true; });
  };

  var setSelected = function (b, on) {
    b.classList.toggle('is-selected', on);
    b.setAttribute('aria-pressed', on ? 'true' : 'false');
  };

  $$('.seat', sf).forEach(function (b) {
    b.addEventListener('click', function () {
      var c = b.dataset.code;
      if (b.getAttribute('aria-disabled') === 'true') {
        showErr('Ghế ' + c + (b.classList.contains('is-booked') ? LABEL.booked : b.classList.contains('is-blocked') ? LABEL.blocked : LABEL.held) + ' Vui lòng chọn ghế khác.');
        return;
      }
      var i = sel.indexOf(c);
      if (i >= 0) { sel.splice(i, 1); setSelected(b, false); showErr(''); }
      else {
        if (sel.length >= max) { showErr('Mỗi lượt đặt tối đa ' + max + ' ghế.'); return; }
        sel.push(c); setSelected(b, true); showErr('');
      }
      render();
    });
  });

  sf.addEventListener('submit', function (e) {
    if (!sel.length) { e.preventDefault(); showErr('Vui lòng chọn ít nhất 1 ghế để tiếp tục.'); errBox.scrollIntoView({ block: 'center', behavior: 'smooth' }); }
  });
  if (sel.length) render();

  // ----- Tự cập nhật tình trạng ghế (5 giây/lần, dừng khi tab bị ẩn) -----
  var url = sf.dataset.statusUrl;
  var apply = function (seats) {
    var lost = [];
    Object.keys(seats).forEach(function (code) {
      var b = seatEl(code);
      if (!b) return;
      var st = seats[code];
      var blocked = st === 'booked' || st === 'held' || st === 'blocked';
      b.classList.toggle('is-booked', st === 'booked');
      b.classList.toggle('is-held', st === 'held');
      b.classList.toggle('is-blocked', st === 'blocked');
      if (blocked) b.setAttribute('aria-disabled', 'true'); else b.removeAttribute('aria-disabled');
      if (blocked && sel.indexOf(code) >= 0) {
        sel.splice(sel.indexOf(code), 1);
        setSelected(b, false);
        lost.push(code);
      }
    });
    if (lost.length) {
      render();
      showErr('Ghế ' + lost.join(', ') + ' vừa được người khác chọn và đã bị bỏ khỏi lựa chọn của bạn.');
    }
  };
  var poll = function () {
    if (document.hidden || !url || !window.fetch) return;
    fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' } })
      .then(function (r) { return r.ok ? r.json() : null; })
      .then(function (d) { if (d && d.seats) apply(d.seats); })
      .catch(function () { /* mất mạng tạm thời: bỏ qua, lần sau thử lại */ });
  };
  setInterval(poll, 5000);
  document.addEventListener('visibilitychange', function () { if (!document.hidden) poll(); });
})();
