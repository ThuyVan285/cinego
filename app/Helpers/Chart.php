<?php
declare(strict_types=1);

/**
 * Biểu đồ SVG vẽ phía máy chủ (không cần thư viện JS hay CDN).
 * Màu lấy từ CSS (.chart-bar, .chart-line, .chart-grid...) nên tự theo giao diện sáng/tối.
 */
final class Chart
{
    /** Số tiền rút gọn: 1.250.000 → "1,3tr", 85.000 → "85k". */
    public static function short(int $v): string
    {
        return match (true) {
            $v >= 1_000_000_000 => rtrim(rtrim(number_format($v / 1e9, 1, ',', ''), '0'), ',') . 'tỷ',
            $v >= 1_000_000 => rtrim(rtrim(number_format($v / 1e6, 1, ',', ''), '0'), ',') . 'tr',
            $v >= 1_000 => round($v / 1000) . 'k',
            default => (string)$v,
        };
    }

    /** Lưới ngang + nhãn trục Y dùng chung. */
    private static function grid(int $w, int $padL, int $padT, int $plotH, int $max): string
    {
        $svg = '';
        for ($i = 0; $i <= 4; $i++) {
            $y = $padT + $plotH - $plotH * $i / 4;
            $svg .= '<line class="chart-grid" x1="' . $padL . '" x2="' . $w . '" y1="' . $y . '" y2="' . $y . '"/>'
                . '<text class="chart-axis" x="' . ($padL - 8) . '" y="' . ($y + 4) . '" text-anchor="end">' . self::short((int)($max * $i / 4)) . '</text>';
        }
        return $svg;
    }

    /**
     * Biểu đồ cột dọc.
     * @param array<string,int> $data nhãn → giá trị
     * @param callable|null $label  định dạng nhãn trục X (nhận khoá)
     */
    public static function bars(array $data, string $title, ?callable $label = null, int $height = 240): string
    {
        $n = max(1, count($data));
        // Đủ rộng để nhãn trục X không chồng nhau, nhưng có trần để chữ không bị thu quá nhỏ khi co vào khung.
        $w = (int)min(1100, max(560, $n * 34));
        $padL = 48;
        $padB = 30;
        $padT = 10;
        $plotH = $height - $padB - $padT;
        $max = self::niceMax(max(1, ...array_values($data ?: [0])));
        $bw = ($w - $padL - 8) / $n;
        $svg = '<svg class="chart" viewBox="0 0 ' . $w . ' ' . $height . '" role="img" aria-label="' . e($title) . '">' . self::grid($w, $padL, $padT, $plotH, $max);
        $every = (int)ceil($n / 16);
        $i = 0;
        foreach ($data as $k => $v) {
            $h = $plotH * $v / $max;
            $x = $padL + $i * $bw + $bw * 0.2;
            $y = $padT + $plotH - $h;
            $text = $label ? $label($k) : (string)$k;
            $svg .= '<g class="chart-col"><rect class="chart-bar" x="' . round($x, 1) . '" y="' . round($y, 1) . '" width="' . round($bw * 0.6, 1)
                . '" height="' . round(max($h, $v > 0 ? 2 : 0), 1) . '" rx="4"><title>' . e($text) . ': ' . e(money((int)$v)) . '</title></rect>';
            if ($i % $every === 0) {
                $svg .= '<text class="chart-axis" x="' . round($x + $bw * 0.3, 1) . '" y="' . ($height - 9) . '" text-anchor="middle">' . e($text) . '</text>';
            }
            $svg .= '</g>';
            $i++;
        }
        return $svg . '</svg>';
    }

    /**
     * Biểu đồ đường + vùng (xu hướng theo thời gian), có điểm và tooltip theo từng mốc.
     * @param array<string,int> $data
     */
    public static function area(array $data, string $title, ?callable $label = null, int $height = 260): string
    {
        $n = count($data);
        if ($n < 2) {
            return self::bars($data, $title, $label, $height);
        }
        $w = 1000;
        $padL = 52;
        $padR = 12;
        $padB = 30;
        $padT = 12;
        $plotH = $height - $padB - $padT;
        $plotW = $w - $padL - $padR;
        $max = self::niceMax(max(1, ...array_values($data)));
        $step = $plotW / ($n - 1);
        $pts = [];
        $i = 0;
        foreach ($data as $v) {
            $pts[] = [round($padL + $i * $step, 1), round($padT + $plotH - $plotH * $v / $max, 1)];
            $i++;
        }
        $line = 'M' . implode(' L', array_map(fn($p) => $p[0] . ' ' . $p[1], $pts));
        $base = $padT + $plotH;
        $area = $line . ' L' . end($pts)[0] . ' ' . $base . ' L' . $pts[0][0] . ' ' . $base . ' Z';
        $svg = '<svg class="chart" viewBox="0 0 ' . $w . ' ' . $height . '" role="img" aria-label="' . e($title) . '">' . self::grid($w - $padR, $padL, $padT, $plotH, $max)
            . '<path class="chart-area" d="' . $area . '"/><path class="chart-line" d="' . $line . '"/>';
        $every = (int)ceil($n / 12);
        $i = 0;
        foreach ($data as $k => $v) {
            [$x, $y] = $pts[$i];
            $text = $label ? $label($k) : (string)$k;
            $svg .= '<g class="chart-hit"><rect x="' . round($x - $step / 2, 1) . '" y="' . $padT . '" width="' . round($step, 1) . '" height="' . $plotH . '"/>'
                . '<circle class="chart-dot" cx="' . $x . '" cy="' . $y . '" r="4"/><title>' . e($text) . ': ' . e(money((int)$v)) . '</title></g>';
            if ($i % $every === 0 || $i === $n - 1) {
                $svg .= '<text class="chart-axis" x="' . $x . '" y="' . ($height - 9) . '" text-anchor="' . ($i === 0 ? 'start' : ($i === $n - 1 ? 'end' : 'middle')) . '">' . e($text) . '</text>';
            }
            $i++;
        }
        return $svg . '</svg>';
    }

