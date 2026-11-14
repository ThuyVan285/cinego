<?php
declare(strict_types=1);

// Phương thức thanh toán đọc từ app/Config/payment.php
$methods = Payment::methods();
check(isset($methods['momo'], $methods['demo'], $methods['counter']), 'Có đủ 3 phương thức: MoMo, cổng thử nghiệm, tại quầy');
check(!isset($methods['vnpay']), 'Đã bỏ VNPay');
check(Payment::isAvailable(Payment::defaultMethod()), 'Phương thức mặc định đang hoạt động');
check(Payment::isAvailable('demo') && Payment::isAvailable('counter'), 'Cổng thử nghiệm và tại quầy luôn hoạt động');
same(MomoService::isConfigured(), Payment::isAvailable('momo'), 'MoMo chỉ bật khi đã điền đủ khoá trong .env');
check(!Payment::isValid('bitcoin'), 'Phương thức lạ bị từ chối');
check(!Payment::isOnline('counter') && Payment::isOnline('momo'), 'Tại quầy là offline, MoMo là online');

// Chữ ký MoMo (HMAC-SHA256 trên chuỗi key=value theo thứ tự tài liệu MoMo)
$cfg = ['partner_code' => 'MOMOTEST', 'access_key' => 'AK', 'secret_key' => 'SK'];
$result = [
    'partnerCode' => 'MOMOTEST', 'orderId' => 'CG20261115ABCDE10101', 'requestId' => 'CG20261115ABCDE10101', 'amount' => '150000',
    'orderInfo' => 'Thanh toan ve CineGo', 'orderType' => 'momo_wallet', 'transId' => '4088878653', 'resultCode' => '0',
    'message' => 'Successful.', 'payType' => 'qr', 'responseTime' => '1731650000000', 'extraData' => '',
];
$order = ['accessKey', 'amount', 'extraData', 'message', 'orderId', 'orderInfo', 'orderType', 'partnerCode', 'payType', 'requestId', 'responseTime', 'resultCode', 'transId'];
$raw = 'accessKey=AK&amount=150000&extraData=&message=Successful.&orderId=CG20261115ABCDE10101&orderInfo=Thanh toan ve CineGo&orderType=momo_wallet'
    . '&partnerCode=MOMOTEST&payType=qr&requestId=CG20261115ABCDE10101&responseTime=1731650000000&resultCode=0&transId=4088878653';
same($raw, MomoService::rawSignature(['accessKey' => 'AK'] + $result, $order), 'Chuỗi ký kết quả MoMo đúng thứ tự, không urlencode');
$result['signature'] = hash_hmac('sha256', $raw, 'SK');
check(MomoService::verifyResult($result, $cfg), 'Kết quả MoMo có chữ ký đúng được chấp nhận');
check(!MomoService::verifyResult(['amount' => '1000'] + $result, $cfg), 'Sửa số tiền thì chữ ký sai');
check(!MomoService::verifyResult(['partnerCode' => 'KHAC'] + $result, $cfg), 'Sai mã đối tác bị từ chối');
check(!MomoService::verifyResult($result, ['secret_key' => 'SAI'] + $cfg), 'Sai khoá bí mật bị từ chối');
same('Tài khoản MoMo không đủ số dư', MomoService::message('1001'), 'resultCode 1001 = không đủ số dư');
check(str_contains(MomoService::message('123456'), '123456'), 'Mã lạ vẫn có thông báo kèm mã');

// Cổng thử nghiệm: ký số HMAC-SHA512, sắp xếp tham số theo tên, urlencode
$params = ['vnp_TxnRef' => 'ABC123', 'vnp_Amount' => '15000000', 'vnp_OrderInfo' => 'Thanh toan ve', 'vnp_BankCode' => ''];
same('vnp_Amount=15000000&vnp_BankCode=&vnp_OrderInfo=Thanh+toan+ve&vnp_TxnRef=ABC123', PaymentService::hashData($params), 'Cổng thử nghiệm: chuỗi ký sắp xếp theo tên, giữ tham số rỗng');
parse_str(PaymentService::signedQuery($params, 'secret'), $back);
check(PaymentService::verify($back, 'secret'), 'Cổng thử nghiệm: tham số đã ký qua URL xác thực được');
$back['vnp_Amount'] = '100';
check(!PaymentService::verify($back, 'secret'), 'Cổng thử nghiệm: sửa số tiền thì chữ ký sai');

// handleResult với MoMo: chữ ký giả bị từ chối trước khi đụng tới CSDL
same('97', PaymentService::handleResult('momo', ['signature' => 'gia'] + $result)['rsp'], 'Kết quả MoMo giả bị từ chối (mã 97)');
