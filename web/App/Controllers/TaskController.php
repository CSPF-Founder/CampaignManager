<?php
/**
 * Copyright (c) 2019 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

namespace App\Controllers;


use App\Auth;
use App\Config;
use App\Models\Constituency;
use App\Models\Enums\ConstituencyStrength;
use App\Models\Enums\TaskCriticality;
use App\Models\Enums\TaskStatus;
use App\Models\Feed;
use App\Models\FeedFile;
use App\Models\Organization;
use App\Models\Task;
use App\Models\TaskFeedback;
use App\Models\TaskFile;
use App\Models\User;
use Core\AuthController;
use Core\Flash;
use Core\Role;
use Core\Security\Validator;
use Core\Utils;
use Core\View;

class TaskController extends AuthController {

    /**
     * @throws \Core\AppError
     * @throws \Exception
     */
    protected function addAction() {
        Auth::throwErrorIfPermissionDenied("add_Task");

        if ($_SERVER['REQUEST_METHOD'] === "GET") {
            $feed = null;
            $feed_files = null;
            $organization = $this->__getOrganization();
            if (isset($_GET['feed_id']) && $_GET['feed_id']) {
                $feed = Feed::findByOrgAndId($organization->getId(), $_GET['feed_id']);
                $feed_files = FeedFile::getObjectListByFeedId($feed->getId());
                if (!$feed) {
                    View::throwAppError("Invalid Request");
                }
            }

            $feeder_list = $organization->getUserListByRole('feeder');

            $this->masterView('Task/add.php', [
                'feed' => $feed,
                'feeder_list' => $feeder_list,
                'feed_files' => $feed_files
            ]);
        }
        else if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $required_params = array("headline", "summary", "responsibility", "due_date", "criticality");
            if (Validator::checkAllParamsExists($required_params) !== true) {
                View::throwAppError("Please fill all the inputs");
            }

            $organization = $this->__getOrganization();

            $source_feed = null;
            $source_feed_files = null;
            if(isset($_POST['feed_source']) && $_POST['feed_source']){
                $source_feed = Feed::findByOrgAndId($organization->getId(), $_POST['feed_source']);
                $source_feed_files = FeedFile::getObjectListByFeedId($source_feed->getId());
                if (!$source_feed) {
                    View::throwAppError("Invalid Request");
                }
            }

            $feeder = User::findByOrgAndId($organization->getId(), $_POST['responsibility']);
            if (!$feeder || !$feeder->hasRole('feeder')) {
                View::throwAppError("Invalid User Selected");
            }

            $constituency = Constituency::findByUserId($feeder->getId());
            if (!$constituency) {
                View::throwAppError("Constituency details not found!");
            }

            $task = Task::getInstance()
                ->setOrgId($organization->getId())
                ->setConstituencyId($constituency->getId())
                ->setResponsibility($feeder->getId())
                ->setHeadline($_POST['headline'])
                ->setSummary($_POST['summary'])
                ->setDueDate($_POST['due_date'])
                ->setCriticality($_POST['criticality']);

            if ($task->getErrors()) {
                Flash::addMessageListAndGoBack($task->getErrors(), Flash::WARNING);
            }
            else if (!$task->save()) {
                Flash::addAndGoBack("Unable to create the task", Flash::WARNING);
            }

            if($source_feed_files && $source_feed){
                /** @var FeedFile $media_file */
                foreach ($source_feed_files as $media_file){
                    $src_file = $source_feed->getMediaFolder() . $media_file->getFilename();
                    $destination_file = $task->getMediaFolder() . $media_file->getFilename();
                    if(link($src_file, $destination_file)){
                        $task_file = TaskFile::getInstance()
                            ->setFilename($media_file->getFilename())
                            ->setTaskId($task->getId());

                        $task_file->save();
                    }
                }
            }

            $uploaded_files = [];
            if (isset($_FILES) && isset($_FILES["files"])) {
                $file_count = count($_FILES['files']["name"]);
                $rearranged_files = Utils::rearrangeFilesArray($_FILES['files']);
                foreach ($rearranged_files as $file) {
                    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
                    if (strlen($file_extension) > 6) {
                        // file extension more than 6 characters, skip & eventually will stop addition.
                        continue;
                    }
                    $file_name = time() . "_" . Utils::getRandomBytes(16) . "." . $file_extension;
                    $destination_file = $task->getMediaFolder() . $file_name;
                    if (move_uploaded_file($file["tmp_name"], $destination_file)) {
                        $task_file = TaskFile::getInstance()
                            ->setFilename($file_name)
                            ->setTaskId($task->getId());
                        if ($task_file->save()) {
                            $uploaded_files[] = $file_name;
                        }
                    }
                }

                if(count($uploaded_files) !== $file_count && count($_FILES['files']["tmp_name"]) > 1){
                    foreach ($uploaded_files as $file_name) {
                        $destination_file = $task->getMediaFolder() . $file_name;
                        unlink($destination_file);
                    }
                    $task->delete();

                    Flash::addAndGoBack("Failed to upload the media file", Flash::WARNING);
                }
            }

            Flash::addAndGoBack("Created", Flash::SUCCESS);
        }
    }


    /**
     * @throws \Core\AppError
     * @throws \Exception
     */
    protected function listAction() {
        Auth::throwErrorIfPermissionDenied("view_Task");

        $organization = $this->__getOrganization();
        $filter = array();
        if(isset($_POST['filter_constituency']) && $_POST['filter_constituency']){
            $constituency_to_filter = Constituency::findByOrgAndId($organization->getId(), $_POST['filter_constituency']);
            if($constituency_to_filter && $constituency_to_filter instanceof Constituency){
                $filter['constituency_id'] = $constituency_to_filter->getId();
            }
        }
        if(isset($_POST['filter_keyword']) && $_POST['filter_keyword']){
            $filter['keyword'] = $_POST['filter_keyword'];
        }
        if(isset($_POST['filter_criticality']) && $_POST['filter_criticality']
            && array_key_exists($_POST['filter_criticality'], TaskCriticality::ENUM_LIST)){
            $filter['criticality'] = $_POST['filter_criticality'];
        }
        if(isset($_POST['filter_constituency_strength']) && $_POST['filter_constituency_strength']
            && array_key_exists($_POST['filter_constituency_strength'], ConstituencyStrength::ENUM_LIST)){
            $filter['constituency_strength'] = $_POST['filter_constituency_strength'];
        }
        if(isset($_POST['filter_to_date']) && $_POST['filter_to_date']){
            $filter['to_date'] = Validator::getDateTimeFromString($_POST["filter_to_date"]);
        }
        if(isset($_POST['filter_from_date']) && $_POST['filter_from_date']){
            $filter['from_date'] = Validator::getDateTimeFromString($_POST["filter_from_date"]);
        }

        $task_list = Task::getListByOrganization($organization->getId(), $filter);

        $overdue_filter = $filter;
        $overdue_filter['overdue_tasks'] = true;
        $overdue_task_list = Task::getListByOrganization($organization->getId(), $overdue_filter);

        $verify_tasks_filter = $filter;
        $verify_tasks_filter['tasks_to_verify'] = true;
        $tasks_to_verify = Task::getListByOrganization($organization->getId(), $verify_tasks_filter);

        $constituency_list = Constituency::getListByOrganization($organization->getId());

        $this->masterView('Task/list.php',
            [
                'task_list' => $task_list,
                'overdue_task_list' => $overdue_task_list,
                'tasks_to_verify' => $tasks_to_verify,
                'constituency_list' => $constituency_list,
                'filter' => $filter
            ]
        );
    }

    /**
     * @throws \Core\AppError
     * @throws \Exception
     */
    protected function listAssignedAction(){
        Auth::throwErrorIfPermissionDenied("viewAssigned_Task");

        $organization = $this->__getOrganization();

        $task_list = Task::getListByOrganizationAndUser($organization->getId(), Auth::user()->getId());
        $overdue_task_list = Task::getOverdueListByOrganizationAndUser($organization->getId(), Auth::user()->getId());

        $this->masterView('Task/list-assigned.php',
            [
                'task_list' => $task_list,
                'overdue_task_list' => $overdue_task_list
            ]
        );
    }

    /**
     * @throws \Core\AppError
     */
    protected function markAsCompletedAction(){
        Auth::throwErrorIfPermissionDenied("viewAssigned_Task");

        $required_params = array("id");
        if(Validator::checkAllParamsExists($required_params) !== true){
            View::throwAppError("Invalid Request");
        }

        $organization = $this->__getOrganization();
        $task = Task::findByMultipleProperties(
            array(
                "id" => $_POST['id'],
                "org_id" => $organization->getId(),
                "responsibility" => Auth::user()->getId()
            )
        );

        if(!$task){
            View::throwAppError("Invalid Request");
        }

        if($task->updateStatus(TaskStatus::COMPLETED_UNVERIFIED)){
            View::displayJsonSuccess("Marked as completed");
        }
        else{
            View::throwAppError("Unable to mark as completed");
        }

    }

    /**
     * @throws \Core\AppError
     */
    protected function markAsVerifiedAction(){
        Auth::throwErrorIfPermissionDenied("edit_Task");

        $required_params = array("id");
        if(Validator::checkAllParamsExists($required_params) !== true){
            View::throwAppError("Invalid Request");
        }

        $organization = $this->__getOrganization();
        $task = Task::findByMultipleProperties(
            array(
                "id" => $_POST['id'],
                "org_id" => $organization->getId(),
            )
        );

        if(!$task){
            View::throwAppError("Invalid Request");
        }

        if($task->updateStatus(TaskStatus::COMPLETED_VERIFIED)){
            View::displayJsonSuccess("Marked as Verified");
        }
        else{
            View::throwAppError("Unable to mark as verified");
        }

    }

    /**
     * @throws \Core\AppError
     * @throws \Exception
     */
    protected function editAction(){
        Auth::throwErrorIfPermissionDenied("edit_Task");

        if(!isset($this->route_params["id"])){
            View::throwAppError("Invalid Request");
        }

        $organization = $this->__getOrganization();

        $task = Task::findByMultipleProperties(
            array(
                "id" => $this->route_params["id"],
                "org_id" => $organization->getId(),
            )
        );

        if(!$task){
            View::throwAppError("Invalid Request");
        }

        $feeder_list = $organization->getUserListByRole('feeder');

        if($_SERVER['REQUEST_METHOD'] === "GET"){
            $media_files = TaskFile::getObjectListByTaskId($task->getId());
            $this->masterView('Task/edit.php',[
                'task' => $task,
                'feeder_list' => $feeder_list,
                'media_files' => $media_files,
            ]);
        }
        else if($_SERVER['REQUEST_METHOD'] === "POST"){
            $updated = array();

            if(isset($_POST['responsibility'])){
                $feeder = User::findByOrgAndId($organization->getId(), $_POST['responsibility']);
                if ($feeder && $task->updateResponsibility($feeder->getId())) {
                    $updated [] = "Responsibility";
                }
            }
            if(isset($_POST['headline'])){
                if ($task->updateHeadline($_POST ['headline'])) {
                    $updated [] = "Headline";
                }
            }
            if(isset($_POST['summary'])){
                if ($task->updateSummary($_POST ['summary'])) {
                    $updated [] = "Due Date";
                }
            }
            if(isset($_POST['due_date'])){
                if ($task->updateDueDate($_POST ['due_date'])) {
                    $updated [] = "Due Date";
                }
            }
            if(isset($_POST['criticality'])){
                if ($task->updateCriticality($_POST ['criticality'])) {
                    $updated [] = "Criticality";
                }
            }

            $uploaded_files = [];
            if (isset($_FILES) && isset($_FILES["files"])) {
                $file_count = count($_FILES['files']["name"]);
                $rearranged_files = Utils::rearrangeFilesArray($_FILES['files']);
                foreach ($rearranged_files as $file) {
                    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
                    if (strlen($file_extension) > 6) {
                        // file extension more than 6 characters, skip & eventually will stop addition.
                        continue;
                    }
                    $file_name = time() . "_" . Utils::getRandomBytes(16) . "." . $file_extension;
                    $destination_file = $task->getMediaFolder() . $file_name;
                    if (move_uploaded_file($file["tmp_name"], $destination_file)) {
                        $task_file = TaskFile::getInstance()
                            ->setFilename($file_name)
                            ->setTaskId($task->getId());
                        if($task_file->save()){
                            $uploaded_files[] = $file_name;
                        }
                    }
                }

                if(count($uploaded_files) !== $file_count && count($_FILES['files']["tmp_name"]) > 1){
                    Flash::addAndGoBack("Failed to upload the media file", Flash::WARNING);
                }
                else if(count($uploaded_files) > 0){
                    $updated[] = "Files";
                }
            }

            if($updated){
                Flash::addAndGoBack("Updated", Flash::SUCCESS);
            }
            else{
                Flash::addAndGoBack("No changes done", Flash::INFO);
            }
        }
    }

    /**
     * @throws \Core\AppError
     * @throws \Exception
     */
    protected function viewAction(){
        if(!Auth::user()->can('viewAssigned_Task')
            && !Auth::user()->can('view_Task')){
            View::throwAppError("You are not authorized", 403);
        }

        if(!isset($this->route_params["id"])){
            View::throwAppError("Invalid Request");
        }

        $organization = $this->__getOrganization();
        $task = null;
        if(Auth::user()->can('viewAssigned_Task')){
            $task = Task::findByMultipleProperties(
                array(
                    "id" => $this->route_params["id"],
                    "org_id" => $organization->getId(),
                    "responsibility" => Auth::user()->getId()
                )
            );
        }
        else if(Auth::user()->can('view_Task')){
            $task = Task::findByMultipleProperties(
                array(
                    "id" => $this->route_params["id"],
                    "org_id" => $organization->getId(),
                )
            );
        }

        if(!$task){
            View::throwAppError("Invalid Request");
        }

        $media_files = TaskFile::getObjectListByTaskId($task->getId());

        if($_SERVER['REQUEST_METHOD'] === "GET"){

            $existing_feedback = null;
            $hide_feedback = true;
            if(!isset($_GET['hide_feedback']) || $_GET['hide_feedback'] != 1){
                $hide_feedback = false;
                $existing_feedback = TaskFeedback::getListByTaskId($task->getId());
            }

            $this->masterView('Task/view.php',[
                'task' => $task,
                "existing_feedback" => $existing_feedback,
                'hide_feedback' => $hide_feedback,
                'media_files' => $media_files,
            ]);
        }
    }

    /**
     * @throws \Core\AppError
     */
    protected function feedbackAction(){
        if(!Auth::user()->can('viewAssigned_Task')
            && !Auth::user()->can('view_Task')){
            View::throwAppError("You are not authorized", 403);
        }

        if(!isset($this->route_params["id"])){
            View::throwAppError("Invalid Request");
        }

        $organization = $this->__getOrganization();
        $task = null;
        if(Auth::user()->can('viewAssigned_Task')){
            $task = Task::findByMultipleProperties(
                array(
                    "id" => $this->route_params["id"],
                    "org_id" => $organization->getId(),
                    "responsibility" => Auth::user()->getId()
                )
            );
        }
        else if(Auth::user()->can('view_Task')){
            $task = Task::findByMultipleProperties(
                array(
                    "id" => $this->route_params["id"],
                    "org_id" => $organization->getId(),
                )
            );
        }

        if($_SERVER['REQUEST_METHOD'] === "POST"){

            $feedback = TaskFeedback::getInstance()
                ->setTaskId($task->getId())
                ->setUserId(Auth::user()->getId())
                ->setOrgId($organization->getId())
                ->setComment($_POST['new_comment']);

            if($feedback->getErrors()){
                Flash::addMessageListAndGoBack($feedback->getErrors(), Flash::WARNING);
            }
            else if(!$feedback->save()){
                Flash::addAndGoBack("Unable to add the feed", Flash::WARNING);
            }

            Flash::addAndGoBack("Posted", Flash::SUCCESS);
        }
    }
}