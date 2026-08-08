<?php

use Potsky\LaravelLocalizationHelpers\Factory\Localization;

/**
 * Setup the test environment.
 *
 * - Remove all previous lang files before each test
 * - Set custom configuration paths
 */
beforeEach(function (): void {
    $this->langFolder = TestCase::MOCK_DIR_PATH.'/gh47/lang';
    $this->langFile = $this->langFolder.'/en/things.php';
});
test('when akey is used to access an array and not astring', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', $this->langFolder);
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH.'/gh47/code1');

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup' => true,
        '--verbose' => true,
        '--no-date' => true,
        '--no-comment' => true,
        '--dry-run' => true,
    ]);

    $output = Artisan::output();

    expect($output)->not->toContain('obsolete strings');
    expect($output)->toContain('foo seems to be used to access an array and is already defined in lang file as foo.bar');
});
test('when akey accessing an array was used', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', $this->langFolder);
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH.'/gh47/code2');

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup' => true,
        '--verbose' => true,
        '--no-date' => true,
        '--no-comment' => true,
        '--dry-run' => true,
    ]);

    $output = Artisan::output();

    expect($output)->toContain('2 obsolete strings');
});
