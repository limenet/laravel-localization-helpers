<?php

use Potsky\LaravelLocalizationHelpers\Factory\Localization;
use Potsky\LaravelLocalizationHelpers\Factory\MessageBag;

test('dot in path', function (): void {
    $messageBag = new MessageBag;
    $manager = new Localization($messageBag);
    $now = '20160129_202938';

    expect($manager->getBackupPath('/nia/nio/message.php', $now))->toBe('/nia/nio/message.'.$now.'.php');
    expect($manager->getBackupPath('/var/www/kd/domain.com/message.php', $now))->toBe('/var/www/kd/domain.com/message.'.$now.'.php');
    expect($manager->getBackupPath('/var/www/kd/domain.com/message.txt', $now, 'txt'))->toBe('/var/www/kd/domain.com/message.'.$now.'.txt');
});
