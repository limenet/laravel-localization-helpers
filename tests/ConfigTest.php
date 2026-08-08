<?php

declare(strict_types=1);

test('laravel4 config', function (): void {
    /** @noinspection PhpIncludeInspection */
    $config = include 'src/config/config.php';
    expect($config)->toBeArray();
});

test('laravel5 config', function (): void {
    /** @noinspection PhpIncludeInspection */
    $config = include 'src/config/config.php';
    expect($config)->toBeArray();
});
