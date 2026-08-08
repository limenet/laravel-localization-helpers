<?php

declare(strict_types=1);

use Potsky\LaravelLocalizationHelpers\Factory\Translator;
use Potsky\LaravelLocalizationHelpers\Factory\TranslatorSample;

test('injection', function (): void {
    $translator = new Translator('Sample');
    expect($translator instanceof Translator)->toBeTrue();
    expect($translator->getTranslator() instanceof TranslatorSample)->toBeTrue();
});
test('real case', function (): void {
    $translator = new Translator('Sample');
    expect($translator->translate('chair', 'de'))->toEqual('de(): chair');
    expect($translator->translate('chair', 'de', 'fr'))->toEqual('de(fr): chair');
});
test('no translation', function (): void {
    $translator = new Translator('Sample');
    expect($translator->translate('', ''))->toEqual('(): ');
});
test('unknown lang', function (): void {
    $translator = new Translator('Sample');
    expect($translator->translate('dog', 'zz'))->toEqual('zz(): dog');
});
test('real case with default language', function (): void {
    $translator = new Translator('Sample');
    expect($translator->translate('chair', 'de'))->toEqual('de(): chair');
});
