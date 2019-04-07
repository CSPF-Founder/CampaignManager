<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace Core;

use App\Config;
use Core\Security\Validator;
use finfo;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Utils{
    public static function redirectJSON($url){

        echo '{"redirect":'.json_encode($url)."}";
        exit();
    }
    public static function printError($msg){
        echo $msg;
        exit();
    }

    public static function jsonError($message,$exitScript=true) {
        /*
         * To create error message in JSON format
         */
        echo '{"error":'.json_encode($message).'}';
        if($exitScript){
            exit();
        }
    }
    public static function jsonSuccess($message,$exitScript=true){
        /*
         * To create Success message in JSON format
         */
        echo '{"success":'.json_encode($message).'}';
        if($exitScript){
            exit();
        }
    }

    /**
     * A method to print html content
     * Deliberately created replacement of 'echo' method to avoid using echo directly in the app
     * @param string $html
     */
    public static function printHtml(string $html){
        echo $html;
    }

    /**
     * Function to delete the non-empty folder
     */
    public static function deleteFolder($path){

        if (is_dir($path) === true){
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($files as $file)
            {
                if (in_array($file->getBasename(), array('.', '..')) !== true)
                {
                    if ($file->isDir() === true)
                    {
                        rmdir($file->getPathName());
                    }

                    else if (($file->isFile() === true) || ($file->isLink() === true))
                    {
                        unlink($file->getPathname());
                    }
                }
            }

            return rmdir($path);
        }

        else if ((is_file($path) === true) || (is_link($path) === true)){
            return unlink($path);
        }

        return false;
    }

    /**
     * Security Headers
     */
    public static function setSecurityHeaders(){
        foreach(Config::SECURITY_HEADERS as $header_value){
            header($header_value);
        }
    }

    public static function calculateExpiryFromDuration($duration){
        if(is_numeric($duration)){
            if($duration && $duration >= 1){
                return new \DateTime('today +'.intval($duration)."day", Config::getTimeZone());
            }
        }
    }

    /**
     * Get Random byte string
     * @param int $length
     * @return string
     */
    public static function getRandomBytes($length = 32){
        $random_bytes = openssl_random_pseudo_bytes($length, $cstrong);
        return bin2hex($random_bytes);
    }


    /**
     * Convert Absolute url into relative url
     * @param $url
     * @return string
     */
    public static function getRelativeFromAbsoluteUrl($url){
        if($url){
            $parsed = parse_url($url);
            $relative = $parsed["path"];
            if(isset($parsed["query"]) && $parsed["query"]){
                $relative = $relative . "?". $parsed["query"];
            }
            if($relative && Validator::isRelativeUrl($relative)){
                return $relative;
            }
        }
        return "";
    }

    /**
     * Gets the referer only if it is internal
     * @return string
     */
    public static function getInternalReferer(){
        return Utils::getRelativeFromAbsoluteUrl($_SERVER['HTTP_REFERER']);
    }

    /**
     * Rearrange the multiple uploaded-files array into a cleaner code:
     * Reference:
     * http://php.net/manual/en/features.file-upload.multiple.php
     * @param $files
     * @return mixed
     */
    public static function rearrangeFilesArray($files) {
        $cleanerArray = null;
        foreach ($files as $key => $all) {
            foreach ($all as $i => $val) {
                $cleanerArray[$i][$key] = $val;
            }
        }
        return $cleanerArray;
    }

    /**
     * @param $file_path
     * @return string|null
     */
    public static function getFileMimeType($file_path){
        $result = new finfo();

        if ($result) {
            return $result->file($file_path, FILEINFO_MIME_TYPE);
        }

        return null;
    }
}