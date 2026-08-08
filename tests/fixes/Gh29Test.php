<?php

use Potsky\LaravelLocalizationHelpers\Factory\Localization;
use Potsky\LaravelLocalizationHelpers\Factory\Tools;

/**
 * Setup the test environment.
 *
 * - Remove all previous lang files before each test
 * - Set custom configuration paths
 */
beforeEach(function (): void {
    Tools::unlinkGlobFiles(TestCase::LANG_DIR_PATH.'/*/message*.php');

    $this->langFile = TestCase::LANG_DIR_PATH.'/en/message.php';
});
test('obsolete strings should return in main array', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH.'/gh29/code');
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', TestCase::LANG_DIR_PATH);

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup'      => true,
        '--verbose'        => true,
        '--no-date'        => true,
        '--no-comment'     => true,
    ]);

    $lemmas = require $this->langFile;
    expect($lemmas)->toHaveKey('lemma1');

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction'     => true,
        '--no-backup'          => true,
        '--verbose'            => true,
        '--no-date'            => true,
        '--no-comment'         => true,
        '--php-file-extension' => 'copy',
    ]);

    $lemmas = require $this->langFile;
    expect($lemmas)->not->toHaveKey('lemma1');
    expect($lemmas)->toHaveKey('lemma2');
    expect($lemmas['LLH:obsolete'])->toHaveKey('lemma1');

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup'      => true,
        '--verbose'        => true,
        '--no-date'        => true,
        '--no-comment'     => true,
    ]);

    $lemmas = require $this->langFile;
    expect($lemmas)->toHaveKey('lemma1');
    expect($lemmas)->not->toHaveKey('lemma2');
    expect($lemmas['LLH:obsolete'])->toHaveKey('lemma2');
});
