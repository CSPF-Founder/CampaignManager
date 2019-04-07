<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Controllers;


use App\Auth;
use App\Config;
use App\Models\Constituency;
use App\Models\FeederInfo;
use App\Models\Organization;
use App\Models\OrganizationUserMap;
use App\Models\User;
use Core\AppError;
use Core\AuthController;
use Core\Role;
use Core\Security\CSRF;
use Core\Security\Validator;
use Core\View;

class UserController extends AuthController {
    protected $login_url;
    protected $login_view;
    protected $login_success_page;

    public function __construct($route_params) {
        parent::__construct($route_params);
        $this->login_url = '/user/login';
        $this->login_view = 'User/login.php';
        $this->login_success_page = '/feed/list';
    }

    /**
     * Display login form
     * @throws \Exception
     */
    public function loginNonFilteredAction(){

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            View::render('User/login.php');
        }
        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $requiredParams = array('username', 'password');
            if(Validator::checkAllParamsExists($requiredParams) === false) {
                View::throwAppError("Invalid Request");
            }

            if(Auth::attempt($_POST['username'], $_POST['password']) === true){
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
                    //View::displayJsonSuccess("Logged In");
                    View::displayJson(
                      array("success" => "Logged In",
                          "csrf_token" => CSRF::get(),
                          "Cookie" => Config::SESSION_ID_NAME . '=' . session_id()
                      )
                    );
                }
                else{
                    static::redirect('/home/index');
                }
            }
            else{
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
                    View::throwAppError("Invalid Credentials");
                }
                else {
                    View::render($this->login_view, ['error_message' => 'Invalid Username/Password']);
                }
            }
        }
    }

    /**
     * Add User method
     * @throws AppError
     * @throws \Exception
     */
    protected function addAction(){
        Auth::throwErrorIfPermissionDenied("add_User");

        $organization_list = null;
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if(Auth::user()->hasRole('super_admin')){
                $organization_list = Organization::getObjectList();
                $constituency_list = Constituency::getObjectList();
                if($organization_list){
                    $this->masterView('User/add.php',[
                        'organization_list' => $organization_list,
                        'constituency_list' => $constituency_list
                    ]);
                }
                else{
                    View::throwAppError("Please add the organization list first");
                }
            }
            else{
                $organization = Organization::findByUserId(Auth::user()->getId());
                if($organization and $organization instanceof Organization){
                    $constituency_list = Constituency::getListByOrganization($organization->getId());
                    $this->masterView('User/add.php',[
                        'constituency_list' => $constituency_list
                    ]);
                }
                else{
                    View::throwAppError("Invalid Request");
                }
            }
        }
        else if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $requiredParams = array('name', 'username', 'password', 'role');
            if(Validator::checkAllParamsExists($requiredParams) !== true) {
                View::displayJsonError("Please fill all the inputs");
            }

            /** @var Role $role */
            $role = Role::findByKeyword($_POST['role']);
            if(!$role){
                View::throwAppError("Invalid role specified");
            }

            $organization = $this->__getOrganization();

            $feeder_info = null;
            if($role->keyword === "feeder"){
                $feeder_info = $this->__prepareFeederInfo($organization);
            }

            $permission = "add_" . $_POST['role'];
            if(!Auth::user()->can($permission)){
                View::throwAppError("You don't have enough permission");
            }

            $user = $this->__storeUser();
            if(!$user || !$user->getId()){
                View::throwAppError("Unable to add user");
            }

            if($role->keyword === "feeder"){
                if(!$feeder_info){
                    $user->delete();  // delete the user entry
                    View::throwAppError("Unable to add the user");
                }

                $feeder_info->setUserId($user->getId());
                if (!$feeder_info->save()) {
                    $user->delete();  // delete the user entry
                    View::throwAppError("Unable to add the user");
                }
            }

            if(!$user->assignRole($role)){
                $user->delete();  // delete the user entry
                View::throwAppError("Unable to assign the role");
            }

            //Store organization user info:
            if (!$this->__assignOrganization($organization, $user)) {
                $user->delete();  // delete the user entry
                View::throwAppError("Unable to add the user");
            }

            // reaches ony there is no error
            View::displayJsonSuccess("Added");

        }
        else{
            throw new AppError("Invalid Request", 403);
        }
    }

    /**
     * Internal function for adding User entry & returns the user object
     * @return User
     */
    private function __storeUser(){
        $user = User::getInstance()
            ->setName($_POST['name'])
            ->setUsername($_POST['username'])
            ->setPassword($_POST['password']);

        if(!$user->getErrors() && $user->save()){
            return $user;
        }
        else if ($user->getErrors()){
            View::displayJsonError($user->getErrors());
        }
        else{
            View::displayJsonError("Unable to add the user");
        }
        return null;
    }

    /**
     * internal function to save Organization type user
     * @param Organization $organization
     * @param User $user
     * @return bool
     */
    private function __assignOrganization(Organization $organization, User $user){
        if($organization && $user) {
            //Store user info:
            $orgUserInfo = OrganizationUserMap::getInstance()
                ->setUserId($user->getId())
                ->setOrgId($organization->getId());

            return $orgUserInfo->save();
        }
        return false;
    }

    /**
     * List users
     * @throws \Exception
     */
    protected function listAction(){
        Auth::throwErrorIfPermissionDenied("view_User");

        $user_list = null;
        if(Auth::user()->hasRole('super_admin')){
            $user_list = User::getObjectList();
        }
        else{
            $organization = Organization::findByUserId(Auth::user()->getId());
            if($organization){
                $user_list = $organization->getUserList();
            }
            else{
                View::throwAppError("Invalid Request");
            }
        }
        $this->masterView('User/list.php', [
            'user_list' => $user_list
        ]);
    }

    /**
     * Log out
     * Deletes session & redirects to the login page
     * Note: This function doesn't check whether the user is logged in or not
     */
    protected function logoutNonFilteredAction(){
        Auth::logout();
        static::redirect('/user/login');
    }

    /**
     * Change user password
     */
    protected function changePasswordAction(){

    }

    /**
     * @throws \Exception
     */
    protected function deleteAction() {
        Auth::throwErrorIfPermissionDenied("delete_User");

        if(isset($_POST['id_list']) && $_POST['id_list'] && is_array($_POST['id_list'])){
            $deleted_id_list = [];
            if(Auth::user()->hasRole('super_admin')){
                foreach ($_POST['id_list'] as $id_to_delete){
                    $user = User::findById($id_to_delete);
                    if($user && $user instanceof User && $user->delete()){
                        $deleted_id_list[] = $id_to_delete ;
                    }
                }
            }
            else if(Auth::user()->hasRole('admin')){
                $organization = Organization::findByUserId(Auth::user()->getId());
                if($organization and $organization instanceof Organization){
                    foreach ($_POST['id_list'] as $id_to_delete){
                        $user = User::findByOrgAndId($organization->getId(), $id_to_delete);
                        if($user && $user instanceof User and $user->delete()){
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
            $user = User::findById($_POST['id']);
            if($user && $user instanceof User && $user->delete()){
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

    /**
     * @param Organization $organization
     * @return FeederInfo
     * @throws AppError
     */
    private function __prepareFeederInfo(Organization $organization) {
        $requiredParams = array('constituency', 'mobile_number', 'email', "feeder_type");
        if(Validator::checkAllParamsExists($requiredParams) !== true) {
            View::throwAppError("Please fill all the inputs");
        }

        $constituency = null;
        if(!isset($_POST['constituency']) || !$_POST['constituency']){
            View::throwAppError("Invalid Constituency");
        }
        else if(Auth::user()->hasRole('super_admin')){
            $constituency = Constituency::findById($_POST['constituency']);
        }
        else{
            $constituency = Constituency::findByOrgAndId($organization->getId(), $_POST['constituency']);
        }

        if(!$constituency || !$constituency instanceof Constituency){
            View::throwAppError("Invalid Constituency");
        }

        $feeder_info = FeederInfo::getInstance()
            ->setConstituencyId($constituency->getId())
            ->setMobileNumber($_POST['mobile_number'])
            ->setEmail($_POST['email'])
            ->setFeederType($_POST['feeder_type']);

        if($feeder_info->getErrors()){
            View::throwAppError($feeder_info->getErrors());
        }

        return $feeder_info;
    }
}