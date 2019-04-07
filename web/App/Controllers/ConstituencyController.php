<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Controllers;

use App\Auth;
use App\Models\Constituency;
use Core\AuthController;
use App\Models\Organization;
use Core\Security\Validator;
use Core\View;

class ConstituencyController extends AuthController{

    /**
     * Add organization
     * @throws \Exception
     */
    protected function addAction(){
        Auth::throwErrorIfPermissionDenied("add_Constituency");

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if(Auth::user()->hasRole('super_admin')){
                $organization_list = Organization::getObjectList();
                if($organization_list){
                    $this->masterView('Constituency/add.php',[
                        'organization_list' => $organization_list,
                    ]);
                }
                else{
                    View::throwAppError("Please add the organization list first");
                }
            }
            else{
                $this->masterView('Constituency/add.php');
            }
        }
        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $required_params = array('name', "strength");
            if(Validator::checkAllParamsExists($required_params) !== true){
                View::throwAppError("Please fill all the inputs");
            }

            $organization = null;
            if(Auth::user()->hasRole('super_admin')){
                $organization = Organization::findById($_POST['organization']);
            }
            else{
                $organization = Organization::findByUserId(Auth::user()->getId());
            }

            $constituency_name = trim($_POST['name']);
            $existing_constituency_count = Constituency::getCountByOrganization($organization->getId());
            if(!$organization->getMaxConstituencyCount() ||
                $existing_constituency_count >= $organization->getMaxConstituencyCount()){
                // allow only the maximum constituency
                View::throwAppError("You have reached maximum constituency allowed!");
            }

            $existing_constituency = Constituency::findByOrgAndName($organization->getId(), $constituency_name);
            if($existing_constituency){
                View::throwAppError("Duplicate Constituency");
            }

            $constituency = Constituency::getInstance()
                ->setOrgId($organization->getId())
                ->setStrength($_POST['strength'])
                ->setName($constituency_name);

            if(!$constituency->getErrors() && $constituency->save()){
                View::displayJsonSuccess("Added");
            }
            else{
                View::throwAppError("No entries added");
            }
        }
    }

    /**
     * @throws \Exception
     */
    protected function listAction(){
        Auth::throwErrorIfPermissionDenied("view_Constituency");

        $constituency_list = null;

        if(Auth::user()->hasRole('super_admin')) {
            $constituency_list = Constituency::getObjectList();
        }
        else{
            $organization = Organization::findByUserId(Auth::user()->getId());
            if($organization && $organization instanceof Organization) {
                $constituency_list = Constituency::getListByOrganization($organization->getId());
            }
        }
        $this->masterView('Constituency/list.php', [
            'constituency_list' => $constituency_list
        ]);
    }

    /**
     * @throws \Exception
     */
    protected function deleteAction(){
        Auth::throwErrorIfPermissionDenied("delete_Constituency");

        if(isset($_POST['id_list']) && $_POST['id_list'] && is_array($_POST['id_list'])){
            $deleted_id_list = [];
            if(Auth::user()->hasRole('super_admin')){
                foreach ($_POST['id_list'] as $id_to_delete){
                    $constituency = Constituency::findById($id_to_delete);
                    if($constituency && $constituency instanceof Constituency && $constituency->delete()){
                        $deleted_id_list[] = $id_to_delete ;
                    }
                }
            }
            else if(Auth::user()->hasRole('admin')){
                $organization = Organization::findByUserId(Auth::user()->getId());
                if($organization and $organization instanceof Organization){
                    foreach ($_POST['id_list'] as $id_to_delete){
                        $constituency = Constituency::findByOrgAndId($organization->getId(), $id_to_delete);
                        if($constituency && $constituency instanceof Constituency and $constituency->delete()){
                            $deleted_id_list[] = $id_to_delete ;
                        }
                    }
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
            $constituency = Constituency::findById($_POST['id']);
            if($constituency && $constituency instanceof Constituency && $constituency->delete()){
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