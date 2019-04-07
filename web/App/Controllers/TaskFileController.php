<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Controllers;


use App\Auth;
use App\Models\Feed;
use App\Models\FeedFile;
use App\Models\Organization;
use App\Models\Task;
use App\Models\TaskFile;
use Core\AuthController;
use Core\Utils;
use Core\View;

class TaskFileController extends AuthController {

    /**
     * @throws \Core\AppError
     */
    protected function viewAction(){
        if(!Auth::user()->can('view_TaskFile') && !Auth::user()->can('viewAssigned_TaskFile')){
            View::throwAppError(" You are not authorized ");
        }

        if(!isset($this->route_params["id"])){
            View::throwAppError("Invalid Request");
        }

        if(!isset($_GET["task_id"]) || !$_GET["task_id"]){
            View::throwAppError("Invalid Request");
        }

        $organization = $this->__getOrganization();
        $task = null;
        if(Auth::user()->can('view_TaskFile')){
            $task = Task::findByOrgAndId($organization->getId(), $_GET['task_id']);
        }
        else if(Auth::user()->can('viewAssigned_TaskFile')){
            $task = Task::findByMultipleProperties(
                array(
                    "id" => $_GET['task_id'],
                    "org_id" => $organization->getId(),
                    "responsibility" => Auth::user()->getId(),
                )
            );
        }

        if(!$task){
            View::throwAppError("Invalid Request");
        }

        $task_file = TaskFile::findByTaskIdAndId($task->getId(), $this->route_params["id"]);
        if(!$task_file){
            View::throwAppError("Invalid File");
        }

        $file_path  = $task->getMediaFolder() . $task_file->getFilename();
        if(!file_exists($file_path)){
            View::throwAppError("File not found");
        }

        header('Content-Type: ' . Utils::getFileMimeType($file_path));
        header('Content-Disposition: filename="' . $task_file->getFilename() . '"');
        readfile($file_path);
        exit();
    }
}