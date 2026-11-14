<?php
declare(strict_types=1);

// Reed–Solomon: ví dụ "HELLO WORLD" phiên bản 1-M (bảng tra chuẩn của thonky.com)
$data = [32, 91, 11, 120, 209, 114, 220, 77, 67, 64, 236, 17, 236, 17, 236, 17];
same([196, 35, 39, 119, 235, 215, 231, 226, 93, 23], QRCodeService::eccFor($data, 10), 'Mã sửa lỗi Reed–Solomon đúng chuẩn');

// Kích thước theo độ dài nội dung
same(21, count(QRCodeService::matrix('CG-1')), 'Nội dung ngắn → phiên bản 1 (21×21)');
same(29, count(QRCodeService::matrix(QRCodeService::ticketPayload('CG-20261114-ABCDE'))), 'Nội dung vé → phiên bản 3 (29×29)');
same(57, count(QRCodeService::matrix(str_repeat('x', 200))), '200 byte → phiên bản 10 (57×57)');

// Ba mắt định vị ở đúng góc
$m = QRCodeService::matrix('CINEGO');
$finderOk = true;
foreach ([[0, 0], [14, 0], [0, 14]] as [$ox, $oy]) {
    foreach ([[0, 0, true], [1, 1, false], [3, 3, true], [6, 6, true], [5, 1, false], [2, 4, true]] as [$dx, $dy, $dark]) {
        $finderOk = $finderOk && ($m[$oy + $dy][$ox + $dx] ?? null) === $dark;
    }
}
check($finderOk, 'Ba mắt định vị đặt đúng góc');

// Nội dung QR trên vé có chữ ký, sửa mã vé thì không hợp lệ
$payload = QRCodeService::ticketPayload('CG-20261114-ABCDE');
same('CG-20261114-ABCDE', QRCodeService::verifyTicketPayload($payload), 'Quét QR vé hợp lệ ra đúng mã đặt vé');
same(null, QRCodeService::verifyTicketPayload(str_replace('ABCDE', 'ABCDF', $payload)), 'QR bị sửa mã vé thì không hợp lệ');
check(str_starts_with(QRCodeService::svg('x'), '<svg'), 'Xuất được SVG');
