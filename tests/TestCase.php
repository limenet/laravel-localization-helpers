<?php

declare(strict_types=1);
use Potsky\LaravelLocalizationHelpers\LaravelLocalizationHelpersServiceProvider;

class TestCase extends Orchestra\Testbench\TestCase
{
    const MOCK_DIR_PATH = 'tests/mock';

    const MOCK_DIR_PATH_GLOBAL = 'tests/mock/global';

    const MOCK_DIR_PATH_WO_LEMMA = 'tests/mock/wo_lemma';

    const LANG_DIR_PATH = 'tests/lang';

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Get package providers.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [LaravelLocalizationHelpersServiceProvider::class];
    }
}
