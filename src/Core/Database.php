<?php
declare(strict_types=1);

final class Database
{
    private static ?PDO $pdo = null;

    public static function connect(bool $withDb = true): PDO
    {
        $c = require __DIR__ . '/../config.php';
        $dsn = "mysql:host={$c['db_host']};port={$c['db_port']};" . ($withDb ? "dbname={$c['db_name']};" : '') . 'charset=utf8mb4';
        return new PDO($dsn, $c['db_user'], $c['db_pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public static function pdo(): PDO
    {
        return self::$pdo ??= self::connect();
    }

    public static function all(string $sql, array $params = []): array
    {
        $st = self::pdo()->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    public static function one(string $sql, array $params = []): ?array
    {
        $st = self::pdo()->prepare($sql);
        $st->execute($params);
        $r = $st->fetch();
        return $r === false ? null : $r;
    }

    public static function exec(string $sql, array $params = []): int
    {
        $st = self::pdo()->prepare($sql);
        $st->execute($params);
        return $st->rowCount();
    }
}
