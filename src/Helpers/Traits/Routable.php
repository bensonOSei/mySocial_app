<?php

namespace Benson\InforSharing\Helpers\Traits;

use Benson\InforSharing\Handlers\ErrorHandler;
use Benson\InforSharing\Handlers\ExceptionHandler;
use Benson\InforSharing\Handlers\RouterErrorHandler;
use Throwable;

trait Routable
{
    private string $requestMethod;
    private string $requestUrl;


    /**
     * Handle an incoming request.
     *
     * @param string $method The HTTP method of the request.
     * @param string $url    The URL of the request.
     */
    public function run()
    {
        $method = $this->requestMethod;
        $url = $this->requestUrl;

        foreach ($this->routes as $route) {
            $params = []; // Initialize $params array

            if ($route['method'] === $method && $this->matchRoutePattern($route['pattern'], $url, $params)) {
                try {

                    if (count($route['middleware']) > 0) {
                        $this->applyMiddleware($route['middleware']);
                    }
                    // Call the route callback with the matched parameters
                    if (is_string($route['callback']) && strpos($route['callback'], '@') !== false) {
                        [$class, $method] = explode('@', $route['callback']);
                        $callbackInstance = $this->instantiateClass($class);
                        $this->callFunction([$callbackInstance, $method], $params);
                    } else {
                        call_user_func($route['callback'], ...array_values($params));
                    }

                    // var_dump($this->getRoutes());
                        // exit;
                    return;

                } catch (Throwable $e) {
                    ErrorHandler::handle($e);
                }
            }
        }

        // No matching route found, handle 404 Not Found
        RouterErrorHandler::handleRouteNotFound();
    }


    public function getRoutes()
    {
        return $this->routes;
    }


    private function callFunction($callback, $params)
    {
        try {
            call_user_func($callback, ...array_values($params));
            
        } catch (ExceptionHandler $e) {
            $e->handle();
        }
    }

    private function instantiateClass($class)
    {
        try {
            return new $class();

        } catch (ExceptionHandler $e) {
            $e->handle();
        }
    }


    /**
     * Match the route pattern against the URL.
     *
     * @param string $pattern The route pattern to match.
     * @param string $url     The URL to match against.
     * @param array  $params  The matched route parameters (output).
     * @return bool True if the pattern matches, false otherwise.
     */
    private function matchRoutePattern(string $pattern, string $url, array &$params): bool
    {
        $pattern = '#^' . str_replace('/', '\/', $pattern) . '$#';
        $pattern = preg_replace('/\{(.+?)\}/', '(?<$1>[^\/]+)', $pattern);
        return preg_match($pattern, $url, $matches) && $this->extractRouteParams($matches, $params);
    }

    private function extractRouteParams(array $matches, array &$params): bool
    {
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }
        return true;
    }
}
