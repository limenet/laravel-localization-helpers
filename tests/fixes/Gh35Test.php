<?php

use Potsky\LaravelLocalizationHelpers\Factory\Localization;

/**
 * Setup the test environment.
 *
 * - Remove all previous lang files before each test
 * - Set custom configuration paths
 */
beforeEach(function (): void {
    $this->langFolder = TestCase::MOCK_DIR_PATH.'/gh35/lang';
    $this->langFile = $this->langFolder.'/en/message.php';
});
test('multiline trans should be catched', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', $this->langFolder);
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH.'/gh35/code');

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup'      => true,
        '--verbose'        => true,
        '--no-date'        => true,
        '--no-comment'     => true,
    ]);

    $lemmas = require $this->langFile;

    // FIXME: weird behavior
    expect($lemmas['LLH:obsolete'])->toHaveKey('multiline1');
    expect($lemmas['LLH:obsolete'])->toHaveKey('multiline2');
    expect($lemmas['LLH:obsolete'])->toHaveKey('multiline3');
    expect($lemmas)->toHaveKey('multiline4');
    expect($lemmas['LLH:obsolete'])->toHaveKey('multiline5');
    expect($lemmas['LLH:obsolete'])->toHaveKey('multiline6');
    expect($lemmas)->toHaveKey('multiline7');
    expect($lemmas['LLH:obsolete'])->toHaveKey('multiline8');
});
