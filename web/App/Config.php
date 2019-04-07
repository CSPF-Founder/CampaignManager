<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */
namespace App;

define('ROOT_DIR', dirname(__DIR__).'/');

use Core\DataModel;

class Config{
    /**
     * Application Constants
     */

    /**
     * Debug mode
     *
     */
    const DEBUG_MODE = false;
    /**
     * Allow Setup
     */
    const ALLOW_SETUP = self::DEBUG_MODE;

    const APP_TITLE = "Campaign Manager";

  
    const NUMBER_OF_FEEDS_PER_PAGE = 150;
    const NUMBER_OF_TASKS_PER_PAGE = 150;


    /**
     * Minimum number of password characters required
     */
    const MIN_PASSWORD_LENGTH = 10;

    /**
     * App Session Configurations
     */
    const SECURE_COOKE_FLAG = true;
    const HTTP_ONLY_FLAG = true;
    //const SESSION_EXPIRY_TIME = 3600;
    const SESSION_EXPIRY_TIME = 259200; //1day=86400 , 86400 * 3 days;
    const SESSION_ID_NAME = "AppSessionId";

    //Security Headers
    const SECURITY_HEADERS = array(
        "Strict-Transport-Security: max-age=31536000; includeSubDomains",
        "X-XSS-Protection: 1; mode=block",
        "X-Frame-Options: DENY",
        "X-Content-Type-Options: nosniff"
    );

    /**
     * Database Configuration
     */
    const DB_HOST = "";
    const DB_NAME = "";
    const DB_USER = "";
    const DB_PASSWORD = '';

    /**
     * App timezone
     */
    const TIME_ZONE = 'Asia/Kolkata';

    /**
     * convert timezone string to datetimezone and return
     * @return \DateTimeZone
     */
    public static function getTimeZone(){
        return new \DateTimeZone(static::TIME_ZONE);
    }


    /**
     * Configuration to Load from Database
     */
    const CONFIG_TABLE = "app_config";

    /**
     * Mail Configuration
     */
    const MAIL_USER = '';
    const MAIL_PASS = ''; 
    const MAIL_HOST = '';
    const MAIL_FROM_NAME = '';

    /**
     * Get Configuration from Database
     * @param $name
     */
    public static function get($name){

    }

    /**
     * Set configuration in Database
     * @param $name
     * @param $value
     */
    public static function set($name, $value){

    }

    const DATA_DIR = ROOT_DIR. "../data/";


    /**
     * Create table in database
     */
    public static function createTable(){
        $db = DataModel::getDBInstance();
        $db->modify(" CREATE TABLE ". static::CONFIG_TABLE . " (
                              `name` varchar(64) NOT NULL,
                              `value` text NOT NULL
                            ) 
                    ");
    }
}
