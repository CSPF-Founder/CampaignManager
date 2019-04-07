<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace Core\Security;

use Core\Utils;
/**
 * Class CSRF
 * Single CSRF Token per session  - Prevents CSRF & at the same time, back button will for users.
 * @package Core
 */
class CSRF {
    const TOKEN_NAME = "csrf_token";

    /**
     * Generate the Token value
     */
    public static function generate() {
        $token= Utils::getRandomBytes();
        $_SESSION[static::TOKEN_NAME] = $token;  //Storing in Session
    }

    public static function tokenExists(){
        return isset($_SESSION[static::TOKEN_NAME]);
    }

    /**
     * Get the token value
     * @return mixed
     */
    public static function get() {
        if(static::tokenExists()){
            return $_SESSION[static::TOKEN_NAME];
        }
    }

    /**
     * Validate the Token
     * @param $token
     * @return bool
     */
    public static function validate($token) {
        if($token){
            if($token === $_SESSION[static::TOKEN_NAME]){
                return true;
            }
        }
        return false;
    }

    /**
     * Add token to forms
     */
    public static function addInputField(){
        if(!static::tokenExists()){
            static::generate();
        }
        echo '<input type="hidden" name="' . static::TOKEN_NAME . '" value="' . static::get() . '"/>';
    }

    /**
     * Add token to Ajax field
     */
    public static function addAjaxField(){
        if(!static::tokenExists()){
            static::generate();
        }
        echo '"' . static::TOKEN_NAME . '":"' . static::get() . '"';
    }
}