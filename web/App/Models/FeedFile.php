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

class FeedFile extends DataModel {

    const TABLE_NAME = "feed_files";
    protected $feed_id;
    protected $filename;

    public static function createTable(){
        $db = static::getDBInstance();
        return $db->modify("CREATE TABLE `" . static::TABLE_NAME . "` (
                          `id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                          `feed_id` bigint(11) UNSIGNED NOT NULL,
                          `filename` varchar(255),
                           PRIMARY KEY (`id`),
                          FOREIGN KEY (feed_id) REFERENCES " . Feed::TABLE_NAME . "(id) ON DELETE CASCADE
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }


    /**
     * Validate whether the given id is Integer and assign
     * @param $feed_id
     * @return $this
     */
    public function setFeedId($feed_id){
        $feed_id = filter_var ( $feed_id, FILTER_VALIDATE_INT );
        if ($feed_id!==false){
            $this->feed_id = $feed_id;
        }
        else{
            $this->errors[] = "Invalid Feed Id" ;
        }
        return $this;
    }

    /**
     * Get Organization Id
     * @return mixed
     */
    public function getFeedId(){
        return $this->feed_id;
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
            . " (feed_id, filename) "
            . " values(:feed_id, :filename);";
        $updated = $db->modify( $query, array(
            "feed_id" => $this->feed_id,
            "filename" => $this->filename,
        ));

        $this->setId($db->getLastInsertid());
        return $updated;
    }

    /**
     * Get rows from the database as Object
     * @param $feed_id
     * @param int $start
     * @param int $length
     * @return array
     */
    public static function getObjectListByFeedId($feed_id,$start=0, $length=100000) {
        $feed_id = filter_var($feed_id, FILTER_VALIDATE_INT);
        $offset = filter_var($start, FILTER_VALIDATE_INT);
        $limit = filter_var($length, FILTER_VALIDATE_INT);

        if ($feed_id === false || $offset === false || $limit === false || !defined("static::TABLE_NAME")
            || $offset < 0 || $limit < 1
        ) {
            return null;
        }

        $query = "select * from " . static::TABLE_NAME
            . " where feed_id=:feed_id "
            . " order by id LIMIT :offset,:row_limit";
        $db = static::getDBInstance();

        $objects = $db->fetchObjectList(get_called_class(), $query, array(
                array("feed_id", $feed_id, PDO::PARAM_INT),
                array("offset", $offset, PDO::PARAM_INT),
                array("row_limit", $limit, PDO::PARAM_INT),
            )
            , "bindParam"
        );

        return $objects;
    }

    /**
     * Get Model object with id
     * @param $feed_id
     * @param $id
     * @return $this
     */
    public static function findByFeedIdAndId($feed_id,$id){
        $feed_id = filter_var ( $feed_id, FILTER_VALIDATE_INT );
        $id = filter_var ( $id, FILTER_VALIDATE_INT );
        if ($id!==false && $feed_id !== false){
            $db = static::getDBInstance();

            $query = "select * from " . static::TABLE_NAME
                . " where feed_id=:feed_id and id=:id;";
            return $db->fetchObject(get_called_class(), $query, array (
                "feed_id" => $feed_id,
                "id" => $id
            ) );
        }
    }
}