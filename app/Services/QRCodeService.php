<?php
declare(strict_types=1);

/**
 * Sinh mã QR (ISO/IEC 18004) bằng PHP thuần, xuất SVG – không cần thư viện hay extension GD.
 * Hỗ trợ: chế độ byte (UTF-8), mức sửa lỗi M (~15%), phiên bản 1–10 (tối đa 213 byte).
 *
 * Nội dung QR trên vé: "CINEGO|<mã đặt vé>|<chữ ký 10 ký tự>" – nhân viên quét và đối chiếu bằng verifyTicketPayload().
 */
final class QRCodeService
{
    /** [tổng số codeword, số codeword sửa lỗi mỗi khối, số khối] cho mức M, phiên bản 1–10. */
    private const VERSIONS_M = [
        1 => [26, 10, 1], 2 => [44, 16, 1], 3 => [70, 26, 1], 4 => [100, 18, 2], 5 => [134, 24, 2],
        6 => [172, 16, 4], 7 => [196, 18, 4], 8 => [242, 22, 4], 9 => [292, 22, 5], 10 => [346, 26, 5],
    ];
    private const ALIGN = [
        1 => [], 2 => [6, 18], 3 => [6, 22], 4 => [6, 26], 5 => [6, 30],
        6 => [6, 34], 7 => [6, 22, 38], 8 => [6, 24, 42], 9 => [6, 26, 46], 10 => [6, 28, 50],
    ];
    private const EC_FORMAT_BITS_M = 0;

    // ---------- Nội dung QR trên vé ----------
    public static function ticketPayload(string $bookingCode): string
    {
        return 'CINEGO|' . $bookingCode . '|' . self::ticketSignature($bookingCode);
    }

    /** Trả về mã đặt vé nếu nội dung QR hợp lệ (đúng chữ ký), ngược lại null. */
    public static function verifyTicketPayload(string $payload): ?string
    {
        $parts = explode('|', $payload);
        if (count($parts) !== 3 || $parts[0] !== 'CINEGO') {
            return null;
        }
        return hash_equals(self::ticketSignature($parts[1]), $parts[2]) ? $parts[1] : null;
    }

    private static function ticketSignature(string $code): string
    {
        return strtoupper(substr(hash_hmac('sha256', 'ticket|' . $code, PaymentService::demoSecret()), 0, 10));
    }

    // ---------- SVG ----------
    /** SVG của mã QR, có lề trắng 4 ô theo chuẩn. */
    public static function svg(string $text, string $label = 'Mã QR'): string
    {
        $m = self::matrix($text);
        $n = count($m);
        $q = 4;
        $path = '';
        for ($y = 0; $y < $n; $y++) {
            for ($x = 0; $x < $n; $x++) {
                if ($m[$y][$x]) {
                    $path .= 'M' . ($x + $q) . ' ' . ($y + $q) . 'h1v1h-1z';
                }
            }
        }
        $size = $n + 2 * $q;
        return '<svg class="qr" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $size . ' ' . $size . '" role="img" aria-label="'
            . htmlspecialchars($label, ENT_QUOTES) . '" shape-rendering="crispEdges"><rect width="' . $size . '" height="' . $size
            . '" fill="#fff"/><path fill="#000" d="' . $path . '"/></svg>';
    }

