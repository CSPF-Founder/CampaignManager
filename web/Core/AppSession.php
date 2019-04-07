<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace Core;

use \App\Config;
use Core\Security\CSRF;

class AppSession {

    /**
     * Start the Session with config & generate CSRF tokens
     * @param $path
     * @param bool $regenerate_id
     */
    public static function start($path, $regenerate_id=false){
        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies.
        $cookieParams = session_get_cookie_params(); // Gets current cookies params.

        session_set_cookie_params(
            Config::SESSION_EXPIRY_TIME,
            $path,
            $cookieParams["domain"],
            Config::SECURE_COOKE_FLAG,
            Config::HTTP_ONLY_FLAG);
        //Change session id name
        session_name(Config::SESSION_ID_NAME);

        session_start();
        $now = time();
        //Regenerate Session, if it is expired, new session is requested:
        if (( isset($_SESSION['session_expiry_time']) && $now > $_SESSION['session_expiry_time'])
            || $regenerate_id ) {
            static::regenerate();
        }
    }

    /**
     * Generate new session
     */
    public static function regenerate(){
        //put return_to page before destroying the session
        if(isset($_SESSION['return_to'])) {
            $returnToPage = $_SESSION['return_to'];
        }

        // Destroy old session & data:
        $_SESSION = array();
        session_unset();
        session_destroy();

        //Start the new session:
        session_start();

        //New session ID:
        session_regenerate_id(TRUE);
        $_SESSION['session_expiry_time'] = time() + Config::SESSION_EXPIRY_TIME;

        if(isset($returnToPage)){
            $_SESSION['return_to'] = $returnToPage;
        }

        //Generate CSRF Token
        CSRF::generate();
    }

}