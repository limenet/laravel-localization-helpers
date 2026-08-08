<?php

use Potsky\LaravelLocalizationHelpers\Factory\Localization;
use Potsky\LaravelLocalizationHelpers\Factory\MessageBag;

test('get message bag', function (): void {
    $messageBag = new MessageBag();
    $manager = new Localization($messageBag);
    expect($manager->getMessageBag())->toBe($messageBag);
});
test('get files with extension with no dir path', function (): void {
    $manager = new Localization(new MessageBag());
    expect($manager->getFilesWithExtension(__FILE__))->toHaveCount(0);
});
test('no translation', function (): void {
    $manager = new Localization(new MessageBag());
    expect($manager->translate('AAA', 'zz'))->toEqual('zz(): AAA');
});
