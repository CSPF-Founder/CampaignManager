<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Models;

use App\Config;
use Core\AppError;
use Core\DataModel;
use Core\Role;
use \Core\Security\Validator;
use PDO;

class User extends DataModel {

    const TABLE_NAME = "users";

    private $roles;

    //Properties:
    protected $name;
    protected $username;
    protected $password;

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
        $db->modify("CREATE TABLE IF NOT EXISTS " . static::TABLE_NAME . " (
                        `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                        `name` varchar(255) NOT NULL,
                        `username` varchar(60) NOT NULL,
                        `password` varchar(64) NOT NULL,
                         PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    /**
     * Setter Function for the username:
     * @param $username
     * @return $this
     */
    public function setUsername($username) {

        if ($username) {
            if (Validator::isValidUsername($username)) {
                $this->username = $username;
            }
            else {
                $this->errors[] = "Invalid characters found in username";
            }
        }
        else {
            $this->errors[] = "Invalid user name";
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Setter Function for the username:
     * @param $name
     * @return $this
     */
    public function setName($name) {

        if ($name) {
            $this->name = $name;
        }
        else {
            $this->errors[] = "Invalid Name";
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    //Setter & Getter of Password:
    public function setPassword($password) {
        if ($password) {
            if (strlen($password) < Config::MIN_PASSWORD_LENGTH) {
                $this->errors[] = "Password must be at least ". Config::MIN_PASSWORD_LENGTH ." characters";
            }
            else{
                // Hash the password with Bcrypt:
                $options = [
                    "cost" => 12
                ];
                $hash = password_hash($password, PASSWORD_BCRYPT, $options);
                $this->password = $hash;
            }
        }
        else {
            $this->errors[] = "Password can not be empty";
        }
        return $this;
    }

    public function getPassword() {
        return $this->password;
    }

    public static function exists(User $user) {
        if(static::findByUsername($user->getUsername())){
            return true;
        }
        return false;
    }

    /**
     * Authenticate a user by username and password
     * @param $username
     * @param $password
     * @return User
     */
    public static function authenticate($username, $password) {
        $user = static::findByUsername($username);

        if ($user) {
            if (password_verify($password, $user->getPassword())) {
                return $user;
            }
        }
    }

    /**
     * Add the user entry to DB
     * @return bool|int
     */
    public function save(){
        $db = static::getDBInstance();
        if(!static::exists($this)){
            $query = "insert into " . self::TABLE_NAME
                . " (name, username, password) "
                . " values(:name, :username, :password);";
            $updated = $db->modify(
                $query,
                array(
                    "name" => $this->name,
                    "username" => $this->username,
                    "password" => $this->password,
                )
            );

            if ($updated > 0){
                $this->id = $db->getLastInsertid();
                return true;
            }
        }
        else{
            $this->errors[] = "User already exists";
        }
        return false;
    }


    /**
     * Get User object with username
     * @param $username
     * @return User
     */
    public static function findByUsername($username) {
        $db = static::getDBInstance();
        $query = "select * from " . static::TABLE_NAME . " where username=:username;";
        $user = $db->fetchObject(get_called_class(), $query, array("username" => $username));
        if($user and $user instanceof User){
            $user->initRoles();
            return $user;
        }
        return null;
    }

    /**
     * @param $id
     * @return User
     */
    public static function findById($id) {
        $user = parent::findById($id);
        if($user and $user instanceof User){
            $user->initRoles();
            return $user;
        }
        return null;
    }

    /**
     * @param $org_id
     * @param $id
     * @return User
     */
    public static function findByOrgAndId($org_id, $id) {
        $org_id = filter_var ( $org_id, FILTER_VALIDATE_INT );
        $id = filter_var ( $id, FILTER_VALIDATE_INT );
        if ($org_id !==false && $id !== false){
            $db = static::getDBInstance();
            $query = "select * from " . static::TABLE_NAME . " u1 "
                . " inner join " . OrganizationUserMap::TABLE_NAME . " ou1 on u1.id=ou1.user_id "
                . " where ou1.org_id=:org_id and u1.id=:id;";

            $user = $db->fetchObject(get_called_class(), $query, array (
                "org_id" => $org_id,
                "id" => $id
            ));

            if($user and $user instanceof User){
                $user->initRoles();
                return $user;
            }
        }
        return null;
    }

    /**
     * Get roles associated with the user in Human readable format
     * @return array
     */
    public function getRolesDescription() {
        if(!$this->roles){
            // If role is not loaded, get associated roles from DB
            $this->initRoles();
        }

        $roles_desc = array();
        if($this->roles){
            /** @var Role $role */
            foreach($this->roles as $keyword => $role){
                $role->keyword = $keyword;
                $roles_desc [] = $role->getDescription();
            }
        }
        return $roles_desc;
    }

    /**
     * load roles associated with the user
     */
    protected function initRoles(){
        $this->roles = array();
        $db = static::getDBInstance();
        $query ="select user_role.role_id, roles.keyword from user_role
                join roles on user_role.role_id = roles.id
                where user_role.user_id = :user_id
            ";
        $rows = $db->fetch($query, array("user_id" => $this->id));
        if($rows){
            foreach($rows as $row){
                $role = new Role();
                $role->id = $row["role_id"];
                $role->initPermissions();
                $this->roles[$row["keyword"]] = $role;
            }
        }
    }

    /**
     * Check if user has specified privilege
     * @param $required_permission
     * @return bool
     */
    public function can($required_permission){
        if($this->hasRole('super_admin')){
            // super admin can do anything
            return true;
        }
        else{
            $hasPermission = false;
            /** @var Role $role */
            if($this->roles){
                foreach($this->roles as $role){
                    if($role->hasPermission($required_permission)){
                        $hasPermission = true;
                        break;
                    }
                }
            }

            if($hasPermission === true){
                return true;
            }
        }

        return false;
    }

    /**
     * Check user has the role
     * @param $role_name
     * @return bool
     */
    public function hasRole($role_name){
        if(!$this->roles){
            // If role is not loaded, get associated roles from DB
            $this->initRoles();
        }

        return isset($this->roles[$role_name]);
    }

    /**
     * Add new role to user
     * @param Role $role
     * @return bool
     */
    public function assignRole($role){
        if($role){
            $db = DataModel::getDBInstance();
            $query = "insert into user_role(user_id, role_id) values(:user_id,:role_id)";
            return $db->modify($query, array("user_id" => $this->id, "role_id" => $role->id));
        }
        return false;
    }

    public function delete(){
        if ($this->id) {
            $db = static::getDBInstance();
            $query = "delete from user_role where user_id=:id";
            $db->modify($query, array("id" => $this->id));
            $query = "delete from users where id=:id";
            $updated = $db->modify($query, array("id" => $this->id));
            return $updated;
        }
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
}