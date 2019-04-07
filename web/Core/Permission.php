<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */


namespace Core;


class Permission {

    #properties
    public $keyword;
    public $id;

    public static function getDefaultList(){
        return array(
            "view_Feed",
            "add_Feed",
            "edit_Feed",
            "delete_Feed",

            "view_FeedFile",

            "view_Constituency",
            "add_Constituency",
            "edit_Constituency",
            "delete_Constituency",

            "view_User",
            "add_User",
            "edit_User",
            "delete_User",

            "view_Feed",
            "add_Feed",
            "edit_Feed",
            "delete_Feed",

            "view_Task",
            "add_Task",
            "edit_Task",
            "delete_Task",

            "view_TaskFile",

            "viewAssigned_Task",
            "editAssigned_Task",
            "viewAssigned_TaskFile",

            // Other Permissions
            "list_admin",
            "add_admin",
            "edit_admin",
            "delete_admin",

            "list_sub_admin",
            "add_sub_admin",
            "edit_sub_admin",
            "delete_sub_admin",

            "list_feeder",
            "add_feeder",
            "edit_feeder",
            "delete_feeder",

            "addAsFeeder_Feed",
        );
    }

    public function getDescription() {
        return ucwords(str_replace('_', ' ', $this->keyword));
    }

    /*
     * Get object using keyword
     */
    public static function findByKeyword($keyword){
        $db = DataModel::getDBInstance();
        $query = "select * from permissions where keyword=:keyword;";
        return $db->fetchObject(get_called_class(), $query, array ("keyword" => $keyword) );
    }

    /**
     * add new permission
     * @param $keyword
     * @return Permission
     */
    public static function addNew($keyword){
        if(!static::findBykeyword($keyword)) {
            $db = DataModel::getDBInstance();
            $query = "INSERT INTO permissions(keyword) VALUES (:keyword)";
            if ($db->modify($query, array("keyword" => $keyword))) {
                $permission = new Permission();
                $permission->keyword = $keyword;
                $permission->id = $db->getLastInsertid();
                return $permission;
            }
        }
        return null;
    }

    /*
     * Add list of permissions
     */
    public static function addList($keyword_list){
        if($keyword_list){
            foreach ($keyword_list as $keyword){
                static::addNew($keyword);
            }
        }
    }

    /**
     * Add default entries to the database
     */
    public static function setupDefault(){
        static::addList(static::getDefaultList());
    }

    public static function createTable(){
        $db = DataModel::getDBInstance();
        return $db->modify("CREATE TABLE permissions (
                      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                      `keyword` varchar(64) NOT NULL,
                      PRIMARY KEY (id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    /*
     * return all permissions
     */
    public static function all(){
        $db = DataModel::getDBInstance();
        $query = "select * from permissions";
        $objects = $db->fetchObjectList(get_called_class(), $query);
        return $objects;
    }

    /*
     * return all permissions
     */
    public static function allIds(){
        $db = DataModel::getDBInstance();
        $query = "select id from permissions";
        $rows = $db->fetch($query);
        if($rows){
            $ids = array();
            foreach ($rows as $row){
                $ids [] = $row["id"];
            }
            return $ids;
        }
        return [];
    }
}