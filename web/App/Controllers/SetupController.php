<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Controllers;


use App\Config;
use App\Models\Setup;
use App\Models\User;
use Core\AppError;
use Core\Controller;
use Core\Role;
use Core\Security\Validator;
use Core\View;

class SetupController extends Controller{

    /**
     * SetupController constructor.
     * @param $route_params
     * @throws AppError
     */
    public function __construct($route_params) {
        parent::__construct($route_params);
        //If table exists already return false;
        try{
            if(Config::ALLOW_SETUP){
                if(Setup::isDatabaseAlreadyConfigured()){
                    View::throwAppError("Database is already configured, Please delete the db & create db(not the tables) to reconfigure", 403);
                }
            }
            else{
                View::throwAppError("Setup is disabled", 403);
            }
        }
        catch (\PDOException $e){
            View::throwAppError("Database connection error");
        }
    }

    /**
     * Default page
     * @throws \Exception
     */
    public function indexNonFilteredAction(){
        if ($_SERVER['REQUEST_METHOD'] === 'GET'){
            View::render("Setup/index.php");
        }
        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $requiredParams = array("name", "username", "password");
            if (!Validator::checkAllParamsExists($requiredParams, "POST")) {
                $this->displayErrorFormNonFilteredAction("Please fill all the inputs");
            }
            else{
                $user = User::getInstance()
                ->setName($_POST['name'])
                ->setUsername($_POST['username'])
                ->setPassword($_POST['password']);
                if($user->getErrors()){
                    $this->displayErrorFormNonFilteredAction($user->getErrors());
                }
                else{
                    Setup::install();
                    if( $user->save()){
                        $role = Role::findByKeyword("super_admin");
                        if($role && $user->assignRole($role)){
                            View::render("Setup/success.php");
                        }
                        else{
                            View::securePrint("Unable to assign role to the user");
                        }
                    }
                    else{
                        $this->displayErrorFormNonFilteredAction("Unable to add the user");
                    }
                }
                
            }
            
        }
    }

    /**
     * Error Page
     * @param $error_message
     * @throws \Exception
     */
    public function displayErrorFormNonFilteredAction($error_messages){
        View::render("Setup/index.php", ['error_messages' => $error_messages]);
    }
}
