<?php

use Potsky\LaravelLocalizationHelpers\Factory\Translator;

class TranslatorTests extends TestCase
{
    public function testInjection()
    {
        $this->markTestSkipped();
        $translator = new Translator('Microsoft', [
            'client_id'     => 'xxx',
            'client_secret' => 'yyy',
        ]);
        $this->assertTrue($translator instanceof \Potsky\LaravelLocalizationHelpers\Factory\Translator);
        $this->assertTrue($translator->getTranslator() instanceof \Potsky\LaravelLocalizationHelpers\Factory\TranslatorMicrosoft);
    }

    /**
     * Microsoft credentials are set in environment on my computer
     * export LLH_MICROSOFT_TRANSLATOR_CLIENT_ID="..."
     * export LLH_MICROSOFT_TRANSLATOR_CLIENT_SECRET="...".
     *
     * Go to your Azure account to retrieve client id and secret :
     * https://datamarket.azure.com/developer/applications
     */
    public function testRealCase()
    {
        $this->markTestSkipped();
        $translator = new Translator('Microsoft', []);
        $this->assertEquals('Stuhl', $translator->translate('chair', 'de'));
        $this->assertEquals('Fleisch', $translator->translate('chair', 'de', 'fr'));
    }

    public function testNoTranslation()
    {
        $this->markTestSkipped();
        $translator = new Translator('Microsoft', []);
        $this->assertNull($translator->translate('', ''));
    }

    public function testUnknownLang()
    {
        $this->markTestSkipped();
        $translator = new Translator('Microsoft', []);
        $this->assertNull($translator->translate('dog', 'zz'));
    }

    public function testRealCaseWithDefaultLanguage()
    {
        $this->markTestSkipped();
        $translator = new Translator('Microsoft', ['default_language' => 'fr']);
        $this->assertEquals('Fleisch', $translator->translate('chair', 'de'));
    }

    public function testNoCredentialsClientId()
    {
        $this->markTestSkipped();
        $this->expectException('\\Potsky\\LaravelLocalizationHelpers\\Factory\\Exception', 'Please provide a client_id for Microsoft Bing Translator service');
        new Translator('Microsoft', [
            'env_name_client_id'     => 'this_env_does_not_exist',
            'env_name_client_secret' => 'this_env_does_not_exist',
        ]);
    }

    public function testNoCredentialsClientSecret()
    {
        $this->markTestSkipped();
        $this->expectException('\\Potsky\\LaravelLocalizationHelpers\\Factory\\Exception', 'Please provide a client_secret for Microsoft Bing Translator service');
        new Translator('Microsoft', [
            'env_name_client_secret' => 'this_env_does_not_exist',
        ]);
    }
}
