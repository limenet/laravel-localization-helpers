<?php

use Potsky\LaravelLocalizationHelpers\Factory\Tools;

test('laravel version', function (): void {
    $major = Tools::getLaravelMajorVersion();

    expect([12, 13])->toContain($major);
    expect(Tools::isLaravel($major))->toBeTrue();
    expect(Tools::isLaravel($major + 1))->toBeFalse();
});
test('valid directory', function (): void {
    expect(Tools::isValidDirectory(__DIR__, __FILE__))->toBeFalse();
});
test('array set dot first level', function (): void {
    $array = [];

    Tools::arraySet($array, 'a.b.c', true, '/\\./', 2);
    expect($array['a']['b.c'])->toBeTrue();

    Tools::arraySet($array, 'a.b.c', true, '/\\./', 2);
    expect(@$array['a']['b']['c'])->toBeNull();

    Tools::arraySet($array, null, true, '/\\./', 2);

    /** @var bool $array */
    expect($array)->toBeTrue();
});
