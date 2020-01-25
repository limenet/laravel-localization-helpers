<?php

class TestCase extends Orchestra\Testbench\TestCase
{
    const MOCK_DIR_PATH = 'tests/mock';
    const MOCK_DIR_PATH_GLOBAL = 'tests/mock/global';
    const MOCK_DIR_PATH_WO_LEMMA = 'tests/mock/wo_lemma';
    const LANG_DIR_PATH = 'tests/lang';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * Get package providers.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return ['Potsky\LaravelLocalizationHelpers\LaravelLocalizationHelpersServiceProvider'];
    }
}
