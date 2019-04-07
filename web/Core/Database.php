<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace Core;

use PDO;


class Database {

    protected $host;
    protected $user;
    protected $pass;
    protected $db_name;
    protected $db;
    public $errors;
    protected $error;
    protected $throw_exception;
    protected $last_insert_id;

    //Get the ID of last inserted Row:
    public function getLastInsertid() {
        if (!is_null($this->last_insert_id)) {
            return $this->last_insert_id;
        }
        return -1;
    }

    public function __construct($host, $user, $password, $dbName, $exception = false) {   //Constructor:
        $this->host = $host;
        $this->user = $user;
        $this->pass = $password;
        $this->db_name = $dbName;
        $this->throw_exception = $exception;
        $this->connect();
    }

    protected function setError($error) {
        $this->error = $error;
    }

    public function getError() {
        $this->error;
    }

    protected function connect() {
        $this->db = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->user, $this->pass);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Custom Function that prepares the sql query and returns the $stmt object
     * */
    public function prepare($query, $dataArray, $bindType) {

        if ($this->db) {
            $stmt = $this->db->prepare($query);

            if ($bindType == "execute") {
                //To use Execute
                $stmt->execute($dataArray);
            } else if ($bindType == "bindParam") {
                //To Use Bind Param method -> To specify the data type explicitly
                foreach ($dataArray as $data) {
                    # parameter,value,data_type
                    $stmt->bindParam($data[0], $data[1], $data[2]);
                }
                $stmt->execute();
            }
            return $stmt;
        } else {
            $this->setError("Database Connection is not established");
        }
    }

    /**
     * Function to retrieve all rows
     * @param $query
     * @param array $dataArray
     * @param string $bindType
     * @param int $fetchType
     * @return array
     */
    public function fetch($query, $dataArray = array(), $bindType = "execute", $fetchType = PDO::FETCH_ASSOC) {
        $stmt = $this->prepare($query, $dataArray, $bindType);
        if ($stmt) {
            $rows = $stmt->fetchAll($fetchType);
            $stmt->closeCursor();
            return $rows;
        }

        return array();
    }

    /**
     * Function to retrieve one row
     * @param $query
     * @param array $dataArray
     * @param string $bindType
     * @param int $fetchType
     * @return array
     */
    public function fetchOne($query, $dataArray = array(), $bindType = "execute", $fetchType = PDO::FETCH_ASSOC) {
        $stmt = $this->prepare($query, $dataArray, $bindType);
        if ($stmt) {
            $row = $stmt->fetch($fetchType);
            $stmt->closeCursor();
            return $row;
        }

        return array();
    }

    /**
     * Fetch row and convert into a class
     * @param $className
     * @param $query
     * @param array $dataArray
     * @param string $bindType
     */
    public function fetchObject($className, $query, $dataArray = array(), $bindType="execute") {

        if ($this->db) {
            $stmt = $this->db->prepare($query);

            if ($bindType == "execute") {
                //To use Execute
                $stmt->execute($dataArray);
            } else if ($bindType == "bindParam") {
                //To Use Bind Param method -> To specify the data type explicitly
                foreach ($dataArray as $data) {
                    # parameter,value,data_type
                    $stmt->bindParam($data[0], $data[1], $data[2]);
                }
            }

            $stmt->setFetchMode(PDO::FETCH_CLASS, $className);
            $stmt->execute();
            $object = $stmt->fetch();
            $stmt->closeCursor();
            return $object;

        } else {
            $this->setError("Database Connection is not established");
        }

        return;
    }

    public function fetchObjectList($className, $query, $dataArray = array(), $bindType="execute") {

        if ($this->db) {
            $stmt = $this->db->prepare($query);

            if ($bindType == "execute") {
                //To use Execute
                $stmt->execute($dataArray);
            } else if ($bindType == "bindParam") {
                //To Use Bind Param method -> To specify the data type explicitly
                foreach ($dataArray as $data) {
                    # parameter,value,data_type
                    $stmt->bindParam($data[0], $data[1], $data[2]);
                }
            }

            $stmt->setFetchMode(PDO::FETCH_CLASS, $className);
            $stmt->execute();
            $object = $stmt->fetchAll();
            $stmt->closeCursor();
            return $object;

        } else {
            $this->setError("Database Connection is not established");
        }

        return;
    }

    /**
     * Function to retrieve Only Row Counts
     * @param $query
     * @param array $dataArray
     * @param string $bindType
     * @return int
     */
    public function getRowCount($query, $dataArray = array(), $bindType = "execute") {
        $stmt = $this->prepare($query, $dataArray, $bindType);
        if ($stmt) {
            $count = $stmt->rowCount();
            $stmt->closeCursor();
            return $count;
        }

        return 0;
    }

    /**
     * Function to Modify Database
     * */
    public function modify($query, $dataArray = array(), $bindType = "execute") {
        if (!is_null($this->db)) {
            $count = false;

            $this->beginTransaction();
            $stmt = $this->prepare($query, $dataArray, $bindType);

            if ($stmt) {
                $this->last_insert_id = $this->db->lastInsertId();
                $count = $stmt->rowCount();
            }

            $this->commit();

            return $count;
        } else {
            $this->setError("Database Connection is not established");
        }

        return 0;
    }

    /**
     * Function that will not Save the data, until you explicitly call commit()
     * Useful when running multiple modification queries that depends each other, can be easily rolled back with rollback()
     * */
    public function modify_nocommit($query, $dataArray = array(), $bindType = "execute") {

        $count = false;

        $stmt = $this->prepare($query, $dataArray, $bindType);

        if ($stmt) {
            $this->last_insert_id = $this->db->lastInsertId();
            $count = $stmt->rowCount();
        }

        return $count;
    }

    /**
     * Check table exists
     * Note: When
     * @param $tableName
     * @return bool
     */
    public function tableExists($tableName){
        $row = $this->fetchOne("SHOW TABLES like :table_name", array("table_name" => $tableName));
        if($row){
            return true;
        }
        return false;
    }

    function checkColumnExists($tableName, $columnName){
        $query = 'SHOW field FROM ' . $tableName . " where Field=:column_name";
        $row = $this->fetchOne($query, array("column_name" => $columnName));
        if($row){
            return true;
        }
    }

    public function rollback() {
        $this->db->rollback();
    }

    public function commit() {
        $this->db->commit();
    }

    public function beginTransaction() {
        $this->db->beginTransaction();
    }

    public function __destruct() {
        //Destructor
    }
}

