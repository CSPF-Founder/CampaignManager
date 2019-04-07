<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace Core;
use \App\Config;

abstract class BaseModel {

    /**
     * Error messages
     * @var array
     */
    protected $id;
    protected $errors = [];

    /**
     * Static constructor / factory
     */
    public static function getInstance() {
        $instance = new static();
        return $instance;
    }

    /**
     * Get database instance
     * @return Database|null
     */
    public static function getDBInstance(){
        static $db = null;

        if($db == null){
            $db = new Database(Config::DB_HOST, Config::DB_USER, Config::DB_PASSWORD, Config::DB_NAME);
            return $db;
        }

        return $db;
    }

    /**
     * Getter for error messages
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Get Id
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Validate whether the given id is Integer and assign
     * @param $id
     * @return bool
     */
    public function setId($id){
        $id = filter_var ( $id, FILTER_VALIDATE_INT );
        if ($id!==false){
            $this->id = $id;
        }
        else{
            $this->errors[] = "Invalid Id" ;
        }
        return $this;
    }


}