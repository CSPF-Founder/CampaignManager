<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Controllers;
use App\Auth;
use Core\AuthController;


class HomeController extends AuthController {

    /**
     * Home page
     * @throws \Exception
     */
    protected function indexAction(){
        if(Auth::user()->hasRole('super_admin')){
            $this->masterView('default.php');
        }
        else if(Auth::user()->can('view_Task')){
            static::redirect('/task/list');
        }
        else if(Auth::user()->can('view_Feed')){
            static::redirect('/feed/list');
        }
        else if(Auth::user()->can('viewAssigned_Task')){
            static::redirect('/task/list-assigned');
        }
        else if(Auth::user()->can('add_Feed')){
            static::redirect('/feed/add');
        }
        else{
            $this->masterView('default.php');
        }
    }
}