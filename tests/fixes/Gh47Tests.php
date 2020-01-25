<?php

use Potsky\LaravelLocalizationHelpers\Factory\Localization;

class Gh47Tests extends TestCase
{
    private static $langFolder;

    private static $langFile;

    /**
     * Setup the test environment.
     *
     * - Remove all previous lang files before each test
     * - Set custom configuration paths
     */
    public function setUp(): void
    {
        parent::setUp();

        self::$langFolder = self::MOCK_DIR_PATH.'/gh47/lang';
        self::$langFile = self::$langFolder.'/en/things.php';
    }

    /**
     * https://github.com/potsky/laravel-localization-helpers/issues/47.
     */
    public function testWhenAKeyIsUsedToAccessAnArrayAndNotAString()
    {
        Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', self::$langFolder);
        Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', self::MOCK_DIR_PATH.'/gh47/code1');

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        Artisan::call('localization:missing', [
            '--no-interaction' => true,
            '--no-backup'      => true,
            '--verbose'        => true,
            '--no-date'        => true,
            '--no-comment'     => true,
            '--dry-run'        => true,
        ]);

        $output = Artisan::output();

        $this->assertStringNotContainsString('obsolete strings', $output);
        $this->assertStringContainsString('foo seems to be used to access an array and is already defined in lang file as foo.bar', $output);
    }

    /**
     * https://github.com/potsky/laravel-localization-helpers/issues/47.
     */
    public function testWhenAKeyAccessingAnArrayWasUsed()
    {
        Config::set(Localization::PREFIX_LARAVEL_CONFIG.'lang_folder_path', self::$langFolder);
        Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', self::MOCK_DIR_PATH.'/gh47/code2');

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        Artisan::call('localization:missing', [
            '--no-interaction' => true,
            '--no-backup'      => true,
            '--verbose'        => true,
            '--no-date'        => true,
            '--no-comment'     => true,
            '--dry-run'        => true,
        ]);

        $output = Artisan::output();

        $this->assertStringContainsString('2 obsolete strings', $output);
    }
}