    // ---------- Mã hoá ----------
    /** @return bool[][] ma trận [hàng][cột], true = ô đen */
    public static function matrix(string $text): array
    {
        $data = array_values(unpack('C*', $text) ?: []);
        $len = count($data);
        $version = null;
        foreach (self::VERSIONS_M as $v => [$total, $ecLen, $blocks]) {
            $capacityBits = ($total - $ecLen * $blocks) * 8;
            if (4 + ($v < 10 ? 8 : 16) + $len * 8 <= $capacityBits) {
                $version = $v;
                break;
            }
        }
        if ($version === null) {
            throw new InvalidArgumentException('Nội dung QR quá dài (tối đa 213 byte).');
        }
        $codewords = self::addErrorCorrection(self::dataCodewords($data, $version), $version);

        $size = $version * 4 + 17;
        $modules = array_fill(0, $size, array_fill(0, $size, false));
        $isFunc = $modules;
        self::drawFunctionPatterns($modules, $isFunc, $version);
        self::drawCodewords($modules, $isFunc, $codewords);

        // Chọn mặt nạ có điểm phạt thấp nhất.
        $best = null;
        $bestScore = PHP_INT_MAX;
        for ($mask = 0; $mask < 8; $mask++) {
            $m = $modules;
            self::applyMask($m, $isFunc, $mask);
            self::drawFormatBits($m, $isFunc, $mask);
            $score = self::penalty($m);
            if ($score < $bestScore) {
                $bestScore = $score;
                $best = $m;
            }
        }
        return $best;
    }

    /** Chuỗi bit: mode byte (0100) + độ dài + dữ liệu + kết thúc + đệm 0xEC/0x11. @return int[] */
    public static function dataCodewords(array $data, int $version): array
    {
        [$total, $ecLen, $blocks] = self::VERSIONS_M[$version];
        $capacity = ($total - $ecLen * $blocks) * 8;
        $bits = [];
        $put = function (int $val, int $n) use (&$bits): void {
            for ($i = $n - 1; $i >= 0; $i--) {
                $bits[] = ($val >> $i) & 1;
            }
        };
        $put(0b0100, 4);
        $put(count($data), $version < 10 ? 8 : 16);
        foreach ($data as $b) {
            $put($b, 8);
        }
        $put(0, min(4, $capacity - count($bits)));
        $put(0, (8 - count($bits) % 8) % 8);
        $out = [];
        foreach (array_chunk($bits, 8) as $byte) {
            $out[] = (int)bindec(implode('', $byte));
        }
        for ($pad = 0xEC; count($out) < $capacity / 8; $pad ^= 0xEC ^ 0x11) {
            $out[] = $pad;
        }
        return $out;
    }

    /** Chia khối, tính Reed–Solomon cho từng khối, rồi xen kẽ codeword. @return int[] */
    private static function addErrorCorrection(array $data, int $version): array
    {
        [$total, $ecLen, $numBlocks] = self::VERSIONS_M[$version];
        $numShort = $numBlocks - $total % $numBlocks;
        $shortLen = intdiv($total, $numBlocks);
        $divisor = self::rsDivisor($ecLen);
        $blocks = [];
        $eccs = [];
        $k = 0;
        for ($i = 0; $i < $numBlocks; $i++) {
            $dataLen = $shortLen - $ecLen + ($i < $numShort ? 0 : 1);
            $blocks[$i] = array_slice($data, $k, $dataLen);
            $k += $dataLen;
            $eccs[$i] = self::rsRemainder($blocks[$i], $divisor);
        }
        $out = [];
        $maxData = max(array_map('count', $blocks));
        for ($i = 0; $i < $maxData; $i++) {
            foreach ($blocks as $b) {
                if ($i < count($b)) {
                    $out[] = $b[$i];
                }
            }
        }
        for ($i = 0; $i < $ecLen; $i++) {
            foreach ($eccs as $e) {
                $out[] = $e[$i];
            }
        }
        return $out;
    }

    // ---------- Reed–Solomon trên GF(256), đa thức 0x11D ----------
    private static function gfMul(int $x, int $y): int
    {
        $z = 0;
        for ($i = 7; $i >= 0; $i--) {
            $z = (($z << 1) ^ (($z >> 7) * 0x11D)) & 0xFF;
            $z ^= (($y >> $i) & 1) * $x;
        }
        return $z;
    }

    /** @return int[] */
    private static function rsDivisor(int $degree): array
    {
        $r = array_fill(0, $degree, 0);
        $r[$degree - 1] = 1;
        $root = 1;
        for ($i = 0; $i < $degree; $i++) {
            for ($j = 0; $j < $degree; $j++) {
                $r[$j] = self::gfMul($r[$j], $root);
                if ($j + 1 < $degree) {
                    $r[$j] ^= $r[$j + 1];
                }
            }
            $root = self::gfMul($root, 0x02);
        }
        return $r;
    }

