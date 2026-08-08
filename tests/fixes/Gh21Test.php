<?php

use Potsky\LaravelLocalizationHelpers\Factory\Localization;

/**
 * Setup the test environment.
 *
 * - Remove all previous lang files before each test
 * - Set custom configuration paths
 */
beforeEach(function (): void {
    $this->defaultLangWithObsoleteContent = "<?php
return array (
	'section' => array (
		1 => array (
			'name' => 'First lady',
		),
	),
	'LLH:obsolete' => array (
		'section' => array (
			2 => array (
				'name' => 'Second to die',
			),
		),
	),
);";

    $this->defaultLangContent = "<?php
return array(
	'section'        => array(
		1 => array(
			'name' => 'First lady',
		),
		2 => array(
			'name' => 'Second to die',
		),
	),
);";

    $this->langFolder = TestCase::MOCK_DIR_PATH.'/gh21/lang';
    $this->langFile = $this->langFolder.'/en/message.php';

    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', $this->langFolder);

    // Set content in lang file
    File::put($this->langFile, $this->defaultLangContent);
});
test('obsolete sub key removed', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH.'/gh21/code');

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup' => true,
        '--verbose' => true,
        '--no-date' => true,
    ]);

    expect(Artisan::output())->toContain('1 obsolete string');

    expect(require $this->langFile)->toHaveKey('LLH:obsolete');
});
test('obsolete are kept', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH.'/gh21/code');

    // Set content in lang file with obsolete lemma
    File::put($this->langFile, $this->defaultLangWithObsoleteContent);

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    Artisan::call('localization:missing', [
        '--no-interaction' => true,
        '--no-backup' => true,
        '--no-date' => true,
    ]);

    expect(Artisan::output())->toContain('1 obsolete string');

    $lemmas = require $this->langFile;

    expect($lemmas)->toHaveKey('LLH:obsolete');
    expect($lemmas['LLH:obsolete'])->not->toHaveKey('LLH:obsolete');
});