    /** Đường xu hướng nhỏ cho thẻ KPI. @param int[] $values */
    public static function spark(array $values, string $label = 'Xu hướng'): string
    {
        $values = array_values($values);
        $n = count($values);
        if ($n < 2) {
            return '';
        }
        $max = max(1, ...$values);
        $min = min($values);
        $range = max(1, $max - $min);
        $pts = [];
        foreach ($values as $i => $v) {
            $pts[] = round($i * 100 / ($n - 1), 1) . ' ' . round(26 - 24 * ($v - $min) / $range, 1);
        }
        return '<svg class="kpi-spark" viewBox="0 0 100 28" preserveAspectRatio="none" role="img" aria-label="' . e($label) . '">'
            . '<path d="M' . implode(' L', $pts) . '" fill="none" stroke="currentColor" stroke-width="1.8" vector-effect="non-scaling-stroke" stroke-linejoin="round" stroke-linecap="round"/></svg>';
    }

    /**
     * Biểu đồ tròn (donut) + chú thích.
     * @param array<int,array{label:string,value:int,color:string,note?:string}> $parts
     */
    public static function donut(array $parts, string $centerTop, string $centerSub, string $title): string
    {
        $total = array_sum(array_column($parts, 'value'));
        if ($total <= 0) {
            return '<p class="muted small">Chưa có dữ liệu.</p>';
        }
        $r = 52;
        $c = 2 * M_PI * $r;
        $offset = 0.0;
        $rings = '';
        $legend = '';
        foreach ($parts as $p) {
            if ($p['value'] <= 0) {
                continue;
            }
            $len = $c * $p['value'] / $total;
            $rings .= '<circle cx="70" cy="70" r="' . $r . '" fill="none" stroke="' . e($p['color']) . '" stroke-width="16" stroke-dasharray="'
                . round(max(0, $len - 2), 2) . ' ' . round($c, 2) . '" stroke-dashoffset="' . round(-$offset, 2) . '" transform="rotate(-90 70 70)"><title>'
                . e($p['label']) . ': ' . $p['value'] . '</title></circle>';
            $offset += $len;
            $legend .= '<li><i style="background:' . e($p['color']) . '"></i><span>' . e($p['label']) . (isset($p['note']) ? ' <small class="muted">' . e($p['note']) . '</small>' : '')
                . '</span><b>' . round($p['value'] * 100 / $total) . '%</b></li>';
        }
        return '<div class="donut-wrap"><svg class="donut" viewBox="0 0 140 140" role="img" aria-label="' . e($title) . '">'
            . '<circle cx="70" cy="70" r="' . $r . '" fill="none" stroke="var(--ad-surface-2)" stroke-width="16"/>' . $rings
            . '<text class="donut-center" x="70" y="70" text-anchor="middle">' . e($centerTop) . '</text>'
            . '<text class="donut-sub" x="70" y="88" text-anchor="middle">' . e($centerSub) . '</text></svg>'
            . '<ul class="donut-legend">' . $legend . '</ul></div>';
    }

    /**
     * Thanh ngang xếp hạng.
     * @param array<int,array{label:string,value:int,note?:string}> $rows
     */
    public static function hbars(array $rows, string $title): string
    {
        if (!$rows) {
            return '<p class="muted small">Chưa có dữ liệu trong khoảng thời gian này.</p>';
        }
        $max = max(1, ...array_column($rows, 'value'));
        $out = '<ol class="hbars" aria-label="' . e($title) . '">';
        foreach (array_values($rows) as $i => $r) {
            $pct = round($r['value'] * 100 / $max, 1);
            $out .= '<li><span class="hbars-rank">' . ($i + 1) . '</span><span class="hbars-label" title="' . e($r['label']) . '">' . e($r['label']) . '</span>'
                . '<span class="hbars-value">' . e(money($r['value'])) . (isset($r['note']) ? ' <small class="muted">· ' . e($r['note']) . '</small>' : '') . '</span>'
                . '<span class="hbars-track"><span class="hbars-fill" style="width:' . $pct . '%"></span></span></li>';
        }
        return $out . '</ol>';
    }

    /** Làm tròn trục Y lên số "đẹp" (1, 2, 2.5, 5 × 10^n). */
    private static function niceMax(int $v): int
    {
        $p = 10 ** max(0, (int)floor(log10(max(1, $v))));
        foreach ([1, 2, 2.5, 5, 10] as $m) {
            if ($v <= $m * $p) {
                return (int)($m * $p);
            }
        }
        return $v;
    }
}
