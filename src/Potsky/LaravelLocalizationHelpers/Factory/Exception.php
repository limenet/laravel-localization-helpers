<?php

namespace Potsky\LaravelLocalizationHelpers\Factory;

class Exception extends \Exception
{
    protected $parameter;

    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct($message = '', $code = 0, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Set a parameter used to manage error messages.
     */
    public function setParameter($parameter): void
    {
        $this->parameter = $parameter;
    }

    /**
     * Get the parameter.
     *
     * @return mixed
     */
    public function getParameter()
    {
        return $this->parameter;
    }
}
