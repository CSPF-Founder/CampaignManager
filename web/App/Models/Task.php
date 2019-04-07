<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Models;

use App\Config;
use App\Models\Enums\TaskCriticality;
use App\Models\Enums\TaskProperties;
use App\Models\Enums\TaskStatus;
use Core\DataModel;
use Core\Security\Validator;
use DateTime;
use PDO;

class Task extends DataModel {
    const TABLE_NAME = "tasks";

    const PROPERTIES = array("id","headline", "summary", "org_id","constituency_id","status",
        "created_date", "responsibility", "due_date", "criticality");

    //properties
    protected $headline;
    protected $summary;
    protected $org_id;
    protected $constituency_id;
    protected $status;
    protected $created_date;
    protected $responsibility;
    protected $due_date;
    protected $criticality;

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
        if ($status!==false && in_array($status, TaskStatus::getAllKeys())){
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

    public function getStatusText(){
        return TaskStatus::getString($this->status);
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
     * @param $responsibility
     * @return $this
     */
    public function setResponsibility($responsibility){
        $responsibility = filter_var ( $responsibility, FILTER_VALIDATE_INT );
        if ($responsibility!==false){
            $this->responsibility = $responsibility;
        }
        else{
            $this->errors[] = "Invalid Responsibility" ;
        }
        return $this;
    }

    public function updateResponsibility($responsibility){
        $this->setResponsibility($responsibility);

        return $this->updateProperty('responsibility', $this->responsibility);
    }

    /**
     * @return mixed
     */
    public function getResponsibility(){
        return $this->responsibility;
    }

    public function setDueDate($due_date){
        if (is_string($due_date)) {
            $due_date = new DateTime($due_date);
        }

        $min_date = new DateTime();
        if($due_date && $due_date > $min_date){
            $this->due_date = $due_date->format("Y-m-d");
        }
        else{
            $this->errors[]= "Invalid Due date";
        }
        return $this;
    }

    public function getDueDate($date_format="d/m/Y"){
        if ($this->due_date) {
            $due_date = new DateTime($this->due_date);
            return $due_date->format($date_format);
        }
        return null;
    }

    public function updateDueDate($due_date){
        $this->setDueDate($due_date);

        return $this->updateProperty('due_date', $this->due_date);
    }

    public function updateSummary($summary){
        $this->setSummary($summary);

        return $this->updateProperty('summary', $this->summary);
    }


    public function updateHeadline($headline){
        $this->setHeadline($headline);

        return $this->updateProperty('headline', $this->headline);
    }


    public function getCreatedDate($date_format="d/m/Y"){
        if ($this->created_date) {
            $created_date = new DateTime($this->created_date);
            return $created_date->format($date_format);
        }
        return null;
    }

    /**
     * @param $criticality
     * @return $this
     */
    public function setCriticality($criticality){
        $criticality = filter_var ( $criticality, FILTER_VALIDATE_INT );
        if ($criticality!==false && in_array($criticality, TaskCriticality::getAllKeys())){
            $this->criticality = $criticality;
        }
        else{
            $this->errors[] = "Invalid Criticality" ;
        }
        return $this;
    }

    public function updateCriticality($criticality){
        $this->setCriticality($criticality);

        return $this->updateProperty('criticality', $this->criticality);
    }

    /**
     * @param $format
     * @return mixed
     */
    public function getCriticality($format=null){
        if($format == 'text'){
            return TaskCriticality::getString($this->criticality);
        }
        else{
            return $this->criticality;
        }
    }

    public function getMediaCount(){
        return $this->media_count;
    }

    public function getMediaFolder(){
        if($this->id && $this->org_id){
            $org_data_dir = Config::DATA_DIR . "org_" . $this->org_id . "/";
            $task_dir = $org_data_dir . "task_" . $this->id . "/";
            $media_dir = $task_dir . "media/";

            if(!file_exists($media_dir)){
                if(!file_exists($task_dir)){
                    if(!file_exists($org_data_dir)){
                        mkdir($org_data_dir);
                    }
                    mkdir($task_dir);
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
     * @return bool
     */
    public function save(){
        $db = static::getDBInstance();
        $query = "insert into " . self::TABLE_NAME
            . " (headline,summary, org_id, constituency_id, responsibility,due_date, criticality) "
            . " values(:headline,:summary, :org_id, :constituency_id, :responsibility,:due_date,:criticality);";
        $updated = $db->modify( $query, array(
            "headline" => $this->headline,
            "summary" => $this->summary,
            "org_id" => $this->org_id,
            "constituency_id" => $this->constituency_id,
            "responsibility" => $this->responsibility,
            "due_date" => $this->due_date,
            "criticality" => $this->criticality
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
                          `responsibility` int(11) UNSIGNED NOT NULL,
                          `created_date` date default now(),
                          `due_date` date NOT NULL,
                           `criticality` int(11) UNSIGNED ,
                          `status` int(11) UNSIGNED DEFAULT '0',
                           PRIMARY KEY (`id`),
                          INDEX (org_id, constituency_id),
                          FOREIGN KEY (constituency_id) REFERENCES " . Constituency::TABLE_NAME . "(id) ON DELETE CASCADE,
                          FOREIGN KEY (org_id) REFERENCES " . Organization::TABLE_NAME . "(id) ON DELETE CASCADE
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
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
        $query = "select * from " . static::TABLE_NAME
            . " where org_id=:org_id and due_date >= now() and status=:status";

        return $db->getRowCount($query, array(
            "status" => TaskStatus::DEFAULT_STATUS,
            "org_id"=> $org_id
        ));
    }


    /**
     * @param $org_id
     * @param $filter
     * @return array
     */
    public static function getListByOrganization($org_id, $filter=null) {
        $order_by = "criticality";
        $offset=0;
        $limit=1000000;

       if($filter && is_array($filter)){
           if(array_key_exists("order_by", $filter)){
               $property_name = static::convertUnsafePropertyNameToPredefined($filter["order_by"]);
               if($property_name){
                   $order_by = $property_name;
               }
               else{
                   return null;
               }
           }

           if(array_key_exists("offset", $filter)){
               $offset = filter_var($filter['offset'], FILTER_VALIDATE_INT);
           }
           if(array_key_exists("limit", $filter)){
               $limit = filter_var($filter['limit'], FILTER_VALIDATE_INT);
           }

           if ($offset === false || $limit === false || $offset < 0 || $limit < 1) {
               return null;
           }
       }

        $org_id = filter_var($org_id, FILTER_VALIDATE_INT);

        if ($org_id === false || $offset === false || $limit === false || $offset < 0 || $limit < 1) {
            return null;
        }

        if(!in_array($order_by, static::PROPERTIES)){
            // double check the property name to avoid SQL injection
            return null;
        }

        $db = static::getDBInstance();
        $prepared_params =  array(
            array("offset", $offset, PDO::PARAM_INT),
            array("row_limit", $limit, PDO::PARAM_INT),
            array("org_id",$org_id,PDO::PARAM_INT)
        );

        $where_filter = null;

        if($filter && is_array($filter) && array_key_exists("overdue_tasks", $filter) && $filter["overdue_tasks"]){
            // overdue list
            $where_filter =  " where t.org_id=:org_id and t.due_date < now() and t.status=:status ";

            $prepared_params[] = array(
                "status",
                TaskStatus::DEFAULT_STATUS,
                PDO::PARAM_INT
            );
        }
        elseif($filter && is_array($filter) && array_key_exists("tasks_to_verify", $filter) && $filter["tasks_to_verify"]){
            //completed task that needs to be verified list
            $where_filter =  " where t.org_id=:org_id and t.status=:status ";

            $prepared_params[] = array(
                "status",
                TaskStatus::COMPLETED_UNVERIFIED,
                PDO::PARAM_INT
            );
        }
        else{
            $where_filter = " where t.org_id=:org_id and t.due_date >= now() and t.status=:status ";

            $prepared_params[] = array(
                "status",
                TaskStatus::DEFAULT_STATUS,
                PDO::PARAM_INT
            );
        }

        if(!$where_filter){
            return null;
        }

        if($filter && is_array($filter)){
            if(array_key_exists("constituency_id", $filter) && $filter["constituency_id"] && !is_array($filter["constituency_id"])){
                $where_filter .= " and t.constituency_id=:constituency_id";
                $prepared_params[] = array(
                    "constituency_id",
                    $filter["constituency_id"],
                    PDO::PARAM_INT
                );
            }

            if(array_key_exists("to_date", $filter) && $filter["to_date"] && !is_array($filter["to_date"])){
                $to_date = $filter["to_date"];
                if($to_date instanceof DateTime){
                    $where_filter .= " and t.created_date<=:to_date";
                    $prepared_params[] = array(
                        "to_date",
                        $to_date->format('Y-m-d'),
                        PDO::PARAM_STR
                    );
                }
            }

            if(array_key_exists("from_date", $filter) && $filter["from_date"] && !is_array($filter["from_date"])){
                $from_date = $filter["from_date"];
                if($from_date instanceof DateTime){
                    $where_filter .= " and t.created_date>=:from_date";
                    $prepared_params[] = array(
                        "from_date",
                        $from_date->format('Y-m-d'),
                        PDO::PARAM_STR
                    );
                }
            }

            if(array_key_exists("criticality", $filter) && $filter["criticality"] && !is_array($filter["criticality"])){
                $where_filter .= " and t.criticality=:criticality";
                $prepared_params[] = array(
                    "criticality",
                    $filter["criticality"],
                    PDO::PARAM_INT
                );
            }

            if(array_key_exists("keyword", $filter) && $filter["keyword"] && !is_array($filter["keyword"])){
                $keyword = str_replace(array('%', '_'), array('\%', '\_'), $filter["keyword"] );
                $keyword = '%'. $keyword . "%";
                $where_filter .= " and (t.headline like :keyword or t.summary like :keyword)";
                $prepared_params[] = array(
                    "keyword",
                    $keyword,
                    PDO::PARAM_STR
                );
            }
        }

        $query = "select t.id,t.headline,t.summary,t.org_id,t.constituency_id,t.responsibility,t.created_date,t.due_date,t.criticality,t.status "
            . " ,count(tf.id) as media_count "
            . " from " . static::TABLE_NAME . " as t"
            . " left join " . TaskFile::TABLE_NAME . " as tf on tf.task_id=t.id";

        if($filter && is_array($filter) && array_key_exists("constituency_strength", $filter) && $filter["constituency_strength"] && !is_array($filter["constituency_strength"])){
            $constituency_strength = $filter["constituency_strength"];
            $query .= " inner join " . Constituency::TABLE_NAME . " as c1 on c1.id=t.constituency_id";
            $where_filter .= " and c1.strength=:constituency_strength";
            $prepared_params[] = array(
                "constituency_strength",
                $constituency_strength,
                PDO::PARAM_INT
            );
        }

        $query .= $where_filter;
        $query .=" group by t.id";
        $query .=" order by t." . $order_by . "  LIMIT :offset,:row_limit";

        $objects = $db->fetchObjectList(get_called_class(), $query,$prepared_params, "bindParam");

        return $objects;
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

    /**
     * Get total number of rows
     * @param $org_id
     * @param $user_id
     * @return int
     */
    public static function getCountByOrganizationAndUser($org_id, $user_id){
        $org_id = filter_var($org_id, FILTER_VALIDATE_INT);
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);

        if ($org_id === false || $user_id === false) {
            return null;
        }

        $db = static::getDBInstance();
        $query = "select * from " . static::TABLE_NAME
            . " where org_id=:org_id and responsibility=:user_id and status<>:status_exception";
        return $db->getRowCount($query, array(
            "status_exception" => TaskStatus::COMPLETED_UNVERIFIED,
            "org_id"=> $org_id,
            "user_id" => $user_id
        ));
    }


    /**
     * @param $org_id
     * @param $user_id
     * @param int $start
     * @param int $length
     * @return array
     */
    public static function getListByOrganizationAndUser($org_id, $user_id, $start=0, $length=1000000) {
        $org_id = filter_var($org_id, FILTER_VALIDATE_INT);
        $offset = filter_var($start, FILTER_VALIDATE_INT);
        $limit = filter_var($length, FILTER_VALIDATE_INT);
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);

        if ($org_id === false || $user_id === false || $offset === false || $limit === false
            || $offset < 0 || $limit < 1) {
            return null;
        }
        $db = static::getDBInstance();

        $query = "select t.id,t.headline,t.summary,t.org_id,t.constituency_id,t.responsibility,t.created_date,t.due_date,t.criticality,t.status "
            . " ,count(tf.id) as media_count "
            . " from " . static::TABLE_NAME . " as t"
            . " left join " . TaskFile::TABLE_NAME . " as tf on tf.task_id=t.id"
            ." where org_id=:org_id and due_date >= now() and responsibility=:user_id and status=:status"
            . " group by t.id"
            . " order by t.criticality LIMIT :offset,:row_limit";

        $objects = $db->fetchObjectList(get_called_class(), $query, array(
                array("offset", $offset, PDO::PARAM_INT),
                array("row_limit", $limit, PDO::PARAM_INT),
                array("org_id", $org_id, PDO::PARAM_INT),
                array("user_id", $user_id, PDO::PARAM_INT),
                array("status", TaskStatus::DEFAULT_STATUS, PDO::PARAM_INT),
            )
            , "bindParam"
        );

        return $objects;
    }

    /**
     * @param $org_id
     * @param $user_id
     * @param int $start
     * @param int $length
     * @return array
     */
    public static function getOverdueListByOrganizationAndUser($org_id, $user_id, $start=0, $length=1000000) {
        $org_id = filter_var($org_id, FILTER_VALIDATE_INT);
        $offset = filter_var($start, FILTER_VALIDATE_INT);
        $limit = filter_var($length, FILTER_VALIDATE_INT);
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);

        if ($org_id === false || $user_id === false || $offset === false || $limit === false
            || $offset < 0 || $limit < 1) {
            return null;
        }
        $db = static::getDBInstance();


        $query = "select t.id,t.headline,t.summary,t.org_id,t.constituency_id,t.responsibility,t.created_date,t.due_date,t.criticality,t.status "
            . " ,count(tf.id) as media_count "
            . " from " . static::TABLE_NAME . " as t"
            . " left join " . TaskFile::TABLE_NAME . " as tf on tf.task_id=t.id"
            ." where due_date < now() and org_id=:org_id and responsibility=:user_id and status=:status"
            . " group by t.id"
            . " order by t.criticality LIMIT :offset,:row_limit";

        $objects = $db->fetchObjectList(get_called_class(), $query, array(
                array("offset", $offset, PDO::PARAM_INT),
                array("row_limit", $limit, PDO::PARAM_INT),
                array("org_id", $org_id, PDO::PARAM_INT),
                array("user_id", $user_id, PDO::PARAM_INT),
                array("status", TaskStatus::DEFAULT_STATUS, PDO::PARAM_INT),
            )
            , "bindParam"
        );

        return $objects;
    }

    /**
     * Never use User-Given property name (though property name check is there)
     * @param $properties_input
     * @return $this
     */
    public static function findByMultipleProperties($properties_input) {
        if(!$properties_input || !is_array($properties_input)){
            return null;
        }

        $unsafe_input_keys = array_keys($properties_input);
        if(!$unsafe_input_keys){
            return null;
        }

        foreach ($unsafe_input_keys as $unsafe_input_key){
            if(!in_array($unsafe_input_key,static::PROPERTIES)){
                return null; // even if one of the properties is invalid
            }
        }

        $properties_count = count($properties_input);
        $prepared_params = array();
        $query = "select * from " . static::TABLE_NAME . " where ";
        $i = 0;
        foreach ($properties_input as $unsafe_input_key => $property_value){
            if(is_array($property_value) || !$property_value){
                return null; // prevent invalid value
            }

            $predefined_property_name = static::convertUnsafePropertyNameToPredefined($unsafe_input_key);
            if($predefined_property_name){
                $query .= $predefined_property_name . "=:" . $predefined_property_name;
                if($properties_count !==1 && $i<($properties_count-1)){
                    $query .= " and " ;
                }
                $prepared_params[$predefined_property_name] = $property_value;
            }
            $i++;
        }

        if($prepared_params){
            $db = static::getDBInstance();
            return $db->fetchObject(get_called_class(), $query, $prepared_params);
        }

        return null;
    }

    /**
     * function to convert unsafe property name into a safe property name by doing validation
     * & getting from the predefined list
     * @param $unsafe_property_name
     * @return string
     */
    private static function convertUnsafePropertyNameToPredefined($unsafe_property_name){
        if(!$unsafe_property_name || !is_string($unsafe_property_name)){
            return null;
        }

        if(in_array($unsafe_property_name, static::PROPERTIES)){
            $temp_index = array_search($unsafe_property_name, static::PROPERTIES);
            $safe_property_name = static::PROPERTIES[$temp_index];
            if($safe_property_name && is_string($safe_property_name)){
                return $safe_property_name;
            }
        }
        return null;
    }

}