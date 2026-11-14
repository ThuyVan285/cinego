<?php
declare(strict_types=1);

/**
 * Bộ chạy test tối giản (chưa dùng PHPUnit để không cần Composer).
 * Chạy tất cả:  php tests/run.php        Chạy một file:  php tests/run.php SeatTest
 * Test có đụng CSDL (AuthTest) cần bật MySQL và đã chạy DatabaseSeeder.
 */
require __DIR__ . '/../app/bootstrap.php';
date_default_timezone_set((string)config('app.timezone'));

$GLOBALS['__pass'] = 0;
$GLOBALS['__fail'] = [];

function check(bool $cond, string $label): void
{
    if ($cond) {
        $GLOBALS['__pass']++;
        echo "  ✓ $label\n";
    } else {
        $GLOBALS['__fail'][] = $label;
        echo "  ✗ $label\n";
    }
}

function same(mixed $expected, mixed $actual, string $label): void
{
    check($expected === $actual, $label . ($expected === $actual ? '' : ' (mong đợi ' . var_export($expected, true) . ', nhận ' . var_export($actual, true) . ')'));
}

$only = $argv[1] ?? null;
foreach (glob(__DIR__ . '/*Test.php') as $file) {
    $name = basename($file, '.php');
    if ($only !== null && $only !== $name) {
        continue;
    }
    echo "\n$name\n";
    try {
        require $file;
    } catch (Throwable $e) {
        $GLOBALS['__fail'][] = "$name: " . $e->getMessage();
        echo '  ✗ Lỗi: ' . $e->getMessage() . "\n";
    }
}

$fail = count($GLOBALS['__fail']);
echo "\nKết quả: {$GLOBALS['__pass']} đạt, $fail lỗi\n";
exit($fail ? 1 : 0);
