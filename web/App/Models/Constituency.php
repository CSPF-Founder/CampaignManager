<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Models;


use App\Models\Enums\ConstituencyStrength;
use App\Models\Enums\FeederType;
use Core\DataModel;
use Core\Security\Validator;
use PDO;

class Constituency extends DataModel {
    const TABLE_NAME = "constituency_list";

    private $name;
    private $strength;
    private $org_id;

    /**
     * @param $org_id
     * @param $name
     * @return $this
     */
    public static function findByOrgAndName($org_id, $name) {
        if($name){
            $db = static::getDBInstance();
            $query = "select * from " . static::TABLE_NAME . " where org_id=:org_id and name=:name;";
            return $db->fetchObject(get_called_class(), $query, array (
                "org_id" => $org_id,
                "name" => $name
            ) );
        }
        return null;
    }

    /**
     * @param $org_id
     * @param $id
     * @return $this
     */
    public static function findByOrgAndId($org_id, $id) {
        $id = filter_var ( $id, FILTER_VALIDATE_INT );
        if ($id!==false){
            $db = static::getDBInstance();
            $query = "select * from " . static::TABLE_NAME
                . " where org_id=:org_id and id=:id;";
            return $db->fetchObject(get_called_class(), $query, array (
                "org_id" => $org_id,
                "id" => $id
            ) );
        }
    }

    /**
     * Setter Function for the name:
     * @param $name
     * @return $this
     */
    public function setName($name) {
        if ($name) {
            if (Validator::isValidConstituencyName($name)) {
                $this->name = $name;
            }
            else {
                $this->errors[] = "Invalid characters found in name";
            }
        }
        else {
            $this->errors[] = "Invalid name";
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /*
    **
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

    /*
    **
    * @param $strength
    * @return $this
    */
    public function setStrength($strength){
        $strength = filter_var ( $strength, FILTER_VALIDATE_INT );
        if ($strength!==false && array_key_exists($strength, ConstituencyStrength::ENUM_LIST)){
            $this->strength = $strength;
        }
        else{
            $this->errors[] = "Invalid Constituency Strength" ;
        }
        return $this;
    }

    /**
     * @param $format
     * @return mixed
     */
    public function getStrength($format=null){
        if($format === 'text'){
            return ConstituencyStrength::getString($this->strength);
        }
        else{
            return $this->strength;
        }
    }

    /**
     * Get organization associated with the user id
     * @param $user_id
     * @return $this
     */
    public static function findByUserId($user_id){
        $user_id = filter_var ( $user_id, FILTER_VALIDATE_INT );
        if ($user_id!==false){
            $db = static::getDBInstance();
            $query = "select * from " . static::TABLE_NAME . " c1"
                ." inner join " . FeederInfo::TABLE_NAME ." t2 on c1.id=t2.constituency_id"
                . " where t2.user_id=:user_id;";
            return $db->fetchObject(get_called_class(), $query, array ("user_id" => $user_id) );
        }
        return null;
    }

    /**
     * @return bool
     */
    public function save(){
        $db = static::getDBInstance();
        $query = "insert into " . self::TABLE_NAME . " (name,strength,org_id) "
            . " values(:name,:strength,:org_id);";
        $updated = $db->modify( $query, array(
            "name" => $this->name,
            "strength" => $this->strength,
            "org_id" => $this->org_id
        ));

        $this->setId($db->getLastInsertid());
        return $updated;
    }

    /**
     * @return bool
     */
    public static function createTable(){
        $db = static::getDBInstance();
        return $db->modify("CREATE TABLE `" . static::TABLE_NAME . "` (
                          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                          `name` text NOT NULL,
                          `strength` tinyint not null default 1,
                          `org_id` int(11) UNSIGNED NOT NULL,
                          PRIMARY KEY (`id`),
                          FOREIGN KEY (org_id) REFERENCES " . Organization::TABLE_NAME . "(id) ON DELETE CASCADE
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    /**
     * Get rows from the database as Object
     * @param $org_id
     * @param int $start
     * @param int $length
     * @return array
     */
    public static function getListByOrganization($org_id, $start=0, $length=100000) {
        $org_id = filter_var($org_id, FILTER_VALIDATE_INT);
        $offset = filter_var($start, FILTER_VALIDATE_INT);
        $limit = filter_var($length, FILTER_VALIDATE_INT);

        if ($org_id === false || $offset === false || $limit === false || $offset < 0 || $limit < 1) {
            return null;
        }
        $db = static::getDBInstance();

        $query = "select * from " . static::TABLE_NAME
            ." where org_id=:org_id"
            . " order by strength LIMIT :offset,:row_limit";

        $objects = $db->fetchObjectList(get_called_class(), $query, array(
                array("offset", $offset, PDO::PARAM_INT),
                array("row_limit", $limit, PDO::PARAM_INT),
                array("org_id", $org_id, PDO::PARAM_INT),
            )
            , "bindParam"
        );

        return $objects;
    }

    public static function getNameFromId($id_val){
        static $id_name_map = array();
        $id_val = filter_var($id_val, FILTER_VALIDATE_INT);

        if ($id_val === false) {
            return null;
        }

        $db = static::getDBInstance();

        $query = "select id,name from " . static::TABLE_NAME . " where id=:id";

        if(!array_key_exists($id_val,$id_name_map)){
            $row = $db->fetchOne($query, array("id" => $id_val) );
            if($row){
                $id_name_map[$row["id"]] = $row["name"];
            }
        }

        if(array_key_exists($id_val,$id_name_map)){
            return $id_name_map[$id_val];
        }

        return null;
    }

    /**
     * Get total number of rows
     * @param $org_id
     * @return int
     */
    public static function getCountByOrganization($org_id){
        $org_id = filter_var($org_id, FILTER_VALIDATE_INT);

        if ($org_id === false) {
            return null;
        }

        $db = static::getDBInstance();
        $query = "select id from " . static::TABLE_NAME . " where org_id=:org_id";

        return $db->getRowCount($query, array(
            "org_id"=> $org_id
        ));
    }
}