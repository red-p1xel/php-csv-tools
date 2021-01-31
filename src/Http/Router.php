<?php

namespace Http;

use phpDocumentor\Reflection\Types\This;

/**
 * @method get(string $string, \Closure $param)
 * @method post(string $string, \Closure $param)
 */
class Router
{
    private $request;
    private $supportedHttpMethods = [
        "GET",
        "POST"
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function __call($name, $args)
    {
        list($route, $method) = $args;

        if (!in_array(strtoupper($name), $this->supportedHttpMethods)) {
            $this->invalidMethodHandler();
        }

        $this->{strtolower($name)}[$this->removeTrailingSlashes($route)] = $method;
    }

    /**
     * @param string $route
     * @return string
     */
    private function removeTrailingSlashes(string $route): string
    {
        $result = rtrim($route, '/');
        if ($result == '') {
            return '/';
        }
        return $result;
    }

    private function invalidMethodHandler()
    {
        header($this->request->serverProtocol . " 405 Method Not Allowed.");
    }

    private function defaultRequestHandler()
    {
        header($this->request->serverProtocol . " 404 Not Found.");
    }

    /**
     * @param array $data
     */
    private function withData(array $data): Request
    {
        $this->request->data = $data;

        return $this->request;
    }

    private function resolve()
    {
        $methodDictionary = $this->{strtolower($this->request->requestMethod)};
        $formattedRoute = $this->removeTrailingSlashes($this->request->requestUri);
        $method = $methodDictionary[$formattedRoute];
        if (!$method) {
            $this->defaultRequestHandler();
            return;
        }
        echo call_user_func_array($method, [$this->request]);
    }

    /**
     * @param string $url
     * @param int $statusCode
     */
    public static function redirect(string $url, int $statusCode = 302): void
    {
        header('Location: ' . $url, true, $statusCode);
    }

    public function __destruct()
    {
        $this->resolve();
    }
}
