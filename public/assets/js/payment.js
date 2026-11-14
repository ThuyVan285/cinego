// Thanh toán mô phỏng: khoá nút, hiện 'đang xử lý' rồi gửi form (trang /booking/payment)
(function () {
  'use strict';
  var fmt = function (n) { return n.toLocaleString('vi-VN') + 'đ'; };
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  // ----- Thanh toán mô phỏng -----
  var pf = $('#pay-form');
  if (pf) {
    var busy = false;
    pf.addEventListener('submit', function (e) {
      if (busy) {
        e.preventDefault();
        return;
      }
      e.preventDefault();
      busy = true;
      var proc = $('#processing');
      if (proc) proc.hidden = false;
      var btn = pf.querySelector('button[type=submit]');
      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Đang xử lý...';
      }
      setTimeout(function () {
        HTMLFormElement.prototype.submit.call(pf);
      }, 1200);
    });
  }
})();
