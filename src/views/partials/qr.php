<?php
/**
 * Hình "giống QR" sinh từ mã đặt vé (minh hoạ, KHÔNG quét được).
 * @var string $code
 */
$n = 25;
$cells = '';
$bits = [];
for ($i = 0; $i < $n * $n; $i++) {
    $bits[$i] = (crc32($code . '|' . $i) & 3) < 2;
}
$inFinder = function (int $x, int $y) use ($n): bool {
    return ($x < 8 && $y < 8) || ($x >= $n - 8 && $y < 8) || ($x < 8 && $y >= $n - 8);
};
for ($y = 0; $y < $n; $y++) {
    for ($x = 0; $x < $n; $x++) {
        if (!$inFinder($x, $y) && $bits[$y * $n + $x]) {
            $cells .= "<rect x='$x' y='$y' width='1' height='1'/>";
        }
    }
}
$finder = '';
foreach ([[0, 0], [$n - 7, 0], [0, $n - 7]] as [$fx, $fy]) {
    $finder .= "<rect x='$fx' y='$fy' width='7' height='7'/><rect x='" . ($fx + 1) . "' y='" . ($fy + 1) . "' width='5' height='5' fill='#fff'/><rect x='" . ($fx + 2) . "' y='" . ($fy + 2) . "' width='3' height='3'/>";
}
?>
<svg class="qr" viewBox="-1 -1 <?= $n + 2 ?> <?= $n + 2 ?>" role="img" aria-label="Mã QR minh hoạ cho <?= e($code) ?>" shape-rendering="crispEdges">
    <rect x="-1" y="-1" width="<?= $n + 2 ?>" height="<?= $n + 2 ?>" fill="#fff"/>
    <g fill="#14123a"><?= $cells . $finder ?></g>
</svg>
