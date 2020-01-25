<?php

use Potsky\LaravelLocalizationHelpers\Factory\Translator;

class TranslatorTests extends TestCase
{
    public function testInjection()
    {
        $translator = new Translator('Sample');
        $this->assertTrue($translator instanceof \Potsky\LaravelLocalizationHelpers\Factory\Translator);
        $this->assertTrue($translator->getTranslator() instanceof \Potsky\LaravelLocalizationHelpers\Factory\TranslatorSample);
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
        $translator = new Translator('Sample');
        $this->assertEquals('de(): chair', $translator->translate('chair', 'de'));
        $this->assertEquals('de(fr): chair', $translator->translate('chair', 'de', 'fr'));
    }

    public function testNoTranslation()
    {
        $translator = new Translator('Sample');
        $this->assertEquals('(): ', $translator->translate('', ''));
    }

    public function testUnknownLang()
    {
        $translator = new Translator('Sample');
        $this->assertEquals('zz(): dog', $translator->translate('dog', 'zz'));
    }

    public function testRealCaseWithDefaultLanguage()
    {
        $translator = new Translator('Sample');
        $this->assertEquals('de(): chair', $translator->translate('chair', 'de'));
    }
}
