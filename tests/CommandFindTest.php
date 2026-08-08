<?php

use Potsky\LaravelLocalizationHelpers\Factory\Localization;
use Potsky\LaravelLocalizationHelpers\Factory\MessageBag;

/**
 * Setup the test environment.
 *
 * - Set custom configuration paths
 */
beforeEach(function (): void {
    Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', TestCase::MOCK_DIR_PATH_GLOBAL);
});
test('search for regular lemma', function (): void {
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:find', ['lemma' => 'message.lemma', '--verbose' => true, '--short' => true]);

    expect($return)->toEqual(0);
    expect(Artisan::output())->toContain('Lemma message.lemma has been found in');
});
test('search for regex lemma', function (): void {
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:find', ['lemma' => 'message\\.lemma.*', '--verbose' => true, '--short' => true, '--regex' => true]);

    expect($return)->toEqual(1);
    expect(Artisan::output())->toContain('The argument is not a valid regular expression:');

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = Artisan::call('localization:find', ['lemma' => '@message\\.lemma.*@', '--verbose' => true, '--short' => true, '--regex' => true]);

    expect($return)->toEqual(0);
    expect(Artisan::output())->toContain('has been found in');

    $messageBag = new MessageBag();
    $manager = new Localization($messageBag);

    $trans_methods = [
        'trans' => [
            '@trans\(\s*(\'.*\')\s*(,.*)*\)@U',
            '@trans\(\s*(".*")\s*(,.*)*\)@U',
        ],
        'Lang::Get' => [
            '@Lang::Get\(\s*(\'.*\')\s*(,.*)*\)@U',
            '@Lang::Get\(\s*(".*")\s*(,.*)*\)@U',
            '@Lang::get\(\s*(\'.*\')\s*(,.*)*\)@U',
            '@Lang::get\(\s*(".*")\s*(,.*)*\)@U',
        ],
        'trans_choice' => [
            '@trans_choice\(\s*(\'.*\')\s*,.*\)@U',
            '@trans_choice\(\s*(".*")\s*,.*\)@U',
        ],
        'Lang::choice' => [
            '@Lang::choice\(\s*(\'.*\')\s*,.*\)@U',
            '@Lang::choice\(\s*(".*")\s*,.*\)@U',
        ],
        '@lang' => [
            '@\@lang\(\s*(\'.*\')\s*(,.*)*\)@U',
            '@\@lang\(\s*(".*")\s*(,.*)*\)@U',
        ],
        '@choice' => [
            '@\@choice\(\s*(\'.*\')\s*,.*\)@U',
            '@\@choice\(\s*(".*")\s*,.*\)@U',
        ],
    ];

    $return = $manager->findLemma('not a valid regex', $manager->getPath(TestCase::MOCK_DIR_PATH_GLOBAL), $trans_methods, true, true);
    $messages = $messageBag->getMessages();
    expect($return)->toBeFalse();
    expect($messages[0][1])->toContain('The argument is not a valid regular expression:');
});
