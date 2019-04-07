<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Models;

use App\Config;
use App\Models\Enums\FeedStatus;
use Core\DataModel;
use PDO;

class Feed extends DataModel {
    const TABLE_NAME = "input_feeds";

    //properties
    protected $headline;
    protected $summary;
    protected $org_id;
    protected $constituency_id;
    protected $user_id;
    protected $added_by;
    protected $status;

    //virtual properties - does not exist in db
    private $media_count;

    public function getHeadline(){
        return $this->headline;
    }

    public function setHeadline($headline){
        if($headline){
            $this->headline = $headline;
        }
        return $this;
    }

    public function getSummary(){
        return $this->summary;
    }

    public function setSummary($summary){
        if($summary){
            $this->summary = $summary;
        }
        return $this;
    }

    /**
     * Validate whether the given id is Integer and assign
     * @param $status
     * @return $this
     */
    public function setStatus($status){
        $status = filter_var ( $status, FILTER_VALIDATE_INT );
        if ($status!==false && in_array($status, FeedStatus::getAllKeys())){
            $this->status = $status;
        }
        else{
            $this->errors[] = "Invalid Status" ;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus(){
        return $this->status;
    }

    /**
     * Validate whether the given id is Integer and assign
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
     * @param $status
     * @return bool
     */
    public function updateStatus($status) {
        $this->setStatus($status);
        return $this->updateProperty('status', $this->status);
    }

    /**
     * Validate whether the given id is Integer and assign
     * @param $constituency_id
     * @return $this
     */
    public function setConstituencyId($constituency_id){
        $constituency_id = filter_var ( $constituency_id, FILTER_VALIDATE_INT );
        if ($constituency_id!==false){
            $this->constituency_id = $constituency_id;
        }
        else{
            $this->errors[] = "Invalid Constituency Id" ;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConstituencyId(){
        return $this->constituency_id;
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

    /**
     * Validate whether the given id is Integer and assign
     * @param $added_by_user_id
     * @return $this
     */
    public function setAddedBy($added_by_user_id){
        $added_by_user_id = filter_var ( $added_by_user_id, FILTER_VALIDATE_INT );
        if ($added_by_user_id!==false){
            $this->added_by = $added_by_user_id;
        }
        else{
            $this->errors[] = "Invalid Added By User Id" ;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddedBy(){
        return $this->added_by;
    }

    public function getMediaCount(){
        return $this->media_count;
    }

    /**
     * @return bool
     */
    public function save(){
        $db = static::getDBInstance();
        $query = "insert into " . self::TABLE_NAME
            . " (headline,summary, org_id, constituency_id, user_id,added_by) "
            . " values(:headline,:summary, :org_id, :constituency_id, :user_id,:added_by);";
        $updated = $db->modify( $query, array(
            "headline" => $this->headline,
            "summary" => $this->summary,
            "org_id" => $this->org_id,
            "constituency_id" => $this->constituency_id,
            "user_id" => $this->user_id,
            "added_by" => $this->added_by
        ));

        $this->setId($db->getLastInsertid());
        return $updated;
    }

    public static function createTable(){
        $db = static::getDBInstance();
        return $db->modify("CREATE TABLE `" . static::TABLE_NAME . "` (
                          `id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                          `headline` text NOT NULL,
                          `summary` text NOT NULL,
                          `org_id` int(11) UNSIGNED NOT NULL,
                          `constituency_id` int(11) UNSIGNED NOT NULL,
                          `user_id` int(11) UNSIGNED NOT NULL,
                          `added_by` int(11) UNSIGNED NOT NULL,
                          `status` int(11) UNSIGNED DEFAULT '0',
                           PRIMARY KEY (`id`),
                          INDEX (org_id, constituency_id),
                          FOREIGN KEY (constituency_id) REFERENCES " . Constituency::TABLE_NAME . "(id) ON DELETE CASCADE,
                          FOREIGN KEY (org_id) REFERENCES " . Organization::TABLE_NAME . "(id) ON DELETE CASCADE
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    public function getMediaFolder(){
        if($this->id && $this->org_id){
            $org_data_dir = Config::DATA_DIR . "org_" . $this->org_id . "/";
            $feed_dir = $org_data_dir . "feed_" . $this->id . "/";
            $media_dir = $feed_dir . "media/";

            if(!file_exists($media_dir)){
                if(!file_exists($feed_dir)){
                    if(!file_exists($org_data_dir)){
                        mkdir($org_data_dir);
                    }
                    mkdir($feed_dir);
                }
                mkdir($media_dir);
            }
            return $media_dir;
        }
        else{
            // will/should not happen; just as precaution
            return "/tmp/";
        }
    }


    /**
     * Get rows from the database as Object
     * @param $org_id
     * @param int $start
     * @param int $length
     * @return array
     */
    public static function getListByOrganization($org_id, $start=0, $length=1000000) {
        $org_id = filter_var($org_id, FILTER_VALIDATE_INT);
        $offset = filter_var($start, FILTER_VALIDATE_INT);
        $limit = filter_var($length, FILTER_VALIDATE_INT);

        if ($org_id === false || $offset === false || $limit === false || $offset < 0 || $limit < 1) {
            return null;
        }
        $db = static::getDBInstance();

        $query = "select f.id,f.headline,f.summary,f.org_id,f.constituency_id,f.user_id,f.added_by,f.status "
            . " ,count(ff.id) as media_count "
            . " from " . static::TABLE_NAME . " as f"
            . " left join " . FeedFile::TABLE_NAME . " as ff on ff.feed_id=f.id"
            ." where org_id=:org_id and status=0 "
            . " group by f.id"
            . " order by f.id LIMIT :offset,:row_limit";

        $objects = $db->fetchObjectList(get_called_class(), $query, array(
                array("offset", $offset, PDO::PARAM_INT),
                array("row_limit", $limit, PDO::PARAM_INT),
                array("org_id", $org_id, PDO::PARAM_INT),
            )
            , "bindParam"
        );

        return $objects;
    }

    /**
     * Get total number of rows
     * @param $org_id
     * @return int
     */
    public static function getTotalRowsByOrganization($org_id){
        $org_id = filter_var($org_id, FILTER_VALIDATE_INT);

        if ($org_id === false) {
            return null;
        }

        $db = static::getDBInstance();
        $query = "select * from " . static::TABLE_NAME . " where org_id=:org_id and status=0 ";
        return $db->getRowCount($query, array("org_id"=> $org_id));
    }

    /**
     * @param $org_id
     * @param $id_val
     * @return $this
     */
    public static function findByOrgAndId($org_id, $id_val) {
        $org_id = filter_var($org_id, FILTER_VALIDATE_INT);
        $id_val = filter_var($id_val, FILTER_VALIDATE_INT);
        if($id_val !== false && $org_id !== false){
            $db = static::getDBInstance();
            $query = "select * from " . static::TABLE_NAME . " where org_id=:org_id and id=:id;";
            return $db->fetchObject(get_called_class(), $query, array (
                "org_id" => $org_id,
                "id" => $id_val
            ) );
        }
        return null;
    }

}