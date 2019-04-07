<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Controllers;


use App\Auth;
use Core\AppError;
use Core\AuthController;
use Core\Permission;
use Core\Role;
use Core\View;

class RoleController extends AuthController {

    public function __construct($route_params) {
        parent::__construct($route_params);
        $this->allowed_roles = array('super_admin');
    }

    /**
     * @throws \Exception
     */
    protected function listAction(){
        $roles = Role::all(true);
        $permissions = Permission::all();
        $this->masterView('Role/list.php',
            ["roles" => $roles, "permissions" => $permissions]);
    }

    protected function syncPermissionsAction(){
        if($_SERVER['REQUEST_METHOD']){
            Permission::setupDefault();
            Role::setupDefault();
            View::displayJsonSuccess("Permissions refreshed");
        }
    }
}
