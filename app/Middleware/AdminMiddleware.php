<?php
declare(strict_types=1);

/** Chỉ cho tài khoản có role = admin. */
final class AdminMiddleware
{
    public static function handle(): void
    {
        User::requireAdmin();
    }
}
