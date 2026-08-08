<?php

use Potsky\LaravelLocalizationHelpers\Factory\Localization;

/**
 * Setup the test environment.
 *
 * - Remove all previous lang files before each test
 * - Set custom configuration paths
 */
beforeEach(function (): void {
    $this->langFolder = TestCase::MOCK_DIR_PATH.'/gh54/lang';
    $this->langFile = $this->langFolder.'/en/message.php';
});
test('lang files are created when using dry run', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', $this->langFolder);
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH.'/gh54/code');

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup'      => true,
        '--verbose'        => true,
        '--no-date'        => true,
        '--no-comment'     => true,
        '--dry-run'        => true,
    ]);

    expect($this->langFile)->not->toBeFile();
});
