<?php

use Potsky\LaravelLocalizationHelpers\Factory\Localization;
use Potsky\LaravelLocalizationHelpers\Factory\MessageBag;
use Potsky\LaravelLocalizationHelpers\Factory\Tools;

/**
 * Setup the test environment.
 *
 * - Remove all previous lang files before each test
 * - Create 10 backup files, one per day
 */
beforeEach(function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', TestCase::LANG_DIR_PATH);
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH_GLOBAL);

    Tools::unlinkGlobFiles(TestCase::LANG_DIR_PATH.'/*/message*.php');

    $manager = new Localization(new MessageBag());

    for ($i = 0; $i < 10; $i++) {
        $time = $manager->getBackupDate($i);

        touch(TestCase::LANG_DIR_PATH.'/en/message'.$time.'.php');
        touch(TestCase::LANG_DIR_PATH.'/fr/message'.$time.'.php');
    }
});
test('clean all', function (): void {
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:clear', []);

    expect($return)->toEqual(0);
    expect(glob(TestCase::LANG_DIR_PATH.'/*/message*.php'))->toHaveCount(0);
});
test('clean30 days', function (): void {
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:clear', ['--days' => 30]);

    expect($return)->toEqual(0);
    expect(glob(TestCase::LANG_DIR_PATH.'/*/message*.php'))->toHaveCount(20);
});
test('clean3 days', function (): void {
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:clear', ['--days' => 3]);

    expect($return)->toEqual(0);
    expect(glob(TestCase::LANG_DIR_PATH.'/*/message*.php'))->toHaveCount(6);
});
test('error days negative', function (): void {
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:clear', ['--days' => -3]);
    expect($return)->toEqual(1);

    $manager = new Localization(new MessageBag());
    expect($manager->deleteBackupFiles('', -3, true))->toBeFalse();
});
test('dry run', function (): void {
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:clear', ['--dry-run' => true]);

    expect($return)->toEqual(0);
    expect(glob(TestCase::LANG_DIR_PATH.'/*/message*.php'))->toHaveCount(20);
});
test('lang folder does not exist', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', TestCase::LANG_DIR_PATH.'doesnotexist');

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:clear', ['--dry-run' => true]);

    expect($return)->toEqual(1);
    expect(Artisan::output())->toContain('No lang folder found in your custom path:');
});
test('default lang folder does not exist', function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', null);

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:clear', ['--dry-run' => true]);

    expect($return)->toEqual(0);
});
