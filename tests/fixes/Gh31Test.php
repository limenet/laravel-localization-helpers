<?php

use Potsky\LaravelLocalizationHelpers\Factory\Localization;

/**
 * Setup the test environment.
 *
 * - Remove all previous lang files before each test
 * - Set custom configuration paths
 */
beforeEach(function (): void {
    $this->langFolder = TestCase::MOCK_DIR_PATH.'/gh31/lang';
    $this->langFile = $this->langFolder.'/en/message.php';
    $this->langFileVendor42 = $this->langFolder.'/packages/message.php';
    $this->langFileVendor = $this->langFolder.'/vendor/message.php';
});
test('vendor is ignored', function (): void {
    @unlink($this->langFile);
    @unlink($this->langFileVendor);

    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', $this->langFolder);
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH.'/gh31/code');

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup'      => true,
        '--verbose'        => true,
        '--no-date'        => true,
        '--no-comment'     => true,
    ]);

    expect($this->langFile)->toBeFile();
    expect($this->langFileVendor)->not->toBeFile();
    expect($this->langFileVendor42)->not->toBeFile();
});
