<?php

namespace Potsky\LaravelLocalizationHelpers\Factory;

class Translator implements TranslatorInterface
{
    /** @var TranslatorInterface */
    protected $translator;

    /**
     * @param string $translator The translation service name
     * @param array  $config     The configuration array for the translation service
     *
     * @throws Exception
     */
    public function __construct($translator, $config = [])
    {
        $class = Translator::class.$translator;
        $translator = new $class($config);

        if (!$translator instanceof TranslatorInterface) {
            // @codeCoverageIgnoreStart
            // Cannot test a Fatal Error in PHPUnit by invoking non existing class...
            throw new Exception('Provided translator does not implement TranslatorInterface');
            // @codeCoverageIgnoreEnd
        }

        $this->translator = $translator;
    }

    public function translate($word, $toLang, $fromLang = null)
    {
        return $this->translator->translate($word, $toLang, $fromLang);
    }

    /**
     * Return the used translator.
     *
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }
}
