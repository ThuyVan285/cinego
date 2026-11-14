<?php
declare(strict_types=1);

// Tính giảm giá – Promotion::evaluate(promo, tiền vé, tiền combo) → [giảm, lỗi]
$pct = ['expires_at' => addDays(today(), 10), 'type' => 'pct', 'value' => 10, 'max_discount' => 40000, 'min_spend' => 150000, 'need_combo' => 0];
$combo = ['expires_at' => addDays(today(), 10), 'type' => 'fixed', 'value' => 20000, 'max_discount' => 20000, 'min_spend' => 200000, 'need_combo' => 1];

same([20000, null], Promotion::evaluate($pct, 200000, 0), 'Giảm 10% của 200.000đ = 20.000đ');
same([40000, null], Promotion::evaluate($pct, 600000, 0), 'Giảm % không vượt mức tối đa 40.000đ');
check(Promotion::evaluate($pct, 100000, 0)[1] !== null, 'Đơn dưới mức tối thiểu thì báo lỗi');
check(Promotion::evaluate(['expires_at' => addDays(today(), -1)] + $pct, 300000, 0)[1] !== null, 'Mã hết hạn thì báo lỗi');
check(Promotion::evaluate($combo, 250000, 0)[1] !== null, 'Mã cần combo nhưng đơn không có combo thì báo lỗi');
same([20000, null], Promotion::evaluate($combo, 200000, 60000), 'Mã combo áp dụng khi có combo');
check(Promotion::evaluate(null, 300000, 0)[1] !== null, 'Mã không tồn tại thì báo lỗi');