    /** Phần dư Reed–Solomon (codeword sửa lỗi) của một khối dữ liệu. @return int[] */
    public static function rsRemainder(array $data, array $divisor): array
    {
        $r = array_fill(0, count($divisor), 0);
        foreach ($data as $b) {
            $factor = $b ^ array_shift($r);
            $r[] = 0;
            foreach ($divisor as $i => $d) {
                $r[$i] ^= self::gfMul($d, $factor);
            }
        }
        return $r;
    }

    /** Dùng cho test: codeword sửa lỗi của dữ liệu với $ecLen codeword. */
    public static function eccFor(array $data, int $ecLen): array
    {
        return self::rsRemainder($data, self::rsDivisor($ecLen));
    }

    // ---------- Vẽ ma trận (toạ độ: $m[y][x]) ----------
    private static function set(array &$m, array &$f, int $x, int $y, bool $dark): void
    {
        $m[$y][$x] = $dark;
        $f[$y][$x] = true;
    }

    private static function drawFunctionPatterns(array &$m, array &$f, int $version): void
    {
        $size = count($m);
        for ($i = 0; $i < $size; $i++) { // vạch định thời
            self::set($m, $f, 6, $i, $i % 2 === 0);
            self::set($m, $f, $i, 6, $i % 2 === 0);
        }
        foreach ([[3, 3], [$size - 4, 3], [3, $size - 4]] as [$cx, $cy]) { // 3 mắt định vị + viền trắng
            for ($dy = -4; $dy <= 4; $dy++) {
                for ($dx = -4; $dx <= 4; $dx++) {
                    $x = $cx + $dx;
                    $y = $cy + $dy;
                    if ($x >= 0 && $x < $size && $y >= 0 && $y < $size) {
                        $d = max(abs($dx), abs($dy));
                        self::set($m, $f, $x, $y, $d !== 2 && $d !== 4);
                    }
                }
            }
        }
        $pos = self::ALIGN[$version]; // mẫu căn chỉnh
        $last = count($pos) - 1;
        foreach ($pos as $i => $px) {
            foreach ($pos as $j => $py) {
                if (($i === 0 && $j === 0) || ($i === 0 && $j === $last) || ($i === $last && $j === 0)) {
                    continue;
                }
                for ($dy = -2; $dy <= 2; $dy++) {
                    for ($dx = -2; $dx <= 2; $dx++) {
                        self::set($m, $f, $px + $dx, $py + $dy, max(abs($dx), abs($dy)) !== 1);
                    }
                }
            }
        }
        self::drawFormatBits($m, $f, 0); // giữ chỗ, vẽ lại sau khi chọn mặt nạ
        if ($version >= 7) { // thông tin phiên bản
            $rem = $version;
            for ($i = 0; $i < 12; $i++) {
                $rem = ($rem << 1) ^ (($rem >> 11) * 0x1F25);
            }
            $bits = ($version << 12) | $rem;
            for ($i = 0; $i < 18; $i++) {
                $bit = (($bits >> $i) & 1) === 1;
                $a = $size - 11 + $i % 3;
                $b = intdiv($i, 3);
                self::set($m, $f, $a, $b, $bit);
                self::set($m, $f, $b, $a, $bit);
            }
        }
    }

