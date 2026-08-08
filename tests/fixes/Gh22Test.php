<?php

use Potsky\LaravelLocalizationHelpers\Factory\Localization;

/**
 * Setup the test environment.
 *
 * - Remove all previous lang files before each test
 * - Set custom configuration paths
 */
beforeEach(function (): void {
    $this->defaultLangContent = "<?php
return array(
	'my dog is rich' => 'My dog is rich' ,
	'section'        => array(
		1 => array(
			'name' => 'Niania',
		),
	),
);";

    $this->langFolder = TestCase::MOCK_DIR_PATH.'/gh22/lang';
    $this->langFile = $this->langFolder.'/en/message.php';

    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', $this->langFolder);

    // Set content in lang file
    File::put($this->langFile, $this->defaultLangContent);
});
test('obsolete key is not removed', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH.'/gh22/phase1');

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup' => true,
        '--no-date' => true,
    ]);

    expect(Artisan::output())->toContain('1 obsolete string');

    $lemmas = require $this->langFile;

    expect($lemmas)->toHaveKey('LLH:obsolete');
    expect($lemmas['LLH:obsolete'])->toHaveKey('section');
});
test('obsolete key is removed when setting option', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH.'/gh22/phase1');

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup' => true,
        '--no-obsolete' => true,
        '--no-date' => true,
    ]);

    expect(Artisan::output())->toContain('1 obsolete string');

    $lemmas = require $this->langFile;

    expect($lemmas)->not->toHaveKey('LLH:obsolete');
});
test('dynamic field should not be obsolete when not adding anew lemma', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH.'/gh22/phase1');
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'never_obsolete_keys', ['section']);

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup' => true,
        '--verbose' => true,
        '--no-date' => true,
    ]);

    expect(File::get($this->langFile))->toEqual($this->defaultLangContent);
});
test('dynamic field should not be obsolete when adding anew lemma', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH.'/gh22/phase2');
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'never_obsolete_keys', ['section']);

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup' => true,
        '--verbose' => true,
        '--no-date' => true,
    ]);

    expect(require $this->langFile)->toHaveKey('section');
});
