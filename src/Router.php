<?php

namespace SimpleRouter\Application;

use SimpleRouter\Application\Dispatch;

/**
 * Class Router
 * @package SimpleRouter\Application
 * @author Célio Junior <profissional.celiovmjunior@outlook.com>
 */
class Router extends Dispatch
{
    /**
     * @access public
     * @param string|null $namespace
     * @return Router
     */
    public function namespace (?string $namespace = null) : Router
    {
        if (substr($namespace, 0, 1) === "/" || substr($namespace, 0, 1) === "\\") {
            $namespace = substr($namespace, 1);
        }

        if (substr($namespace, -1) === "/" || substr($namespace, -1) === "\\") {
            $namespace = substr($namespace, 0, -1);
        }

        $this->namespace = ($namespace ? "\\" . ucwords(str_replace("/", "\\", $namespace)) . "\\" : null);
        return $this;
    }

    /**
     * @access public
     * @param string|null $group
     * @return Router
     */
    public function group(? string $group) : Router
    {
        $this->group = ($group ? str_replace("/", "", $group) : null);
        return $this;
    }

    /**
     * @access public
     * @param string $method
     * @param string $path
     * @param $callback
     */
    public function create(string $method, string $path, $callback): void
    {
        $this->route[$method][(!$this->group ? $path : "/{$this->group}{$path}")] = $callback;
    }

    /**
     * @access public
     * @param string $path
     * @param $handler
     */
    public function post(string $path, $handler): void
    {
        $this->create("POST", $path, $handler);
    }

    /**
     * @access public
     * @param string $path
     * @param $handler
     */
    public function get(string $path, $handler): void
    {
        $this->create("GET", $path, $handler);
    }

    /**
     * @access public
     * @param string $path
     * @param $handler
     */
    public function put(string $path, $handler): void
    {
        $this->create("PUT", $path, $handler);
    }

    /**
     * @access public
     * @param string $path
     * @param $handler
     */
    public function patch(string $path, $handler): void
    {
        $this->create("PATCH", $path, $handler);
    }

    /**
     * @access public
     * @param string $path
     * @param $handler
     */
    public function delete(string $path, $handler): void
    {
        $this->create("DELETE", $path, $handler);
    }

    /**
     * @access public
     * @param string $route
     */
    public function redirect(string $route): void
    {
        header("Location: {$route}");
        exit;
    }
}