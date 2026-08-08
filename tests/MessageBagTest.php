<?php

use Potsky\LaravelLocalizationHelpers\Factory\MessageBag;

test('message bag', function (): void {
    $messageBag = new MessageBag;
    $messageBag->writeInfo('  <blah>this is a line</blah>  ');
    $messageBag->writeLine('  <blah>this is a line</blah>  ');
    $messageBag->writeError('  <blah>this is a line</blah>  ');
    $messageBag->writeComment('  <blah>this is a line</blah>  ');
    $messageBag->writeQuestion('  <blah>this is a line</blah>  ');

    $messages = $messageBag->getMessages();

    expect($messageBag->hasMessages())->toBeTrue();
    expect($messages)->toBeArray();

    $message = current($messages);
    expect($messageBag->getMessageType($message))->toEqual(MessageBag::INFO);
    expect($messageBag->getMessage($message))->toEqual('this is a line');

    $message = next($messages);
    expect($messageBag->getMessageType($message))->toEqual(MessageBag::LINE);
    expect($messageBag->getMessage($message))->toEqual('this is a line');

    $message = next($messages);
    expect($messageBag->getMessageType($message))->toEqual(MessageBag::ERROR);
    expect($messageBag->getMessage($message))->toEqual('this is a line');

    $message = next($messages);
    expect($messageBag->getMessageType($message))->toEqual(MessageBag::COMMENT);
    expect($messageBag->getMessage($message))->toEqual('this is a line');

    $message = next($messages);
    expect($messageBag->getMessageType($message))->toEqual(MessageBag::QUESTION);
    expect($messageBag->getMessage($message))->toEqual('this is a line');

    $messageBag->deleteMessages();
    expect($messageBag->hasMessages())->toBeFalse();
});
