<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Models;


use Core\DataModel;
use PDO;

class TaskFile extends DataModel {

    const TABLE_NAME = "task_files";
    protected $task_id;
    protected $filename;

    public static function createTable(){
        $db = static::getDBInstance();
        return $db->modify("CREATE TABLE `" . static::TABLE_NAME . "` (
                          `id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                          `task_id` bigint(11) UNSIGNED NOT NULL,
                          `filename` varchar(255),
                           PRIMARY KEY (`id`),
                          FOREIGN KEY (task_id) REFERENCES " . Task::TABLE_NAME . "(id) ON DELETE CASCADE
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }


    /**
     * Validate whether the given id is Integer and assign
     * @param $task_id
     * @return $this
     */
    public function setTaskId($task_id){
        $task_id = filter_var ( $task_id, FILTER_VALIDATE_INT );
        if ($task_id!==false){
            $this->task_id = $task_id;
        }
        else{
            $this->errors[] = "Invalid Task Id" ;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTaskId(){
        return $this->task_id;
    }

    public function setFilename($filename){
        $this->filename = $filename;
        return $this;
    }

    public function getFilename(){
        return $this->filename;
    }

    /**
     * @return bool
     */
    public function save(){
        $db = static::getDBInstance();
        $query = "insert into " . self::TABLE_NAME
            . " (task_id, filename) "
            . " values(:task_id, :filename);";
        $updated = $db->modify( $query, array(
            "task_id" => $this->task_id,
            "filename" => $this->filename,
        ));

        $this->setId($db->getLastInsertid());
        return $updated;
    }

    /**
     * Get rows from the database as Object
     * @param $task_id
     * @param int $start
     * @param int $length
     * @return array
     */
    public static function getObjectListByTaskId($task_id,$start=0, $length=100000) {
        $task_id = filter_var($task_id, FILTER_VALIDATE_INT);
        $offset = filter_var($start, FILTER_VALIDATE_INT);
        $limit = filter_var($length, FILTER_VALIDATE_INT);

        if ($task_id === false || $offset === false || $limit === false || !defined("static::TABLE_NAME")
            || $offset < 0 || $limit < 1
        ) {
            return null;
        }

        $query = "select * from " . static::TABLE_NAME
            . " where task_id=:task_id "
            . " order by id LIMIT :offset,:row_limit";
        $db = static::getDBInstance();

        $objects = $db->fetchObjectList(get_called_class(), $query, array(
                array("task_id", $task_id, PDO::PARAM_INT),
                array("offset", $offset, PDO::PARAM_INT),
                array("row_limit", $limit, PDO::PARAM_INT),
            )
            , "bindParam"
        );

        return $objects;
    }

    /**
     * Get Model object with id
     * @param $task_id
     * @param $id
     * @return $this
     */
    public static function findByTaskIdAndId($task_id,$id){
        $task_id = filter_var ( $task_id, FILTER_VALIDATE_INT );
        $id = filter_var ( $id, FILTER_VALIDATE_INT );
        if ($id!==false && $task_id !== false){
            $db = static::getDBInstance();

            $query = "select * from " . static::TABLE_NAME
                . " where task_id=:task_id and id=:id;";
            return $db->fetchObject(get_called_class(), $query, array (
                "task_id" => $task_id,
                "id" => $id
            ) );
        }
    }
}