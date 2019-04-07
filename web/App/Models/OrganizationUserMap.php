<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Models;

use Core\DataModel;

class OrganizationUserMap extends DataModel {
    const TABLE_NAME = "org_user_map";

    //Properties:
    protected $user_id;
    protected $org_id;

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
     * @param $org_id
     * @return $this
     */
    public function setOrgId($org_id){
        $org_id = filter_var ( $org_id, FILTER_VALIDATE_INT );
        if ($org_id!==false){
            $this->org_id = $org_id;
        }
        else{
            $this->errors[] = "Invalid Organization Id" ;
        }
        return $this;
    }

    /**
     * Get Organization Id
     * @return mixed
     */
    public function getOrgId(){
        return $this->org_id;
    }

    /**
     * Get Model object with user id
     * @param $user_id
     * @return OrganizationUserMap
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
        $query = "insert into " . self::TABLE_NAME . " (user_id, org_id) values(:user_id, :org_id);";
        $db = static::getDBInstance();
        $updated = $db->modify( $query, array( "user_id" => $this->user_id, "org_id" => $this->org_id));
        if($updated > 0){
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function delete(){
        $query = "delete from " . self::TABLE_NAME . " where org_id=:org_id and user_id=:user_id;";
        $db = static::getDBInstance();
        $updated = $db->modify( $query, array( "user_id" => $this->user_id, "org_id" => $this->org_id));
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
                        org_id int(11) UNSIGNED NOT NULL,
                        FOREIGN KEY (user_id) REFERENCES " . User::TABLE_NAME . "(id) ON DELETE CASCADE,
                        FOREIGN KEY (org_id) REFERENCES " . Organization::TABLE_NAME . "(id) ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }
}