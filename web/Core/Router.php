<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace Core;

/*
 * Router
 */

class Router {
    /**
     * Associative array of routes (the routing table
     * @var array
     */
    protected $routes = [];

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $params = [];

    /**
     * Add the route to routing table
     * Before adding -> Convert the route into a regular expression
     * Example:
     * {controller}/{action} will be converted into a regular exp: /^(?P<controller>[a-z-]+)\/(?P<action>[a-z-]+)$/
     * this will match URL with pattern /controller/action/
     * @param string $route The Route URL
     * @param array $params Parameters (controller,action, etc.)
     *
     * @return void
     */
    public function add($route, $params = []) {

        $route = preg_replace('/\//', '\\/', $route); //escape forward slashes;

        /*convert variables e.g. {controller}
         * Example
         *  {controller} will become => (?P<controller>[a-z-]+)
         *  {action} will become => (?P<action>[a-z-]+)
         */
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        /*
         * convert variables with custom regular expression
         * Example:
         *      {id:\d+} will become (?P<id>\d+)    (this matches digits and put into id )
         */
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        //add start & end delimiter & case insensitive
        $route = '/^' . $route . '$/i';       //route variable now contains a regular exp to match the url patterns

        $this->routes[$route] = $params;

    }

    /**
     * Get all the routes from the routing table
     * @return array route table
     */
    public function getRoutes() {
        return $this->routes;
    }

    /**
     * Match the route to the routes in the routing table
     * sets $params property if a route is found
     *
     * @param string $url The route URL
     *
     * @return boolean true if a match found
     */
    public function match($url) {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {     //if url matches with any regular expression pattern in $routes
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        /**
                         * Example:
                         * If url is "/posts/new" & it matches with {controller}/{action} pattern
                         * $params['controller'] = 'posts';
                         * $params['action'] = 'new';
                         */
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }

        return false;
    }

    /**
     * Get the currently matched parameters
     *
     * @return array list of parameters
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * Dispatch the route, creating the controller object & running the action
     * method
     * @param string $url the route URL
     * @throws \Exception
     */
    public function dispatch($url) {
        $url = $this->removeQueryStringVariables($url);
        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            //Remove "Controller" keyword from url input
            $controller = str_ireplace("Controller", "", $controller);
            $controller = $this->getNamespace() . $controller;


            if (!class_exists($controller)) {
                $controller = $controller . "Controller";
            }

            if (class_exists($controller)) {
                $controllerObject = new $controller($this->params);

                $action = $this->params['action'];
                if (preg_match('/action$/i', $action) == 0) {
                    $action = $this->convertToCamelCase($action);
                    //Remove "Action" or "NonFilteredAction" keyword from url input of 'action' name
                    $action = str_ireplace("Action", "", $action);
                    $action = str_ireplace("NonFilteredAction", "", $action);
                    $arguments = [];
                    /** @var Controller $controllerObject */
                    $controllerObject->filterAction($action, $arguments);
                }
                else{
                    throw new \Exception("404 not found", 404);
                }

            } else {
                throw new \Exception("404 not found", 404);
            }

        } else {
            throw new \Exception("404 not found", 404);
        }
    }

    /**
     * Get namespace of a controller
     * @return string
     */
    protected function getNamespace() {
        $namespace = "App\Controllers\\";  //Default
        if (isset($this->params['namespace'])) {
            $namespace .= $this->params["namespace"] . "\\";
        }

        return $namespace;
    }

    /**
     * Converts the string with the hyphen to StudlyCaps
     * Example: convert posts-authors into PostsAuthors
     * First convert '-' into ' ' (space)  => post authors
     * ucwords will convert "post authors" into "Post Authors"
     * remove the empty spaces; so "Post Authors" will become "PostAuthors"
     * @param string $string
     * @return string StudlyCaps string
     */
    private function convertToStudlyCaps($string) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    /**
     * Converts the string with the hyphen to camelCase
     * e.g: add-new to addNew
     * @return string StudlyCaps string
     */
    private function convertToCamelCase($string) {
        return lcfirst($this->convertToStudlyCaps($string));
    }


    //To remove query params
    protected function removeQueryStringVariables($url) {
        if ($url != '') {
            $parts = explode('&', $url, 2);
            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }

        return $url;
    }
}

