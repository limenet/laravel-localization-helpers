<?php

namespace Potsky\LaravelLocalizationHelpers\Factory;

class TranslatorSample implements TranslatorInterface
{
    /**
     * @param array $config
     *
     * @throws \Potsky\LaravelLocalizationHelpers\Factory\Exception
     */
    public function __construct()
    {
    }

    /**
     * @param string $word     Sentence or word to translate
     * @param string $toLang   Target language
     * @param null   $fromLang Source language (if set to null, translator will try to guess)
     *
     * @return null|string The translated sentence or null if an error occurs
     */
    public function translate($word, $toLang, $fromLang = null)
    {
        return sprintf('%s(%s): %s', $toLang, $fromLang,$word);
    }
}
