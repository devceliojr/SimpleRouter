<?php

namespace SimpleRouter\Application;

use InvalidArgumentException;
use stdClass;

abstract class Request
{
    protected ?object $request = null;

    public function __construct()
    {
        $this->request = (object) [
            "uri" => filter_input(INPUT_GET, 'route', FILTER_UNSAFE_RAW) ?? "/",
            "method" => filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_UNSAFE_RAW),
            "headers" => (object) getallheaders(),
            "body" => new stdClass
        ];
    }

    protected function uriExists(string $uri) {
        $uri = preg_replace("~{([^}]*)}~", "([^/]+)", $uri);
        return !!preg_match("@^" . $uri . "$@", $this->request->uri);
    }

    protected function getUriParameters(string $element, string $type): string
    {
        return match ($type) {
            "number" => preg_replace('/[^0-9]/', '', $element),
            "letter" => preg_replace('/[^a-zA-Zà-ú\s]/', '', $element),
            "alpha" => preg_replace('/^[a-zA-ZÀ-ÖØ-öø-ÿ0-9\s]+$/i', '', $element),
            "any" => preg_replace('/(?!)^/', '', $element),
            default => throw new InvalidArgumentException("Invalid parameter type.")
        };
    }

    protected function getUriData(string $uri, int $index): mixed
    {
        return array_values(
            array_diff(
                explode("/", $this->request->uri),
                explode("/", $uri)
            )
        )[$index];
    }

    protected function traitUri(string $uri, ?string $group): string
    {
        if (substr($group . $uri, -1) === "/" && strlen($group . $uri) > 1) {
            return substr($group . $uri, 0, -1);
        }
        
        return $group . $uri;
    }

    protected function setParameters(string $uri, array $parameters): void
    {
        $i = 0;
        $data = [];

        foreach ($parameters["parameters"] as $index => $type) {
            $data[$index] = $this->getUriParameters($this->getUriData($uri, $i++), $type);
        }

        foreach (array_merge($this->getRequestBody(), $data) as $key => $value) {
            if ($key === "route") continue;
            $data[$key] = is_array($value) || is_object($value)
                ? (object) $value
                : filter_var($value, FILTER_UNSAFE_RAW);
        }

        $this->request->body = (object) $data;
    }

    private function getRequestBody(): array
    {
        return array_merge(
            $_REQUEST,
            (array) json_decode(file_get_contents('php://input'), true)
        );
    }
}
