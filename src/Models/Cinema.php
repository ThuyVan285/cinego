<?php
declare(strict_types=1);

final class Cinema
{
    public static function brands(): array
    {
        $out = [];
        foreach (Database::all('SELECT * FROM brands ORDER BY name') as $b) {
            $out[$b['id']] = $b;
        }
        return $out;
    }

    public static function find(string $id): ?array
    {
        return Database::one('SELECT c.*, b.name AS brand_name, b.color FROM cinemas c JOIN brands b ON b.id = c.brand WHERE c.id = ?', [$id]);
    }

    public static function search(string $q = '', string $city = '', string $brand = '', string $area = ''): array
    {
        $where = [];
        $p = [];
        if ($q !== '') {
            $where[] = 'c.name LIKE ?';
            $p[] = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
        }
        if ($city !== '') {
            $where[] = 'c.city = ?';
            $p[] = $city;
        }
        if ($brand !== '') {
            $where[] = 'c.brand = ?';
            $p[] = $brand;
        }
        if ($area !== '') {
            $where[] = 'c.area = ?';
            $p[] = $area;
        }
        $sql = 'SELECT c.*, b.name AS brand_name, b.color FROM cinemas c JOIN brands b ON b.id = c.brand'
            . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY c.city, c.name';
        return Database::all($sql, $p);
    }

    public static function cities(): array
    {
        return array_column(Database::all('SELECT DISTINCT city FROM cinemas ORDER BY city'), 'city');
    }

    public static function areas(string $city = ''): array
    {
        $rows = $city !== ''
            ? Database::all('SELECT DISTINCT area FROM cinemas WHERE city = ? ORDER BY area', [$city])
            : Database::all('SELECT DISTINCT area FROM cinemas ORDER BY area');
        return array_column($rows, 'area');
    }
}
