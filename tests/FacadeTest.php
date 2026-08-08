<?php

use Illuminate\Foundation\AliasLoader;
use Potsky\LaravelLocalizationHelpers\Factory\Exception;

test('facade', function (): void {
    $loader = AliasLoader::getInstance();
    $loader->alias('LocalizationHelpers', Potsky\LaravelLocalizationHelpers\Facade\LocalizationHelpers::class);

    $this->expectException(Exception::class);
    LocalizationHelpers::getLangPath('/these_folder_does_not_exist_right?');
});
