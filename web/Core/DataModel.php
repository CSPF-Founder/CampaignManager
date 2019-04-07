<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace Core;


use Core\Security\Validator;
use PDO;

abstract class DataModel extends BaseModel {

    /**
     * Get Model object with id
     * @param $id
     * @return $this
     */
    public static function findById($id){
        $id = filter_var ( $id, FILTER_VALIDATE_INT );
        if ($id!==false){
            $db = static::getDBInstance();
            if(defined("static::TABLE_NAME")){
                $query = "select * from " . static::TABLE_NAME . " where id=:id;";
                if(!$db->tableExists(static::TABLE_NAME)){
                    return null;
                }
                return $db->fetchObject(get_called_class(), $query, array ("id" => $id) );
            }
        }
    }

    /**
     *  Get all rows from the db associated with the current model
     * @return array
     */
    public static function getList(){
        if(defined("static::TABLE_NAME")) {
            $query = "select * from " . static::TABLE_NAME;
            $db = static::getDBInstance();
            if(!$db->tableExists(static::TABLE_NAME)){
                return;
            }
            $rows = $db->fetch($query);
            return $rows;
        }
    }

    /**
     * Get Id list from the db associated with the current model
     * @return array
     */
    public static function getIdList(){
        if(defined("static::TABLE_NAME")) {
            $query = "select id from " . static::TABLE_NAME;
            $db = static::getDBInstance();
            if(!$db->tableExists(static::TABLE_NAME)){
                return null;
            }
            $rows = $db->fetch($query);
            if($rows){
                $list = array();
                foreach ($rows as $row) {
                    $list []= $row["id"];
                }
                return $list;
            }
        }
    }

    /**
     *  Delete the entry from the database based on id
     * @return array
     */
    public function delete(){
        if ($this->id) {
            $query = "delete from " . static::TABLE_NAME . " where id=:id";
            $db = static::getDBInstance();
            if(!$db->tableExists(static::TABLE_NAME)){
                return;
            }
            $updated = $db->modify($query, array("id" => $this->id));
            return $updated;
        }
    }

    /**
     * Get Number of rows
     * @return int
     */
    public static function getNumberOfRows(){
        if(defined("static::TABLE_NAME")) {
            $query = "select 1 from " . static::TABLE_NAME ;
            $db = static::getDBInstance();
            return $db->getRowCount($query);
        }
    }


    /**
     * Get rows from the database as Object
     * @param int $start
     * @param int $length
     * @return array
     */
    public static function getObjectList($start=0, $length=100000) {

        $offset = filter_var($start, FILTER_VALIDATE_INT);
        $limit = filter_var($length, FILTER_VALIDATE_INT);

        if ($offset === false || $limit === false || !defined("static::TABLE_NAME")
            || $offset < 0 || $limit < 1
        ) {
            return null;
        }

        $query = "select * from " . static::TABLE_NAME . " order by id LIMIT :offset,:row_limit";
        $db = static::getDBInstance();
        if(!$db->tableExists(static::TABLE_NAME)){
            return null;
        }

        $objects = $db->fetchObjectList(get_called_class(), $query, array(
                array("offset", $offset, PDO::PARAM_INT),
                array("row_limit", $limit, PDO::PARAM_INT),
            )
            , "bindParam"
        );

        return $objects;
    }

    /**
     * @internal DON'T get column name from client side
     * Check entry exists by property
     * @param $property
     * @param $property_value
     * @return bool
     */
    protected static function existsByProperty($property, $property_value){
        if(!Validator::isValidColumnName($property)){
            return false;
        }

        if($property_value === null){
            return false;
        }

        $db = static::getDBInstance();
        if(defined("static::TABLE_NAME")){
            $query = "select 1 from " . static::TABLE_NAME . " where ". $property. "=:property_value;";
            if($db->fetchOne($query, array ("property_value" => $property_value))){
                return true;
            }
        }
        return false;
    }

    /**
     * @internal DON'T get column name from client side
     * @param $property
     * @param $property_value
     * @return $this
     */
    protected static function findByProperty($property, $property_value){
        if(!Validator::isValidColumnName($property)){
            return null;
        }

        if($property_value === null){
            return null;
        }

        $db = static::getDBInstance();
        if(defined("static::TABLE_NAME")){
            $query = "select * from " . static::TABLE_NAME . " where ". $property. "=:property_value;";
            if(!$db->tableExists(static::TABLE_NAME)){
                return null;
            }
            return $db->fetchObject(get_called_class(), $query, array ("property_value" => $property_value) );
        }
    }

    /**
     * @internal DON'T get column name from client side
     * Update Property
     * @param $property
     * @param $propertyValue
     * @param null $propertyType
     * @return bool
     */
    protected function updateProperty($property, $propertyValue, $propertyType = null){
        if(!Validator::isValidColumnName($property) || !$this->id){
            return false;
        }

        $db = static::getDBInstance();
        $query = "update " . static::TABLE_NAME . " set " . $property . "=:property_value where id=:id";
        if(!$db->tableExists(static::TABLE_NAME)){
            return false;
        }
        $updated = 0;
        if ($propertyType === null) {
            $updated = $db->modify($query, array("property_value" => $propertyValue, "id" => $this->id));
        }
        else {
            $updated = $db->modify($query,
                array(
                    array("property_value", $propertyValue, $propertyType),
                    array("id", $this->getId(), PDO::PARAM_INT)
                ), "bindParam");
        }
        return $updated;
    }

    /**
     * @param $property
     * @param $flag
     * @return bool|int
     */
    protected function togglePropertyFlag($property, $flag){
        if($flag === false || $flag === "false"){
            $flag = 0;
        }
        elseif($flag === true || $flag === "true"){
            $flag = 1;
        }
        if($flag !==null) {
            return $this->updateProperty($property, $flag, PDO::PARAM_BOOL);
        }
    }
}
