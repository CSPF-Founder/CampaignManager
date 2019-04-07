<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */
namespace App\Models;


use App\Config;
use Core\DataModel;
use Core\Role;
use Core\Security\Validator;
use PDO;

class Organization extends DataModel{
    const TABLE_NAME = "organizations";

    //Properties:
    private $name;
    private $max_constituency_count;

    /**
     * Error messages
     * @var array
     */
    protected $errors = [];

    public function __construct() {

        // To validate & convert, property is assigned using PDO::FETCH_CLASS,
        if ($this->id !== null) {
            $this->setId($this->id);
        }
    }

    public static function createTable(){
        $db = static::getDBInstance();
        return $db->modify("CREATE TABLE `" . static::TABLE_NAME . "` (
                          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                          `name` text NOT NULL,
                          `max_constituency_count` int(11) UNSIGNED DEFAULT 0,
                          PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    /**
     * @param $name
     * @return $this
     */
    public static function findByName($name) {
        if($name){
            $db = static::getDBInstance();
            $query = "select * from " . static::TABLE_NAME . " where name=:name;";
            return $db->fetchObject(get_called_class(), $query, array (
                "name" => $name
            ) );
        }
        return null;
    }


    /**
     * Setter Function for the name:
     * @param $name
     * @return $this
     */
    public function setName($name) {
        if ($name) {
            if (Validator::isValidOrganizationName($name)) {
                $this->name = $name;
            }
            else {
                $this->errors[] = "Invalid name";
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
    * @param $max_constituency_count
    * @return $this
    */
    public function setMaxConstituencyCount($max_constituency_count){
        $max_constituency_count = filter_var ( $max_constituency_count, FILTER_VALIDATE_INT );
        if ($max_constituency_count!==false){
            $this->max_constituency_count = $max_constituency_count;
        }
        else{
            $this->errors[] = "Invalid Max Constituency count" ;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxConstituencyCount(){
        return $this->max_constituency_count;
    }


    /**
     * @return bool
     */
    public function save(){
        $db = static::getDBInstance();
        $query = "insert into " . self::TABLE_NAME . " (name,max_constituency_count) values(:name,:max_constituency_count);";
        $updated = $db->modify( $query, array(
            "name" => $this->name,
            "max_constituency_count" => $this->max_constituency_count
        ));

        $this->setId($db->getLastInsertid());
        return $updated;
    }


    /**
     * Get rows from the database as Object
     * @param int $start
     * @param int $length
     * @return array
     */
    public function getUserList($start=0, $length=100000) {

        $offset = filter_var($start, FILTER_VALIDATE_INT);
        $limit = filter_var($length, FILTER_VALIDATE_INT);

        if ($offset === false || $limit === false || $offset < 0 || $limit < 1) {
            return null;
        }
        $db = static::getDBInstance();

        $query = "select * from " . User::TABLE_NAME . " u1"
            ." inner join " . OrganizationUserMap::TABLE_NAME ." ou1 on u1.id=ou1.user_id"
            . " where ou1.org_id=:org_id"
            . " order by id LIMIT :offset,:row_limit";

        $objects = $db->fetchObjectList(User::class, $query, array(
                array("offset", $offset, PDO::PARAM_INT),
                array("row_limit", $limit, PDO::PARAM_INT),
                array("org_id", $this->getId(), PDO::PARAM_INT),
            )
            , "bindParam"
        );

        return $objects;
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
            $query = "select * from " . static::TABLE_NAME . " o1"
                ." inner join " . OrganizationUserMap::TABLE_NAME ." ou1 on o1.id=ou1.org_id"
                . " where ou1.user_id=:user_id;";
            return $db->fetchObject(get_called_class(), $query, array ("user_id" => $user_id) );
        }
        return null;
    }

    public function getDataFolder(){
        if($this->id){
            $dir_path = Config::DATA_DIR . "org_". $this->id . "/";
            if(!file_exists($dir_path)){
                mkdir($dir_path);
            }
            return $dir_path;
        }
        else{
            // will/should not happen; just as precaution
            return "/tmp/";
        }
    }

    /**
     * Get rows from the database as Object
     * @param $role
     * @param int $start
     * @param int $length
     * @return array
     */
    public function getUserListByRole($role, $start=0, $length=100000) {

        $offset = filter_var($start, FILTER_VALIDATE_INT);
        $limit = filter_var($length, FILTER_VALIDATE_INT);

        if ($offset === false || $limit === false || $offset < 0 || $limit < 1) {
            return null;
        }
        $db = static::getDBInstance();

        $query = "select u1.id,u1.name,u1.username from " . User::TABLE_NAME . " u1"
            ." inner join " . OrganizationUserMap::TABLE_NAME ." ou1 on u1.id=ou1.user_id"
            . " join roles as t2 "
            . " join user_role as t3 on t2.id = t3.role_id and u1.id=t3.user_id"
            . " where ou1.org_id=:org_id and t2.keyword=:role"
            . " order by id LIMIT :offset,:row_limit";

        $objects = $db->fetchObjectList(User::class, $query, array(
                array("role", $role, PDO::PARAM_STR),
                array("offset", $offset, PDO::PARAM_INT),
                array("row_limit", $limit, PDO::PARAM_INT),
                array("org_id", $this->getId(), PDO::PARAM_INT),
            )
            , "bindParam"
        );

        return $objects;
    }

}