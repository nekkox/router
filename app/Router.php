<?php

namespace App;
use App\Exceptions\RouteNotFoundException;

class Router
{
    public function __construct(private Container $container)
    {
    }
    private array $routes= [];

    public function register(string $requestMethod, string $route, callable|array $action): self
    {
        $this->routes[$requestMethod][$route] = $action;
        return $this;
    }

    public function get(string $route, callable|array $action): self
    {
        return $this->register('get', $route, $action);
    }

    public function post(string $route, callable|array $action): self
    {
        return $this->register('post', $route, $action);
    }

    public function routes(): array
    {
        return $this->routes;
    }

    public function resolve(string $requestUri, string $requestMethod)
    {
        $route = explode('?', $requestUri)[0];
        $action = $this->routes[$requestMethod][$route] ?? null; //check if the route exists in the routes array

        if (!$action) {
            throw new RouteNotFoundException();
        }
        //when the action is a function;
        if (is_callable($action)) {

            return call_user_func($action);
        }

        //when the action is array
        if (is_array($action)) {

            [$class, $method] = $action; //Rozbicie tablicy $action na 2 elementy - class & method

            if (class_exists($class)) {
              //  $class = new $class();

                $class = $this->container->get($class);

                if (method_exists($class, $method)) {
                    return call_user_func_array([$class, $method], []);
                }
            }
        }
        //if the action is not a function or array throw an exception
        throw new RouteNotFoundException();
    }
}
