<?php

use Potsky\LaravelLocalizationHelpers\Factory\Localization;

/**
 * Setup the test environment.
 *
 * - Remove all previous lang files before each test
 * - Set custom configuration paths
 */
beforeEach(function (): void {
    $this->langFolder = TestCase::MOCK_DIR_PATH.'/gh44/lang';
    $this->langValidationEnFile = $this->langFolder.'/en/validation.php';
    $this->langValidationFrFile = $this->langFolder.'/fr/validation.php';
    $this->langMessageEnFile = $this->langFolder.'/en/message.php';
    $this->langMessageFrFile = $this->langFolder.'/fr/message.php';
    $this->langPotskyEnFile = $this->langFolder.'/en/potsky.php';
    $this->langPotskyFrFile = $this->langFolder.'/fr/potsky.php';
});
test('specific file path in ignore configuration', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', $this->langFolder);
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH.'/gh44/code');
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'ignore_lang_files', ['validation', 'tests/mock/gh44/lang/fr/potsky.php']);

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup' => true,
        '--verbose' => true,
        '--no-date' => true,
        '--no-comment' => true,
    ]);

    expect($this->langValidationEnFile)->not->toBeFile();
    expect($this->langValidationFrFile)->not->toBeFile();
    expect($this->langPotskyFrFile)->not->toBeFile();
    expect($this->langPotskyEnFile)->toBeFile();
    expect($this->langMessageEnFile)->toBeFile();
    expect($this->langMessageFrFile)->toBeFile();
});
