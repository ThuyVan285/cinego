<?php
declare(strict_types=1);

/**
 * Sinh ảnh SVG minh hoạ (poster, banner, combo, rạp, ưu đãi) – không phụ thuộc ảnh bên ngoài,
 * nên không bao giờ bị vỡ ảnh. Phim/rạp đều là hư cấu.
 */
final class Art
{
    public static function serve(string $kind, string $id): void
    {
        header('Content-Type: image/svg+xml; charset=utf-8');
        header('Cache-Control: public, max-age=3600');
        echo match ($kind) {
            'poster' => self::movie($id, 400, 600),
            'backdrop' => self::movie($id, 1600, 700),
            'combo' => self::combo($id),
            'cinema' => self::cinema($id),
            'promo' => self::promo($id),
            default => self::movie('fallback', 400, 600),
        };
    }

    private static function x(string $s): string
    {
        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private static function wrap(string $t, int $max): array
    {
        $lines = [];
        $cur = '';
        foreach (explode(' ', $t) as $w) {
            if ($cur !== '' && mb_strlen($cur . ' ' . $w) > $max) {
                $lines[] = $cur;
                $cur = $w;
            } else {
                $cur = $cur === '' ? $w : $cur . ' ' . $w;
            }
        }
        if ($cur !== '') {
            $lines[] = $cur;
        }
        return $lines;
    }

    private static function movie(string $id, int $w, int $h): string
    {
        $m = $id !== 'fallback' ? Movie::find($id) : null;
        $pal = $m['palette'] ?? ['#1e1b4b', '#4f46e5', '#c7d2fe'];
        [$c1, $c2, $c3] = $pal;
        $title = $m['title'] ?? APP_NAME;
        $genre = $m['genres'][0] ?? 'DEMO';
        $wide = $w > $h;
        $u = $wide ? $h : $w;

        $shapes = match ($genre) {
            'Hành động' => "<path d='M0 {$h} L" . (int)($w * .55) . " 0 L" . (int)($w * .7) . " 0 L" . (int)($w * .15) . " {$h}Z' fill='{$c3}' opacity='.22'/>"
                . "<path d='M" . (int)($w * .4) . " {$h} L" . (int)($w * .95) . " 0 L{$w} 0 L{$w} " . (int)($h * .35) . " L" . (int)($w * .6) . " {$h}Z' fill='{$c2}' opacity='.55'/>",
            'Kinh dị' => "<circle cx='" . (int)($w * ($wide ? .78 : .7)) . "' cy='" . (int)($h * .28) . "' r='" . (int)($u * .26) . "' fill='{$c3}' opacity='.85'/>"
                . "<circle cx='" . (int)($w * ($wide ? .76 : .64)) . "' cy='" . (int)($h * .25) . "' r='" . (int)($u * .24) . "' fill='{$c1}'/>",
            'Hoạt hình' => "<circle cx='" . (int)($w * .75) . "' cy='" . (int)($h * .3) . "' r='" . (int)($u * .3) . "' fill='{$c3}' opacity='.9'/>"
                . "<circle cx='" . (int)($w * .2) . "' cy='" . (int)($h * .62) . "' r='" . (int)($u * .18) . "' fill='{$c2}' opacity='.8'/>"
                . "<circle cx='" . (int)($w * .5) . "' cy='" . (int)($h * .75) . "' r='" . (int)($u * .1) . "' fill='{$c3}' opacity='.6'/>",
            'Tình cảm' => "<circle cx='" . (int)($w * .6) . "' cy='" . (int)($h * .32) . "' r='" . (int)($u * .26) . "' fill='{$c3}' opacity='.55'/>"
                . "<circle cx='" . (int)($w * .74) . "' cy='" . (int)($h * .36) . "' r='" . (int)($u * .26) . "' fill='{$c2}' opacity='.6'/>",
            'Hài' => "<circle cx='" . (int)($w * .72) . "' cy='" . (int)($h * .3) . "' r='" . (int)($u * .22) . "' fill='{$c3}'/>"
                . "<path d='M0 " . (int)($h * .78) . " Q" . (int)($w * .5) . " " . (int)($h * .6) . " {$w} " . (int)($h * .78) . " V{$h} H0Z' fill='{$c2}' opacity='.7'/>",
            default => "<circle cx='" . (int)($w * .72) . "' cy='" . (int)($h * .3) . "' r='" . (int)($u * .2) . "' fill='{$c3}' opacity='.9'/>"
                . "<ellipse cx='" . (int)($w * .72) . "' cy='" . (int)($h * .3) . "' rx='" . (int)($u * .36) . "' ry='" . (int)($u * .07) . "' fill='none' stroke='{$c3}' stroke-width='" . max(3, (int)($u * .015)) . "' transform='rotate(-20 " . (int)($w * .72) . " " . (int)($h * .3) . ")'/>"
                . "<circle cx='" . (int)($w * .2) . "' cy='" . (int)($h * .2) . "' r='" . (int)($u * .015) . "' fill='#fff'/><circle cx='" . (int)($w * .4) . "' cy='" . (int)($h * .12) . "' r='" . (int)($u * .01) . "' fill='#fff'/>",
        };

        $lines = self::wrap($title, $wide ? 22 : 11);
        $fs = $wide ? 76 : 44;
        $x0 = $wide ? 90 : (int)($w / 2);
        $y0 = $wide ? (int)($h * .5 - (count($lines) - 1) * $fs * .5) : (int)($h * .64);
        $anchor = $wide ? 'start' : 'middle';
        $text = '';
        foreach ($lines as $i => $ln) {
            $text .= "<text x='{$x0}' y='" . ($y0 + $i * (int)($fs * 1.12)) . "' text-anchor='{$anchor}' font-family='Segoe UI,Arial,sans-serif' font-weight='800' font-size='{$fs}' fill='#fff'>" . self::x(mb_strtoupper($ln)) . "</text>";
        }
        $tagY = $y0 + count($lines) * (int)($fs * 1.12) + 14;
        $tag = "<text x='{$x0}' y='{$tagY}' text-anchor='{$anchor}' font-family='Segoe UI,Arial,sans-serif' font-size='" . ($wide ? 26 : 17) . "' letter-spacing='4' fill='{$c3}'>" . self::x(mb_strtoupper($genre)) . "</text>";
        $demo = "<text x='" . ($wide ? 90 : (int)($w / 2)) . "' y='" . ($h - ($wide ? 36 : 26)) . "' text-anchor='{$anchor}' font-family='Segoe UI,Arial,sans-serif' font-size='" . ($wide ? 20 : 12) . "' letter-spacing='3' fill='#fff' opacity='.5'>" . APP_NAME . "</text>";

        return "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 {$w} {$h}' width='{$w}' height='{$h}'>"
            . "<defs><linearGradient id='g' x1='0' y1='0' x2='1' y2='1'><stop offset='0' stop-color='{$c1}'/><stop offset='1' stop-color='{$c2}'/></linearGradient>"
            . "<linearGradient id='f' x1='0' y1='.4' x2='0' y2='1'><stop offset='0' stop-color='#000' stop-opacity='0'/><stop offset='1' stop-color='#000' stop-opacity='.55'/></linearGradient></defs>"
            . "<rect width='{$w}' height='{$h}' fill='url(#g)'/>{$shapes}<rect width='{$w}' height='{$h}' fill='url(#f)'/>" . ($wide ? '' : "{$text}{$tag}{$demo}") . '</svg>';
    }

    private static function combo(string $id): string
    {
        $bg = ['popcorn' => '#fde68a', 'drink' => '#bae6fd', 'combo1' => '#fbcfe8', 'combo2' => '#ddd6fe'][$id] ?? '#e5e7eb';
        $bucket = "<path d='M70 110 H150 L140 220 H80Z' fill='#e11d48'/><path d='M96 110 L100 220 M124 110 L120 220' stroke='#fff' stroke-width='10' opacity='.85'/>"
            . "<g fill='#fff7ed' stroke='#f59e0b' stroke-width='3'><circle cx='80' cy='100' r='18'/><circle cx='105' cy='86' r='20'/><circle cx='132' cy='96' r='19'/><circle cx='95' cy='108' r='14'/><circle cx='122' cy='110' r='14'/></g>";
        $cup = "<path d='M80 110 H140 L130 225 H90Z' fill='#2563eb'/><rect x='74' y='98' width='72' height='16' rx='6' fill='#1e40af'/><path d='M118 60 L128 98' stroke='#ef4444' stroke-width='7' stroke-linecap='round'/><rect x='92' y='150' width='36' height='30' rx='5' fill='#fff' opacity='.9'/>";
        $body = match ($id) {
            'popcorn' => "<g transform='translate(40 -5) scale(1.2)'>{$bucket}</g>",
            'drink' => "<g transform='translate(40 -5) scale(1.2)'>{$cup}</g>",
            'combo1' => "<g transform='translate(-5 5)'>{$bucket}</g><g transform='translate(95 10) scale(.9)'>{$cup}</g>",
            default => "<g transform='translate(-25 20) scale(.85)'>{$bucket}</g><g transform='translate(70 20) scale(.85)'>{$bucket}</g><g transform='translate(125 35) scale(.75)'>{$cup}</g><g transform='translate(185 35) scale(.75)'>{$cup}</g>",
        };
        return "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 260'><rect width='300' height='260' fill='{$bg}'/><ellipse cx='150' cy='238' rx='100' ry='12' fill='#000' opacity='.1'/>{$body}</svg>";
    }

    private static function cinema(string $id): string
    {
        $c = Cinema::find($id);
        $col = $c['color'] ?? '#4f46e5';
        $name = self::x($c['brand_name'] ?? APP_NAME);
        $seed = crc32($id);
        $b = '';
        for ($i = 0; $i < 7; $i++) {
            $bw = 50 + ($seed >> ($i * 2)) % 40;
            $bh = 80 + ($seed >> ($i * 3 + 1)) % 120;
            $bx = 20 + $i * 82;
            $b .= "<rect x='{$bx}' y='" . (260 - $bh) . "' width='{$bw}' height='{$bh}' fill='#0b1030' opacity='.55'/>";
            for ($r = 0; $r < 5; $r++) {
                $b .= "<rect x='" . ($bx + 8) . "' y='" . (260 - $bh + 12 + $r * 22) . "' width='8' height='10' fill='#fde68a' opacity='" . ((($seed >> ($r + $i)) & 1) ? .85 : .2) . "'/>";
            }
        }
        return "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 640 260'><defs><linearGradient id='g' x1='0' y1='0' x2='1' y2='1'><stop offset='0' stop-color='{$col}'/><stop offset='1' stop-color='#1e1b4b'/></linearGradient></defs>"
            . "<rect width='640' height='260' fill='url(#g)'/>{$b}<rect y='236' width='640' height='24' fill='#0b1030' opacity='.6'/>"
            . "<text x='24' y='46' font-family='Segoe UI,Arial,sans-serif' font-weight='800' font-size='26' fill='#fff'>{$name}</text></svg>";
    }

    private static function promo(string $code): string
    {
        $p = Promotion::find($code);
        $c1 = $p['color1'] ?? '#4f46e5';
        $c2 = $p['color2'] ?? '#c026d3';
        $tag = self::x($p['tag'] ?? 'DEMO');
        return "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 640 300'><defs><linearGradient id='g' x1='0' y1='0' x2='1' y2='1'><stop offset='0' stop-color='{$c1}'/><stop offset='1' stop-color='{$c2}'/></linearGradient></defs>"
            . "<rect width='640' height='300' fill='url(#g)'/><circle cx='560' cy='40' r='140' fill='#fff' opacity='.08'/><circle cx='80' cy='300' r='110' fill='#fff' opacity='.08'/>"
            . "<text x='40' y='170' font-family='Segoe UI,Arial,sans-serif' font-weight='900' font-size='110' fill='#fff'>{$tag}</text>"
            . "<text x='42' y='222' font-family='Segoe UI,Arial,sans-serif' font-size='22' letter-spacing='4' fill='#fff' opacity='.8'>ƯU ĐÃI · " . APP_NAME . "</text></svg>";
    }
}
