<?php

use Potsky\LaravelLocalizationHelpers\Factory\Localization;

/**
 * Setup the test environment.
 *
 * - Remove all previous lang files before each test
 * - Set custom configuration paths
 */
beforeEach(function (): void {
    $this->langFolder = TestCase::MOCK_DIR_PATH.'/gh59/lang';
    $this->langFile = $this->langFolder.'/en/message.php';
});
test('default dot notation', function (): void {
    @unlink($this->langFile);

    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', $this->langFolder);
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH.'/gh59/code');
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'dot_notation_split_regex', null);

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup' => true,
        '--verbose' => true,
        '--no-date' => true,
        '--no-comment' => true,
    ]);

    $lemmas = require $this->langFile;

    expect($lemmas)->toHaveKey('hello');
    expect($lemmas['hello'][''][''])->toHaveKey('');

    expect($lemmas)->toHaveKey('Hello');
    expect($lemmas['Hello'])->toHaveKey(' How are you?');
    expect($lemmas['Hello'])->toHaveKey('How are you?');
});
test('awesome dot notation', function (): void {
    @unlink($this->langFile);

    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', $this->langFolder);
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH.'/gh59/code');
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'dot_notation_split_regex', '/\\.(?=[^ .!?])/');

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup' => true,
        '--verbose' => true,
        '--no-date' => true,
        '--no-comment' => true,
    ]);

    $lemmas = require $this->langFile;

    expect($lemmas)->toHaveKey('Hello. How are you?');
    expect($lemmas['Hello. How are you?'])->toBeString();

    expect($lemmas)->toHaveKey('hello...');
    expect($lemmas['hello...'])->toBeString();

    expect($lemmas)->toHaveKey('Hello');
    expect($lemmas['Hello'])->toHaveKey('How are you?');
});
