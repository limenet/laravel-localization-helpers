<?php

use Potsky\LaravelLocalizationHelpers\Factory\Localization;

/**
 * Setup the test environment.
 *
 * - Remove all previous lang files before each test
 * - Set custom configuration paths
 */
beforeEach(function (): void {
    $this->langFolder = TestCase::MOCK_DIR_PATH.'/gh56/lang';
    $this->langFileEn = $this->langFolder.'/en/message.php';
    $this->langFileFr = $this->langFolder.'/fr/message.php';
    $this->langFileJsonEn = $this->langFolder.'/en.json';
    $this->langFileJsonFr = $this->langFolder.'/fr.json';
    $this->langFileIncorrectGenuine = $this->langFolder.'/en/message....php';
});
test('awesome dot notation', function (): void {
    @unlink($this->langFileFr);
    @unlink($this->langFileEn);
    @unlink($this->langFileJsonEn);
    @unlink($this->langFileJsonFr);
    @rmdir(dirname($this->langFileFr));

    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', $this->langFolder);
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH.'/gh56/code');

    // Config::set(Localization::PREFIX_LARAVEL_CONFIG.'dot_notation_split_regex', '/\\.(?=[^ .!?])/');
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'json_languages', ['en', 'fr']);

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup'      => true,
        '--no-date'        => true,
        '--no-comment'     => true,
        '--verbose'        => true,
    ]);

    expect($this->langFileIncorrectGenuine)->not->toBeFile();
    expect($this->langFileEn)->toBeFile();
    expect($this->langFileJsonEn)->toBeFile();
    expect($this->langFileJsonFr)->toBeFile();

    $lemmas = require $this->langFileEn;

    expect($lemmas)->toHaveKey('any_message');
    expect($lemmas['any_message'])->toBeString();

    $lemmas = file_get_contents($this->langFileJsonEn);
    expect($lemmas)->toBeJson();
});
