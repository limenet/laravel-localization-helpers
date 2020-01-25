<?php

namespace Potsky\LaravelLocalizationHelpers\Object;

abstract class LangFileTypeAbstract
{
    protected $typeVendor;

    protected $typeJson;

    protected $lang;

    /**
     * LangFileTypeAbstract constructor.
     *
     * @param string $lang
     */
    public function __construct($lang)
    {
        $this->setLang($lang);
    }

    /**
     * @param bool $typeVendor
     *
     * @return LangFileAbstract
     */
    public function setTypeVendor($typeVendor)
    {
        $this->typeVendor = $typeVendor;

        return $this;
    }

    /**
     * @return bool
     */
    public function getTypeVendor()
    {
        return $this->typeVendor;
    }

    /**
     * @param bool $typeJson
     *
     * @return LangFileAbstract
     */
    public function setTypeJson($typeJson)
    {
        $this->typeJson = $typeJson;

        return $this;
    }

    /**
     * @return bool
     */
    public function getTypeJson()
    {
        return $this->typeJson;
    }

    /**
     * @param mixed $lang
     *
     * @return LangFileAbstract
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->lang;
    }
}
