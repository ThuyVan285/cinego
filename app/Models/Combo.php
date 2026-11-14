<?php
declare(strict_types=1);

final class Combo
{
    public static function all(): array
    {
        return Database::all('SELECT * FROM combos ORDER BY sort_order');
    }

    public static function map(): array
    {
        $m = [];
        foreach (self::all() as $c) {
            $m[$c['id']] = $c;
        }
        return $m;
    }
}
