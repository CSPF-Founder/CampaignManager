<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace Core;

use Core\Security\CSRF;
use Core\Security\Validator;

/**
 * Base Controller
 */

abstract class Controller{
    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];

    public function __construct($route_params) {
        $this->route_params = $route_params;
    }

    /**
     * Action filter function to be called before the target function called
     *
     * @return boolean
     */
    protected function before(){

    }

    /**
     * Action after effect function to be called after the target function is executed
     */
    protected function after(){

    }

    /**
     * Filter Action
     * @param $name
     * @param $arguments
     * @throws \Exception
     */
    public function filterAction($name, $arguments){
        if(method_exists($this, $name.'Action')){
            $methodName = $name.'Action';
            if($this->before() === true){ // run another code before calling the actual method
                if($_SERVER['REQUEST_METHOD'] == 'POST'){
                    /** Check CSRF Token automatically if it is post request */
                    if(!static::checkCsrfToken()){
                        View::displayJsonError("Invalid CSRF token - Reload the page");
                    }
                }
                call_user_func_array([$this,$methodName],$arguments);
                $this->after(); //run another code after the actual method is called
            }
            else{
                View::throwAppError("You don't have authorization", 403);
            }
        }
        else if(method_exists($this, $name.'NonFilteredAction')){
            $methodName = $name.'NonFilteredAction';
            call_user_func_array([$this,$methodName],$arguments);
        }
        else{
            View::throwAppError("Page not found", 404);
        }
    }

    /**
     * Redirect to the specified location
     * Only for internal url
     * @param $internal_url
     */
    public static function redirect($internal_url){
        if(!Validator::isRelativeUrl($internal_url)){
            // If it is not relative url, redirect to /
            header("Location:/", true,303);
            exit;
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
            // AJAX Requests
            static::redirectJSON($internal_url);
        }
        else {
            // NON-AJAX Requests
            header("Location:".$internal_url, true,303);
        }
        exit;
    }


    /**
     * Json Redirect response
     * @param $url
     */
    public static function redirectJSON($url){
        View::displayJson(array("redirect" => $url));
    }

    /**
     * Checks the CSRF Token.  If token mismatches, displays error
     */
    public static function checkCsrfToken(){
        if(isset($_POST[CSRF::TOKEN_NAME]) && CSRF::validate($_POST[CSRF::TOKEN_NAME])){
            return true;
        }
        return false;
    }

}
