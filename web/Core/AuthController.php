<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace Core;
use App\Auth;
use App\Models\Organization;
use Core\Flash;

/**
 * Base Auth Controller
 * Class AuthController
 * @package Core
 */
abstract class AuthController extends Controller {
    protected $allowed_roles = array();

    //Properties
    protected $login_url;
    protected $header_view;
    protected $footer_view;

    public function __construct($route_params) {
        parent::__construct($route_params);
        $this->login_url = '/user/login';
        $this->header_view = 'User/header.php';
        $this->footer_view = 'User/footer.php';
    }

    /**
     * Require the user be logged in before giving access to the requested page
     * @return bool
     */
    public function isLoggedIn(){
        if(Auth::user()){
            return true;
        }
        else {
            Auth::rememberRequestedPage();
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
                // AJAX Requests
                Utils::redirectJSON($this->login_url);
            }
            else {
                // NON-AJAX Requests
                static::redirect($this->login_url);
            }
        }
        return false;
    }

    /**
     * executed before executing any function suffixed "Action()"
     * @return bool
     */
    protected function before() {
        if($this->isLoggedIn()){

            if(Auth::user()->hasRole('super_admin')){
                // super admin has highest privilege & returns always true for any action
                return true;
            }

            // controller level role check
            if($this->allowed_roles){
                // if allowed roles defined, checks user has a role to access
                $in_allowed_role = false;
                foreach ($this->allowed_roles as $role){
                    if(Auth::user()->hasRole($role)){
                        $in_allowed_role = true;
                        break;
                    }
                }

                if(!$in_allowed_role){
                    return false;
                }
            }
            else{
                return true;
            }
        }
        return false;
    }

    /**
     * Function to automatically include Header & Footer in  panel
     * @param $view
     * @param array $data
     * @throws \Exception
     */
    public function masterView($view, $data=[]){
        $flash_messages = Flash::getMessages();

        //Header:
        View::render($this->header_view, [
            'flash_messages' => $flash_messages
        ]);

        //Page view
        View::render($view, $data);

        //Footer:
        View::render($this->footer_view);

    }


    /**
     * @throws \Core\AppError
     */
    protected function __getOrganization() {
        $organization = null;

        if (Auth::user()->hasRole('super_admin')) {
            if (isset($_REQUEST['org_id']) && $_REQUEST['org_id']) {
                /** @var Organization $organization */
                $organization = Organization::findById($_REQUEST['org_id']);
                if (!$organization) {
                    View::throwAppError("Organization details not found!");
                }
            }
            else {
                View::throwAppError("Invalid Request");
            }
        }
        else {
            $organization = Organization::findByUserId(Auth::user()->getId());
            if (!$organization) {
                View::throwAppError("Organization details not found!");
            }
        }

        return $organization;
    }
}