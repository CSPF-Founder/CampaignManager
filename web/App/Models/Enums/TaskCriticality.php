<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Models\Enums;


class TaskCriticality {

    const URGENT = 1;
    const IMPORTANT = 2;
    const NORMAL = 3;

    const ENUM_LIST = array(
        self::URGENT => "Urgent",
        self::IMPORTANT => "Important",
        self::NORMAL => "Normal",
    );

    public static function getString($typeCode){
        if(!array_key_exists($typeCode, self::ENUM_LIST)){
            return "Invalid value";
        }

        return self::ENUM_LIST[$typeCode];
    }

    public static function getIndex($string_value){
        if(!in_array($string_value, self::ENUM_LIST)){
            return "Invalid value";
        }

        return array_search($string_value, self::ENUM_LIST);
    }

    public static function getAllValues(){
        return array_values( self::ENUM_LIST );
    }

    public static function getAllKeys(){
        return array_keys ( self::ENUM_LIST );
    }
}