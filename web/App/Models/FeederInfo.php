<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Models;


use App\Models\Enums\FeederType;
use Core\DataModel;
use Core\Security\Validator;

class FeederInfo extends DataModel {
    const TABLE_NAME = "feeder_info";

    //Properties:
    protected $user_id;
    protected $feeder_type;
    protected $mobile_number;
    protected $constituency_id;
    protected $email;


    public function __construct() {
        // To validate & convert, property is assigned using PDO::FETCH_CLASS,
        if ($this->user_id !== null) {
            $this->setId($this->user_id);
        }
    }

    /**
     * Validate whether the given id is Integer and assign
     * @param $user_id
     * @return $this
     */
    public function setUserId($user_id){
        $user_id = filter_var ( $user_id, FILTER_VALIDATE_INT );
        if ($user_id!==false){
            $this->user_id = $user_id;
        }
        else{
            $this->errors[] = "Invalid user Id" ;
        }
        return $this;
    }

    /**
     * Get User Id
     * @return mixed
     */
    public function getUserId(){
        return $this->user_id;
    }


    /**
     * Validate whether the given id is Integer and assign
     * @param $constituency_id
     * @return $this
     */
    public function setConstituencyId($constituency_id){
        $constituency_id = filter_var ( $constituency_id, FILTER_VALIDATE_INT );
        if ($constituency_id!==false){
            $this->constituency_id = $constituency_id;
        }
        else{
            $this->errors[] = "Invalid Constituency" ;
        }
        return $this;
    }

    /**
     * Get User Id
     * @return mixed
     */
    public function getConstituencyId(){
        return $this->constituency_id;
    }

    public function setMobileNumber($mobile_number){
        $this->mobile_number = $mobile_number;
        return $this;
    }

    public function getMobileNumber(){
        return $this->mobile_number;
    }

    public function setEmail($email){
        if(Validator::isValidEmail($email)){
            $this->email = $email;
        }
        else{
            $this->errors[] = "Invalid Email address";
        }

        return $this;
    }

    public function getEmail(){
        return $this->email;
    }

    /**
     * Validate whether the given id is Integer and assign
     * @param $feeder_type
     * @return $this
     */
    public function setFeederType($feeder_type){
        $feeder_type = filter_var ( $feeder_type, FILTER_VALIDATE_INT );
        if ($feeder_type!==false && in_array($feeder_type, FeederType::getAllKeys())){
            $this->feeder_type = $feeder_type;
        }
        else{
            $this->errors[] = "Invalid Feeder Type" ;
        }
        return $this;
    }

    /**
     * Get Organization Id
     * @return mixed
     */
    public function getFeederType(){
        return $this->feeder_type;
    }

    /**
     * Get Organization Id
     * @return mixed
     */
    public function getFeederTypeText(){
        return FeederType::getString($this->feeder_type);
    }

    /**
     * Get Model object with user id
     * @param $user_id
     * @return $this
     */
    public static function findByUserId($user_id){
        $user_id = filter_var ( $user_id, FILTER_VALIDATE_INT );
        if ($user_id!==false){
            $db = static::getDBInstance();
            $query = "select * from " . static::TABLE_NAME . " where user_id=:user_id;";
            return $db->fetchObject(get_called_class(), $query, array ("user_id" => $user_id) );
        }
        return null;
    }

    /**
     * add user map entry to the database
     * @return bool
     */
    public function save(){
        $query = "insert into " . self::TABLE_NAME
            . " (user_id, feeder_type, constituency_id, mobile_number, email) "
            . " values(:user_id, :feeder_type, :constituency_id, :mobile_number, :email);";
        $db = static::getDBInstance();
        $updated = $db->modify( $query,
            array(
                "user_id" => $this->user_id,
                "feeder_type" => $this->feeder_type,
                "constituency_id" => $this->constituency_id,
                "mobile_number" => $this->mobile_number,
                "email" => $this->email,
            ));
        if($updated > 0){
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function delete(){
        $query = "delete from " . self::TABLE_NAME . " where user_id=:user_id;";
        $db = static::getDBInstance();
        $updated = $db->modify( $query, array( "user_id" => $this->user_id));
        if($updated > 0){
            return true;
        }
        return false;
    }

    /**
     * Create table
     * @return bool
     */
    public static function createTable(){
        $db = static::getDBInstance();
        return $db->modify("CREATE TABLE " . static::TABLE_NAME . " (
                        user_id int(11) UNSIGNED NOT NULL,
                        feeder_type int(11) UNSIGNED NOT NULL,
                        constituency_id int(11) UNSIGNED NOT NULL,
                        email varchar(320) NOT NULL,
                        mobile_number varchar(40) NOT NULL,
                        FOREIGN KEY (user_id) REFERENCES " . User::TABLE_NAME . "(id) ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }
}