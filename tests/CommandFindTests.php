<?php

use Potsky\LaravelLocalizationHelpers\Factory\Localization;
use Potsky\LaravelLocalizationHelpers\Factory\MessageBag;

class CommandFindTests extends TestCase
{
    /**
     * Setup the test environment.
     *
     * - Set custom configuration paths
     */
    public function setUp(): void
    {
        parent::setUp();

        Config::set(Localization::PREFIX_LARAVEL_CONFIG.'folders', self::MOCK_DIR_PATH_GLOBAL);
    }

    public function testSearchForRegularLemma()
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $return = Artisan::call('localization:find', ['lemma' => 'message.lemma', '--verbose' => true, '--short' => true]);

        $this->assertEquals(0, $return);
        $this->assertStringContainsString('Lemma message.lemma has been found in', Artisan::output());
    }

    public function testSearchForRegexLemma()
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $return = Artisan::call('localization:find', ['lemma' => 'message\\.lemma.*', '--verbose' => true, '--short' => true, '--regex' => true]);

        $this->assertEquals(1, $return);
        $this->assertStringContainsString('The argument is not a valid regular expression:', Artisan::output());

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $return = Artisan::call('localization:find', ['lemma' => '@message\\.lemma.*@', '--verbose' => true, '--short' => true, '--regex' => true]);

        $this->assertEquals(0, $return);
        $this->assertStringContainsString('has been found in', Artisan::output());

        $messageBag = new MessageBag();
        $manager = new Localization($messageBag);

        $trans_methods = [
            'trans'        => [
                '@trans\(\s*(\'.*\')\s*(,.*)*\)@U',
                '@trans\(\s*(".*")\s*(,.*)*\)@U',
            ],
            'Lang::Get'    => [
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
            '@lang'        => [
                '@\@lang\(\s*(\'.*\')\s*(,.*)*\)@U',
                '@\@lang\(\s*(".*")\s*(,.*)*\)@U',
            ],
            '@choice'      => [
                '@\@choice\(\s*(\'.*\')\s*,.*\)@U',
                '@\@choice\(\s*(".*")\s*,.*\)@U',
            ],
        ];

        $return = $manager->findLemma('not a valid regex', $manager->getPath(self::MOCK_DIR_PATH_GLOBAL), $trans_methods, true, true);
        $messages = $messageBag->getMessages();
        $this->assertFalse($return);
        $this->assertStringContainsString('The argument is not a valid regular expression:', $messages[0][1]);
    }
}
