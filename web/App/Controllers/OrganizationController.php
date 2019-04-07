<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Controllers;


use App\Auth;
use App\Models\Organization;
use Core\AuthController;
use Core\Security\Validator;
use Core\View;

class OrganizationController extends AuthController{

    public function __construct($route_params) {
        parent::__construct($route_params);
        $this->allowed_roles = array('super_admin');
    }

    /**
     * @throws \Exception
     */
    protected function addAction(){
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->masterView('Organization/add.php');
        }
        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $required_params = array('name', "max_constituency");
            if(Validator::checkAllParamsExists($required_params) !== true){
                View::throwAppError("Please fill all the inputs");
            }

            $org_name = trim($_POST['name']);
            $existing_entry = Organization::findByName($org_name);
            if($existing_entry){
                View::throwAppError("Duplicate Entry");
            }

            $org = Organization::getInstance()
                ->setName($org_name)
                ->setMaxConstituencyCount($_POST['max_constituency']);

            if(!$org->getErrors() && $org->save()){
                View::displayJsonSuccess("Added");
            }
            else if($org->getErrors()){
                View::throwAppError($org->getErrors());
            }
            else{
                View::throwAppError("Unable to add the entry");
            }
        }
    }

    /**
     * @throws \Exception
     */
    protected function listAction() {
        $organization_list = Organization::getObjectList();
        $this->masterView('Organization/list.php',[
            'organization_list' => $organization_list
        ]);
    }

    /**
     * @throws \Exception
     */
    protected function deleteAction() {

        if(isset($_POST['id_list']) && $_POST['id_list'] && is_array($_POST['id_list'])){
            $deleted_id_list = [];
            foreach ($_POST['id_list'] as $id_to_delete){
                $organization = Organization::findById($id_to_delete);
                if($organization && $organization instanceof Organization && $organization->delete()){
                    $deleted_id_list[] = $id_to_delete ;
                }
            }

            if($deleted_id_list){
                $response = array(
                    "success"=>  count($deleted_id_list). " entries deleted",
                    "deleted_id_list" => $deleted_id_list
                );
                View::displayJson($response);
            }
            else{
                View::throwAppError( "No Entries deleted!" );
            }
        }
        else if(isset($_POST['id']) && $_POST['id']){
            $organization = Organization::findById($_POST['id']);
            if($organization && $organization instanceof Organization && $organization->delete()){
                View::displayJsonSuccess("Deleted");
            }
            else{
                View::throwAppError( "Unable to delete!" );
            }
        }
        else{
            View::throwAppError( "Invalid Request!" );
        }
    }
}