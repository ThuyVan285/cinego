<?php
/**
 * Mã QR của vé (quét được). Nội dung: CINEGO|<mã đặt vé>|<chữ ký> – xem QRCodeService::ticketPayload().
 * @var string $code
 */
echo QRCodeService::svg(QRCodeService::ticketPayload($code), 'Mã QR vé ' . $code);
