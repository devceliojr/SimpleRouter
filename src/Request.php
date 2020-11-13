<?php

namespace SimpleRouter\Application;

/**
 * Class Request
 * @package SimpleRouter\Application
 * @author Célio Junior <profissional.celiovmjunior@outlook.com>
 */
abstract class Request
{
    /**
     * @access private
     * @var string
     */
    private string $url;

    /**
     * @access private
     * @var string
     */
    private string $httpMethod;

    /**
     * @access private
     * @var object
     */
    private object $parameters;

    /**
     * Request constructor.
     * @access protected
     * @return void
     */
    protected function __construct()
    {
        $this->setUrl();
        $this->setHttpMethod();
        $this->setParameters();
    }

    /**
     * @access protected
     * @return string
     */
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

    /**
     * @access private
     * @return void
     */
    private function setUrl(): void
    {
        $this->url = filter_input(INPUT_GET, 'route', FILTER_SANITIZE_STRING) ?: '/';
    }

    /**
     * @access protected
     * @return string
     */
    protected function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    /**
     * @access private
     * @return void
     */
    private function setHttpMethod(): void
    {
        $this->httpMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
    }

    /**
     * @access protected
     * @return object
     */
    protected function getParameters(): object
    {
        return $this->parameters;
    }

    /**
     * @access protected
     * @param array|null $data
     * @return void
     */
    protected function setParameters(?array $data = null): void
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
     * @access protected
     * @param string $separator
     * @param string $base
     * @return array
     */
    protected function traitArray(string $separator, string $base) : array
    {
        return array_values(array_filter(explode($separator, $base)));
    }
}
