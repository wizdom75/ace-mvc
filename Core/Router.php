<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 26/01/2018
 * Time: 22:43
 */

namespace  Core;
/*
 *  Core file for routing.
 *
 *  PHP version 7.2
 */

class Router
{
    /*
     *  Associtaive array of routes
     *  @var array
     */
    protected $routes = [];

    /*
     *  Parameters from the matched route
     *  @var array
     */
    protected $params = [];

    /*
     *  Add a route to the routing table
     *  @param string $route The route URL
     *  @param array $params Parameters (Controllers, Methods etc.)
     *  @return void
     */
    public function add($route, $params=[])
    {
        // Convert the route to a regular expression: escape forward slashes
        $route = preg_replace('/\//', '\\/', $route);

        // Convert variables e.g. {controller}
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        // Convert variables with custom regular expressions e.g. {id:\d+}
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        // Add start and end delimeters, and case insensitive flags
        $route = '/^'.$route.'$/i';


        $this->routes[$route] = $params;
    }
    /*
     *  Get all the routes from the routing table
     *
     *  @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }
    /*
     *  Match the route to the routes in the routing table, setting the $params
     *  property if the route is found
     *
     *  @param string $url The route URL
     *
     *  @return boolean true if a match found, false otherwise
     */
    public function match($url)
    {
        foreach($this->routes as $route => $params){
            if(preg_match($route, $url, $matches)) {
                foreach($matches as $key => $match){
                    if(is_string($key)){
                        $params[$key] = $match;
                    }
                }

                $this->params = $params;
                return TRUE;
            }
        }

        return FALSE;
    }

    /*
     *  Get the currently matched parameters
     *
     *  @return array
     */
    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /*
     *  Dispatch the routes
     *
     *  @param sting $url The route URL
     *
     *  @return void
     */
    public function dispatch($url)
    {
        $url = $this->removeQueryStringVariables($url);

        if($this->match($url)){
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            $controller = $this->getNamespace().$controller;

            if(class_exists($controller)){
                $obj = new $controller($this->params);

                $action = $this->params['action'];
                $action = $this->convertToCamelCase($action);

                if(is_callable([$obj, $action])){
                    $obj->$action();
                }else{
                    throw new \Exception("Method $action (in controller $controller) not found");
                }
            }else{
                throw new \Exception("Controller class $controller not found");
            }
        }else{
            throw new \Exception("No route matched.", 404);
        }

    }
    /*
     *  Convert the sting with hyphens to Studly Caps,
     *  e.g. post-authors => PostAuthors
     *
     *  @param string $sting The string to convert
     *
     *  @return string
     */
    protected function convertToStudlyCaps($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }
    /*
     *  Convert the string with hyphens with camelCase,
     *  e.g. add-new => addNew
     *
     * @param string $string The string to convert
     *
     * @return string
     */
    protected function convertToCamelCase($string)
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }

    /*
     *  A URL of the format localhost/?page (one variable name, no value) won't
     *  work however. (NB. The .htaccess file converts the first ? to a & when it's
     *  passed through to the $_SERVER variable).
     *
     *  @param string $url full URL
     *
     *  @param string The URL with the query string variables removed
     */
    protected function removeQueryStringVariables($url)
    {
        if($url != ''){
            $parts = explode('&', $url, 2);

            if(strpos($parts[0], '=') === false){
                $url = $parts[0];
            }else{
                $url = '';
            }
        }
        return $url;
    }

    /*
     *  Get the namespace for the controller class. The namespace defined in the
     *  route parameters os added if present.
     *
     *  @return the request URL
     */
    protected function getNamespace()
    {
        $namespace = 'App\Controllers\\';

        if(array_key_exists('namespace', $this->params)){
            $namespace .= $this->params['namespace'].'\\';
        }

        return $namespace;
    }

}