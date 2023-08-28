<?php

declare(strict_types=1);

namespace Benson\InforSharing\Http;

use Benson\InforSharing\Helpers\Traits\Routable;

/**
 * The Router class. This class handles the routing of the application.
 * 
 * @package RouteMe\Http
 * @since 1.0.0
 */
class Router
{
    use Routable;
    private $routes = [];
    private $middleware = [];

    /**
     * Create a new Router instance.
     *
     * @return void
     * @since 1.0.0
     */
    public function __construct()
    {
        header('Content-type: application/json');
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestUrl = $_SERVER['REQUEST_URI'];
    }

    /**
     * Add a route to the router.
     *
     * @param string   $method   The HTTP method (GET, POST, etc.).
     * @param string   $pattern  The URL pattern to match.
     * @param callable $callback The callback function to execute for the route.
     * @return self Returns the Router instance.
     * @since 1.0.3 changed method name from addRoute to register
     */
    public function register(string $method, string $pattern, $callback, array $middleware = []): self
    {

        $route = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback,
            'middleware' => $middleware
        ];

        array_push($this->routes, $route);

        return $this;
    }



    /**
     * Add a route that responds to the GET HTTP method
     * 
     * @param string $pattern The URL pattern to match.
     * @param callable|string $callback The callback function to execute for the route.
     * @return Router Register the route and run the router.
     * @since 1.0.3 Made method static
     */
    public function get(string $pattern, callable|string $callback, array $middleware = [])
    {

        return $this->register('GET', $pattern, $callback, $middleware);
    }




    /**
     * Add a route that responds to the POST HTTP method.
     * 
     * @param string   $pattern  The URL pattern to match.
     * @param callable $callback The callback function to execute for the route.
     */
    public function post(string $pattern, $callback, array $middleware = [])
    {

        return $this->register('POST', $pattern, $callback, $middleware);
    }




    /**
     * Add a route that responds to the PUT HTTP method.
     * 
     * @param string   $pattern  The URL pattern to match.
     * @param callable $callback The callback function to execute for the route.
     */
    public function put(string $pattern, $callback, array $middleware = [])
    {

        return $this->register('PUT', $pattern, $callback, $middleware);
    }



    /**
     * Add a route that responds to the DELETE HTTP method.
     * 
     * @param string   $pattern  The URL pattern to match.
     * @param callable $callback The callback function to execute for the route.
     */
    public function delete(string $pattern, $callback, array $middleware = [])
    {

        return $this->register('DELETE', $pattern, $callback, $middleware);
    }



    /**
     * Add a route that responds to the PATCH HTTP method.
     * 
     * @param string   $pattern  The URL pattern to match.
     * @param callable $callback The callback function to execute for the route.
     * @since 1.0.3 Made method
     */
    public function patch(string $pattern, $callback, array $middleware = [])
    {

        return $this->register('PATCH', $pattern, $callback, $middleware);
    }




    /**
     * Add a route that responds to the OPTIONS HTTP method.
     * 
     * @param string   $pattern  The URL pattern to match.
     * @param callable $callback The callback function to execute for the route.
     */
    public function options(string $pattern, $callback, array $middleware = [])
    {

        return $this->register('OPTIONS', $pattern, $callback, $middleware);
    }



    /**
     * Add a route that responds to any HTTP method.
     * 
     * @param string   $pattern  The URL pattern to match.
     * @param callable $callback The callback function to execute for the route.
     */
    public function any(string $pattern, $callback, array $middleware = [])
    {

        // get the request method
        $method = $_SERVER['REQUEST_METHOD'];

        $this->register($method, $pattern, $callback, $middleware);
    }




    /**
     * Handle a 404 Not Found scenario.
     *
     */
    public function handleNotFound()
    {
    }







    /**
     * Add middleware to the router.
     *
     * @param callable $middleware The middleware callback function.
     * @return self
     */
    public function middleware($middleware): self
    {
        $this->middleware[] = $middleware;
        $this->applyMiddleware($this->middleware);
        return $this;
    }





    /**
     * Apply the registered middleware functions.
     *
     * @return void
     */
    private function applyMiddleware(array $middleware): void
    {
        $middlewareResponse = null;

        foreach ($middleware as $middleware) {
            if (is_string($middleware) && strpos($middleware, '@') !== false) {
                [$class, $method] = explode('@', $middleware);
                $middlewareInstance = new $class();
                $middlewareResponse = call_user_func([$middlewareInstance, $method]);
            } else {
                $middlewareResponse = call_user_func($middleware);
            }
        }

        if (!$middlewareResponse) {
            var_dump($middlewareResponse);
            die();
        }
    }


    public function __destruct()
    {
        if(count($this->routes) > 0)
            $this->run();
    }

}
