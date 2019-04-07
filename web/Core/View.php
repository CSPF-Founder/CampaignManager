<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace Core;


use Core\Security\Validator;

class View{
    /**
     * Render a view file
     * @param string $view the view file
     * @param array $data
     * @throws \Exception
     * @internal param array $args
     */
    public static function render($view, $data=[]) {
        extract($data,EXTR_SKIP);

        $file =  "../App/Views/$view"; //relative to Core Directory
        if(file_exists(($file))){
            require $file;
        }
        else{
            throw new AppError("404 not found", 404);
        }

    }

    /**
     * Display Json data
     * @param $message
     */
    public static function displayJson($message){
        header('Content-Type: application/json');
        echo json_encode($message, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_QUOT);
        exit();
    }

    /**
     * Display success json messages
     * @param $message
     * @param bool $exit_script
     */
    public static function displayJsonSuccess($message, $exit_script=true){
        if(!$message){
            $message = "Error Occurred";
        }

        $jsonOutput ['success'] = $message;
        static::displayJson($jsonOutput);
        if($exit_script){
            // By Default, exists json script after printing the message
            exit();
        }
    }

    /**
     * Display error json messages
     * @param $message
     */
    public static function displayJsonError($message){
        if(!$message){
            $message = "Error Occurred";
        }
        $jsonOutput ['error'] = $message;
        static::displayJson($jsonOutput);
        exit();
    }

    /**
     * Display error
     * @param $message
     */
    public static function displayError($message){
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
            // AJAX Requests
            static::displayJsonError($message);
        }
        else{
            echo Validator::sanitizeXss($message);
        }
        exit();
    }

    /**
     * Function to handle display the error.   If it is ajax request, displays json error, otherwise throw app error.
     * @param $message
     * @param $error_code
     * @throws AppError
     */
    public static function throwAppError($message, $error_code=500){
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
            // AJAX Requests
            static::displayJsonError($message);
        }
        else{
            throw new AppError($message, $error_code);
        }
        exit();
    }

    /**
     * Always use this function instead of echo
     * escapes html special character and prints input
     * @param $input
     */
    public static function securePrint($input){
        echo Validator::sanitizeXss($input);
    }

    /**
     * Use this function to print HTML.
     * Warning: Only trusted inputs should be used.
     * Try to avoid using this function
     * @param $trusted_input
     */
    public static function printHtml($trusted_input){
        echo $trusted_input;
    }
}

