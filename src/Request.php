<?php

namespace SimpleRouter\Application;

/**
 * Class Request
 * @package SimpleRouter\Application
 * @author Célio Junior <profissional.celiovmjunior@outlook.com>
 */
abstract class Request
{
    /** @var string */
    private string $url;
    /** @var string */
    private string $httpMethod;
    /** @var object */
    private object $parameters;

    /** Request constructor. */
    protected function __construct()
    {
        $this->setUrl();
        $this->setHttpMethod();
        $this->setParameters();
    }

    /** @return string */
    protected function getUrl(): string
    {
        if (substr($this->url, 0) === "/") {
            return $this->url;
        }

        if (substr($this->url, -1) === "/") {
            return substr($this->url, 0, -1);
        }

        return $this->url;
    }

    /** Define the URL */
    private function setUrl(): void
    {
        $this->url = filter_input(INPUT_GET, 'route', FILTER_SANITIZE_STRING) ?: '/';
    }

    /** @return string */
    protected function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    /** Define the HTTP Verb method */
    private function setHttpMethod(): void
    {
        $this->httpMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
    }

    /** @return object */
    protected function getParameters(): object
    {
        return $this->parameters;
    }

    /** @param array|null $data */
    protected function setParameters(? array $data = null): void
    {
        $obj = new \stdClass();

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $obj->$key = filter_var($value, FILTER_DEFAULT);
            }
        }

        foreach ($_REQUEST as $key => $value) {
            if ($key !== 'route') {
                $obj->$key = filter_var($value, FILTER_DEFAULT);
            }
        }

        $this->parameters = $obj;
    }

    /**
     * @param string $separator
     * @param string $base
     * @return array
     */
    protected function traitArray(string $separator, string $base) : array
    {
        return array_values(array_filter(explode($separator, $base)));
    }

}