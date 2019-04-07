<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Models;


use Core\DataModel;
use DateTime;
use PDO;

class TaskFeedback extends DataModel {

    const TABLE_NAME = "task_feedback";

    protected $task_id;
    protected $comment;
    protected $org_id;
    protected $user_id;
    protected $date_time;

    public static function createTable(){
        $db = static::getDBInstance();
        return $db->modify("CREATE TABLE `" . static::TABLE_NAME . "` (
                          `id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                          `org_id` bigint(11) UNSIGNED NOT NULL,
                          `task_id` bigint(11) UNSIGNED NOT NULL,
                          `user_id` bigint(11) UNSIGNED NOT NULL,
                          `date_time` datetime default now(),
                          `comment` text,
                           PRIMARY KEY (`id`),
                          FOREIGN KEY (task_id) REFERENCES " . Task::TABLE_NAME . "(id) ON DELETE CASCADE
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    /**
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

    public function getDateTime($dt_format="d/m/Y H:i"){
        $date_time = new DateTime($this->date_time);
        return $date_time->format($dt_format);
    }

    /**
     * @param $user_id
     * @return $this
     */
    public function setUserId($user_id){
        $user_id = filter_var ( $user_id, FILTER_VALIDATE_INT );
        if ($user_id!==false){
            $this->user_id = $user_id;
        }
        else{
            $this->errors[] = "Invalid User Id" ;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId(){
        return $this->user_id;
    }

    public function setComment($comment){
        $this->comment = $comment;
        return $this;
    }

    public function getComment(){
        return $this->comment;
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
     * @return bool
     */
    public function save(){
        $db = static::getDBInstance();
        $query = "insert into " . self::TABLE_NAME
            . " (task_id, comment, user_id, org_id) "
            . " values(:task_id, :comment,:user_id,:org_id);";
        $updated = $db->modify( $query, array(
            "task_id" => $this->task_id,
            "comment" => $this->comment,
            "user_id" => $this->user_id,
            "org_id" => $this->org_id
        ));

        $this->setId($db->getLastInsertid());
        return $updated;
    }

    /**
     * Get Model object with id
     * @param $task_id
     * @param $id
     * @return $this
     */
    public static function findByTaskId($task_id,$id){
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

    /**
     * @param $task_id
     * @param int $start
     * @param int $length
     * @return array
     */
    public static function getListByTaskId($task_id, $start=0, $length=1000000) {
        $task_id = filter_var($task_id, FILTER_VALIDATE_INT);
        $offset = filter_var($start, FILTER_VALIDATE_INT);
        $limit = filter_var($length, FILTER_VALIDATE_INT);

        if ($task_id === false || $offset === false || $limit === false
            || $offset < 0 || $limit < 1) {
            return null;
        }
        $db = static::getDBInstance();

        $query = "select * from " . static::TABLE_NAME
            ." where task_id=:task_id "
            . " order by id LIMIT :offset,:row_limit";

        $objects = $db->fetchObjectList(get_called_class(), $query, array(
                array("offset", $offset, PDO::PARAM_INT),
                array("row_limit", $limit, PDO::PARAM_INT),
                array("task_id", $task_id, PDO::PARAM_INT),
            )
            , "bindParam"
        );

        return $objects;
    }
}