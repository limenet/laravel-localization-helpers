<?php

declare(strict_types=1);

use Potsky\LaravelLocalizationHelpers\Factory\Exception;

test('parameters', function (): void {
    $e = new Exception();
    $e->setParameter('coucou');

    expect($e->getParameter())->toEqual('coucou');
});
