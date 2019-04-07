<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Models;


use App\Config;
use Core\DataModel;
use Core\Permission;
use Core\Role;
use Core\View;

class Setup extends DataModel {

    /**
     * Function to check User Table exists
     * @return bool
     */

    public static function checkUserTableExists(){
        $db = static::getDBInstance();
        if($db->tableExists(User::TABLE_NAME)){
            return true;
        }
    }

    public static function isDatabaseAlreadyConfigured(){
        return (static::checkUserTableExists());
    }

    public static function install(){
        if (!file_exists(Config::DATA_DIR)) {
            View::securePrint("Please create directory with write permission to the apache user : "
                . Config::DATA_DIR);
            exit(1);
        }

        User::createTable();
        Config::createTable();
        Organization::createTable();
        Constituency::createTable();
        OrganizationUserMap::createTable();
        Permission::createTable();
        Role::createTables();

        FeederInfo::createTable();

        Feed::createTable();
        FeedFile::createTable();

        Task::createTable();
        TaskFile::createTable();
        TaskFeedback::createTable();

        Permission::setupDefault();
        Role::setupDefault();

        return true;
    }


}