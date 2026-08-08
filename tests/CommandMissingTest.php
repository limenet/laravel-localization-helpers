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

    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', TestCase::LANG_DIR_PATH);
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH_GLOBAL);
});
test('lang file does not exist', function (): void {
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:missing', ['--no-interaction' => true, '--verbose' => true]);
    $result = Artisan::output();

    expect($return)->toEqual(0);
    expect($result)->toContain('File has been created');

    /** @noinspection PhpIncludeInspection */
    $lemmas = include TestCase::LANG_DIR_PATH.'/fr/message.php';
    expect($lemmas['lemma']['child'])->toEqual('TODO: child');
});
test('lang folder does not exist', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', TestCase::LANG_DIR_PATH.'doesnotexist');

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:missing', ['--no-interaction' => true]);

    expect($return)->toEqual(1);
    expect(Artisan::output())->toContain('No lang folder found in your custom path:');
});
test('default lang folder does not exist', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', null);

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:missing', ['--no-interaction' => true]);

    expect($return)->toEqual(0);
});
test('default lang folder exists', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', null);

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:missing', ['--no-interaction' => true]);

    expect($return)->toEqual(0);
});
test('lang file exists with backup', function (): void {
    touch(TestCase::LANG_DIR_PATH.'/en/message.php');
    touch(TestCase::LANG_DIR_PATH.'/fr/message.php');

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:missing', ['--no-interaction' => true]);

    expect($return)->toEqual(0);
    expect(Artisan::output())->toContain('Backup files');
});
test('flat output', function (): void {
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--output-flat' => true,
        '--new-value' => '%LEMMA POTSKY',
    ]);

    expect($return)->toEqual(0);

    /** @noinspection PhpIncludeInspection */
    $lemmas = include TestCase::LANG_DIR_PATH.'/fr/message.php';
    expect($lemmas['lemma.child'])->toEqual('child POTSKY');
});
test('translation fallback', function (): void {
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--output-flat' => true,
        '--new-value' => 'nUll',
    ]);

    expect($return)->toEqual(0);

    /** @noinspection PhpIncludeInspection */
    $lemmas = include TestCase::LANG_DIR_PATH.'/fr/message.php';
    expect($lemmas['lemma.child'])->toBeNull();
});
test('translations', function (): void {
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--output-flat' => true,
        '--translation' => true,
    ]);

    expect($return)->toEqual(0);

    /** @noinspection PhpIncludeInspection */
    $lemmas = include TestCase::LANG_DIR_PATH.'/fr/message.php';
    expect($lemmas['dog'])->toEqual('TODO: fr(): dog');
    expect($lemmas['child.dog'])->toEqual('TODO: fr(): dog');
});
test('verbose', function (): void {
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--verbose' => true,
    ]);

    expect($return)->toEqual(0);
    expect(Artisan::output())->toContain('Lemmas will be searched in the following directories:');

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--verbose' => true,
    ]);

    expect($return)->toEqual(0);
    expect(Artisan::output())->toContain('Nothing to do for this file');
});
test('nothing to do', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH_WO_LEMMA);

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--verbose' => true,
    ]);

    expect($return)->toEqual(0);
    expect(Artisan::output())->toContain('No lemma has been found in code.');
});
test('obsolete lemma', function (): void {
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup' => true,
    ]);

    expect($return)->toEqual(0);

    $lemmas = require $this->langFile;
    expect($lemmas)->toHaveKey('child');

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--verbose' => true,
        '--php-file-extension' => 'copy',
        '--no-backup' => true,
    ]);

    expect($return)->toEqual(0);

    expect(Artisan::output())->toContain('11 obsolete strings');

    $lemmas = require $this->langFile;
    expect($lemmas)->not->toHaveKey('child');
    expect($lemmas['LLH:obsolete'])->toHaveKey('child');
});
test('silent', function (): void {
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--silent' => true,
    ]);

    // Exit code is 1 because there are new lemma to translate
    expect($return)->toEqual(1);
    expect(Artisan::output())->toBeEmpty();
});