    private static function drawFormatBits(array &$m, array &$f, int $mask): void
    {
        $size = count($m);
        $data = (self::EC_FORMAT_BITS_M << 3) | $mask;
        $rem = $data;
        for ($i = 0; $i < 10; $i++) {
            $rem = ($rem << 1) ^ (($rem >> 9) * 0x537);
        }
        $bits = (($data << 10) | $rem) ^ 0x5412;
        $bit = fn(int $i): bool => (($bits >> $i) & 1) === 1;
        for ($i = 0; $i <= 5; $i++) {
            self::set($m, $f, 8, $i, $bit($i));
        }
        self::set($m, $f, 8, 7, $bit(6));
        self::set($m, $f, 8, 8, $bit(7));
        self::set($m, $f, 7, 8, $bit(8));
        for ($i = 9; $i < 15; $i++) {
            self::set($m, $f, 14 - $i, 8, $bit($i));
        }
        for ($i = 0; $i < 8; $i++) {
            self::set($m, $f, $size - 1 - $i, 8, $bit($i));
        }
        for ($i = 8; $i < 15; $i++) {
            self::set($m, $f, 8, $size - 15 + $i, $bit($i));
        }
        self::set($m, $f, 8, $size - 8, true); // ô tối cố định
    }

    /** Đặt bit dữ liệu theo đường zig-zag 2 cột, từ góc dưới phải, bỏ qua cột định thời. */
    private static function drawCodewords(array &$m, array $f, array $codewords): void
    {
        $size = count($m);
        $total = count($codewords) * 8;
        $i = 0;
        for ($right = $size - 1; $right >= 1; $right -= 2) {
            if ($right === 6) {
                $right = 5;
            }
            for ($vert = 0; $vert < $size; $vert++) {
                for ($j = 0; $j < 2; $j++) {
                    $x = $right - $j;
                    $upward = (($right + 1) & 2) === 0;
                    $y = $upward ? $size - 1 - $vert : $vert;
                    if (!$f[$y][$x] && $i < $total) {
                        $m[$y][$x] = (($codewords[$i >> 3] >> (7 - ($i & 7))) & 1) === 1;
                        $i++;
                    }
                }
            }
        }
    }

    private static function applyMask(array &$m, array $f, int $mask): void
    {
        $size = count($m);
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                if ($f[$y][$x]) {
                    continue;
                }
                $invert = match ($mask) {
                    0 => ($x + $y) % 2 === 0,
                    1 => $y % 2 === 0,
                    2 => $x % 3 === 0,
                    3 => ($x + $y) % 3 === 0,
                    4 => (intdiv($x, 3) + intdiv($y, 2)) % 2 === 0,
                    5 => $x * $y % 2 + $x * $y % 3 === 0,
                    6 => ($x * $y % 2 + $x * $y % 3) % 2 === 0,
                    7 => (($x + $y) % 2 + $x * $y % 3) % 2 === 0,
                };
                if ($invert) {
                    $m[$y][$x] = !$m[$y][$x];
                }
            }
        }
    }

    /** Điểm phạt theo 4 quy tắc của chuẩn (càng thấp càng dễ quét). */
    private static function penalty(array $m): int
    {
        $size = count($m);
        $score = 0;
        $dark = 0;
        $lines = [];
        for ($i = 0; $i < $size; $i++) {
            $lines[] = $m[$i];
            $lines[] = array_column($m, $i);
        }
        foreach ($lines as $line) {
            $run = 1;
            for ($k = 1; $k <= $size; $k++) { // quy tắc 1: chuỗi ≥5 ô cùng màu
                if ($k < $size && $line[$k] === $line[$k - 1]) {
                    $run++;
                } else {
                    if ($run >= 5) {
                        $score += $run - 2;
                    }
                    $run = 1;
                }
            }
            $s = implode('', array_map('intval', $line)); // quy tắc 3: mẫu giống mắt định vị
            $score += 40 * (substr_count('0000' . $s, '00001011101') + substr_count($s . '0000', '10111010000'));
        }
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $dark += (int)$m[$y][$x];
                if ($x < $size - 1 && $y < $size - 1 && $m[$y][$x] === $m[$y][$x + 1]
                    && $m[$y][$x] === $m[$y + 1][$x] && $m[$y][$x] === $m[$y + 1][$x + 1]) {
                    $score += 3; // quy tắc 2: khối 2×2 cùng màu
                }
            }
        }
        $score += 10 * intdiv(abs($dark * 20 - $size * $size * 10), $size * $size); // quy tắc 4: cân bằng đen/trắng
        return $score;
    }
}
