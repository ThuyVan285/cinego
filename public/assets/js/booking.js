// Chọn combo bắp nước và tính lại tổng tiền (trang /booking/combos)
(function () {
  'use strict';
  var fmt = function (n) { return n.toLocaleString('vi-VN') + 'đ'; };
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

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
})();
