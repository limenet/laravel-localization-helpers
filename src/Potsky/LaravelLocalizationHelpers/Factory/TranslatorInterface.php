<?php

declare(strict_types=1);

namespace Potsky\LaravelLocalizationHelpers\Factory;

interface TranslatorInterface
{
    public function translate($word, $toLang, $fromLang = null);
}
