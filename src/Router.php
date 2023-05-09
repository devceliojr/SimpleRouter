<?php

declare(strict_types=1);

namespace SimpleRouter\Application;

use Closure;
use Error;
use Exception;
use InvalidArgumentException;
use stdClass;

class Router extends Request
{
    private ?string $path = null;
    private ?string $group = null;
    private ?string $oldGroup = null;
    private array $routes = [];
    private ?stdClass $error = null;

    public function __call(string $name, array $arguments): void
    {
        if(! in_array($name, ["get", "post", "put", "patch", "delete"])) {
            throw new Error("Call to undefined method {$name}");
        }

        $this->addRoute(
            strtoupper($name),
            $arguments[0],
            $arguments[1]
        );
    }

    public function path(string $path): void
    {
        if (empty($path)) {
            $path = "/";
        }

        if (substr($path, 0, 1) !== "/") {
            $path = "/" . $path;
        }

        if (substr($path, -1) === "/" && strlen($path) > 1) {
            $path = substr($path, 0, -1);
        }

        $this->path = $path;
    }

    public function group(?string $prefix, Closure $handler): void
    {
        if (empty($prefix)) {
            $prefix = "/";
        }

        if (substr($prefix, 0, 1) !== "/") {
            $prefix = "/" . $prefix;
        }

        if (substr($prefix, -1) === "/" && strlen($prefix) > 1) {
            $prefix = substr($prefix, 0, -1);
        }

        $this->group = $prefix;
        $this->oldGroup = $this->group;

        if ($handler instanceof Closure) {
            $handler->__invoke();
        }
    }

    public function subgroup(?string $prefix, Closure $handler): void
    {
        if(empty($this->group)) {
            $this->handlerError("The group has not been defined.", 0);
            return;
        }

        if ($this->group !== $this->oldGroup) {
            $this->group = $this->oldGroup;
        }

        $this->group = str_replace("//", "/", ($this->group .= $prefix));
        if ($handler instanceof Closure) {
            $handler->__invoke();
        }
    }

    public function dispatch(): bool
    {
        if (! array_key_exists($this->request->method, $this->routes)) {
            $this->handlerError("Method Not Allowed", 405);
            return false;
        }

        foreach ($this->routes[$this->request->method] as $uri => $router) {
            if (! $this->uriExists($uri)) {
                continue;
            }

            $this->setParameters($uri, $router);
            return $this->run($router);
        }

        $this->handlerError("Not Found", 404);
        return false;
    }

    public function error(): ?stdClass
    {
        return $this->error;
    }

    private function addRoute(string $method, string $uri, array|Closure $handler): void
    {
        preg_match_all('/\{([^}]*)\}/', $uri, $matches);

        if (! empty($matches)) {
            foreach ($matches[1] as $values) {
                $parameters[strstr($values, ":", true)] = substr(strstr($values, ':'), 1);
            }
        }

        if (is_array($handler)) {
            $handler = [
                "controller" => $handler[0],
                "method" => $handler[1]
            ];
        }

        $this->routes[$method][$this->traitUri($uri, $this->path, $this->group)] = [
            "action" => $handler,
            "parameters" => $parameters ?? []
        ];
    }

    private function run(array $router): bool
    {
        if (is_callable($router["action"])) {
            call_user_func($router["action"], $this->request);
            return true;
        }

        if (! $router["action"]["controller"] || ! $router["action"]["method"]) {
            $this->handlerError("Bad request", 400);
            return false;
        }
        
        $controller = $router["action"]["controller"];
        $method = $router["action"]["method"];

        if (! class_exists($controller)) {
            $this->handlerError("Not Found", 404);
            return false;
        }

        if (! method_exists($controller, $method)) {
            $this->handlerError("Not Implemented", 501);
            return false;
        }

        (new $controller())->{$method}($this->request);
        return true;
    }

    private function handlerError(string $message, int $code): void
    {
        $this->error = new stdClass();
        $this->error->message = $message;
        $this->error->code = $code;
    }
}
