<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace Core\Security;

use App\Config;
use \DateTime;

/**
 * Class Validator
 * @package Core
 */
class Validator {

    /**
     * Function to filter XSS
     * @param $input
     * @return string
     */
    public static function sanitizeXss($input) {
        if ($input !== null) {
            $filtered_input = htmlentities($input, ENT_QUOTES, "UTF-8");
            $filtered_input = str_replace("(", '&#40;', $filtered_input);
            $filtered_input = str_replace(")", '&#41;', $filtered_input);
            $filtered_input = str_replace("+", '&#43;', $filtered_input);
            $filtered_input = str_replace("{", '&#123;', $filtered_input);
            $filtered_input = str_replace("}", '&#125;', $filtered_input);
            $filtered_input = str_replace("[", '&#91;', $filtered_input);
            $filtered_input = str_replace("]", '&#93;', $filtered_input);
            return $filtered_input;
        }
    }


    /**
     * Validates whether the field exists or not in the request
     * Checks whether it is empty or not
     * @param $paramName
     * @param string $httpMethod
     * @return bool
     */
    public static function checkParamExists($paramName, $httpMethod = "POST") {

        if ($paramName) {
            $data = NULL;
            if ($httpMethod == "POST") {
                $data = $_POST;
            }
            else if ($httpMethod == "GET") {
                $data = $_GET;
            }

            if ($data) {
                if (isset($data [$paramName])) {
                    if (is_array($data[$paramName]) && $data[$paramName]) {
                        return true;
                    }
                    else {
                        $param = trim($data[$paramName]);
                        if ($param !== "") {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    /***
     * Validates whether the field exists or not in the request
     * Checks whether it is empty or not
     * @param $requiredParams
     * @param string $httpMethod
     * @return bool
     */
    public static function checkAllParamsExists($requiredParams, $httpMethod = "POST") {
        if ($requiredParams) {
            $i = 0;
            foreach ($requiredParams as $paramName) {
                if (self::checkParamExists($paramName, $httpMethod)) {
                    $i = $i + 1;
                }
                else {
                    return false;
                }
            }

            if ($i === count($requiredParams)) {
                return true;
            }
        }
        return false;
    }

    /** Checks whether the given name is valid or not
     * Only allows:
     *        a-z A-Z .(dot character) and white space
     *        and allows 1 to 190 characters
     *        The first character should be a letter
     * @param $name
     * @return bool
     */
    public static function isValidName($name) {
        if ($name) {
            if (preg_match("/^[A-Za-z][a-zA-Z. ]{1,190}$/", $name)) {
                return true;
            }
        }
        return false;
    }


    /** Checks whether the given organization is valid or not
     * Only allows:
     *        a-z,A-Z, numbers
     *        .(dot character), comma, ampersand,hyphen, underscore and white space
     *        and allows 1 to 190 characters
     *   The first character should be a letter or number
     * @param $name
     * @return bool
     */
    public static function isValidOrganizationName($name) {
        if ($name) {
            if (preg_match("/^[a-zA-Z0-9][a-zA-Z0-9.,_\\-\\& ]{1,190}$/", $name)) {
                return true;
            }
        }
        return false;
    }

    /** Checks whether the given organization is valid or not
     * Only allows:
     *        a-z,A-Z, numbers
     *        .(dot character), comma, ampersand,hyphen, underscore and white space
     *        and allows 1 to 190 characters
     *   The first character should be a letter or number
     * @param $name
     * @return bool
     */
    public static function isValidConstituencyName($name) {
        if ($name) {
            if (preg_match("/^[a-zA-Z0-9][a-zA-Z0-9.,_\\-\\& ]{1,190}$/", $name)) {
                return true;
            }
        }
        return false;
    }

    /** Checks whether the given name is valid or not
     * Only allows:
     *        a-z A-Z .(dot character) -(hyphen) and _(underscore)
     *        and allows 2 to 31 characters
     *        The first character should be a letter
     * @param $username
     * @return bool
     */
    public static function isValidUsername($username) {
        if ($username) {
            if (preg_match("/^[A-Za-z][a-zA-Z0-9._-]{1,30}$/", $username)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Function to validate the Email address
     * @param $email
     * @return bool|mixed
     */
    public static function isValidEmail($email) {
        if ($email) {
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
            return $email;
        }
        return false;
    }

    /**
     * Function to Check whether the given input is a valid alpha numeric text
     * @param $data
     * @return bool
     */
    public static function isValidAlphaNumeric($data) {
        if ($data) {
            if (ctype_alnum($data)) {
                return true;
            }
        }

        return false;
    }


    /**
     * check if it is valid date & then return DateTime object
     * The date format should be YYYY-MM-DD (Y-M-D format)
     * Eg:
     *  2017-01-01 -> valid
     *  2017-1-1 -> not valid
     *  2017-28-01 -> not valid
     * @param $date_string
     * @param string $format
     * @return mixed
     * @internal param $date
     */
    public static function getDateTimeFromString($date_string, $format="Y-m-d"){
        if($date_string && is_string($date_string)){
            $d = DateTime::createFromFormat($format, $date_string, Config::getTimeZone());
            if($d && $d->format($format) === $date_string){
                return $d;
            }
        }
        return false;
    }

    /**
     * check if it is valid column name for Database
     * @param $columnName
     * @return bool
     */
    public static function isValidColumnName($columnName) {
        return static::isValidTableName($columnName);
    }

    /**
     * check if it is valid table name for Database
     * allowed characters are characters, '_' '-' and length should be minimum 2 and maximum 40
     * @param $tableName
     * @return bool
     */
    public static function isValidTableName($tableName) {
        if ($tableName) {
            if (preg_match("/^[A-Za-z][a-zA-Z0-9._-]{1,40}$/", $tableName)) {
                return true;
            }
        }
        return false;
    }


    /**
     * Validate given input is valid url
     * @param $url
     * @return bool
     */
    public static function isValidURL($url) {
        if ($url && is_string($url)) {
            $url = trim($url);
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                return true;
            }
        }
        return false;
    }


    /**
     * To check the url is relative url
     * @param $url
     * @return bool
     */
    public static function isRelativeUrl($url) {
        if ($url) {
            var_dump($url);
            if (strlen($url) === 1 && $url === "/") {
                return true;
            }

            $parsed = parse_url($url);
            if (empty($parsed['host']) && preg_match("/^\/[A-Za-z]/", $url)
                && ($url[1] != '/' && $url[1] != '\\')
            ) {
                return true;
            }
        }
        return false;
    }


}
