<?php

namespace SimpleRouter\Application;

/**
 * Class Dispatch
 * @package SimpleRouter\Application
 * @author Célio Junior <profissional.celiovmjunior@outlook.com>
 */
abstract class Dispatch extends Request
{
    /**
     * @access protected
     * @var string|null
     */
    protected ?string $namespace = null;

    /**
     * @access protected
     * @var string|null
     */
    protected ?string $group = null;

    /**
     * @access private
     * @var string
     */
    private string $controller;

    /**
     * @access private
     * @var string
     */
    private string $method;

    /**
     * @access protected
     * @var array
     */
    protected array $route = array();

    /**
     * @access private
     * @var int|null
     */
    private ?int $error = null;

    /**
     * @access public
     * @const int Bad Request
     */
    public const BAD_REQUEST = 400;

    /**
     * @access public
     * @const int Not Found
     */
    public const NOT_FOUND = 404;

    /**
     * @access public
     * @const int Method Not Allowed
     */
    public const METHOD_NOT_ALLOWED = 405;

    /**
     * @access public
     * @const int Not Implemented
     */
    public const NOT_IMPLEMENTED = 501;

    /**
     * @access public
     * Dispatch constructor.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @access public
     * @return bool
     */
    public function dispatch(): bool
    {
        if (empty($this->route) || empty($this->route[$this->getHttpMethod()])) {
            $this->error = self::NOT_IMPLEMENTED;
            return false;
        }
        foreach ($this->route[$this->getHttpMethod()] as $route => $callback) {
            preg_match_all("~\{\s* ([a-zA-Z_][a-zA-Z0-9_-]*) \}~x", $route, $keys, PREG_SET_ORDER);

            if (preg_match("@^" . preg_replace('~{([^}]*)}~', "([^/]+)", $route) . "$@", $this->getUrl())) {
                $this->traitData($route, $this->getUrl(), $keys);

                if (is_callable($callback)) {
                    call_user_func($callback, $this->getParameters());
                    return true;
                }

                $this->controller = $this->namespace . strstr($callback, "@", true);
                $this->method = str_replace("@", "", strstr($callback, "@", false));
                $this->execute();
                return true;
            }
        }

        $this->error = self::NOT_FOUND;
        return false;
    }

    /**
     * @access private
     * @return bool
     */
    private function execute(): bool
    {
        if (class_exists($this->controller)) {
            if (method_exists($this->controller, $this->method)) {
                call_user_func_array(array($this->controller, $this->method), array($this->getParameters()));
                return true;
            } else {
                $this->error = self::METHOD_NOT_ALLOWED;
                return false;
            }
        }

        $this->error = self::BAD_REQUEST;
        return false;
    }

    /**
     * @access public
     * @return int|null
     */
    public function error(): ?int
    {
        return $this->error ?? null;
    }

    /**
     * @access private
     * @param string $route
     * @param string $url
     * @param array $keys
     * @return void
     */
    private function traitData(string $route, string $url, array $keys): void
    {
        $i = 0;
        foreach ($keys as $key) {
            $data[$key[1]] = array_values(array_diff(explode("/", $url), explode("/", $route)))[$i++];
            $this->setParameters($data);
        }
    }
}
